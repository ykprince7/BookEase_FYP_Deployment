<?php 

  require('../admin/inc/db_config.php');
  require('../admin/inc/essentials.php');
  require_once('../inc/room_availability.php');
  require_once('../inc/currency_helpers.php');
  date_default_timezone_set("Asia/Kathmandu");

  session_start();

  $base_currency = 'NPR'; // Define the base currency as Nepalese Rupee

  if(isset($_REQUEST['fetch_rooms']))
  {
    // check availability data
    $checkin = $_REQUEST['checkin'] ?? '';
    $checkout = $_REQUEST['checkout'] ?? '';
    $chk_avail = ['checkin' => $checkin, 'checkout' => $checkout];

    // checkin and checkout filter validations
    if(!empty($chk_avail['checkin']) && !empty($chk_avail['checkout']))
    {
      $today_date = new DateTime(date("Y-m-d"));
      $checkin_date = new DateTime($chk_avail['checkin']);
      $checkout_date = new DateTime($chk_avail['checkout']);
  
      if($checkin_date == $checkout_date){
        echo "<h3 class='text-center text-danger'>Invalid Dates Entered!</h3>";
        exit;
      }
      else if($checkout_date < $checkin_date){
        echo "<h3 class='text-center text-danger'>Invalid Dates Entered!</h3>";
        exit;
      }
      else if($checkin_date < $today_date){
        echo "<h3 class='text-center text-danger'>Invalid Dates Entered!</h3>";
        exit;
      }
    }

    // guests data
    $adults = isset($_REQUEST['adults']) && $_REQUEST['adults'] !== '' ? intval($_REQUEST['adults']) : 0;
    $children = isset($_REQUEST['children']) && $_REQUEST['children'] !== '' ? intval($_REQUEST['children']) : 0;

    // facilities data decode
    $facility_list = json_decode($_REQUEST['facility_list'] ?? '{"facilities":[]}', true);
    if(!is_array($facility_list) || !isset($facility_list['facilities'])) {
      $facility_list = ['facilities' => []];
    }

    // currency selected
    $selected_currency = $_REQUEST['currency'] ?? $base_currency;

    // count no. of rooms and output variable to store room cards
    $count_rooms = 0;
    $output = "";

    // fetching settings table to check website is shutdown or not
    $settings_q = "SELECT * FROM `settings` WHERE `sr_no`=1";
    $settings_r = mysqli_fetch_assoc(mysqli_query($con, $settings_q));

    // query for room cards with guests filter (lowest price first so tiers read as upgrades)
    $room_res = select("SELECT * FROM `rooms` WHERE `adult` >= ? AND `children` >= ? AND `status` = ? AND `removed` = ? ORDER BY `price` ASC", [$adults, $children, 1, 0], 'iiii');

    while($room_data = mysqli_fetch_assoc($room_res))
    {
      // check availability filter
      if($chk_avail['checkin'] != '' && $chk_avail['checkout'] != '')
      {
        $avail = checkRoomDateAvailability(
          $room_data['id'],
          $chk_avail['checkin'],
          $chk_avail['checkout'],
          $con
        );

        if (!$avail['available']) {
          continue;
        }
      }

      // get facilities of room with filters
      $fac_count = 0;
      $fac_q = mysqli_query($con, "SELECT f.name, f.id FROM `facilities` f 
        INNER JOIN `room_facilities` rfac ON f.id = rfac.facilities_id 
        WHERE rfac.room_id = '$room_data[id]'");

      $facility_count = mysqli_num_rows($fac_q);

      $facilities_data = "";
      while($fac_row = mysqli_fetch_assoc($fac_q))
      {
        if(in_array($fac_row['id'], $facility_list['facilities'])){
          $fac_count++;
        }

        $facilities_data .= "<span class='badge rounded-pill bg-light text-dark text-wrap me-1 mb-1'>
          $fac_row[name]
        </span>";
      }

      if(count($facility_list['facilities']) != $fac_count){
        continue;
      }

      // get features of room
      $fea_q = mysqli_query($con, "SELECT f.name FROM `features` f 
        INNER JOIN `room_features` rfea ON f.id = rfea.features_id 
        WHERE rfea.room_id = '$room_data[id]'");

      $features_data = "";
      while($fea_row = mysqli_fetch_assoc($fea_q)){
        $features_data .= "<span class='badge rounded-pill bg-light text-dark text-wrap me-1 mb-1'>
          $fea_row[name]
        </span>";
      }

      // get thumbnail of image
      $room_thumb = ROOMS_IMG_PATH."thumbnail.jpg";
      $thumb_q = mysqli_query($con, "SELECT * FROM `room_images` 
        WHERE `room_id` = '$room_data[id]' 
        AND `thumb` = '1'");

      if(mysqli_num_rows($thumb_q) > 0){
        $thumb_res = mysqli_fetch_assoc($thumb_q);
        $room_thumb = ROOMS_IMG_PATH.$thumb_res['image'];
      }

      // Convert room price based on selected currency
      $converted_price = formatCurrencyAmount(convertCurrency($room_data['price'], $base_currency, $selected_currency, $con));

      // Book button (if applicable)
      $book_btn = "";
      if(!$settings_r['shutdown']){
        $login = 0;
        if(isset($_SESSION['login']) && $_SESSION['login'] == true){
          $login = 1;
        }
        $book_btn = "<button class='btn btn-sm w-100 text-white custom-bg shadow-none mb-2 book-now-btn' data-login='$login' data-room-id='$room_data[id]'>Book Now</button>";
      }

      // print room card
      // Inside the while loop where $output is built
$output .= "
<div class='card mb-4 border-0 shadow room-list-card'>
  <div class='row g-0 p-3 align-items-center'>
    <div class='col-md-5 mb-lg-0 mb-md-0 mb-3'>
      <img src='$room_thumb' class='img-fluid rounded room-thumb'>
    </div>
    <div class='col-md-5 px-lg-3 px-md-3 px-0'>
      <h5 class='mb-3'>$room_data[name]</h5>
      <div class='features mb-3'>
        <h6 class='mb-1'>Features</h6>
        $features_data
      </div>
      <div class='facilities mb-3'>
        <h6 class='mb-1'>Facilities</h6>
        $facilities_data
        <div class='small text-muted mt-2'>$facility_count amenities included.</div>
      </div>
      <div class='guests'>
        <h6 class='mb-1'>Guests</h6>
        <span class='badge rounded-pill bg-light text-dark text-wrap'>
          $room_data[adult] Adults
        </span>
        <span class='badge rounded-pill bg-light text-dark text-wrap'>
          $room_data[children] Children
        </span>
      </div>
    </div>
    <div class='col-md-2 mt-lg-0 mt-md-0 mt-4 text-center'>
      <h6 class='mb-4'>$selected_currency $converted_price per night</h6>
      $book_btn
      <a href='room_details.php?id=$room_data[id]&currency=$selected_currency' class='btn btn-sm w-100 room-details-btn shadow-none'>View details</a>
    </div>
  </div>
</div>
";

      $count_rooms++;
    }

    if($count_rooms > 0){
      echo $output;
    }
    else{
      echo "<h3 class='text-center text-danger'>No rooms to show!</h3>";
    }

  }

?>
