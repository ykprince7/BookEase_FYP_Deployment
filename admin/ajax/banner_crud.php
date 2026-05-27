<?php

require(__DIR__ .'../inc/db_config.php');
require(__DIR__ .'../inc/essentials.php');
adminLogin();

$con = $GLOBALS['con'];

// ── Base table (original columns) ──────────────────────────
mysqli_query($con, "CREATE TABLE IF NOT EXISTS `promo_banner` (
    `id`             int(11)      NOT NULL AUTO_INCREMENT,
    `title`          varchar(255) NOT NULL DEFAULT 'Welcome to BookEase!',
    `subtitle`       varchar(255) NOT NULL DEFAULT 'Exclusive New Member Offer',
    `description`    text         NOT NULL,
    `loyalty_points` int(11)      NOT NULL DEFAULT 500,
    `is_active`      tinyint(1)   NOT NULL DEFAULT 1,
    `bg_image`       varchar(255) NOT NULL DEFAULT '',
    `updated_at`     timestamp    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

// ── Extend with future-proof columns (safe ALTER if not already present) ──
$extend_cols = [
    "occasion"        => "varchar(50)  NOT NULL DEFAULT 'welcome_bonus'",
    "offer_text"      => "varchar(100) NOT NULL DEFAULT ''",
    "offer_label"     => "varchar(100) NOT NULL DEFAULT 'LOYALTY POINTS'",
    "badge_label"     => "varchar(100) NOT NULL DEFAULT 'NEW MEMBER EXCLUSIVE'",
    "cta_text"        => "varchar(100) NOT NULL DEFAULT ''",
    "cta_url"         => "varchar(255) NOT NULL DEFAULT ''",
    "target_audience" => "varchar(50)  NOT NULL DEFAULT 'new_members'",
    "expiry_date"     => "date         DEFAULT NULL",
];
foreach ($extend_cols as $col => $def) {
    $chk = mysqli_query($con, "SHOW COLUMNS FROM `promo_banner` LIKE '$col'");
    if (mysqli_num_rows($chk) == 0) {
        mysqli_query($con, "ALTER TABLE `promo_banner` ADD `$col` $def");
    }
}

// ── Claims table ────────────────────────────────────────────
mysqli_query($con, "CREATE TABLE IF NOT EXISTS `banner_claims` (
    `id`         int(11)  NOT NULL AUTO_INCREMENT,
    `user_id`    int(11)  NOT NULL,
    `claimed_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

// ── Seed default banner row ─────────────────────────────────
$check = select("SELECT id FROM `promo_banner` LIMIT 1", [], '');
if (mysqli_num_rows($check) == 0) {
    insert(
        "INSERT INTO `promo_banner`
         (`title`,`subtitle`,`description`,`loyalty_points`,`is_active`,`bg_image`,
          `occasion`,`offer_text`,`offer_label`,`badge_label`,`cta_text`,`cta_url`,`target_audience`,`expiry_date`)
         VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)",
        ['Welcome to BookEase!','Exclusive New Member Offer',
         'Start your journey with us and enjoy complimentary loyalty points on your very first login. Redeem them for exclusive discounts on future bookings!',
         500, 1, '', 'welcome_bonus', '', 'LOYALTY POINTS', 'NEW MEMBER EXCLUSIVE', '', '', 'new_members', null],
        'sssiisssssssss'
    );
}

// ── GET ─────────────────────────────────────────────────────
if (isset($_POST['get_banner'])) {
    $result = select("SELECT * FROM `promo_banner` WHERE `id`=1 LIMIT 1", [], '');
    $banner = mysqli_fetch_assoc($result);

    $claims_q  = mysqli_query($con, "SELECT COUNT(*) as total FROM `banner_claims`");
    $claims_row = mysqli_fetch_assoc($claims_q);
    $banner['total_claims'] = $claims_row ? (int)$claims_row['total'] : 0;

    echo json_encode($banner);
}

// ── UPDATE ──────────────────────────────────────────────────
if (isset($_POST['upd_banner'])) {
    $data = filteration($_POST);

    $title          = $data['title']          ?? '';
    $subtitle       = $data['subtitle']       ?? '';
    $description    = $data['description']    ?? '';
    $points         = max(0, (int)($data['loyalty_points'] ?? 0));
    $occasion       = $data['occasion']       ?? 'welcome_bonus';
    $offer_text     = $data['offer_text']     ?? '';
    $offer_label    = $data['offer_label']    ?? '';
    $badge_label    = $data['badge_label']    ?? '';
    $cta_text       = $data['cta_text']       ?? '';
    $cta_url        = $data['cta_url']        ?? '';
    $target_audience = $data['target_audience'] ?? 'new_members';
    $expiry_date    = !empty($data['expiry_date']) ? $data['expiry_date'] : null;

    $base_sql = "UPDATE `promo_banner` SET
        `title`=?,`subtitle`=?,`description`=?,`loyalty_points`=?,
        `occasion`=?,`offer_text`=?,`offer_label`=?,`badge_label`=?,
        `cta_text`=?,`cta_url`=?,`target_audience`=?,`expiry_date`=?";
    $base_vals  = [$title,$subtitle,$description,$points,$occasion,$offer_text,$offer_label,$badge_label,$cta_text,$cta_url,$target_audience,$expiry_date];
    // title(s) subtitle(s) description(s) points(i) occasion(s) offer_text(s) offer_label(s) badge_label(s) cta_text(s) cta_url(s) target_audience(s) expiry_date(s) = 12
    $base_types = 'sssissssssss';

    if (isset($_FILES['bg_image']) && $_FILES['bg_image']['error'] === 0) {
        $img = uploadImage($_FILES['bg_image'], BANNER_FOLDER);
        if ($img === 'inv_img')    { echo 'inv_img';    exit; }
        if ($img === 'inv_size')   { echo 'inv_size';   exit; }
        if ($img === 'upd_failed') { echo 'upd_failed'; exit; }

        $old_res = select("SELECT `bg_image` FROM `promo_banner` WHERE `id`=1 LIMIT 1", [], '');
        if ($old_res) {
            $old = mysqli_fetch_assoc($old_res);
            if (!empty($old['bg_image'])) deleteImage($old['bg_image'], BANNER_FOLDER);
        }
        update($base_sql . ",`bg_image`=? WHERE `id`=1",
            array_merge($base_vals, [$img]), $base_types . 's');
    } else {
        update($base_sql . " WHERE `id`=1", $base_vals, $base_types);
    }
    echo 'success';
}

// ── TOGGLE ──────────────────────────────────────────────────
if (isset($_POST['toggle_banner'])) {
    $status = (int)$_POST['status'] === 1 ? 1 : 0;
    update("UPDATE `promo_banner` SET `is_active`=? WHERE `id`=1", [$status], 'i');
    echo 'success';
}

// ── REMOVE BG IMAGE ─────────────────────────────────────────
if (isset($_POST['remove_bg_image'])) {
    $old_res = select("SELECT `bg_image` FROM `promo_banner` WHERE `id`=1 LIMIT 1", [], '');
    if ($old_res) {
        $old = mysqli_fetch_assoc($old_res);
        if (!empty($old['bg_image'])) deleteImage($old['bg_image'], BANNER_FOLDER);
    }
    update("UPDATE `promo_banner` SET `bg_image`='' WHERE `id`=1", [], '');
    echo 'success';
}

// ── RESET CLAIMS ────────────────────────────────────────────
if (isset($_POST['reset_claims'])) {
    mysqli_query($con, "DELETE FROM `banner_claims`");
    echo 'success';
}
