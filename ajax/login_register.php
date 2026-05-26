<?php 
ini_set('display_errors', 1);
error_reporting(E_ALL);

require('../admin/inc/db_config.php'); // Includes db_config.php where the filteration function is defined
require('../admin/inc/essentials.php');
require('../inc/google_config.php');
require('../inc/welcome_bonus.php');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
date_default_timezone_set("Asia/Kathmandu");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php'; // Include PHPMailer's autoloader (if using Composer)
if (isset($_GET['test_mail'])) {
    $test_email = 'np03cs4a230422@heraldcollege.edu.np';   // Change if needed
    $otp = '123456';
    $result = send_mail($test_email, $otp, 'email_confirmation');
    echo $result ? '✅ Mail sent successfully! Check Mailtrap Inbox.' : '❌ Mail failed!';
    exit;
}

function fetch_remote_json($url)
{
    if (function_exists('curl_init')) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($response !== false && $http_code === 200) {
            return json_decode($response, true);
        }
        return null;
    }

    $response = @file_get_contents($url);
    if ($response === false) {
        return null;
    }

    return json_decode($response, true);
}

function save_remote_user_image($image_url, $sub)
{
    if (!$image_url) {
        return 'default.svg';
    }

    $img_data = null;
    if (function_exists('curl_init')) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $image_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        $img_data = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($img_data === false || $http_code !== 200) {
            return 'default.svg';
        }
    } else {
        $img_data = @file_get_contents($image_url);
        if ($img_data === false) {
            return 'default.svg';
        }
    }

    $file_name = 'GOOGLE_' . preg_replace('/[^a-zA-Z0-9_-]/', '', $sub) . '.jpg';
    $save_path = UPLOAD_IMAGE_PATH . USERS_FOLDER . $file_name;

    if (@file_put_contents($save_path, $img_data) !== false) {
        return $file_name;
    }

    return 'default.svg';
}

function get_public_site_base_url()
{
    // Prefer the host the user actually used (localhost vs 127.0.0.1 vs your domain).
    // SITE_URL is often fixed to 127.0.0.1 — that breaks links on phones or if you open the site via localhost.
    if (!empty($_SERVER['HTTP_HOST']) && !empty($_SERVER['SCRIPT_NAME'])) {
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'];
        $script = $_SERVER['SCRIPT_NAME'];
        $base = dirname(dirname($script));
        $base = str_replace('\\', '/', $base);
        if ($base === '/' || $base === '.') {
            $base = '';
        }
        return rtrim($scheme . '://' . $host . $base, '/');
    }
    if (defined('SITE_URL')) {
        return rtrim(str_replace('/images/', '/', SITE_URL), '/');
    }
    return 'http://localhost/BookEase';
}


function send_mail($uemail, $token, $type)
{
    $base = get_public_site_base_url();

    if ($type == "email_confirmation") {
        $subject = "BookEase - Verify your account OTP";
        $mailBody = "<p>Your verification OTP code is: <b>$token</b></p><p>Enter this OTP to verify your account.</p>";
        $mailAltBody = "Your verification OTP code is: $token";
    } else if ($type == "account_recovery_otp") {
        $subject = "BookEase - Password Reset OTP";
        $mailBody = "<p>Your password reset OTP is: <b>$token</b></p>";
        $mailAltBody = "Your password reset OTP is: $token";
    } else {
        $subject = "Account Reset Link";
        $query = http_build_query(['account_recovery' => '1', 'email' => $uemail, 'token' => $token]);
        $link = $base . '/reset_password.php?' . $query;
        $mailBody = "Click here to reset: <a href=\"$link\">Reset Password</a>";
        $mailAltBody = "Reset link: $link";
    }

    // ✅ Read from Railway environment variables
    $mailtrap_user = getenv('MAILTRAP_USER') ?: 'f6f8a44d849cf8';
    $mailtrap_pass = getenv('MAILTRAP_PASS') ?: '5979edcf8bb21d';

    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = 'sandbox.smtp.mailtrap.io';
        $mail->SMTPAuth   = true;
        $mail->Username   = $mailtrap_user;  // ✅ was hardcoded
        $mail->Password   = $mailtrap_pass;  // ✅ was hardcoded
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 2525;

        $mail->SMTPDebug  = 2;
        $mail->Debugoutput = function($str, $level) {
            error_log("SMTP Debug: " . $str);
        };

        $mail->setFrom('bookease.noreply69@gmail.com', 'BookEase');
        $mail->addAddress($uemail);

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $mailBody;
        $mail->AltBody = $mailAltBody;

        return $mail->send() ? 1 : 0;
    } catch (Exception $e) {
        error_log('Mail Error: ' . $mail->ErrorInfo . ' | Exception: ' . $e->getMessage());
        return 0;
    }
}

function is_valid_phone($phone)
{
    // Remove any non-numeric characters
    $clean_phone = preg_replace('/\D/', '', $phone);
    // Check if exactly 10 digits
    return strlen($clean_phone) === 10 && preg_match('/^[0-9]{10}$/', $clean_phone) === 1;
}

function is_at_least_18($dob)
{
    $dob = trim($dob);
    $dob_date = DateTime::createFromFormat('Y-m-d', $dob);
    if (!$dob_date || $dob_date->format('Y-m-d') !== $dob) {
        return false;
    }

    $today = new DateTime('today');
    $age = $today->diff($dob_date)->y;
    return $age >= 18 && $dob_date <= $today;
}

// Ensure banner_eligible column exists before any registration
(function() {
    global $con;
    if (!isset($con)) { $con = $GLOBALS['con']; }
    $chk = mysqli_query($con, "SHOW COLUMNS FROM `user_cred` LIKE 'banner_eligible'");
    if ($chk && mysqli_num_rows($chk) == 0) {
        mysqli_query($con, "ALTER TABLE `user_cred` ADD `banner_eligible` tinyint(1) NOT NULL DEFAULT 0");
    }
})();

// Registration logic starts here

if (isset($_POST['register'])) {
    $data = filteration($_POST);  // Using the filteration function defined in db_config.php

    // Clean phone number: remove non-numeric characters
    $data['phonenum'] = preg_replace('/\D/', '', $data['phonenum']);

    // Match password and confirm password field
    if ($data['pass'] != $data['cpass']) {
        echo 'pass_mismatch';
        exit;
    }

    if (!is_valid_phone($data['phonenum'])) {
        echo 'phone_invalid';
        exit;
    }

    if (!is_at_least_18($data['dob'])) {
        echo 'age_invalid';
        exit;
    }

    // Check if user exists (email or phone)
    $u_exist = select("SELECT * FROM `user_cred` WHERE `email` = ? OR `phonenum` = ? LIMIT 1",
        [$data['email'], $data['phonenum']], "ss");

    if (mysqli_num_rows($u_exist) != 0) {
        $u_exist_fetch = mysqli_fetch_assoc($u_exist);
        echo ($u_exist_fetch['email'] == $data['email']) ? 'email_already' : 'phone_already';
        exit;
    }

    // Profile picture is optional — use default avatar when none uploaded
    $img = 'default.svg';
    if (!empty($_FILES['profile']['name']) && (int) $_FILES['profile']['error'] === UPLOAD_ERR_OK) {
        $img = uploadUserImage($_FILES['profile']);

        if ($img == 'inv_img') {
            echo 'inv_img';
            exit;
        } else if ($img == 'upd_failed') {
            echo 'upd_failed';
            exit;
        }
    }

    $enc_pass = password_hash($data['pass'], PASSWORD_BCRYPT);

    // OTP-based verification
    $otp = (string) random_int(100000, 999999);

    $otp_expire_date = date("Y-m-d"); // stored as DATE in this project
    $query = "INSERT INTO `user_cred`(`name`, `email`, `address`, `phonenum`, `pincode`, `dob`, `profile`, `password`, `token`, `t_expire`, `banner_eligible`)
        VALUES (?,?,?,?,?,?,?,?,?,?,?)";
    $values = [$data['name'], $data['email'], $data['address'], $data['phonenum'], $data['pincode'], $data['dob'], $img, $enc_pass, $otp, $otp_expire_date, 1];

    if (insert($query, $values, 'ssssssssssi')) {
        // Send OTP only after the user record is created successfully.
        if (!send_mail($data['email'], $otp, "email_confirmation")) {
            echo 'mail_failed';
            exit;
        }
        echo 1;
    } else {
        echo 'ins_failed';
    }
}

// OTP verification logic starts here
if (isset($_POST['verify_otp'])) {
    $data = filteration($_POST);

    $email = $data['email'] ?? '';
    $otp = $data['otp'] ?? '';

    if ($email === '' || $otp === '') {
        echo 'invalid_data';
        exit;
    }

    if (!preg_match('/^[0-9]{6}$/', $otp)) {
        echo 'otp_invalid_format';
        exit;
    }

    $u_fetch_res = select(
        "SELECT `id`, `token`, `t_expire`, `is_verified` FROM `user_cred` WHERE `email`=? LIMIT 1",
        [$email],
        's'
    );

    if (mysqli_num_rows($u_fetch_res) != 1) {
        echo 'inv_email';
        exit;
    }

    $u_fetch = mysqli_fetch_assoc($u_fetch_res);

    if ((int) $u_fetch['is_verified'] === 1) {
        echo 'already_verified';
        exit;
    }

    if ((string) $u_fetch['token'] !== (string) $otp) {
        echo 'otp_invalid';
        exit;
    }

    $today = date("Y-m-d");
    $otpExpireTs = !empty($u_fetch['t_expire']) ? strtotime($u_fetch['t_expire']) : false;
    $todayTs = strtotime($today);

    if ($otpExpireTs === false || $otpExpireTs < $todayTs) {
        echo 'otp_expired';
        exit;
    }

    $update = update(
        "UPDATE `user_cred` SET `is_verified`=?, `token`='', `t_expire`=NULL WHERE `id`=?",
        [1, $u_fetch['id']],
        'ii'
    );

    if ($update) {
        $bonus = awardWelcomeBonusIfEligible((int) $u_fetch['id']);
        if ($bonus['awarded']) {
            if (session_status() === PHP_SESSION_NONE) { session_start(); }
            $_SESSION['welcome_bonus_awarded'] = $bonus['points'];
        }
        echo 1;
    } else {
        echo 'otp_verify_failed';
    }
    exit;
}

// Login logic starts here

if (isset($_POST['login'])) {
    $data = filteration($_POST);

    $u_exist = select("SELECT * FROM `user_cred` WHERE `email`=? OR `phonenum`=? LIMIT 1",
        [$data['email_mob'], $data['email_mob']], "ss");

    if (mysqli_num_rows($u_exist) == 0) {
        echo 'inv_email_mob';
    } else {
        $u_fetch = mysqli_fetch_assoc($u_exist);
        if ($u_fetch['is_verified'] == 0) {
            echo 'not_verified';
        } else if ($u_fetch['status'] == 0) {
            echo 'inactive';
        } else {
            if (!password_verify($data['pass'], $u_fetch['password'])) {
                echo 'invalid_pass';
            } else {
                if (session_status() === PHP_SESSION_NONE) { session_start(); }
                $_SESSION['login'] = true;
                $_SESSION['uId'] = $u_fetch['id'];
                $_SESSION['uName'] = $u_fetch['name'];
                $_SESSION['uPic'] = $u_fetch['profile'];
                $_SESSION['uPhone'] = $u_fetch['phonenum'];

                $bonus = awardWelcomeBonusIfEligible((int) $u_fetch['id']);
                if ($bonus['awarded']) {
                    $_SESSION['welcome_bonus_awarded'] = $bonus['points'];
                }

                echo 1;
            }
        }
    }
}

// Forgot password logic starts here

if (isset($_POST['forgot_pass'])) {
    $data = filteration($_POST);
    
    $u_exist = select("SELECT * FROM `user_cred` WHERE `email`=? LIMIT 1", [$data['email']], "s");

    if (mysqli_num_rows($u_exist) == 0) {
        echo 'inv_email';
    } else {
        $u_fetch = mysqli_fetch_assoc($u_exist);
        if ($u_fetch['is_verified'] == 0) {
            echo 'not_verified';
        } else if ($u_fetch['status'] == 0) {
            echo 'inactive';
        } else {
            // Send OTP to email for password reset
            $token = (string) random_int(100000, 999999);

            if (!send_mail($data['email'], $token, 'account_recovery_otp')) {
                echo 'mail_failed';
            } else {
                $date = date("Y-m-d");
                $query = mysqli_query($con, "UPDATE `user_cred` SET `token`='$token', `t_expire`='$date' 
                    WHERE `id`='$u_fetch[id]'");

                if ($query) {
                    echo 1;
                } else {
                    echo 'upd_failed';
                }
            }
        }
    }
}

if (isset($_POST['recover_user'])) {
    $data = filteration($_POST);
    $email = $data['email'] ?? '';
    $otp = $data['otp'] ?? '';
    $pass = $data['pass'] ?? '';

    if ($email === '') {
        echo 'inv_email';
        exit;
    }

    if (!preg_match('/^[0-9]{6}$/', $otp)) {
        echo 'otp_invalid_format';
        exit;
    }

    $today = date("Y-m-d");
    $u_exist = select("SELECT `token`, `t_expire` FROM `user_cred` WHERE `email`=? LIMIT 1", [$email], 's');

    if (mysqli_num_rows($u_exist) === 0) {
        echo 'inv_email';
        exit;
    }

    $u_fetch = mysqli_fetch_assoc($u_exist);
    if (empty($u_fetch['t_expire']) || strtotime($u_fetch['t_expire']) < strtotime($today)) {
        echo 'otp_expired';
        exit;
    }

    if ((string) $u_fetch['token'] !== (string) $otp) {
        echo 'otp_invalid';
        exit;
    }

    $enc_pass = password_hash($pass, PASSWORD_BCRYPT);
    $query = "UPDATE `user_cred` SET `password`=?, `token`=?, `t_expire`=? 
        WHERE `email`=? AND `token`=?";
    $values = [$enc_pass, null, null, $email, $otp];

    if (update($query, $values, 'sssss')) {
        echo 1;
    } else {
        echo 'failed';
    }
}

if (isset($_POST['google_auth'])) {
    if (!defined('GOOGLE_CLIENT_ID') || GOOGLE_CLIENT_ID === '') {
        echo 'google_not_configured';
        exit;
    }

    $data = filteration($_POST);
    $credential = isset($data['credential']) ? $data['credential'] : '';

    if ($credential === '') {
        echo 'google_invalid';
        exit;
    }

    $verify_url = 'https://oauth2.googleapis.com/tokeninfo?id_token=' . urlencode($credential);
    $google_user = fetch_remote_json($verify_url);

    if (!$google_user || !isset($google_user['email']) || !isset($google_user['sub'])) {
        echo 'google_invalid';
        exit;
    }

    if (!isset($google_user['aud']) || $google_user['aud'] !== GOOGLE_CLIENT_ID) {
        echo 'google_invalid';
        exit;
    }

    if (isset($google_user['email_verified']) && $google_user['email_verified'] !== 'true') {
        echo 'google_invalid';
        exit;
    }

    $email = $google_user['email'];
    $name = isset($google_user['name']) ? $google_user['name'] : 'Google User';
    $picture = isset($google_user['picture']) ? $google_user['picture'] : '';

    $u_exist = select("SELECT * FROM `user_cred` WHERE `email`=? LIMIT 1", [$email], "s");

    if (mysqli_num_rows($u_exist) == 1) {
        $u_fetch = mysqli_fetch_assoc($u_exist);

        if ($u_fetch['status'] == 0) {
            echo 'inactive';
            exit;
        }

        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        $_SESSION['login'] = true;
        $_SESSION['uId'] = $u_fetch['id'];
        $_SESSION['uName'] = $u_fetch['name'];
        $_SESSION['uPic'] = $u_fetch['profile'];
        $_SESSION['uPhone'] = $u_fetch['phonenum'];
        echo 1;
        exit;
    }

    $google_profile = save_remote_user_image($picture, $google_user['sub']);
    $random_pass = bin2hex(random_bytes(16));
    $enc_pass = password_hash($random_pass, PASSWORD_BCRYPT);

    $query = "INSERT INTO `user_cred`(`name`, `email`, `address`, `phonenum`, `pincode`, `dob`, `profile`, `password`, `token`, `is_verified`, `banner_eligible`) VALUES (?,?,?,?,?,?,?,?,?,?,?)";
    $values = [$name, $email, '', '0', '', '2000-01-01', $google_profile, $enc_pass, '', 1, 1];

    if (!insert($query, $values, 'sssssssssii')) {
        echo 'ins_failed';
        exit;
    }

    $new_user = select("SELECT * FROM `user_cred` WHERE `email`=? LIMIT 1", [$email], "s");
    if (mysqli_num_rows($new_user) == 0) {
        echo 'ins_failed';
        exit;
    }

    $u_fetch = mysqli_fetch_assoc($new_user);
    if ($u_fetch['status'] == 0) {
        echo 'inactive';
        exit;
    }

    if (session_status() === PHP_SESSION_NONE) { session_start(); }
    $_SESSION['login'] = true;
    $_SESSION['uId'] = $u_fetch['id'];
    $_SESSION['uName'] = $u_fetch['name'];
    $_SESSION['uPic'] = $u_fetch['profile'];
    $_SESSION['uPhone'] = $u_fetch['phonenum'];

    $bonus = awardWelcomeBonusIfEligible((int) $u_fetch['id']);
    if ($bonus['awarded']) {
        $_SESSION['welcome_bonus_awarded'] = $bonus['points'];
    }

    echo 1;
    exit;
}
?>
