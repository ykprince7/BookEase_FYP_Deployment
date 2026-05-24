<?php
  // No HTML/markup before output — bookings.php expects plain "1" on success.

  require('../admin/inc/db_config.php');
  require('../admin/inc/essentials.php');

  date_default_timezone_set("Asia/Kolkata");
  session_start();


  if(!(isset($_SESSION['login']) && $_SESSION['login']==true)){
    redirect('index.php');
  }

  if(isset($_POST['review_form']))
  {
    $frm_data = filteration($_POST);

    $check_query = "SELECT `sr_no` FROM `rating_review` WHERE `booking_id`=? AND `user_id`=? LIMIT 1";
    $check_values = [$frm_data['booking_id'],$_SESSION['uId']];
    $check_result = select($check_query,$check_values,'ii');

    if(mysqli_num_rows($check_result)>0){
      echo 'already_reviewed';
      exit;
    }

    $upd_query = "UPDATE `booking_order` SET `rate_review`=? WHERE `booking_id`=? AND `user_id`=?";
    $upd_values = [1,$frm_data['booking_id'],$_SESSION['uId']];
    $upd_result = update($upd_query,$upd_values,'iii');

    // seen = 1: live immediately; no admin "mark read" step required for visibility.
    $ins_query = "INSERT INTO `rating_review`(`booking_id`, `room_id`, `user_id`, `rating`, `review`, `seen`)
      VALUES (?,?,?,?,?,?)";

    $ins_values = [$frm_data['booking_id'],$frm_data['room_id'],$_SESSION['uId'],
      $frm_data['rating'],$frm_data['review'], 1];

    $ins_result = insert($ins_query,$ins_values,'iiiisi');

    echo $ins_result;
  }
