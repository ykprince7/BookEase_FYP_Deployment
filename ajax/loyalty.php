<!-- Loyalty Points Redemption  -->
<?php
require_once('../admin/inc/db_config.php');
require_once('../inc/loyalty_points.php');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if(!(isset($_SESSION['login']) && $_SESSION['login']==true)){
    echo json_encode(['success' => false, 'message' => 'Please login first']);
    exit;
}

if(isset($_POST['redeem_points'])) {
    $reward_id = (int) $_POST['redeem_points'];
    $user_id = $_SESSION['uId'];
    
    // Log for debugging
    error_log("Redeem attempt - User ID: $user_id, Reward ID: $reward_id");
    
    try {
        $result = redeemLoyaltyPoints($user_id, $reward_id);
        error_log("Redeem result: " . json_encode($result));
        echo json_encode($result);
    } catch (Exception $e) {
        error_log("Redeem exception: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
    }
    exit;
}

echo json_encode(['success' => false, 'message' => 'Invalid request']);
?> 