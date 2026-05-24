<?php
require 'admin/inc/db_config.php';
$res = mysqli_query($con, 'SELECT id,name,adult,children FROM rooms');
while ($row = mysqli_fetch_assoc($res)) {
    echo $row['id'] . '|' . $row['name'] . '|' . $row['adult'] . '|' . $row['children'] . "\n";
}
