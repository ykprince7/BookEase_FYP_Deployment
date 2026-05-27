<?php
$file = '/var/www/html/admin/ajax/dashboard.php';
echo file_exists($file) ? 'EXISTS: ' . $file : 'NOT FOUND: ' . $file;
echo '<br>';
echo 'Document root: ' . $_SERVER['DOCUMENT_ROOT'];
?>