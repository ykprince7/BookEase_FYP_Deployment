<?php
require('admin/inc/db_config.php');
require('inc/loyalty_points.php');

// Award points for booking 199 ($19 USD)
$npr_199 = 19 * 149.43; // Approximate conversion
$result1 = addLoyaltyPoints(39, 199, $npr_199);
echo 'Booking 199: ' . ($result1 ? 'Points awarded' : 'Failed') . PHP_EOL;

// Award points for booking 200 ($7 USD)
$npr_200 = 7 * 149.43; // Approximate conversion
$result2 = addLoyaltyPoints(39, 200, $npr_200);
echo 'Booking 200: ' . ($result2 ? 'Points awarded' : 'Failed') . PHP_EOL;
?>