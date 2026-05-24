<?php
require 'admin/inc/db_config.php';
$res = mysqli_query($con, 'SELECT booking_id, order_id, booking_status, trans_status, trans_resp_msg FROM booking_order ORDER BY booking_id DESC LIMIT 50');
while ($row = mysqli_fetch_assoc($res)) {
    echo implode(' | ', [$row['booking_id'], $row['order_id'], $row['booking_status'], $row['trans_status'], $row['trans_resp_msg']]) . "\n";
}
