<?php
$connection = @fsockopen('sandbox.smtp.mailtrap.io', 587, $errno, $errstr, 5);
if ($connection) {
    echo '✅ Port 587 reachable';
    fclose($connection);
} else {
    echo "❌ Port 587 blocked: $errstr ($errno)";
}

$connection2 = @fsockopen('sandbox.smtp.mailtrap.io', 2525, $errno, $errstr, 5);
if ($connection2) {
    echo '<br>✅ Port 2525 reachable';
    fclose($connection2);
} else {
    echo "<br>❌ Port 2525 blocked: $errstr ($errno)";
}
?>