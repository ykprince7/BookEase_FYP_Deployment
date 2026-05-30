<?php
require('admin/inc/db_config.php');
require('admin/inc/essentials.php');
require_once('inc/loyalty_points.php');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_GET['data'])) {
    if (!(isset($_SESSION['login']) && $_SESSION['login'] == true)) {
        redirect('index.php');
    }
}

// ── Reusable page renderer ────────────────────────────────────────────────────
function render_page($title, $icon, $icon_bg, $heading, $heading_color, $body_html, $btn_label, $btn_href, $btn_color)
{
    echo <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>$title</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root { --teal: #2ec1ac; --teal-dark: #1a9e8c; }
        body { background: linear-gradient(135deg, #f0fdf9 0%, #f4f6f8 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; font-family: 'Segoe UI', Arial, sans-serif; }
        .card { border: none; border-radius: 20px; box-shadow: 0 8px 40px rgba(46,193,172,.13); max-width: 520px; width: 100%; }
        .card-header { background: linear-gradient(135deg, var(--teal), var(--teal-dark)); border-radius: 20px 20px 0 0 !important; padding: 28px 32px; }
        .card-header h1 { color: #fff; font-size: 1.5rem; font-weight: 700; margin: 0; }
        .card-header p { color: rgba(255,255,255,.8); font-size: 13px; margin: 4px 0 0; }
        .icon-wrap { width: 72px; height: 72px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 2rem; margin: 0 auto 16px; }
        .detail-table td { padding: 10px 14px; font-size: 14px; border-bottom: 1px solid #f1f5f9; }
        .detail-table tr:last-child td { border-bottom: none; }
        .detail-table td:first-child { color: #6b7280; width: 45%; }
        .detail-table td:last-child { color: #0f172a; font-weight: 600; word-break: break-all; }
        .btn-teal { background: linear-gradient(135deg, var(--teal), var(--teal-dark)); border: none; color: #fff; padding: 12px 32px; border-radius: 8px; font-weight: 600; font-size: 15px; }
        .btn-teal:hover { background: var(--teal-dark); color: #fff; }
        .btn-danger-soft { background: #fee2e2; border: none; color: #dc2626; padding: 12px 32px; border-radius: 8px; font-weight: 600; font-size: 15px; }
        .btn-danger-soft:hover { background: #fecaca; color: #dc2626; }
    </style>
</head>
<body>
<div class="container px-3">
    <div class="card mx-auto">
        <div class="card-header">
            <h1>BookEase</h1>
            <p>Your trusted hotel booking platform</p>
        </div>
        <div class="card-body p-4 text-center">
            <div class="icon-wrap $icon_bg mb-3">$icon</div>
            <h2 style="color:$heading_color;font-size:1.4rem;font-weight:700;margin-bottom:6px;">$heading</h2>
            $body_html
        </div>
        <div class="card-footer bg-transparent border-0 text-center pb-4">
            <a href="$btn_href" class="btn $btn_color px-4">$btn_label</a>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
HTML;
}

// ── Main logic ────────────────────────────────────────────────────────────────
if (isset($_GET['data'])) {
    $decoded_data  = base64_decode($_GET['data']);
    $response_data = json_decode($decoded_data, true);

    error_log("Decoded eSewa Response: " . $decoded_data);

    if (json_last_error() !== JSON_ERROR_NONE) {
        error_log("JSON Decode Error: " . json_last_error_msg());
        render_page(
            'Payment Error', '⚠️', 'bg-warning bg-opacity-10',
            'Payment Processing Error', '#b45309',
            '<p class="text-muted">We encountered an error while processing your payment response.<br>Please contact support if this issue persists.</p>',
            'Return to Home', 'index.php', 'btn-danger-soft'
        );
        exit;
    }

    error_log("Response Data: " . print_r($response_data, true));

    if ($response_data && isset($response_data['transaction_code'])) {
        $transaction_code = $response_data['transaction_code'];
        $status           = $response_data['status'] ?? '';
        $total_amount     = $response_data['total_amount'] ?? 0;
        $transaction_uuid = $response_data['transaction_uuid'] ?? '';
        $order_id_for_db  = $transaction_uuid;

        // Verify with eSewa
        $query_params = http_build_query([
            'transaction_uuid' => $transaction_uuid,
            'product_code'     => 'EPAYTEST',
            'total_amount'     => number_format((float) str_replace(',', '', $total_amount), 2, '.', ''),
        ]);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://rc.esewa.com.np/api/epay/transaction/status?" . $query_params);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response    = curl_exec($ch);
        $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        error_log("eSewa Verification Response: " . $response);
        error_log("HTTP Status: " . $http_status);

        if ($http_status === 200) {
            $verification_data = json_decode($response, true);

            if ($verification_data && $verification_data['status'] === 'COMPLETE') {
                $booking_res = select("SELECT `booking_id`, `user_id` FROM `booking_order` WHERE `order_id`=?", [$order_id_for_db], 's');

                if (mysqli_num_rows($booking_res) > 0) {
                    $booking_fetch = mysqli_fetch_assoc($booking_res);

                    update(
                        "UPDATE `booking_order` SET `booking_status`='booked', `trans_id`=?, `trans_amt`=?, `trans_status`='TXN_SUCCESS', `trans_resp_msg`='eSewa payment successful' WHERE `booking_id`=? AND (`booking_status`='pending' OR `booking_status` IS NULL)",
                        [$transaction_code, str_replace(',', '', $total_amount), $booking_fetch['booking_id']],
                        'ssi'
                    );

                    require_once __DIR__ . '/inc/booking_confirmation_mail.php';
                    send_booking_payment_confirmation_email($con, (int) $booking_fetch['booking_id']);

                    // Redeem pending loyalty points
                    if (!empty($_SESSION['pending_loyalty_reward'])) {
                        $reward_info = $_SESSION['pending_loyalty_reward'];
                        $result = redeemLoyaltyPoints($booking_fetch['user_id'], $reward_info['reward_id']);
                        if ($result['success']) {
                            $_SESSION['loyalty_redeem_notice'] =
                                'You used ' . $reward_info['points_required'] . ' loyalty points on "' . $reward_info['reward_name'] . '" for a ' .
                                rtrim(rtrim(number_format($reward_info['discount_percent'], 2, '.', ''), '0'), '.') . '% discount.';
                        }
                        unset($_SESSION['pending_loyalty_reward']);
                    }

                    // Earn loyalty points
                    $existing_points = select("SELECT COUNT(*) as count FROM loyalty_transactions WHERE booking_id = ? AND type = 'earn'", [$booking_fetch['booking_id']], 'i');
                    if (mysqli_fetch_assoc($existing_points)['count'] == 0) {
                        addLoyaltyPoints($booking_fetch['user_id'], $booking_fetch['booking_id'], (float) str_replace(',', '', $total_amount));
                    }
                    awardMissingLoyaltyPointsForUser($booking_fetch['user_id']);

                    $loyalty_html = '';
                    if (!empty($_SESSION['loyalty_redeem_notice'])) {
                        $loyalty_html = '<div class="alert alert-success text-start mt-3 mb-0 rounded-3"><i class="bi bi-gift-fill me-2"></i>' .
                            htmlspecialchars($_SESSION['loyalty_redeem_notice'], ENT_QUOTES, 'UTF-8') . '</div>';
                        unset($_SESSION['loyalty_redeem_notice']);
                    }

                    $body = '
                    <p class="text-muted mb-3">Your payment has been received and your booking is confirmed.</p>
                    <table class="table detail-table text-start rounded-3 overflow-hidden border mb-3">
                        <tr><td>Order ID</td><td><span class="font-monospace">' . htmlspecialchars($order_id_for_db) . '</span></td></tr>
                        <tr><td>Transaction ID</td><td><span class="font-monospace">' . htmlspecialchars($transaction_code) . '</span></td></tr>
                        <tr><td>Amount</td><td style="color:var(--teal);font-weight:700;">NPR ' . htmlspecialchars(str_replace(',', '', $total_amount)) . '</td></tr>
                    </table>
                    ' . $loyalty_html . '
                    <p class="text-muted small mt-3">A confirmation email has been sent to your registered email address.</p>';

                    render_page(
                        'Payment Successful', '✅', 'bg-success bg-opacity-10',
                        'Payment Successful!', '#2ec1ac',
                        $body,
                        'View My Bookings', 'bookings.php', 'btn-teal'
                    );
                    exit;
                }

            } else {
                render_page(
                    'Payment Incomplete', '❌', 'bg-danger bg-opacity-10',
                    'Payment Incomplete', '#dc2626',
                    '<p class="text-muted">Your payment could not be completed. Please try again or contact support.</p>',
                    'Try Again', 'rooms.php', 'btn-danger-soft'
                );
                exit;
            }
        } else {
            render_page(
                'Verification Failed', '⚠️', 'bg-warning bg-opacity-10',
                'Verification Failed', '#b45309',
                '<p class="text-muted">We could not verify your payment with eSewa. Please contact support with your Order ID.</p>',
                'Return to Home', 'index.php', 'btn-danger-soft'
            );
            exit;
        }
    } else {
        redirect('index.php');
    }
} else {
    redirect('index.php');
}
?>