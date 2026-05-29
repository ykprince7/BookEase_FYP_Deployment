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

// Get the encoded response data from the query parameter
if(isset($_GET['data'])) {
    // Decode the base64 response
    $decoded_data = base64_decode($_GET['data']);
    
    // Add error logging to debug the response
    error_log("Decoded eSewa Response: " . $decoded_data);
    
    // Try to decode JSON and handle potential errors
    $response_data = json_decode($decoded_data, true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        // Log JSON decode error
        error_log("JSON Decode Error: " . json_last_error_msg());
        
        // Display error message
        echo '
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Payment Processing Error</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
            <style>
                .container {
                    max-width: 600px;
                    margin-top: 50px;
                    text-align: center;
                }
                .error-message {
                    color: #dc3545;
                    font-size: 1.5rem;
                    margin-bottom: 20px;
                }
                .home-button {
                    margin-top: 30px;
                }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="error-message">
                    <h2>Payment Processing Error</h2>
                    <p>We encountered an error while processing your payment response.</p>
                    <p>Please contact support if this issue persists.</p>
                </div>
                <a href="index.php" class="btn btn-primary home-button">Return to Home</a>
            </div>
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
        </body>
        </html>';
        exit;
    }

    // Print the response data for debugging
    error_log("Response Data: " . print_r($response_data, true));

    if ($response_data && isset($response_data['transaction_code'])) {
        $transaction_code = $response_data['transaction_code'];
        $status = $response_data['status'] ?? '';
        $total_amount = $response_data['total_amount'] ?? 0;
        $transaction_uuid = $response_data['transaction_uuid'] ?? '';
        $order_id_for_db  = $transaction_uuid;

        // Verify the transaction with eSewa
        $merchant_code = "EPAYTEST";
        $url = "https://rc.esewa.com.np/api/epay/transaction/status";
        
        $query_params = http_build_query([
            'transaction_uuid' => $transaction_uuid,
            'product_code'     => $merchant_code,
            'total_amount'     => number_format((float) str_replace(',', '', $total_amount), 2, '.', ''),
        ]);

        $verification_url = $url . '?' . $query_params;
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $verification_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        // Log verification response
        error_log("eSewa Verification Response: " . $response);
        error_log("HTTP Status: " . $http_status);

        if ($http_status === 200) {
            $verification_data = json_decode($response, true);
            
            if ($verification_data && $verification_data['status'] === 'COMPLETE') {
                // Payment successful, update database
                $query = "SELECT `booking_id`, `user_id` FROM `booking_order` WHERE `order_id`=?";
                $booking_res = select($query, [$order_id_for_db], 's');
                
                if(mysqli_num_rows($booking_res) > 0) {
                    $booking_fetch = mysqli_fetch_assoc($booking_res);
                    
                    $updateQuery = "UPDATE `booking_order` SET `booking_status`='booked', 
                        `trans_id`=?, `trans_amt`=?, `trans_status`='TXN_SUCCESS', 
                        `trans_resp_msg`='eSewa payment successful' 
                        WHERE `booking_id`=? AND (`booking_status`='pending' OR `booking_status` IS NULL)";
                    
                    $rowsUpdated = update($updateQuery, [$transaction_code, str_replace(',', '', $total_amount), $booking_fetch['booking_id']], 'ssi');
                    
                    // Always send confirmation email for successful payments
                    require_once __DIR__ . '/inc/booking_confirmation_mail.php';
                    send_booking_payment_confirmation_email($con, (int) $booking_fetch['booking_id']);
                    
                    // Redeem pending loyalty points after successful payment
                    if (!empty($_SESSION['pending_loyalty_reward'])) {
                      $reward_info = $_SESSION['pending_loyalty_reward'];
                      $result = redeemLoyaltyPoints($booking_fetch['user_id'], $reward_info['reward_id']);
                      if ($result['success']) {
                        // Update the notice to reflect actual redemption
                        $_SESSION['loyalty_redeem_notice'] = 
                          'You used ' . $reward_info['points_required'] . ' loyalty points on "' . $reward_info['reward_name'] . '" for a ' . rtrim(rtrim(number_format($reward_info['discount_percent'], 2, '.', ''), '0'), '.') . '% discount.';
                      }
                      unset($_SESSION['pending_loyalty_reward']);
                    }
                    
                    // Earn loyalty points on successful payment (amount already in NPR)
                    // Check if loyalty points have already been awarded for this booking
                    $existing_points = select("SELECT COUNT(*) as count FROM loyalty_transactions WHERE booking_id = ? AND type = 'earn'", [$booking_fetch['booking_id']], 'i');
                    $points_count = mysqli_fetch_assoc($existing_points)['count'];
                    
                    if ($points_count == 0) {
                      addLoyaltyPoints($booking_fetch['user_id'], $booking_fetch['booking_id'], (float) str_replace(',', '', $total_amount));
                    }

                    // Repair any other booked orders for this user missing earn transactions
                    awardMissingLoyaltyPointsForUser($booking_fetch['user_id']);

                    $loyalty_esewa_html = '';
                    if (!empty($_SESSION['loyalty_redeem_notice'])) {
                        $loyalty_esewa_html = '<div class="alert alert-success text-start mt-3 mb-0"><i class="bi bi-gift-fill me-2"></i>' . htmlspecialchars($_SESSION['loyalty_redeem_notice'], ENT_QUOTES, 'UTF-8') . '</div>';
                        unset($_SESSION['loyalty_redeem_notice']);
                    }
                    
                    // Display success message with HTML and Bootstrap 5 styling
                    echo '
                    <!DOCTYPE html>
                    <html lang="en">
                    <head>
                        <meta charset="UTF-8">
                        <meta name="viewport" content="width=device-width, initial-scale=1.0">
                        <title>Payment Successful</title>
                        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
                        <style>
                            .container {
                                max-width: 600px;
                                margin-top: 50px;
                                text-align: center;
                            }
                            .success-message {
                                color: #28a745;
                                font-size: 1.5rem;
                                margin-bottom: 20px;
                            }
                            .home-button {
                                margin-top: 30px;
                            }
                        </style>
                    </head>
                    <body>
                        <div class="container">
                            <div class="success-message">
                                <h2>Payment Successful!</h2>
                                <p>Your booking has been confirmed.</p>
                                <p>Order ID: ' . $order_id_for_db . '</p>
                                <p>Transaction ID: ' . $transaction_code . '</p>
                                ' . $loyalty_esewa_html . '
                            </div>
                            <a href="index.php" class="btn btn-primary home-button">Return to Home</a>
                        </div>
                        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
                    </body>
                    </html>';
                    exit;
                }
            } else {
                // Display error message for incomplete payment
                echo '
                <!DOCTYPE html>
                <html lang="en">
                <head>
                    <meta charset="UTF-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <title>Payment Incomplete</title>
                    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
                    <style>
                        .container {
                            max-width: 600px;
                            margin-top: 50px;
                            text-align: center;
                        }
                        .error-message {
                            color: #dc3545;
                            font-size: 1.5rem;
                            margin-bottom: 20px;
                        }
                        .home-button {
                            margin-top: 30px;
                        }
                    </style>
                </head>
                <body>
                    <div class="container">
                        <div class="error-message">
                            <h2>Payment Incomplete</h2>
                            <p>Your payment could not be completed. Please try again.</p>
                        </div>
                        <a href="index.php" class="btn btn-primary home-button">Return to Home</a>
                    </div>
                    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
                </body>
                </html>';
                exit;
            }
        } else {
            // Display error message for verification failure
            echo '
            <!DOCTYPE html>
            <html lang="en">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Verification Failed</title>
                <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
                <style>
                    .container {
                        max-width: 600px;
                        margin-top: 50px;
                        text-align: center;
                    }
                    .error-message {
                        color: #dc3545;
                        font-size: 1.5rem;
                        margin-bottom: 20px;
                    }
                    .home-button {
                        margin-top: 30px;
                    }
                </style>
            </head>
            <body>
                <div class="container">
                    <div class="error-message">
                        <h2>Verification Failed</h2>
                        <p>We could not verify your payment. Please contact support.</p>
                    </div>
                    <a href="index.php" class="btn btn-primary home-button">Return to Home</a>
                </div>
                <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
            </body>
            </html>';
            exit;
        }
    } else {
        redirect('index.php');
    }
} else {
    redirect('index.php');
}
?> 