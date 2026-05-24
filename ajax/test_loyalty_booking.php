<?php
require('admin/inc/db_config.php');
require('inc/loyalty_points.php');

$booking_ids = [200, 201];
foreach ($booking_ids as $booking_id) {
    $res = select('SELECT booking_id, user_id, trans_amt, currency FROM booking_order WHERE booking_id = ?', [$booking_id], 'i');
    $row = mysqli_fetch_assoc($res);
    if (!$row) {
        echo "Booking $booking_id not found\n";
        continue;
    }
    echo "Booking {$row['booking_id']} user {$row['user_id']} amt {$row['trans_amt']} {$row['currency']}\n";
    $npr_amount = ($row['currency'] === 'NPR') ? $row['trans_amt'] : convertCurrency($row['trans_amt'], $row['currency'], 'NPR', $con);
    echo "Converted to NPR: $npr_amount\n";
    $usd_amount = convertCurrency($npr_amount, 'NPR', 'USD', $con);
    echo "Converted back to USD: $usd_amount\n";
    $points = floor($usd_amount / 10);
    echo "Points: $points\n";
    if ($points > 0) {
        $result = addLoyaltyPoints($row['user_id'], $row['booking_id'], $npr_amount);
        echo "addLoyaltyPoints returned: " . ($result ? 'true' : 'false') . "\n";
    }
    echo "---\n";
}
?>