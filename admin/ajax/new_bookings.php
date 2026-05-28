<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require(__DIR__ . '/../inc/db_config.php');
require(__DIR__ . '/../inc/essentials.php');
adminLogin();

// PHPMailer via Composer autoloader (same as essentials.php)
require __DIR__ . '/../../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

header('Content-Type: application/json');

function send_booking_mail($to, $subject, $content) {
    $mailtrap_user = getenv('MAILTRAP_USER') ?: 'f6f8a44d849cf8';
    $mailtrap_pass = getenv('MAILTRAP_PASS') ?: '5979edcf8bb21d';

    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'sandbox.smtp.mailtrap.io';
        $mail->SMTPAuth   = true;
        $mail->Username   = $mailtrap_user;
        $mail->Password   = $mailtrap_pass;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 2525;
        $mail->Timeout    = 5; // Stop waiting after 5s (Railway blocks SMTP ports)

        $mail->setFrom('bookease.noreply69@gmail.com', 'BookEase');
        $mail->addAddress($to);

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $content;

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Mailer Error: " . $mail->ErrorInfo);
        return false;
    }
}

// GET BOOKINGS
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

// ASSIGN ROOM
if (isset($_POST['assign_room'])) {
    $frm_data = filteration($_POST);

    // DEBUG: log incoming values
    error_log("assign_room called | booking_id: " . $frm_data['booking_id'] . " | room_no: " . $frm_data['room_no']);

    // DEBUG: check if booking exists and show its current state
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
    error_log("DB row found: " . json_encode($check_row));

    // Fetch full booking data for email
    $query = "SELECT bo.*, bd.*, uc.email FROM `booking_order` bo
              INNER JOIN `booking_details` bd ON bo.booking_id = bd.booking_id
              INNER JOIN `user_cred` uc ON bo.user_id = uc.id
              WHERE bo.booking_id = ?";
    $res          = select($query, [$frm_data['booking_id']], 'i');
    $booking_data = mysqli_fetch_assoc($res);

    // Run the update
    $update_query = "UPDATE `booking_order` bo INNER JOIN `booking_details` bd
                     ON bo.booking_id = bd.booking_id
                     SET bo.arrival = ?, bo.rate_review = ?, bd.room_no = ?
                     WHERE bo.booking_id = ?";
    $res = update($update_query, [1, 0, $frm_data['room_no'], $frm_data['booking_id']], 'iisi');

    error_log("update() result: " . var_export($res, true));

    if ($res > 0) {
        $subject = "Booking Confirmed - Order #" . $booking_data['order_id'];
        $content = "
        <html><body style='font-family:Arial,sans-serif;line-height:1.6'>
            <div style='max-width:600px;margin:0 auto;padding:20px'>
                <div style='background:#f8f9fa;padding:20px;text-align:center'>
                    <h2>Your Booking Has Been Confirmed!</h2>
                </div>
                <p>Dear " . htmlspecialchars($booking_data['user_name']) . ",</p>
                <p>Your booking has been confirmed and your room has been assigned.</p>
                <div style='margin:20px 0;padding:15px;background:#f8f9fa'>
                    <h3>Booking Details</h3>
                    <p><strong>Order ID:</strong> " . htmlspecialchars($booking_data['order_id']) . "</p>
                    <p><strong>Room Type:</strong> " . htmlspecialchars($booking_data['room_name']) . "</p>
                    <p><strong>Room Number:</strong> " . htmlspecialchars($frm_data['room_no']) . "</p>
                    <p><strong>Check-in:</strong> " . date("d-m-Y", strtotime($booking_data['check_in'])) . "</p>
                    <p><strong>Check-out:</strong> " . date("d-m-Y", strtotime($booking_data['check_out'])) . "</p>
                    <p><strong>Total Amount:</strong> NPR" . htmlspecialchars($booking_data['trans_amt']) . "</p>
                </div>
                <p>Best regards,<br>BookEase</p>
            </div>
        </body></html>";

        echo send_booking_mail($booking_data['email'], $subject, $content) ? "1" : "email_failed_ok";
    } else {
        // DEBUG: return detailed info instead of plain "0"
        echo json_encode([
            'debug'      => 'update affected 0 rows',
            'booking_id' => $frm_data['booking_id'],
            'room_no'    => $frm_data['room_no'],
            'db_state'   => $check_row
        ]);
    }
    exit;
}

// CANCEL BOOKING
if (isset($_POST['cancel_booking'])) {
    $frm_data = filteration($_POST);

    // DEBUG: log incoming values
    error_log("cancel_booking called | booking_id: " . $frm_data['booking_id']);

    $query = "SELECT bo.*, bd.*, uc.email FROM `booking_order` bo
              INNER JOIN `booking_details` bd ON bo.booking_id = bd.booking_id
              INNER JOIN `user_cred` uc ON bo.user_id = uc.id
              WHERE bo.booking_id = ?";
    $res          = select($query, [$frm_data['booking_id']], 'i');
    $booking_data = mysqli_fetch_assoc($res);

    $query  = "UPDATE `booking_order` SET `booking_status`=?, `refund`=? WHERE `booking_id`=?";
    $res = update($query, ['cancelled', 0, $frm_data['booking_id']], 'sii');

    error_log("cancel update() result: " . var_export($res, true));

    if ($res) {
        $subject = "Booking Cancelled - Order #" . $booking_data['order_id'];
        $content = "
        <html><body style='font-family:Arial,sans-serif;line-height:1.6'>
            <div style='max-width:600px;margin:0 auto;padding:20px'>
                <div style='background:#f8f9fa;padding:20px;text-align:center'>
                    <h2>Your Booking Has Been Cancelled</h2>
                </div>
                <p>Dear " . htmlspecialchars($booking_data['user_name']) . ",</p>
                <p>Your booking has been cancelled as per your request.</p>
                <div style='margin:20px 0;padding:15px;background:#f8f9fa'>
                    <h3>Cancelled Booking Details</h3>
                    <p><strong>Order ID:</strong> " . htmlspecialchars($booking_data['order_id']) . "</p>
                    <p><strong>Room Type:</strong> " . htmlspecialchars($booking_data['room_name']) . "</p>
                    <p><strong>Check-in:</strong> " . date("d-m-Y", strtotime($booking_data['check_in'])) . "</p>
                    <p><strong>Check-out:</strong> " . date("d-m-Y", strtotime($booking_data['check_out'])) . "</p>
                    <p><strong>Amount Paid:</strong> NPR" . htmlspecialchars($booking_data['trans_amt']) . "</p>
                </div>
                <p>Best regards,<br>BookEase</p>
            </div>
        </body></html>";

        echo send_booking_mail($booking_data['email'], $subject, $content) ? "1" : "Email failed but booking cancelled";
    } else {
        echo "0";
    }
    exit;
}