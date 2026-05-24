<?php

require('../admin/inc/db_config.php');
require('../admin/inc/essentials.php');
require('../inc/welcome_bonus.php');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$con      = $GLOBALS['con'];
$is_logged_in = isset($_SESSION['login']) && $_SESSION['login'] === true;
$user_id  = $is_logged_in ? (int)$_SESSION['uId'] : 0;

// claim_banner requires login; dismiss_banner works for everyone
if (isset($_POST['claim_banner']) && !$is_logged_in) {
    echo 'not_logged_in';
    exit;
}

if (isset($_POST['claim_banner'])) {
    $result = awardWelcomeBonusIfEligible($user_id);

    if ($result['awarded']) {
        echo json_encode(['status' => 'success', 'points' => $result['points']]);
    } elseif ($result['reason'] === 'already_claimed') {
        echo 'already_claimed';
    } elseif ($result['reason'] === 'not_eligible') {
        echo 'not_eligible';
    } elseif ($result['reason'] === 'no_banner' || $result['reason'] === 'banner_inactive' || $result['reason'] === 'banner_expired') {
        echo 'banner_inactive';
    } elseif ($result['reason'] === 'no_points_banner') {
        echo 'no_points_banner';
    } else {
        echo 'failed';
    }
    exit;
}

if (isset($_POST['dismiss_banner'])) {
    // Dismiss is now handled client-side via localStorage.
    echo 'dismissed';
    exit;
}
