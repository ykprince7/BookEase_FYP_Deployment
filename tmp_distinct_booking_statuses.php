<?php
require 'admin/inc/db_config.php';
$res = mysqli_query($con, 'SELECT DISTINCT booking_status, COUNT(*) AS cnt FROM booking_order GROUP BY booking_status');
while ($row = mysqli_fetch_assoc($res)) {
    echo $row['booking_status'] . '|' . $row['cnt'] . "\n";
}
