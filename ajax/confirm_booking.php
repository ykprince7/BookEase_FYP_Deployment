<?php 

  require('../admin/inc/db_config.php');
  require('../admin/inc/essentials.php');
  require_once('../inc/currency_helpers.php');
  require_once('../inc/room_availability.php');

  date_default_timezone_set("Asia/Kolkata");

  if(isset($_POST['check_availability']))
  {
    $frm_data = filteration($_POST);
    $status = "";
    $result = "";

    $today_date = new DateTime(date("Y-m-d"));
    $checkin_date = new DateTime($frm_data['check_in']);
    $checkout_date = new DateTime($frm_data['check_out']);

    if($checkin_date == $checkout_date){
      $status = 'check_in_out_equal';
      $result = json_encode(["status"=>$status]);
    }
    else if($checkout_date < $checkin_date){
      $status = 'check_out_earlier';
      $result = json_encode(["status"=>$status]);
    }
    else if($checkin_date < $today_date){
      $status = 'check_in_earlier';
      $result = json_encode(["status"=>$status]);
    }

    if($status!=''){
      echo $result;
    }
    else{
      if (session_status() === PHP_SESSION_NONE) {
        session_start();
      }

      $room_id = (int) ($frm_data['room_id'] ?? 0);
      if ($room_id <= 0) {
        $room_id = (int) ($_SESSION['room']['id'] ?? 0);
      }

      if ($room_id <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'Room not selected. Please open the booking page again.']);
        exit;
      }

      $avail = checkRoomDateAvailability(
        $room_id,
        $frm_data['check_in'],
        $frm_data['check_out'],
        $con
      );

      if (!$avail['available']) {
        echo json_encode([
          'status'    => 'unavailable',
          'limit'     => $avail['limit'],
          'used'      => $avail['used'],
          'remaining' => $avail['remaining'],
        ]);
        exit;
      }

      $count_days = date_diff($checkin_date,$checkout_date)->days;
      $payment = $_SESSION['room']['price'] * $count_days;

      $selected_currency = $_SESSION['room']['currency'];
      $base_currency = 'NPR';
      $converted_payment = formatCurrencyAmount(convertCurrency($payment, $base_currency, $selected_currency, $con));

      $_SESSION['room']['payment'] = $payment;
      $_SESSION['room']['available'] = true;
      
      $result = json_encode([
        "status"=>'available', 
        "days"=>$count_days, 
        "payment"=> $converted_payment,
        "currency" => $selected_currency,
        "slots_remaining" => $avail['remaining'],
      ]);
      echo $result;
    }

  }

?>
