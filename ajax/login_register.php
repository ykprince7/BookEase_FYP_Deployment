<?php 
ini_set('display_errors', 1);
error_reporting(E_ALL);

require('../admin/inc/db_config.php');
require('../admin/inc/essentials.php');
require('../inc/google_config.php');
require('../inc/welcome_bonus.php');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

date_default_timezone_set("Asia/Kathmandu");

require '../vendor/autoload.php';

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
    if ($response === false) return null;
    return json_decode($response, true);
}

function save_remote_user_image($image_url, $sub)
{
    if (!$image_url) return 'default.svg';

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
        if ($img_data === false || $http_code !== 200) return 'default.svg';
    } else {
        $img_data = @file_get_contents($image_url);
        if ($img_data === false) return 'default.svg';
    }

    $file_name = 'GOOGLE_' . preg_replace('/[^a-zA-Z0-9_-]/', '', $sub) . '.jpg';
    $save_path = UPLOAD_IMAGE_PATH . USERS_FOLDER . $file_name;
    if (@file_put_contents($save_path, $img_data) !== false) return $file_name;
    return 'default.svg';
}

function get_public_site_base_url()
{
    if (!empty($_SERVER['HTTP_HOST']) && !empty($_SERVER['SCRIPT_NAME'])) {
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host   = $_SERVER['HTTP_HOST'];
        $base   = dirname(dirname($_SERVER['SCRIPT_NAME']));
        $base   = str_replace('\\', '/', $base);
        if ($base === '/' || $base === '.') $base = '';
        return rtrim($scheme . '://' . $host . $base, '/');
    }
    if (defined('SITE_URL')) return rtrim(str_replace('/images/', '/', SITE_URL), '/');
    return 'http://localhost/BookEase';
}

function get_email_template($title, $body, $footer_note)
{
    return <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>$title</title>
</head>
<body style="margin:0;padding:0;background-color:#f4f6f8;font-family:'Segoe UI',Arial,sans-serif;">
  <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f4f6f8;padding:40px 0;">
    <tr>
      <td align="center">
        <table width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;">

          <!-- Header -->
          <tr>
            <td align="center" style="background:linear-gradient(135deg,#2ec1ac,#1a9e8c);border-radius:12px 12px 0 0;padding:36px 40px;">
              <h1 style="margin:0;color:#ffffff;font-size:28px;font-weight:700;letter-spacing:-0.5px;">BookEase</h1>
              <p style="margin:6px 0 0;color:rgba(255,255,255,0.85);font-size:14px;">Your trusted hotel booking platform</p>
            </td>
          </tr>

          <!-- Body -->
          <tr>
            <td style="background:#ffffff;padding:40px;border-left:1px solid #e8ecef;border-right:1px solid #e8ecef;">
              $body
            </td>
          </tr>

          <!-- Footer -->
          <tr>
            <td style="background:#f8fafb;border:1px solid #e8ecef;border-top:none;border-radius:0 0 12px 12px;padding:24px 40px;text-align:center;">
              <p style="margin:0;color:#9ca3af;font-size:12px;line-height:1.6;">$footer_note</p>
              <p style="margin:8px 0 0;color:#9ca3af;font-size:12px;">&copy; 2026 BookEase. All rights reserved.</p>
            </td>
          </tr>

        </table>
      </td>
    </tr>
  </table>
</body>
</html>
HTML;
}

function send_mail($uemail, $token, $type)
{
    $base = get_public_site_base_url();

    if ($type == "email_confirmation") {
        $subject = "BookEase - Verify your account OTP";
        $body = <<<HTML
          <h2 style="margin:0 0 8px;color:#0f172a;font-size:22px;font-weight:700;">Verify your account</h2>
          <p style="margin:0 0 24px;color:#6b7280;font-size:15px;line-height:1.6;">Thanks for signing up! Use the OTP below to verify your email address. This code expires in 24 hours.</p>

          <div style="background:#f0fdf9;border:2px dashed #2ec1ac;border-radius:12px;padding:28px;text-align:center;margin:0 0 28px;">
            <p style="margin:0 0 6px;color:#6b7280;font-size:13px;text-transform:uppercase;letter-spacing:1px;font-weight:600;">Your OTP Code</p>
            <p style="margin:0;color:#2ec1ac;font-size:42px;font-weight:800;letter-spacing:10px;">$token</p>
          </div>

          <p style="margin:0;color:#9ca3af;font-size:13px;line-height:1.6;">If you didn't create a BookEase account, you can safely ignore this email.</p>
HTML;
        $footer = "This is an automated message. Please do not reply to this email.";

    } else if ($type == "account_recovery_otp") {
        $subject = "BookEase - Password Reset OTP";
        $body = <<<HTML
          <h2 style="margin:0 0 8px;color:#0f172a;font-size:22px;font-weight:700;">Reset your password</h2>
          <p style="margin:0 0 24px;color:#6b7280;font-size:15px;line-height:1.6;">We received a request to reset your password. Use the OTP below to proceed. This code expires in 24 hours.</p>

          <div style="background:#fff7ed;border:2px dashed #f97316;border-radius:12px;padding:28px;text-align:center;margin:0 0 28px;">
            <p style="margin:0 0 6px;color:#6b7280;font-size:13px;text-transform:uppercase;letter-spacing:1px;font-weight:600;">Password Reset OTP</p>
            <p style="margin:0;color:#f97316;font-size:42px;font-weight:800;letter-spacing:10px;">$token</p>
          </div>

          <p style="margin:0;color:#9ca3af;font-size:13px;line-height:1.6;">If you didn't request a password reset, please ignore this email. Your password will remain unchanged.</p>
HTML;
        $footer = "For security reasons, this OTP is valid for 24 hours only.";

    } else {
        $subject = "BookEase - Account Reset Link";
        $query   = http_build_query(['account_recovery' => '1', 'email' => $uemail, 'token' => $token]);
        $link    = $base . '/reset_password.php?' . $query;
        $body = <<<HTML
          <h2 style="margin:0 0 8px;color:#0f172a;font-size:22px;font-weight:700;">Reset your password</h2>
          <p style="margin:0 0 28px;color:#6b7280;font-size:15px;line-height:1.6;">Click the button below to reset your BookEase password.</p>

          <div style="text-align:center;margin:0 0 28px;">
            <a href="$link" style="display:inline-block;background:linear-gradient(135deg,#2ec1ac,#1a9e8c);color:#ffffff;text-decoration:none;padding:14px 36px;border-radius:8px;font-size:16px;font-weight:600;letter-spacing:0.3px;">Reset Password</a>
          </div>

          <p style="margin:0;color:#9ca3af;font-size:13px;line-height:1.6;">If you didn't request a password reset, please ignore this email. If the button doesn't work, copy and paste this link:<br><a href="$link" style="color:#2ec1ac;word-break:break-all;">$link</a></p>
HTML;
        $footer = "This link is valid for 24 hours. Do not share it with anyone.";
    }

    $mailBody = get_email_template($subject, $body, $footer);

    $api_token = getenv('MAILTRAP_API_TOKEN') ?: '136971d422bb7ec67ebbe3a29cb470d7';
    $inbox_id  = getenv('MAILTRAP_INBOX_ID')  ?: '4664187';

    $payload = json_encode([
        'from'    => ['email' => 'bookease.noreply69@gmail.com', 'name' => 'BookEase'],
        'to'      => [['email' => $uemail]],
        'subject' => $subject,
        'html'    => $mailBody,
    ]);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://sandbox.api.mailtrap.io/api/send/$inbox_id");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $api_token,
        'Content-Type: application/json',
    ]);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    $response = curl_exec($ch);
    $status   = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    error_log("Mailtrap API response ($status): " . $response);
    return $status === 200 ? 1 : 0;
}

// Test mail endpoint
if (isset($_GET['test_mail'])) {
    $result = send_mail('np03cs4a230422@heraldcollege.edu.np', '123456', 'email_confirmation');
    echo $result ? '✅ Mail sent! Check Mailtrap inbox.' : '❌ Mail failed! Check Railway logs.';
    exit;
}

function is_valid_phone($phone)
{
    $clean_phone = preg_replace('/\D/', '', $phone);
    return strlen($clean_phone) === 10 && preg_match('/^[0-9]{10}$/', $clean_phone) === 1;
}

function is_at_least_18($dob)
{
    $dob      = trim($dob);
    $dob_date = DateTime::createFromFormat('Y-m-d', $dob);
    if (!$dob_date || $dob_date->format('Y-m-d') !== $dob) return false;
    $today = new DateTime('today');
    $age   = $today->diff($dob_date)->y;
    return $age >= 18 && $dob_date <= $today;
}

// Ensure banner_eligible column exists — only runs on POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    (function() {
        global $con;
        if (!isset($con)) { $con = $GLOBALS['con']; }
        $chk = mysqli_query($con, "SHOW COLUMNS FROM `user_cred` LIKE 'banner_eligible'");
        if ($chk && mysqli_num_rows($chk) == 0) {
            mysqli_query($con, "ALTER TABLE `user_cred` ADD `banner_eligible` tinyint(1) NOT NULL DEFAULT 0");
        }
    })();
}

// Registration
if (isset($_POST['register'])) {
    $data = filteration($_POST);
    $data['phonenum'] = preg_replace('/\D/', '', $data['phonenum']);

    if ($data['pass'] != $data['cpass']) { echo 'pass_mismatch'; exit; }
    if (!is_valid_phone($data['phonenum'])) { echo 'phone_invalid'; exit; }
    if (!is_at_least_18($data['dob'])) { echo 'age_invalid'; exit; }

    $u_exist = select("SELECT * FROM `user_cred` WHERE `email` = ? OR `phonenum` = ? LIMIT 1",
        [$data['email'], $data['phonenum']], "ss");

    if (mysqli_num_rows($u_exist) != 0) {
        $u_exist_fetch = mysqli_fetch_assoc($u_exist);
        echo ($u_exist_fetch['email'] == $data['email']) ? 'email_already' : 'phone_already';
        exit;
    }

    $img = 'default.svg';
    if (!empty($_FILES['profile']['name']) && (int) $_FILES['profile']['error'] === UPLOAD_ERR_OK) {
        $img = uploadUserImage($_FILES['profile']);
        if ($img == 'inv_img')    { echo 'inv_img';    exit; }
        if ($img == 'upd_failed') { echo 'upd_failed'; exit; }
    }

    $enc_pass        = password_hash($data['pass'], PASSWORD_BCRYPT);
    $otp             = (string) random_int(100000, 999999);
    $otp_expire_date = date("Y-m-d", strtotime('+1 day'));

    $query  = "INSERT INTO `user_cred`(`name`, `email`, `address`, `phonenum`, `pincode`, `dob`, `profile`, `password`, `token`, `t_expire`, `banner_eligible`) VALUES (?,?,?,?,?,?,?,?,?,?,?)";
    $values = [$data['name'], $data['email'], $data['address'], $data['phonenum'], $data['pincode'], $data['dob'], $img, $enc_pass, $otp, $otp_expire_date, 1];

    if (insert($query, $values, 'ssssssssssi')) {
        if (!send_mail($data['email'], $otp, "email_confirmation")) { echo 'mail_failed'; exit; }
        echo 1;
    } else {
        echo 'ins_failed';
    }
}

// OTP verification
if (isset($_POST['verify_otp'])) {
    $data  = filteration($_POST);
    $email = $data['email'] ?? '';
    $otp   = $data['otp']   ?? '';

    if ($email === '' || $otp === '') { echo 'invalid_data'; exit; }
    if (!preg_match('/^[0-9]{6}$/', $otp)) { echo 'otp_invalid_format'; exit; }

    $u_fetch_res = select("SELECT `id`, `token`, `t_expire`, `is_verified` FROM `user_cred` WHERE `email`=? LIMIT 1", [$email], 's');
    if (mysqli_num_rows($u_fetch_res) != 1) { echo 'inv_email'; exit; }

    $u_fetch = mysqli_fetch_assoc($u_fetch_res);
    if ((int) $u_fetch['is_verified'] === 1) { echo 'already_verified'; exit; }
    if ((string) $u_fetch['token'] !== (string) $otp) { echo 'otp_invalid'; exit; }

    $today       = date("Y-m-d");
    $otpExpireTs = !empty($u_fetch['t_expire']) ? strtotime($u_fetch['t_expire']) : false;
    $todayTs     = strtotime($today);
    if ($otpExpireTs === false || $otpExpireTs < $todayTs) { echo 'otp_expired'; exit; }

    $update = update("UPDATE `user_cred` SET `is_verified`=?, `token`='', `t_expire`=NULL WHERE `id`=?", [1, $u_fetch['id']], 'ii');

    if ($update) {
        $bonus = awardWelcomeBonusIfEligible((int) $u_fetch['id']);
        if ($bonus['awarded']) {
            if (session_status() === PHP_SESSION_NONE) session_start();
            $_SESSION['welcome_bonus_awarded'] = $bonus['points'];
        }
        echo 1;
    } else {
        echo 'otp_verify_failed';
    }
    exit;
}

// Login
if (isset($_POST['login'])) {
    $data    = filteration($_POST);
    $u_exist = select("SELECT * FROM `user_cred` WHERE `email`=? OR `phonenum`=? LIMIT 1",
        [$data['email_mob'], $data['email_mob']], "ss");

    if (mysqli_num_rows($u_exist) == 0) {
        echo 'inv_email_mob';
    } else {
        $u_fetch = mysqli_fetch_assoc($u_exist);
        if ($u_fetch['is_verified'] == 0)  { echo 'not_verified'; }
        else if ($u_fetch['status'] == 0)  { echo 'inactive'; }
        else {
            if (!password_verify($data['pass'], $u_fetch['password'])) {
                echo 'invalid_pass';
            } else {
                if (session_status() === PHP_SESSION_NONE) session_start();
                $_SESSION['login']  = true;
                $_SESSION['uId']    = $u_fetch['id'];
                $_SESSION['uName']  = $u_fetch['name'];
                $_SESSION['uPic']   = $u_fetch['profile'];
                $_SESSION['uPhone'] = $u_fetch['phonenum'];

                $bonus = awardWelcomeBonusIfEligible((int) $u_fetch['id']);
                if ($bonus['awarded']) $_SESSION['welcome_bonus_awarded'] = $bonus['points'];
                echo 1;
            }
        }
    }
}

// Forgot password
if (isset($_POST['forgot_pass'])) {
    $data    = filteration($_POST);
    $u_exist = select("SELECT * FROM `user_cred` WHERE `email`=? LIMIT 1", [$data['email']], "s");

    if (mysqli_num_rows($u_exist) == 0) {
        echo 'inv_email';
    } else {
        $u_fetch = mysqli_fetch_assoc($u_exist);
        if ($u_fetch['is_verified'] == 0) { echo 'not_verified'; }
        else if ($u_fetch['status'] == 0) { echo 'inactive'; }
        else {
            $token = (string) random_int(100000, 999999);
            if (!send_mail($data['email'], $token, 'account_recovery_otp')) {
                echo 'mail_failed';
            } else {
                $date  = date("Y-m-d", strtotime('+1 day'));
                $query = mysqli_query($con, "UPDATE `user_cred` SET `token`='$token', `t_expire`='$date' WHERE `id`='$u_fetch[id]'");
                echo $query ? 1 : 'upd_failed';
            }
        }
    }
}

// Recover user
if (isset($_POST['recover_user'])) {
    $data  = filteration($_POST);
    $email = $data['email'] ?? '';
    $otp   = $data['otp']   ?? '';
    $pass  = $data['pass']  ?? '';

    if ($email === '') { echo 'inv_email'; exit; }
    if (!preg_match('/^[0-9]{6}$/', $otp)) { echo 'otp_invalid_format'; exit; }

    $today   = date("Y-m-d");
    $u_exist = select("SELECT `token`, `t_expire` FROM `user_cred` WHERE `email`=? LIMIT 1", [$email], 's');
    if (mysqli_num_rows($u_exist) === 0) { echo 'inv_email'; exit; }

    $u_fetch = mysqli_fetch_assoc($u_exist);
    if (empty($u_fetch['t_expire']) || strtotime($u_fetch['t_expire']) < strtotime($today)) { echo 'otp_expired'; exit; }
    if ((string) $u_fetch['token'] !== (string) $otp) { echo 'otp_invalid'; exit; }

    $enc_pass = password_hash($pass, PASSWORD_BCRYPT);
    $query    = "UPDATE `user_cred` SET `password`=?, `token`=?, `t_expire`=? WHERE `email`=? AND `token`=?";
    $values   = [$enc_pass, null, null, $email, $otp];
    echo update($query, $values, 'sssss') ? 1 : 'failed';
}

// Google auth
if (isset($_POST['google_auth'])) {
    if (!defined('GOOGLE_CLIENT_ID') || GOOGLE_CLIENT_ID === '') { echo 'google_not_configured'; exit; }

    $data       = filteration($_POST);
    $credential = $data['credential'] ?? '';
    if ($credential === '') { echo 'google_invalid'; exit; }

    $verify_url  = 'https://oauth2.googleapis.com/tokeninfo?id_token=' . urlencode($credential);
    $google_user = fetch_remote_json($verify_url);

    if (!$google_user || !isset($google_user['email']) || !isset($google_user['sub'])) { echo 'google_invalid'; exit; }
    if (!isset($google_user['aud']) || $google_user['aud'] !== GOOGLE_CLIENT_ID) { echo 'google_invalid'; exit; }
    if (isset($google_user['email_verified']) && $google_user['email_verified'] !== 'true') { echo 'google_invalid'; exit; }

    $email   = $google_user['email'];
    $name    = $google_user['name']    ?? 'Google User';
    $picture = $google_user['picture'] ?? '';

    $u_exist = select("SELECT * FROM `user_cred` WHERE `email`=? LIMIT 1", [$email], "s");
    if (mysqli_num_rows($u_exist) == 1) {
        $u_fetch = mysqli_fetch_assoc($u_exist);
        if ($u_fetch['status'] == 0) { echo 'inactive'; exit; }
        if (session_status() === PHP_SESSION_NONE) session_start();
        $_SESSION['login']  = true;
        $_SESSION['uId']    = $u_fetch['id'];
        $_SESSION['uName']  = $u_fetch['name'];
        $_SESSION['uPic']   = $u_fetch['profile'];
        $_SESSION['uPhone'] = $u_fetch['phonenum'];
        echo 1;
        exit;
    }

    $google_profile = save_remote_user_image($picture, $google_user['sub']);
    $random_pass    = bin2hex(random_bytes(16));
    $enc_pass       = password_hash($random_pass, PASSWORD_BCRYPT);

    $query  = "INSERT INTO `user_cred`(`name`, `email`, `address`, `phonenum`, `pincode`, `dob`, `profile`, `password`, `token`, `is_verified`, `banner_eligible`) VALUES (?,?,?,?,?,?,?,?,?,?,?)";
    $values = [$name, $email, '', '0', '', '2000-01-01', $google_profile, $enc_pass, '', 1, 1];
    if (!insert($query, $values, 'sssssssssii')) { echo 'ins_failed'; exit; }

    $new_user = select("SELECT * FROM `user_cred` WHERE `email`=? LIMIT 1", [$email], "s");
    if (mysqli_num_rows($new_user) == 0) { echo 'ins_failed'; exit; }

    $u_fetch = mysqli_fetch_assoc($new_user);
    if ($u_fetch['status'] == 0) { echo 'inactive'; exit; }

    if (session_status() === PHP_SESSION_NONE) session_start();
    $_SESSION['login']  = true;
    $_SESSION['uId']    = $u_fetch['id'];
    $_SESSION['uName']  = $u_fetch['name'];
    $_SESSION['uPic']   = $u_fetch['profile'];
    $_SESSION['uPhone'] = $u_fetch['phonenum'];

    $bonus = awardWelcomeBonusIfEligible((int) $u_fetch['id']);
    if ($bonus['awarded']) $_SESSION['welcome_bonus_awarded'] = $bonus['points'];

    echo 1;
    exit;
}