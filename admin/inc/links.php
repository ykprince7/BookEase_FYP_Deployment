<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
date_default_timezone_set("Asia/Kathmandu");

// Flash welcome bonus
if (!defined('BOOKEASE_DB_CONFIG_LOADED')) {
    require(__DIR__ . '/db_config.php');
}
if (!defined('BOOKEASE_ESSENTIALS_LOADED')) {
    require(__DIR__ . '/essentials.php');
}
// Load Database Configuration (Railway)
require(__DIR__ . '/db_config.php');
require(__DIR__ . '/essentials.php');

$contact_q = "SELECT * FROM `contact_details` WHERE `sr_no`=?";
$settings_q = "SELECT * FROM `settings` WHERE `sr_no`=?";
$values = [1];

$contact_r = mysqli_fetch_assoc(select($contact_q, $values, 'i'));
$settings_r = mysqli_fetch_assoc(select($settings_q, $values, 'i'));

if ($settings_r['shutdown'] ?? false) {
    echo <<<alertbar
      <div class='bg-danger text-center p-2 fw-bold'>
        <i class="bi bi-exclamation-triangle-fill"></i>
        Bookings are temporarily closed!
      </div>
    alertbar;
}

// Promotional Banner Logic
$current_page            = basename($_SERVER['PHP_SELF']);
$show_promo_banner       = false;
$promo_banner_data       = [];
$banner_user_can_claim   = false;
$banner_user_not_eligible = false;

if ($current_page === 'index.php') {

    $con = $GLOBALS['con'];

    // Silently add banner_eligible column
    if (empty($_SESSION['__bec_done'])) {
        $col_q = mysqli_query($con, "SHOW COLUMNS FROM `user_cred` LIKE 'banner_eligible'");
        if (mysqli_num_rows($col_q) == 0) {
            mysqli_query($con, "ALTER TABLE `user_cred` ADD `banner_eligible` tinyint(1) NOT NULL DEFAULT 0");
        }
        $_SESSION['__bec_done'] = 1;
    }

    // Create promo tables if not exist
    mysqli_query($con, "CREATE TABLE IF NOT EXISTS `promo_banner` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `title` varchar(255) NOT NULL DEFAULT 'Welcome to BookEase!',
      `subtitle` varchar(255) NOT NULL DEFAULT 'Exclusive New Member Offer',
      `description` text NOT NULL,
      `loyalty_points` int(11) NOT NULL DEFAULT 500,
      `is_active` tinyint(1) NOT NULL DEFAULT 1,
      `bg_image` varchar(255) NOT NULL DEFAULT '',
      `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    mysqli_query($con, "CREATE TABLE IF NOT EXISTS `banner_claims` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `user_id` int(11) NOT NULL,
      `claimed_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`),
      UNIQUE KEY `uq_user_id` (`user_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    // Seed default banner
    $bcheck = select("SELECT id FROM `promo_banner` LIMIT 1", [], '');
    if (mysqli_num_rows($bcheck) == 0) {
        insert(
            "INSERT INTO `promo_banner` (`title`,`subtitle`,`description`,`loyalty_points`,`is_active`,`bg_image`) VALUES (?,?,?,?,?,?)",
            ['Welcome to BookEase!','Exclusive New Member Offer','Start your journey with us and enjoy complimentary loyalty points on your very first login. Redeem them for exclusive discounts on future bookings!',500,1,''],
            'sssiis'
        );
    }

    // Fetch active banner
    $banner_res = select("SELECT * FROM `promo_banner` WHERE `id`=1 AND `is_active`=1 LIMIT 1", [], '');
    if (mysqli_num_rows($banner_res) > 0) {
        $promo_banner_data = mysqli_fetch_assoc($banner_res);

        $b_expired = false;
        if (!empty($promo_banner_data['expiry_date'])) {
            $b_expired = strtotime($promo_banner_data['expiry_date']) < strtotime(date('Y-m-d'));
        }

        if (!$b_expired) {
            $is_logged_in    = isset($_SESSION['login']) && $_SESSION['login'] === true;
            $target_audience = $promo_banner_data['target_audience'] ?? 'new_members';

            if ($target_audience === 'everyone') {
                $show_promo_banner        = true;
                $banner_user_not_eligible = $is_logged_in;
            } elseif ($target_audience === 'guests_only') {
                if (!$is_logged_in) $show_promo_banner = true;
            } elseif ($target_audience === 'all_members') {
                if ($is_logged_in) {
                    $uid = (int)$_SESSION['uId'];
                    $claimed_res = select("SELECT id FROM `banner_claims` WHERE `user_id`=? LIMIT 1", [$uid], 'i');
                    if (mysqli_num_rows($claimed_res) == 0) {
                        $show_promo_banner     = true;
                        $banner_user_can_claim = true;
                    }
                } else {
                    $show_promo_banner = true;
                }
            } else {
                if ($is_logged_in) {
                    $uid   = (int)$_SESSION['uId'];
                    $u_res = select("SELECT `banner_eligible` FROM `user_cred` WHERE `id`=? LIMIT 1", [$uid], 'i');
                    $u_row = mysqli_fetch_assoc($u_res);
                    $is_new_member = (int)($u_row['banner_eligible'] ?? 0) === 1;

                    if ($is_new_member) {
                        $claimed_res = select("SELECT id FROM `banner_claims` WHERE `user_id`=? LIMIT 1", [$uid], 'i');
                        if (mysqli_num_rows($claimed_res) == 0) {
                            $show_promo_banner     = true;
                            $banner_user_can_claim = true;
                        }
                    } else {
                        $show_promo_banner        = true;
                        $banner_user_not_eligible = true;
                    }
                } else {
                    $show_promo_banner = true;
                }
            }
        }
    }
}
?>

<!-- ==================== HTML LINKS ==================== -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,400;0,500;0,600;0,700;1,400&family=Sora:wght@600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.1/font/bootstrap-icons.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<link rel="stylesheet" href="css/common.css">