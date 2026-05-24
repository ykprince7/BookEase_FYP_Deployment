<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../vendor/autoload.php';

/**
 * Sends payment confirmation email to the guest after booking is marked paid.
 * Returns true if mail accepted for sending, false otherwise (does not throw).
 */
function send_booking_payment_confirmation_email($con, $booking_id)
{
    if (!defined('SITE_URL')) {
        require_once __DIR__ . '/../admin/inc/essentials.php';
    }

    $booking_id = (int) $booking_id;
    if ($booking_id < 1) {
        return false;
    }

    $q = "SELECT bo.order_id, bo.check_in, bo.check_out, bo.trans_amt, bo.currency, bo.trans_id,
                 bd.room_name, bd.total_pay, bd.user_name,
                 uc.email, uc.name AS customer_name
          FROM `booking_order` bo
          INNER JOIN `booking_details` bd ON bo.booking_id = bd.booking_id
          INNER JOIN `user_cred` uc ON bo.user_id = uc.id
          WHERE bo.booking_id = ?
          LIMIT 1";

    $res = select($q, [$booking_id], 'i');
    if (!$res || mysqli_num_rows($res) === 0) {
        return false;
    }

    $row = mysqli_fetch_assoc($res);
    $to = trim($row['email'] ?? '');
    if ($to === '' || !filter_var($to, FILTER_VALIDATE_EMAIL)) {
        return false;
    }

    $settings_res = select("SELECT `site_title` FROM `settings` WHERE `sr_no`=?", [1], 'i');
    $settings = $settings_res ? mysqli_fetch_assoc($settings_res) : null;
    $siteName = $settings ? $settings['site_title'] : 'BookEase';

    $orderId = htmlspecialchars($row['order_id'], ENT_QUOTES, 'UTF-8');
    $roomName = htmlspecialchars($row['room_name'], ENT_QUOTES, 'UTF-8');
    $guestName = htmlspecialchars($row['customer_name'] ?: $row['user_name'], ENT_QUOTES, 'UTF-8');
    $checkIn = htmlspecialchars($row['check_in'], ENT_QUOTES, 'UTF-8');
    $checkOut = htmlspecialchars($row['check_out'], ENT_QUOTES, 'UTF-8');
    $currency = strtoupper(htmlspecialchars($row['currency'] ?? 'NPR', ENT_QUOTES, 'UTF-8'));
    $amount = htmlspecialchars((string) $row['trans_amt'], ENT_QUOTES, 'UTF-8');
    $transRef = htmlspecialchars((string) ($row['trans_id'] ?? ''), ENT_QUOTES, 'UTF-8');
    $siteNameH = htmlspecialchars($siteName, ENT_QUOTES, 'UTF-8');

    $bookingsUrl = rtrim(str_replace('/images/', '/', SITE_URL), '/') . '/bookings.php';
    $bookingsUrl = htmlspecialchars($bookingsUrl, ENT_QUOTES, 'UTF-8');

    $subject = $siteName . ' — Booking confirmed (payment received)';

    $body = '
<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"></head>
<body style="font-family:Segoe UI,Roboto,Helvetica,Arial,sans-serif;line-height:1.6;color:#333;max-width:560px;margin:0 auto;padding:16px;">
  <h2 style="color:#198754;margin-top:0;">Payment confirmed</h2>
  <p>Hi ' . $guestName . ',</p>
  <p>Thank you — we have received your payment and your booking is <strong>confirmed</strong>.</p>
  <table style="width:100%;border-collapse:collapse;margin:20px 0;background:#f8f9fa;border-radius:8px;overflow:hidden;">
    <tr><td style="padding:10px 14px;border-bottom:1px solid #e9ecef;"><strong>Order reference</strong></td><td style="padding:10px 14px;border-bottom:1px solid #e9ecef;">' . $orderId . '</td></tr>
    <tr><td style="padding:10px 14px;border-bottom:1px solid #e9ecef;"><strong>Room</strong></td><td style="padding:10px 14px;border-bottom:1px solid #e9ecef;">' . $roomName . '</td></tr>
    <tr><td style="padding:10px 14px;border-bottom:1px solid #e9ecef;"><strong>Check-in</strong></td><td style="padding:10px 14px;border-bottom:1px solid #e9ecef;">' . $checkIn . '</td></tr>
    <tr><td style="padding:10px 14px;border-bottom:1px solid #e9ecef;"><strong>Check-out</strong></td><td style="padding:10px 14px;border-bottom:1px solid #e9ecef;">' . $checkOut . '</td></tr>
    <tr><td style="padding:10px 14px;border-bottom:1px solid #e9ecef;"><strong>Amount</strong></td><td style="padding:10px 14px;border-bottom:1px solid #e9ecef;">' . $currency . ' ' . $amount . '</td></tr>
    <tr><td style="padding:10px 14px;"><strong>Transaction ref.</strong></td><td style="padding:10px 14px;font-size:12px;word-break:break-all;">' . $transRef . '</td></tr>
  </table>
  <p><a href="' . $bookingsUrl . '" style="display:inline-block;background:#198754;color:#fff;padding:10px 18px;text-decoration:none;border-radius:6px;">View my bookings</a></p>
  <p style="font-size:13px;color:#6c757d;">If you did not make this booking, contact us immediately.</p>
  <p style="font-size:13px;color:#6c757d;">— ' . $siteNameH . '</p>
</body>
</html>';

    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'np03cs4a230422@heraldcollege.edu.np';
        $mail->Password = 'pjgd bhlf dngu occa';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = 465;
        $mail->CharSet = 'UTF-8';

        $mail->setFrom('np03cs4a230422@heraldcollege.edu.np', $siteName);
        $mail->addAddress($to, $guestName);

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $body;
        $mail->AltBody = strip_tags(str_replace(['<br>', '<br/>', '<br />'], "\n", $body));

        return $mail->send();
    } catch (Exception $e) {
        error_log('Booking confirmation email failed: ' . $mail->ErrorInfo);
        return false;
    }
}
