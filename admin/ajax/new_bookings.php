<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require(__DIR__ . '/../inc/db_config.php');
require(__DIR__ . '/../inc/essentials.php');
adminLogin();

header('Content-Type: application/json');

// ── Mailtrap HTTP API (no SMTP — works on Railway) ────────────────────────────
function send_booking_mail($to, $subject, $content)
{
    $api_token = getenv('MAILTRAP_API_TOKEN') ?: '136971d422bb7ec67ebbe3a29cb470d7';
    $inbox_id  = getenv('MAILTRAP_INBOX_ID')  ?: '4664187';

    $payload = json_encode([
        'from'    => ['email' => 'bookease.noreply69@gmail.com', 'name' => 'BookEase'],
        'to'      => [['email' => $to]],
        'subject' => $subject,
        'html'    => $content,
    ]);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://sandbox.api.mailtrap.io/api/send/$inbox_id");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $api_token,
        'Content-Type: application/json',
    ]);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    $response = curl_exec($ch);
    $status   = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    error_log("Mailtrap API response ($status): " . $response);
    return $status === 200;
}

function mail_body($heading, $heading_color, $rows_html, $guest_name, $note = '')
{
    return <<<HTML
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"></head>
<body style="margin:0;padding:0;background-color:#f4f6f8;font-family:'Segoe UI',Arial,sans-serif;">
  <table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f6f8;padding:40px 0;">
    <tr><td align="center">
      <table width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;">
        <tr>
          <td align="center" style="background:linear-gradient(135deg,#2ec1ac,#1a9e8c);border-radius:12px 12px 0 0;padding:32px 40px;">
            <h1 style="margin:0;color:#fff;font-size:26px;font-weight:700;">BookEase</h1>
            <p style="margin:4px 0 0;color:rgba(255,255,255,.8);font-size:13px;">Your trusted hotel booking platform</p>
          </td>
        </tr>
        <tr>
          <td style="background:#fff;padding:36px 40px;border-left:1px solid #e8ecef;border-right:1px solid #e8ecef;">
            <h2 style="margin:0 0 6px;color:$heading_color;font-size:20px;font-weight:700;">$heading</h2>
            <p style="margin:0 0 24px;color:#6b7280;font-size:15px;">Hi $guest_name,</p>
            <table width="100%" cellpadding="0" cellspacing="0" style="border-radius:10px;overflow:hidden;border:1px solid #e8ecef;margin-bottom:24px;">
              $rows_html
            </table>
            $note
            <p style="margin:0;color:#9ca3af;font-size:13px;">— BookEase Team</p>
          </td>
        </tr>
        <tr>
          <td style="background:#f8fafb;border:1px solid #e8ecef;border-top:none;border-radius:0 0 12px 12px;padding:20px 40px;text-align:center;">
            <p style="margin:0;color:#9ca3af;font-size:12px;">This is an automated message. Please do not reply.</p>
            <p style="margin:6px 0 0;color:#9ca3af;font-size:12px;">&copy; 2026 BookEase. All rights reserved.</p>
          </td>
        </tr>
      </table>
    </td></tr>
  </table>
</body>
</html>
HTML;
}

function mail_row($label, $value, $alt = false, $highlight = false)
{
    $bg    = $alt ? 'background:#fafafa;' : '';
    $color = $highlight ? 'color:#2ec1ac;font-weight:700;font-size:15px;' : 'color:#0f172a;font-weight:600;';
    return "<tr style=\"$bg\">
        <td style=\"padding:11px 16px;color:#6b7280;font-size:14px;border-bottom:1px solid #f1f5f9;width:42%;\">$label</td>
        <td style=\"padding:11px 16px;$color font-size:14px;border-bottom:1px solid #f1f5f9;\">$value</td>
    </tr>";
}

// ── GET BOOKINGS ──────────────────────────────────────────────────────────────
if (isset($_POST['get_bookings'])) {
    $frm_data = filteration($_POST);

    $query = "SELECT bo.*, bd.*, uc.email FROM `booking_order` bo
              INNER JOIN `booking_details` bd ON bo.booking_id = bd.booking_id
              INNER JOIN `user_cred` uc ON bo.user_id = uc.id
              WHERE (bo.order_id LIKE ? OR bd.phonenum LIKE ? OR bd.user_name LIKE ?)
              AND (bo.booking_status=? AND bo.arrival=?) ORDER BY bo.booking_id ASC";

    $res = select($query, ["%$frm_data[search]%", "%$frm_data[search]%", "%$frm_data[search]%", "booked", 0], 'sssss');

    if (!$res) {
        echo json_encode(['table_data' => "<tr><td colspan='5' class='text-center py-4'><b>Query failed.</b></td></tr>"]);
        exit;
    }

    if (mysqli_num_rows($res) == 0) {
        echo json_encode(['table_data' => "<tr><td colspan='5' class='text-center py-4'><b>No Data Found!</b></td></tr>"]);
        exit;
    }

    $i = 1;
    $table_data = "";

    while ($data = mysqli_fetch_assoc($res)) {
        $date     = date("d-m-Y", strtotime($data['datentime']));
        $checkin  = date("d-m-Y", strtotime($data['check_in']));
        $checkout = date("d-m-Y", strtotime($data['check_out']));

        $table_data .= "
        <tr>
            <td>$i</td>
            <td>
                <span class='badge bg-primary'>Order ID: $data[order_id]</span><br>
                <b>Name:</b> $data[user_name]<br>
                <b>Phone No:</b> $data[phonenum]
            </td>
            <td>
                <b>Room:</b> $data[room_name]<br>
                <b>Price:</b> NPR$data[price]
            </td>
            <td>
                <b>Check-in:</b> $checkin<br>
                <b>Check-out:</b> $checkout<br>
                <b>Paid:</b> $data[trans_amt] $data[currency]<br>
                <b>Date:</b> $date
            </td>
            <td>
                <div class='admin-action-group admin-action-group--stack'>
                    <button type='button' onclick='assign_room($data[booking_id])'
                        class='btn btn-primary shadow-none btn-sm d-inline-flex align-items-center justify-content-center gap-2 rounded-3 fw-semibold w-100'
                        data-bs-toggle='modal' data-bs-target='#assign-room'>
                        <i class='bi bi-door-open' aria-hidden='true'></i>
                        <span>Assign room</span>
                    </button>
                    <button type='button' onclick='cancel_booking($data[booking_id])'
                        class='btn btn-danger shadow-none btn-sm d-inline-flex align-items-center justify-content-center gap-2 rounded-3 fw-semibold w-100'>
                        <i class='bi bi-x-lg' aria-hidden='true'></i>
                        <span>Cancel booking</span>
                    </button>
                </div>
            </td>
        </tr>";
        $i++;
    }

    echo json_encode(['table_data' => $table_data]);
    exit;
}

// ── ASSIGN ROOM ───────────────────────────────────────────────────────────────
if (isset($_POST['assign_room'])) {
    $frm_data = filteration($_POST);

    error_log("assign_room called | booking_id: " . $frm_data['booking_id'] . " | room_no: " . $frm_data['room_no']);

    $check = select(
        "SELECT bo.booking_id, bo.arrival, bo.booking_status, bd.room_no
         FROM booking_order bo
         LEFT JOIN booking_details bd ON bo.booking_id = bd.booking_id
         WHERE bo.booking_id = ?",
        [$frm_data['booking_id']], 'i'
    );

    if (!$check || mysqli_num_rows($check) == 0) {
        echo json_encode(['debug' => 'booking_id not found in DB', 'booking_id' => $frm_data['booking_id']]);
        exit;
    }

    $check_row = mysqli_fetch_assoc($check);

    $query        = "SELECT bo.*, bd.*, uc.email FROM `booking_order` bo
                     INNER JOIN `booking_details` bd ON bo.booking_id = bd.booking_id
                     INNER JOIN `user_cred` uc ON bo.user_id = uc.id
                     WHERE bo.booking_id = ?";
    $res          = select($query, [$frm_data['booking_id']], 'i');
    $booking_data = mysqli_fetch_assoc($res);

    $update_query = "UPDATE `booking_order` bo INNER JOIN `booking_details` bd
                     ON bo.booking_id = bd.booking_id
                     SET bo.arrival = ?, bo.rate_review = ?, bd.room_no = ?
                     WHERE bo.booking_id = ?";
    $res = update($update_query, [1, 0, $frm_data['room_no'], $frm_data['booking_id']], 'iisi');

    error_log("update() result: " . var_export($res, true));

    if ($res > 0) {
        $guest   = htmlspecialchars($booking_data['user_name'], ENT_QUOTES, 'UTF-8');
        $rows    = mail_row('Order ID',     htmlspecialchars($booking_data['order_id']), false)
                 . mail_row('Room Type',    htmlspecialchars($booking_data['room_name']), true)
                 . mail_row('Room Number',  htmlspecialchars($frm_data['room_no']), false)
                 . mail_row('Check-in',     date("d M Y", strtotime($booking_data['check_in'])), true)
                 . mail_row('Check-out',    date("d M Y", strtotime($booking_data['check_out'])), false)
                 . mail_row('Amount Paid',  'NPR ' . htmlspecialchars($booking_data['trans_amt']), true, true);

        $note    = '<p style="margin:0 0 20px;color:#6b7280;font-size:14px;line-height:1.6;">Your room has been assigned. Please bring a valid ID at check-in. We look forward to welcoming you!</p>';
        $content = mail_body('Room Assigned — You\'re All Set! 🎉', '#2ec1ac', $rows, $guest, $note);
        $subject = "BookEase — Room Assigned for Order #" . $booking_data['order_id'];

        echo send_booking_mail($booking_data['email'], $subject, $content) ? "1" : "email_failed_ok";
    } else {
        echo json_encode([
            'debug'      => 'update affected 0 rows',
            'booking_id' => $frm_data['booking_id'],
            'room_no'    => $frm_data['room_no'],
            'db_state'   => $check_row
        ]);
    }
    exit;
}

// ── CANCEL BOOKING ────────────────────────────────────────────────────────────
if (isset($_POST['cancel_booking'])) {
    $frm_data = filteration($_POST);

    error_log("cancel_booking called | booking_id: " . $frm_data['booking_id']);

    $query        = "SELECT bo.*, bd.*, uc.email FROM `booking_order` bo
                     INNER JOIN `booking_details` bd ON bo.booking_id = bd.booking_id
                     INNER JOIN `user_cred` uc ON bo.user_id = uc.id
                     WHERE bo.booking_id = ?";
    $res          = select($query, [$frm_data['booking_id']], 'i');
    $booking_data = mysqli_fetch_assoc($res);

    $res = update(
        "UPDATE `booking_order` SET `booking_status`=?, `refund`=? WHERE `booking_id`=?",
        ['cancelled', 0, $frm_data['booking_id']], 'sii'
    );

    error_log("cancel update() result: " . var_export($res, true));

    if ($res) {
        $guest   = htmlspecialchars($booking_data['user_name'], ENT_QUOTES, 'UTF-8');
        $rows    = mail_row('Order ID',    htmlspecialchars($booking_data['order_id']), false)
                 . mail_row('Room Type',   htmlspecialchars($booking_data['room_name']), true)
                 . mail_row('Check-in',    date("d M Y", strtotime($booking_data['check_in'])), false)
                 . mail_row('Check-out',   date("d M Y", strtotime($booking_data['check_out'])), true)
                 . mail_row('Amount Paid', 'NPR ' . htmlspecialchars($booking_data['trans_amt']), false, true);

        $note    = '<p style="margin:0 0 20px;color:#6b7280;font-size:14px;line-height:1.6;">Your booking has been cancelled. A refund will be processed if applicable. Please contact us if you have any questions.</p>';
        $content = mail_body('Your Booking Has Been Cancelled', '#dc2626', $rows, $guest, $note);
        $subject = "BookEase — Booking Cancelled for Order #" . $booking_data['order_id'];

        echo send_booking_mail($booking_data['email'], $subject, $content) ? "1" : "Email failed but booking cancelled";
    } else {
        echo "0";
    }
    exit;
}