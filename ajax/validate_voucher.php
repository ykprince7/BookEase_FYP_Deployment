<!-- Validating Voucher for the user to Redeem -->
<?php
require_once('../admin/inc/db_config.php');
require_once('../inc/loyalty_points.php');


session_start();


if(!(isset($_SESSION['login']) && $_SESSION['login']==true)){
    echo json_encode(['success' => false, 'message' => 'Please login first']);
    exit;
}

if(isset($_POST['voucher_code'])) {
    $voucher_code = trim($_POST['voucher_code']);
    $user_id = $_SESSION['uId'];
    
    $voucher = validateVoucher($voucher_code, $user_id);
    
    if ($voucher) {
        echo json_encode([
            'success' => true,
            'message' => 'Voucher validated successfully',
            'discount_percent' => (float) $voucher['discount_percent'],
            'reward_name' => $voucher['reward_name']
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid, expired, or already used voucher code'
        ]);
    }
    exit;
}

echo json_encode(['success' => false, 'message' => 'Invalid request']);
?>