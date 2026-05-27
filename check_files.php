<?php
function listFiles($dir, $indent = 0) {
    $files = scandir($dir);
    foreach ($files as $file) {
        if ($file === '.' || $file === '..') continue;
        echo str_repeat('&nbsp;', $indent * 4) . $file . '<br>';
        if (is_dir($dir . '/' . $file) && $indent < 3) {
            listFiles($dir . '/' . $file, $indent + 1);
        }
    }
}
listFiles('/var/www/html');
?>