<?php
if (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
    $_SERVER['HTTPS'] = 'on';
}
if (defined('BOOKEASE_ESSENTIALS_LOADED')) return;
define('BOOKEASE_ESSENTIALS_LOADED', true);

// Frontend data
$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';

// Project is at document root on Railway (/var/www/html)
$basePath = '';

define('SITE_URL', $scheme . '://' . $host . $basePath . '/images/');
define('ABOUT_IMG_PATH', SITE_URL . 'about/');
define('CAROUSEL_IMG_PATH', SITE_URL . 'carousel/');
define('FACILITIES_IMG_PATH', SITE_URL . 'facilities/');
define('ROOMS_IMG_PATH', SITE_URL . 'rooms/');
define('ROOMS_360_PATH', SITE_URL . '360/');
define('USERS_IMG_PATH', SITE_URL . 'users/');

// Backend upload process paths
define('UPLOAD_IMAGE_PATH', $_SERVER['DOCUMENT_ROOT'] . '/images/');
define('ABOUT_FOLDER', 'about/');
define('CAROUSEL_FOLDER', 'carousel/');
define('FACILITIES_FOLDER', 'facilities/');
define('ROOMS_FOLDER', 'rooms/');
define('ROOMS_360_FOLDER', '360/');
define('ROOMS_360_FULL_PATH', UPLOAD_IMAGE_PATH . '360/');
define('BANNER_FOLDER', 'banner/');
define('BANNER_IMG_PATH', SITE_URL . 'banner/');
define('USERS_FOLDER', 'users/');

function adminLogin()
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (!(isset($_SESSION['adminLogin']) && $_SESSION['adminLogin'] === true)) {
        // Check if it's an AJAX request
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            echo 'not_logged_in';
            exit;
        } else {
            header('Location: index.php');
            exit;
        }
    }
}

function redirect($url){
    echo"<script>
        window.location.href='$url';
    </script>";
    exit;
}

function alert($type,$msg){    
    $bs_class = ($type == "success") ? "alert-success" : "alert-danger";
    echo <<<alert
        <div class="alert $bs_class alert-dismissible fade show custom-alert" role="alert">
            <strong class="me-3">$msg</strong>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    alert;
}

function uploadImage($image,$folder)
{
    $valid_mime = ['image/jpeg', 'image/png', 'image/webp', 'image/tiff']; // Added TIFF
    $img_mime = $image['type'];

    if(!in_array($img_mime,$valid_mime)){
        return 'inv_img'; // Invalid image mime or format
    }
    else if(($image['size']/(1024*1024))>2){
        return 'inv_size'; // Invalid size greater than 2MB
    }
    else{
        $ext = pathinfo($image['name'],PATHINFO_EXTENSION);
        $rname = 'IMG_'.random_int(11111,99999).".".$ext;

        $img_path = UPLOAD_IMAGE_PATH.$folder.$rname;
        if(move_uploaded_file($image['tmp_name'],$img_path)){
            return $rname;
        }
        else{
            return 'upd_failed';
        }
    }
}

function deleteImage($image, $folder)
{
    $file_path = UPLOAD_IMAGE_PATH . $folder . $image;
    if (file_exists($file_path)) {
        if (unlink($file_path)) {
            return true;
        } else {
            return false;
        }
    } else {
        error_log("File not found: $file_path");
        return false;
    }
}

function uploadSVGImage($image,$folder)
{
    $valid_mime = ['image/svg+xml'];
    $img_mime = $image['type'];

    if(!in_array($img_mime,$valid_mime)){
        return 'inv_img';
    }
    else if(($image['size']/(1024*1024))>1){
        return 'inv_size';
    }
    else{
        $ext = pathinfo($image['name'],PATHINFO_EXTENSION);
        $rname = 'IMG_'.random_int(11111,99999).".$ext";

        $img_path = UPLOAD_IMAGE_PATH.$folder.$rname;
        if(move_uploaded_file($image['tmp_name'],$img_path)){
            return $rname;
        }
        else{
            return 'upd_failed';
        }
    }
}

function uploadUserImage($image)
{
    $valid_mime = ['image/jpeg', 'image/png', 'image/webp', 'image/tiff']; // Added TIFF
    $img_mime = $image['type'];

    if(!in_array($img_mime,$valid_mime)){
        return 'inv_img';
    }
    else
    {
        $ext = pathinfo($image['name'],PATHINFO_EXTENSION);
        $rname = 'IMG_'.random_int(11111,99999).".".$ext; // Keep original extension

        $img_path = UPLOAD_IMAGE_PATH.USERS_FOLDER.$rname;

        if(move_uploaded_file($image['tmp_name'],$img_path)){
            return $rname;
        }
        else{
            return 'upd_failed';
        }
    }
}

?>
