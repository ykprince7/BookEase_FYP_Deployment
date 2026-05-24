<?php
require('admin/inc/db_config.php');
require('admin/inc/essentials.php');

session_start();

if (!(isset($_SESSION['login']) && $_SESSION['login'] == true)) {
    redirect('index.php');
}

if(isset($_GET['pid'])) {
    $order_id = $_GET['pid'];
    
    // Update the booking status to failed
    $query = "SELECT `booking_id`, `user_id` FROM `booking_order` WHERE `order_id`=?";
    $booking_res = select($query, [$order_id], 's');
    
    if(mysqli_num_rows($booking_res) > 0) {
        $booking_fetch = mysqli_fetch_assoc($booking_res);
        
        $updateQuery = "UPDATE `booking_order` SET `booking_status`='payment_failed', 
            `trans_status`='TXN_FAILURE', 
            `trans_resp_msg`='eSewa payment failed' 
            WHERE `booking_id`=?";
        
        update($updateQuery, [$booking_fetch['booking_id']], 'i');
    }
    
    // Clear pending loyalty reward since payment failed
    unset($_SESSION['pending_loyalty_reward']);
    unset($_SESSION['loyalty_redeem_notice']);
    // Display failure message with HTML and Bootstrap 5 styling
    echo '
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Payment Failed</title>
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
                <h2>Payment Failed</h2>
                <p>Your payment could not be processed. Please try again.</p>
                <p>Order ID: ' . $order_id . '</p>
            </div>
            <a href="index.php" class="btn btn-primary home-button">Return to Home</a>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    </body>
    </html>';
    exit;
} else {
    redirect('index.php');
}
?> 