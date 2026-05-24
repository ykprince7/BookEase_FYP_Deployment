<?php
require_once(__DIR__ . '/../admin/inc/db_config.php');
require_once(__DIR__ . '/../admin/inc/essentials.php');
require_once(__DIR__ . '/currency_helpers.php');

// Function to get user's loyalty points balance
function getLoyaltyPointsBalance($user_id) {
    $query = "SELECT points_balance FROM loyalty_points WHERE user_id = ?";
    $values = [$user_id];
    $result = select($query, $values, 'i');
    
    if ($row = mysqli_fetch_assoc($result)) {
        return $row['points_balance'];
    }
    
    // If no record exists, create one with 0 points
    $query = "INSERT INTO loyalty_points (user_id, points_balance) VALUES (?, 0)";
    insert($query, $values, 'i');
    
    return 0;
}

// Function to add points for a booking
function addLoyaltyPoints($user_id, $booking_id, $amount_npr) {
    // Calculate points based on NPR amount (1 point per NPR 10 spent)
    $points = floor($amount_npr / 10);

    if ($points <= 0) return false;

    $con = $GLOBALS['con'];
    mysqli_begin_transaction($con);
    
    try {
        // Add points to user's balance
        $query = "INSERT INTO loyalty_points (user_id, points_balance) 
                  VALUES (?, ?) 
                  ON DUPLICATE KEY UPDATE points_balance = points_balance + ?";
        $values = [$user_id, $points, $points];
        insert($query, $values, 'iii');
        
        // Record the transaction
        $query = "INSERT INTO loyalty_transactions (user_id, points, type, description, booking_id) 
                  VALUES (?, ?, 'earn', 'Points earned from booking', ?)";
        $values = [$user_id, $points, $booking_id];
        insert($query, $values, 'iii');
        
        mysqli_commit($con);
        return true;
    } catch (Exception $e) {
        mysqli_rollback($con);
        return false;
    }
}

// Award any booked order for this user that has no earned loyalty transaction yet
function awardMissingLoyaltyPointsForUser($user_id) {
    $query = "SELECT bo.booking_id, bo.trans_amt, bo.currency FROM booking_order bo " .
             "WHERE bo.user_id = ? AND bo.booking_status = 'booked' AND bo.trans_status = 'TXN_SUCCESS' " .
             "AND NOT EXISTS (SELECT 1 FROM loyalty_transactions lt WHERE lt.booking_id = bo.booking_id AND lt.type = 'earn')";
    $result = select($query, [$user_id], 'i');

    while ($booking = mysqli_fetch_assoc($result)) {
        $amount_npr = ($booking['currency'] === 'NPR') ? 
            $booking['trans_amt'] : 
            convertCurrency($booking['trans_amt'], $booking['currency'], 'NPR', $GLOBALS['con']);

        addLoyaltyPoints($user_id, $booking['booking_id'], (float) $amount_npr);
    }
}

// Function to redeem points for a voucher code
function redeemLoyaltyPoints($user_id, $reward_id) {
    $reward_id = (int) $reward_id; // Ensure it's an integer
    
    // Get reward details
    $query = "SELECT * FROM loyalty_rewards WHERE id = ? AND is_active = 1";
    $values = [$reward_id];
    $result = select($query, $values, 'i');
    
    if (!($reward = mysqli_fetch_assoc($result))) {
        return ['success' => false, 'message' => 'Invalid reward'];
    }
    
    // Check if user has enough points
    $current_points = getLoyaltyPointsBalance($user_id);
    if ($current_points < $reward['points_required']) {
        return ['success' => false, 'message' => 'Insufficient points'];
    }
    
    $con = $GLOBALS['con'];
    mysqli_begin_transaction($con);
    
    try {
        // Generate unique voucher code
        $voucher_code = '';
        $attempts = 0;
        do {
            $voucher_code = 'VOUCHER-' . strtoupper(substr(md5(uniqid($user_id . $reward_id . time() . rand() . $attempts, true)), 0, 8));
            $check_query = "SELECT id FROM user_vouchers WHERE voucher_code = ?";
            $check_result = select($check_query, [$voucher_code], 's');
            $attempts++;
        } while (mysqli_num_rows($check_result) > 0 && $attempts < 10);
        
        if (mysqli_num_rows($check_result) > 0) {
            throw new Exception('Could not generate unique voucher code');
        }
        
        // Create voucher record
        $query = "INSERT INTO user_vouchers (user_id, reward_id, voucher_code, discount_percent, points_used, is_used, created_at, expires_at) 
                  VALUES (?, ?, ?, ?, ?, 0, NOW(), DATE_ADD(NOW(), INTERVAL 30 DAY))";
        $values = [$user_id, $reward_id, $voucher_code, (float) $reward['discount_percent'], (int) $reward['points_required']];
        insert($query, $values, 'issdi');
        
        // Deduct points from user's balance
        $query = "UPDATE loyalty_points 
                  SET points_balance = points_balance - ? 
                  WHERE user_id = ?";
        $values = [$reward['points_required'], $user_id];
        update($query, $values, 'ii');
        
        // Record the transaction
        $query = "INSERT INTO loyalty_transactions (user_id, points, type, description) 
                  VALUES (?, ?, 'redeem', ?)";
        $values = [$user_id, $reward['points_required'], 'Redeemed ' . $reward['name'] . ' - Voucher: ' . $voucher_code];
        insert($query, $values, 'iis');
        
        mysqli_commit($con);
        return [
            'success' => true,
            'message' => 'Points redeemed successfully! Your voucher code is: <strong>' . $voucher_code . '</strong>',
            'voucher_code' => $voucher_code,
            'discount_percent' => (float) $reward['discount_percent'],
            'points_used' => (int) $reward['points_required'],
            'reward_name' => $reward['name'],
        ];
    } catch (Exception $e) {
        mysqli_rollback($con);
        return ['success' => false, 'message' => 'Failed to redeem points: ' . $e->getMessage()];
    }
}

// Function to get user's vouchers
function getUserVouchers($user_id) {
    $query = "SELECT uv.*, lr.name as reward_name 
              FROM user_vouchers uv 
              JOIN loyalty_rewards lr ON uv.reward_id = lr.id 
              WHERE uv.user_id = ? AND uv.is_used = 0 AND uv.expires_at > NOW() 
              ORDER BY uv.created_at DESC";
    $values = [$user_id];
    return select($query, $values, 'i');
}

// Function to validate and get voucher details
function validateVoucher($voucher_code, $user_id = null) {
    $query = "SELECT uv.*, lr.name as reward_name 
              FROM user_vouchers uv 
              JOIN loyalty_rewards lr ON uv.reward_id = lr.id 
              WHERE uv.voucher_code = ? AND uv.is_used = 0 AND uv.expires_at > NOW()";
    
    if ($user_id) {
        $query .= " AND uv.user_id = ?";
        $values = [$voucher_code, $user_id];
        $types = 'si';
    } else {
        $values = [$voucher_code];
        $types = 's';
    }
    
    $result = select($query, $values, $types);
    
    if ($row = mysqli_fetch_assoc($result)) {
        return $row;
    }
    
    return false;
}

// Function to use a voucher
function useVoucher($voucher_code, $user_id) {
    $voucher = validateVoucher($voucher_code, $user_id);
    
    if (!$voucher) {
        return ['success' => false, 'message' => 'Invalid or expired voucher code'];
    }
    
    $con = $GLOBALS['con'];
    mysqli_begin_transaction($con);
    
    try {
        // Mark voucher as used
        $query = "UPDATE user_vouchers SET is_used = 1, used_at = NOW() WHERE voucher_code = ?";
        $values = [$voucher_code];
        update($query, $values, 's');
        
        mysqli_commit($con);
        return [
            'success' => true,
            'message' => 'Voucher applied successfully',
            'discount_percent' => (float) $voucher['discount_percent'],
            'reward_name' => $voucher['reward_name']
        ];
    } catch (Exception $e) {
        mysqli_rollback($con);
        return ['success' => false, 'message' => 'Failed to apply voucher'];
    }
}

// Function to get available rewards
function getAvailableRewards() {
    $query = "SELECT * FROM loyalty_rewards WHERE is_active = 1 ORDER BY points_required ASC";
    return select($query, [], '');
}

// Function to get user's transaction history
function getLoyaltyTransactions($user_id) {
    $query = "SELECT * FROM loyalty_transactions WHERE user_id = ? ORDER BY created_at DESC";
    $values = [$user_id];
    return select($query, $values, 'i');
}
?>