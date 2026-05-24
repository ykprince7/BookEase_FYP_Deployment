<?php
require('admin/inc/db_config.php');
require('admin/inc/essentials.php');

date_default_timezone_set("Asia/Kathmandu");
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

$settings_q = "SELECT * FROM `settings` WHERE `sr_no`=?";
$settings_r = mysqli_fetch_assoc(select($settings_q, [1], 'i'));
$siteTitle = $settings_r ? $settings_r['site_title'] : 'BookEase';

$isValidLink = false;
$isResetDone = false;
$errorMsg = '';
$email = '';
$token = '';

if (isset($_GET['account_recovery'])) {
    $data = filteration($_GET);
    $email = $data['email'] ?? '';
    $token = $data['token'] ?? '';

    $t_date = date("Y-m-d");
    $query = select(
        "SELECT `id` FROM `user_cred` WHERE `email`=? AND `token`=? AND `t_expire`=? LIMIT 1",
        [$email, $token, $t_date],
        'sss'
    );

    $isValidLink = mysqli_num_rows($query) === 1;
}

if (isset($_POST['reset_password'])) {
    $data = filteration($_POST);
    $email = $data['email'] ?? '';
    $token = $data['token'] ?? '';
    $pass = $data['pass'] ?? '';

    if (strlen($pass) < 6) {
        $errorMsg = 'Password must be at least 6 characters long.';
        $isValidLink = true;
    } else {
        $t_date = date("Y-m-d");
        $query = select(
            "SELECT `id` FROM `user_cred` WHERE `email`=? AND `token`=? AND `t_expire`=? LIMIT 1",
            [$email, $token, $t_date],
            'sss'
        );

        if (mysqli_num_rows($query) === 1) {
            $enc_pass = password_hash($pass, PASSWORD_BCRYPT);
            $updated = update(
                "UPDATE `user_cred` SET `password`=?, `token`=?, `t_expire`=? WHERE `email`=? AND `token`=?",
                [$enc_pass, null, null, $email, $token],
                'sssss'
            );

            if ($updated) {
                $isResetDone = true;
                $isValidLink = true;
            } else {
                $errorMsg = 'Account reset failed. Please try again.';
                $isValidLink = true;
            }
        } else {
            $errorMsg = 'Invalid or expired reset link.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reset Password - <?php echo htmlspecialchars($siteTitle, ENT_QUOTES, 'UTF-8'); ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.1/font/bootstrap-icons.css">
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,400;0,500;0,600;0,700;1,400&family=Sora:wght@600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/common.css">
  <style>
    body {
      min-height: 100vh;
      background: linear-gradient(145deg, #f8fafc 0%, #eef7f5 45%, #f8fafc 100%);
    }
    .reset-wrap {
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 24px 12px;
    }
    .reset-card {
      width: 100%;
      max-width: 480px;
      border: 0;
      border-radius: 16px;
      box-shadow: 0 20px 45px rgba(15, 23, 42, 0.1);
      overflow: hidden;
    }
    .reset-card-top {
      background: linear-gradient(135deg, var(--teal) 0%, var(--teal_hover) 100%);
      color: #fff;
      padding: 22px 24px;
    }
    .reset-card-body {
      padding: 24px;
    }
  </style>
</head>
<body>
  <div class="reset-wrap">
    <div class="card reset-card">
      <div class="reset-card-top">
        <h1 class="h4 mb-1">Reset your password</h1>
        <p class="mb-0 small opacity-75">Secure access for your <?php echo htmlspecialchars($siteTitle, ENT_QUOTES, 'UTF-8'); ?> account</p>
      </div>
      <div class="reset-card-body">
        <?php if ($isResetDone): ?>
          <div class="text-center py-2">
            <i class="bi bi-check-circle-fill text-success" style="font-size: 2rem;"></i>
            <h2 class="h5 mt-3">Password reset successful</h2>
            <p class="text-muted mb-4">You can now log in with your new password.</p>
            <a href="index.php" class="btn btn-primary px-4">Back to home</a>
          </div>
        <?php elseif ($isValidLink): ?>
          <?php if ($errorMsg !== ''): ?>
            <div class="alert alert-danger py-2"><?php echo htmlspecialchars($errorMsg, ENT_QUOTES, 'UTF-8'); ?></div>
          <?php endif; ?>
          <form method="POST">
            <input type="hidden" name="email" value="<?php echo htmlspecialchars($email, ENT_QUOTES, 'UTF-8'); ?>">
            <input type="hidden" name="token" value="<?php echo htmlspecialchars($token, ENT_QUOTES, 'UTF-8'); ?>">
            <div class="mb-3">
              <label class="form-label fw-semibold">New password</label>
              <input type="password" name="pass" class="form-control shadow-none" minlength="6" required>
            </div>
            <div class="d-grid gap-2">
              <button type="submit" name="reset_password" class="btn btn-primary">Update password</button>
              <a href="index.php" class="btn btn-outline-primary">Cancel</a>
            </div>
          </form>
        <?php else: ?>
          <div class="text-center py-2">
            <i class="bi bi-exclamation-triangle-fill text-warning" style="font-size: 2rem;"></i>
            <h2 class="h5 mt-3">Invalid or expired link</h2>
            <p class="text-muted mb-4">Please request a new password reset link from login.</p>
            <a href="index.php" class="btn btn-primary px-4">Go to home</a>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</body>
</html>
