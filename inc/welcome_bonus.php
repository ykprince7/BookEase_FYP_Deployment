<?php
/**
 * Welcome loyalty bonus (promo banner points) for new members.
 * Awards once per user; safe to call on login, verify, or manual claim.
 */
if (!function_exists('awardWelcomeBonusIfEligible')) {

    function welcome_bonus_ensure_tables($con)
    {
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
    }

    /**
     * @return array{awarded:bool, points:int, reason:string}
     */
    function awardWelcomeBonusIfEligible($user_id)
    {
        $user_id = (int) $user_id;
        if ($user_id <= 0) {
            return ['awarded' => false, 'points' => 0, 'reason' => 'invalid_user'];
        }

        $con = $GLOBALS['con'];
        welcome_bonus_ensure_tables($con);

        $claimed_res = select("SELECT `id` FROM `banner_claims` WHERE `user_id`=? LIMIT 1", [$user_id], 'i');
        if (mysqli_num_rows($claimed_res) > 0) {
            return ['awarded' => false, 'points' => 0, 'reason' => 'already_claimed'];
        }

        $banner_res = select(
            "SELECT `loyalty_points`, `is_active`, `cta_url`, `target_audience`, `expiry_date`
             FROM `promo_banner` WHERE `id`=1 LIMIT 1",
            [],
            ''
        );
        if (!$banner_res || mysqli_num_rows($banner_res) == 0) {
            return ['awarded' => false, 'points' => 0, 'reason' => 'no_banner'];
        }

        $banner = mysqli_fetch_assoc($banner_res);

        if ((int) $banner['is_active'] !== 1) {
            return ['awarded' => false, 'points' => 0, 'reason' => 'banner_inactive'];
        }

        if (!empty($banner['expiry_date']) && strtotime($banner['expiry_date']) < strtotime(date('Y-m-d'))) {
            return ['awarded' => false, 'points' => 0, 'reason' => 'banner_expired'];
        }

        if (!empty($banner['cta_url'])) {
            return ['awarded' => false, 'points' => 0, 'reason' => 'no_points_banner'];
        }

        $target_audience = $banner['target_audience'] ?? 'new_members';

        if ($target_audience === 'guests_only') {
            return ['awarded' => false, 'points' => 0, 'reason' => 'guests_only'];
        }

        if ($target_audience === 'new_members') {
            $elig_res = select("SELECT `banner_eligible` FROM `user_cred` WHERE `id`=? LIMIT 1", [$user_id], 'i');
            $elig_row = mysqli_fetch_assoc($elig_res);
            if ((int) ($elig_row['banner_eligible'] ?? 0) !== 1) {
                return ['awarded' => false, 'points' => 0, 'reason' => 'not_eligible'];
            }
        }

        $points = max(1, (int) $banner['loyalty_points']);

        mysqli_begin_transaction($con);
        try {
            $upsert = "INSERT INTO `loyalty_points` (`user_id`, `points_balance`) VALUES (?, ?)
                       ON DUPLICATE KEY UPDATE `points_balance` = `points_balance` + ?";
            insert($upsert, [$user_id, $points, $points], 'iii');

            $desc = 'Welcome bonus - New member reward';
            insert(
                "INSERT INTO `loyalty_transactions` (`user_id`, `points`, `type`, `description`) VALUES (?,?,'earn',?)",
                [$user_id, $points, $desc],
                'iis'
            );

            insert(
                "INSERT INTO `banner_claims` (`user_id`, `claimed_at`) VALUES (?, NOW())",
                [$user_id],
                'i'
            );

            mysqli_commit($con);
            return ['awarded' => true, 'points' => $points, 'reason' => 'success'];
        } catch (Exception $e) {
            mysqli_rollback($con);
            return ['awarded' => false, 'points' => 0, 'reason' => 'failed'];
        }
    }
}
