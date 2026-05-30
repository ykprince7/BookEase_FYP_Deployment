<?php

/**
 * Sends payment confirmation email to the guest after booking is marked paid.
 * Uses Mailtrap HTTP API — works on Railway (no SMTP needed).
 */
function send_booking_payment_confirmation_email($con, $booking_id)
{
    if (!defined('SITE_URL')) {
        require_once __DIR__ . '/../admin/inc/essentials.php';
    }

    $booking_id = (int) $booking_id;
    if ($booking_id < 1) return false;

    $q = "SELECT bo.order_id, bo.check_in, bo.check_out, bo.trans_amt, bo.currency, bo.trans_id,
                 bd.room_name, bd.total_pay, bd.user_name,
                 uc.email, uc.name AS customer_name
          FROM `booking_order` bo
          INNER JOIN `booking_details` bd ON bo.booking_id = bd.booking_id
          INNER JOIN `user_cred` uc ON bo.user_id = uc.id
          WHERE bo.booking_id = ?
          LIMIT 1";

    $res = select($q, [$booking_id], 'i');
    if (!$res || mysqli_num_rows($res) === 0) return false;

    $row = mysqli_fetch_assoc($res);
    $to  = trim($row['email'] ?? '');
    if ($to === '' || !filter_var($to, FILTER_VALIDATE_EMAIL)) return false;

    $settings_res = select("SELECT `site_title` FROM `settings` WHERE `sr_no`=?", [1], 'i');
    $settings     = $settings_res ? mysqli_fetch_assoc($settings_res) : null;
    $siteName     = $settings ? $settings['site_title'] : 'BookEase';

    $orderId   = htmlspecialchars($row['order_id'],                        ENT_QUOTES, 'UTF-8');
    $roomName  = htmlspecialchars($row['room_name'],                       ENT_QUOTES, 'UTF-8');
    $guestName = htmlspecialchars($row['customer_name'] ?: $row['user_name'], ENT_QUOTES, 'UTF-8');
    $checkIn   = htmlspecialchars($row['check_in'],                        ENT_QUOTES, 'UTF-8');
    $checkOut  = htmlspecialchars($row['check_out'],                       ENT_QUOTES, 'UTF-8');
    $currency  = strtoupper(htmlspecialchars($row['currency'] ?? 'NPR',    ENT_QUOTES, 'UTF-8'));
    $amount    = htmlspecialchars((string) $row['trans_amt'],              ENT_QUOTES, 'UTF-8');
    $transRef  = htmlspecialchars((string) ($row['trans_id'] ?? ''),       ENT_QUOTES, 'UTF-8');
    $siteNameH = htmlspecialchars($siteName,                               ENT_QUOTES, 'UTF-8');

    $bookingsUrl = rtrim(str_replace('/images/', '/', SITE_URL), '/') . '/bookings.php';
    $bookingsUrl = htmlspecialchars($bookingsUrl, ENT_QUOTES, 'UTF-8');

    $subject = $siteName . ' — Booking confirmed (payment received)';

    $body = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"></head>
<body style="margin:0;padding:0;background-color:#f4f6f8;font-family:'Segoe UI',Arial,sans-serif;">
  <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f4f6f8;padding:40px 0;">
    <tr>
      <td align="center">
        <table width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;">

          <!-- Header -->
          <tr>
            <td align="center" style="background:linear-gradient(135deg,#2ec1ac,#1a9e8c);border-radius:12px 12px 0 0;padding:36px 40px;">
              <h1 style="margin:0;color:#ffffff;font-size:28px;font-weight:700;letter-spacing:-0.5px;">$siteNameH</h1>
              <p style="margin:6px 0 0;color:rgba(255,255,255,0.85);font-size:14px;">Your trusted hotel booking platform</p>
            </td>
          </tr>

          <!-- Body -->
          <tr>
            <td style="background:#ffffff;padding:40px;border-left:1px solid #e8ecef;border-right:1px solid #e8ecef;">

              <div style="text-align:center;margin-bottom:28px;">
                <div style="display:inline-block;background:#f0fdf9;border-radius:50%;width:64px;height:64px;line-height:64px;font-size:32px;">✅</div>
              </div>

              <h2 style="margin:0 0 8px;color:#0f172a;font-size:22px;font-weight:700;text-align:center;">Booking Confirmed!</h2>
              <p style="margin:0 0 28px;color:#6b7280;font-size:15px;line-height:1.6;text-align:center;">Hi $guestName, your payment has been received and your booking is confirmed.</p>

              <!-- Booking Details -->
              <table width="100%" cellpadding="0" cellspacing="0" style="border-radius:10px;overflow:hidden;border:1px solid #e8ecef;margin-bottom:28px;">
                <tr style="background:#f8fafb;">
                  <td colspan="2" style="padding:12px 16px;font-weight:700;color:#0f172a;font-size:13px;text-transform:uppercase;letter-spacing:0.5px;border-bottom:1px solid #e8ecef;">Booking Details</td>
                </tr>
                <tr>
                  <td style="padding:12px 16px;color:#6b7280;font-size:14px;border-bottom:1px solid #f1f5f9;width:40%;">Order Reference</td>
                  <td style="padding:12px 16px;color:#0f172a;font-size:14px;font-weight:600;border-bottom:1px solid #f1f5f9;font-family:monospace;">$orderId</td>
                </tr>
                <tr style="background:#fafafa;">
                  <td style="padding:12px 16px;color:#6b7280;font-size:14px;border-bottom:1px solid #f1f5f9;">Room</td>
                  <td style="padding:12px 16px;color:#0f172a;font-size:14px;font-weight:600;border-bottom:1px solid #f1f5f9;">$roomName</td>
                </tr>
                <tr>
                  <td style="padding:12px 16px;color:#6b7280;font-size:14px;border-bottom:1px solid #f1f5f9;">Check-in</td>
                  <td style="padding:12px 16px;color:#0f172a;font-size:14px;font-weight:600;border-bottom:1px solid #f1f5f9;">$checkIn</td>
                </tr>
                <tr style="background:#fafafa;">
                  <td style="padding:12px 16px;color:#6b7280;font-size:14px;border-bottom:1px solid #f1f5f9;">Check-out</td>
                  <td style="padding:12px 16px;color:#0f172a;font-size:14px;font-weight:600;border-bottom:1px solid #f1f5f9;">$checkOut</td>
                </tr>
                <tr>
                  <td style="padding:12px 16px;color:#6b7280;font-size:14px;border-bottom:1px solid #f1f5f9;">Amount Paid</td>
                  <td style="padding:12px 16px;color:#2ec1ac;font-size:15px;font-weight:700;border-bottom:1px solid #f1f5f9;">$currency $amount</td>
                </tr>
                <tr style="background:#fafafa;">
                  <td style="padding:12px 16px;color:#6b7280;font-size:14px;">Transaction Ref.</td>
                  <td style="padding:12px 16px;color:#0f172a;font-size:12px;font-family:monospace;word-break:break-all;">$transRef</td>
                </tr>
              </table>

              <div style="text-align:center;margin-bottom:20px;">
                <a href="$bookingsUrl" style="display:inline-block;background:linear-gradient(135deg,#2ec1ac,#1a9e8c);color:#ffffff;text-decoration:none;padding:14px 36px;border-radius:8px;font-size:16px;font-weight:600;">View My Bookings</a>
              </div>

              <p style="margin:0;color:#9ca3af;font-size:13px;line-height:1.6;text-align:center;">If you did not make this booking, please contact us immediately.</p>
            </td>
          </tr>

          <!-- Footer -->
          <tr>
            <td style="background:#f8fafb;border:1px solid #e8ecef;border-top:none;border-radius:0 0 12px 12px;padding:24px 40px;text-align:center;">
              <p style="margin:0;color:#9ca3af;font-size:12px;line-height:1.6;">This is an automated message. Please do not reply to this email.</p>
              <p style="margin:8px 0 0;color:#9ca3af;font-size:12px;">&copy; 2026 $siteNameH. All rights reserved.</p>
            </td>
          </tr>

        </table>
      </td>
    </tr>
  </table>
</body>
</html>
HTML;

    $api_token = getenv('MAILTRAP_API_TOKEN') ?: '136971d422bb7ec67ebbe3a29cb470d7';
    $inbox_id  = getenv('MAILTRAP_INBOX_ID')  ?: '4664187';

    $payload = json_encode([
        'from'    => ['email' => 'bookease.noreply69@gmail.com', 'name' => $siteName],
        'to'      => [['email' => $to, 'name' => $guestName]],
        'subject' => $subject,
        'html'    => $body,
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

    error_log("Booking confirmation email API response ($status): " . $response);
    return $status === 200;
}