<?php
require('admin/inc/db_config.php');

if (!$con) {
    echo "Database connection failed: " . mysqli_connect_error();
    exit;
}

echo "Database connected successfully.\n";

// Check if database 'hbw' exists
$result = mysqli_query($con, "SELECT DATABASE()");
$row = mysqli_fetch_row($result);
echo "Current database: " . $row[0] . "\n";

// Check if settings table exists
$result = mysqli_query($con, "SHOW TABLES LIKE 'settings'");
if (mysqli_num_rows($result) > 0) {
    echo "Settings table exists.\n";
    
    // Check shutdown value
    $shutdown = mysqli_fetch_assoc(mysqli_query($con, "SELECT `shutdown` FROM `settings`"));
    echo "Shutdown value: " . $shutdown['shutdown'] . "\n";
} else {
    echo "Settings table does not exist.\n";
}

// Check admin_cred table
$result = mysqli_query($con, "SHOW TABLES LIKE 'admin_cred'");
if (mysqli_num_rows($result) > 0) {
    echo "Admin_cred table exists.\n";
    
    $result = mysqli_query($con, "SELECT COUNT(*) as count FROM admin_cred");
    $row = mysqli_fetch_assoc($result);
    echo "Admin users: " . $row['count'] . "\n";
} else {
    echo "Admin_cred table does not exist.\n";
}

// Check other tables
$tables = ['booking_order', 'user_queries', 'rating_review', 'user_cred', 'carousel'];
foreach ($tables as $table) {
    $result = mysqli_query($con, "SHOW TABLES LIKE '$table'");
    if (mysqli_num_rows($result) > 0) {
        echo "$table table exists.\n";
        
        // Check columns for carousel
        if ($table == 'carousel') {
            $result2 = mysqli_query($con, "DESCRIBE $table");
            echo "Columns in $table: ";
            while ($row = mysqli_fetch_assoc($result2)) {
                echo $row['Field'] . " (" . $row['Type'] . "), ";
            }
            echo "\n";
        }
    } else {
        echo "$table table does not exist.\n";
    }
}
?>