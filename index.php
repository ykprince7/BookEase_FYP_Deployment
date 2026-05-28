<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
date_default_timezone_set("Asia/Kathmandu");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link  rel="stylesheet" href="https://unpkg.com/swiper@7/swiper-bundle.min.css">
  <?php require('inc/links.php'); ?>
  <title><?php echo $settings_r['site_title'] ?> - HOME</title>
  <style>
    .availability-form{
      margin-top: -50px;
      z-index: 2;
      position: relative;
    }

    .availability-form .submit-btn{
      width: 100%;
      min-height: 38px;
      padding: .45rem .75rem !important;
      line-height: 1.2;
      white-space: nowrap;
    }

    /* Button 34 (requested) */
    .button-34{
      background: #2d9c95;
      border-radius: 999px;
      box-shadow: rgba(45, 156, 149, 0.5) 0 10px 20px -10px;
      box-sizing: border-box;
      color: #FFFFFF;
      cursor: pointer;
      font-family: Inter,Helvetica,"Apple Color Emoji","Segoe UI Emoji",NotoColorEmoji,"Noto Color Emoji","Segoe UI Symbol","Android Emoji",EmojiSymbols,-apple-system,system-ui,"Segoe UI",Roboto,"Helvetica Neue","Noto Sans",sans-serif;
      font-size: 16px;
      font-weight: 700;
      line-height: 24px;
      opacity: 1;
      outline: 0 solid transparent;
      padding: 8px 18px;
      user-select: none;
      -webkit-user-select: none;
      touch-action: manipulation;
      width: fit-content;
      word-break: break-word;
      border: 0;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      transition: transform .2s ease, box-shadow .2s ease, filter .2s ease;
    }

    .button-34:hover{
      transform: translateY(-1px);
      filter: brightness(1.04);
      box-shadow: rgba(45, 156, 149, 0.52) 0 14px 26px -12px;
    }

    .button-34:focus{
      outline: 0 solid transparent;
      box-shadow: 0 0 0 .2rem rgba(45, 156, 149, 0.35);
    }

    /* Plain variant for secondary actions (e.g., More Details) */
    .button-34.button-34-plain{
      background: transparent;
      color: #247d77;
      border: 2px solid #2d9c95;
      box-shadow: rgba(45, 156, 149, 0.3) 0 10px 20px -14px;
    }

    .button-34.button-34-plain:hover{
      background: rgba(45, 156, 149, 0.12);
      box-shadow: rgba(45, 156, 149, 0.3) 0 14px 26px -16px;
    }

    /* Keep homepage room-card action buttons same size */
    .home-room-actions{
      gap: .5rem;
    }

    .home-room-actions .btn,
    .home-room-actions .button-34{
      flex: 1 1 0;
      min-height: 38px;
      white-space: nowrap;
      width: 100%;
    }

    .home-room-actions .custom-bg{
      padding: .45rem .75rem !important;
    }

    .home-room-actions .btn:focus{
      outline: none;
      box-shadow: 0 0 0 .2rem rgba(45, 156, 149, 0.25);
    }

    .home-room-actions .btn:hover{
      transform: translateY(-1px);
    }

    .home-room-card{
      border-radius: 16px;
      border: 1px solid rgba(148, 163, 184, 0.18);
      position: relative;
      overflow: hidden;
      transform: translateY(0) rotateX(0deg) rotateY(0deg);
      transform-style: preserve-3d;
      transition: transform .45s cubic-bezier(.22,.61,.36,1), box-shadow .35s ease, border-color .35s ease;
    }

    .home-room-card::before{
      content: "";
      position: absolute;
      inset: 0;
      border-radius: inherit;
      padding: 1px;
      background: linear-gradient(135deg, rgba(45,156,149,0.0), rgba(45,156,149,0.45), rgba(45,156,149,0.0));
      -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
      -webkit-mask-composite: xor;
      mask-composite: exclude;
      opacity: 0;
      transition: opacity .35s ease;
      pointer-events: none;
    }

    .home-room-card::after{
      content: "";
      position: absolute;
      top: 0;
      left: -130%;
      width: 58%;
      height: 100%;
      background: linear-gradient(110deg, rgba(255,255,255,0), rgba(255,255,255,.45), rgba(255,255,255,0));
      transform: skewX(-20deg);
      transition: transform .85s cubic-bezier(.22,.61,.36,1);
      pointer-events: none;
      z-index: 2;
    }

    .home-room-card .card-img-top{
      transition: transform .65s cubic-bezier(.22,.61,.36,1), filter .45s ease;
      transform-origin: center;
    }

    .home-room-card:hover{
      transform: translateY(-7px) rotateX(2.2deg) rotateY(-2.2deg);
      box-shadow: 0 20px 38px rgba(15, 23, 42, 0.16), 0 8px 18px rgba(45, 156, 149, 0.14) !important;
      border-color: rgba(45, 156, 149, 0.45);
    }

    .home-room-card:hover::before{
      opacity: 1;
    }

    .home-room-card:hover::after{
      transform: translateX(420%) skewX(-20deg);
    }

    .home-room-card:hover .card-img-top{
      transform: scale(1.06);
      filter: saturate(1.06) contrast(1.02);
    }

    @media (max-width: 768px){
      .home-room-card:hover{
        transform: translateY(-4px);
      }
    }

    @media (max-width: 360px){
      .home-room-actions{
        flex-direction: column;
      }
    }

    .home-reviews-section .review-card{
      border-radius: 14px;
      border: 1px solid rgba(0,0,0,0.06);
      transition: transform .35s ease, box-shadow .35s ease, border-color .35s ease;
      position: relative;
      overflow: hidden;
      animation: reviewFadeUp .55s ease both;
    }

    .home-reviews-section .review-card::before{
      content: "";
      position: absolute;
      top: 0;
      left: -130%;
      width: 80%;
      height: 100%;
      background: linear-gradient(120deg, rgba(255,255,255,0), rgba(255,255,255,.35), rgba(255,255,255,0));
      transition: left .6s ease;
      pointer-events: none;
    }

    .facility-card{
      transition: transform 0.3s ease, box-shadow 0.3s ease, border-color 0.3s ease;
      border: 1px solid rgba(0,0,0,0.04);
      cursor: pointer;
    }

    .facility-card:hover{
      transform: translateY(-6px);
      box-shadow: 0 18px 38px rgba(0,0,0,0.12);
      border-color: rgba(46,193,172,0.25);
    }

    .home-reviews-section .swiper-slide:hover .review-card{
      transform: translateY(-8px) scale(1.01);
      box-shadow: 0 14px 34px rgba(0,0,0,0.12) !important;
      border-color: rgba(46,193,172,0.4);
    }

    .home-reviews-section .swiper-slide:hover .review-card::before{
      left: 140%;
    }

    .home-reviews-section .review-text{
      transition: color .3s ease;
    }

    .home-reviews-section .swiper-slide:hover .review-text{
      color: #1f2937;
    }

    .home-reviews-section .rating i{
      transition: transform .25s ease;
    }

    .home-reviews-section .swiper-slide:hover .rating i{
      transform: scale(1.14);
    }

    .home-reviews-section .swiper-slide{
      opacity: 0;
      transform: translateY(14px);
      animation: reviewSlideIn .5s ease forwards;
    }

    @keyframes reviewFadeUp{
      from{
        opacity: 0;
        transform: translateY(10px);
      }
      to{
        opacity: 1;
        transform: translateY(0);
      }
    }

    @keyframes reviewSlideIn{
      to{
        opacity: 1;
        transform: translateY(0);
      }
    }

    @media screen and (max-width: 575px) {
      .availability-form{
        margin-top: 25px;
        padding: 0 35px;
      } 
    }
  </style>
</head>
<body class="bg-light">

  <?php require('inc/header.php'); ?>

  <!-- Carousel -->

  <div class="container-fluid px-lg-4 mt-4">
    <div class="swiper swiper-container">
      <div class="swiper-wrapper">
        <?php 
          $res = selectAll('carousel');
          while($row = mysqli_fetch_assoc($res))
          {
            $path = CAROUSEL_IMG_PATH;
            echo <<<data
              <div class="swiper-slide">
                <img src="$path$row[image]" class="w-100 d-block">
              </div>
            data;
          }
        ?>
      </div>
    </div>
  </div>

  <!-- check availability form -->
  <?php
    $idx_book_today = date('Y-m-d');
    $idx_book_tmr = date('Y-m-d', strtotime('+1 day'));
  ?>

  <div class="container availability-form">
    <div class="row">
      <div class="col-lg-12 bg-white shadow p-4 rounded">
        <h5 class="mb-4">Check Booking Availability</h5>
        <form action="rooms.php" id="home_booking_availability_form" data-booking-today="<?php echo htmlspecialchars($idx_book_today, ENT_QUOTES, 'UTF-8'); ?>">
          <div class="row align-items-end">
            <div class="col-lg-3 mb-3">
              <label class="form-label" style="font-weight: 500;">Check-in</label>
              <input type="date" class="form-control shadow-none" name="checkin" id="home_booking_checkin" required autocomplete="off"
                min="<?php echo htmlspecialchars($idx_book_today, ENT_QUOTES, 'UTF-8'); ?>">
            </div>
            <div class="col-lg-3 mb-3">
              <label class="form-label" style="font-weight: 500;">Check-out</label>
              <input type="date" class="form-control shadow-none" name="checkout" id="home_booking_checkout" required autocomplete="off"
                min="<?php echo htmlspecialchars($idx_book_tmr, ENT_QUOTES, 'UTF-8'); ?>">
            </div>
            <div class="col-lg-3 mb-3">
              <label class="form-label" style="font-weight: 500;">Adult</label>
              <select class="form-select shadow-none" name="adult">
                <?php 
                  $guests_q = mysqli_query($con,"SELECT MAX(adult) AS `max_adult`, MAX(children) AS `max_children` 
                    FROM `rooms` WHERE `status`='1' AND `removed`='0'");  
                  $guests_res = mysqli_fetch_assoc($guests_q);
                  
                  for($i=1; $i<=$guests_res['max_adult']; $i++){
                    echo"<option value='$i'>$i</option>";
                  }
                ?>
              </select>
            </div>
            <div class="col-lg-2 mb-3">
              <label class="form-label" style="font-weight: 500;">Children</label>
              <select class="form-select shadow-none" name="children">
                <?php 
                  for($i=1; $i<=$guests_res['max_children']; $i++){
                    echo"<option value='$i'>$i</option>";
                  }
                ?>
              </select>
            </div>
            <input type="hidden" name="check_availability">
            <div class="col-lg-1 mb-lg-3 mt-2">
              <button type="submit" class="btn text-white shadow-none custom-bg submit-btn">Submit</button>
            </div>
          </div>
        </form>
        <script>
        (function () {
          function addDaysISO(iso, n) {
            var parts = iso.split('-').map(Number);
            var dt = new Date(parts[0], parts[1] - 1, parts[2] + n);
            var y = dt.getFullYear();
            var m = String(dt.getMonth() + 1).padStart(2, '0');
            var d = String(dt.getDate()).padStart(2, '0');
            return y + '-' + m + '-' + d;
          }
          function todayISO() {
            var form = document.getElementById('home_booking_availability_form');
            if (!form) return null;
            var t = form.getAttribute('data-booking-today');
            if (t && /^\d{4}-\d{2}-\d{2}$/.test(t)) return t;
            return null;
          }
          function clampHomeBookingDates() {
            var form = document.getElementById('home_booking_availability_form');
            var checkin = document.getElementById('home_booking_checkin');
            var checkout = document.getElementById('home_booking_checkout');
            if (!form || !checkin || !checkout) return;
            var today = todayISO();
            if (!today) {
              var now = new Date();
              today = now.getFullYear() + '-' + String(now.getMonth() + 1).padStart(2, '0') + '-' + String(now.getDate()).padStart(2, '0');
            }
            checkin.min = today;
            if (checkin.value && checkin.value < today) checkin.value = today;
            if (!checkin.value) {
              checkout.min = addDaysISO(today, 1);
              if (checkout.value && checkout.value < checkout.min) checkout.value = checkout.min;
              return;
            }
            var minOut = addDaysISO(checkin.value, 1);
            checkout.min = minOut;
            if (!checkout.value || checkout.value <= checkin.value) checkout.value = minOut;
          }
          function bindOnce() {
            var checkin = document.getElementById('home_booking_checkin');
            var checkout = document.getElementById('home_booking_checkout');
            if (!checkin || checkin.dataset.bookingRangeBound === '1') return;
            checkin.dataset.bookingRangeBound = '1';
            checkin.addEventListener('change', clampHomeBookingDates);
            checkin.addEventListener('input', clampHomeBookingDates);
            if (checkout) checkout.addEventListener('input', clampHomeBookingDates);
          }
          function run() {
            clampHomeBookingDates();
            bindOnce();
          }
          if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', run);
          else run();
          window.addEventListener('load', clampHomeBookingDates);
          setTimeout(clampHomeBookingDates, 300);
          setTimeout(clampHomeBookingDates, 1500);
        })();
        </script>
      </div>
    </div>
  </div>

  <!-- Our Rooms -->

  <h2 class="mt-5 pt-4 mb-4 text-center fw-bold h-font"  style="color: #0f172a;">OUR ROOMS</h2>

  <div class="container">
    <p class="text-center text-muted small mb-4 px-3">
      Rooms are shown from lowest to highest nightly rate. Higher rates include more facilities and comforts.
    </p>
    <div class="swiper swiper-Rooms">
      <div class="swiper-wrapper pb-2">

      <?php 
            
        $room_res = select("SELECT * FROM `rooms` WHERE `status`=? AND `removed`=? ORDER BY `price` ASC",[1,0],'ii');

        while($room_data = mysqli_fetch_assoc($room_res))
        {
          // get features of room

          $fea_q = mysqli_query($con,"SELECT f.name FROM `features` f 
            INNER JOIN `room_features` rfea ON f.id = rfea.features_id 
            WHERE rfea.room_id = '$room_data[id]'");

          $features_data = "";
          while($fea_row = mysqli_fetch_assoc($fea_q)){
            $features_data .="<span class='badge rounded-pill bg-light text-dark text-wrap me-1 mb-1'>
              $fea_row[name]
            </span>";
          }

          // get facilities of room

          $fac_q = mysqli_query($con,"SELECT f.name FROM `facilities` f 
            INNER JOIN `room_facilities` rfac ON f.id = rfac.facilities_id 
            WHERE rfac.room_id = '$room_data[id]'");

          $facility_count = mysqli_num_rows($fac_q);

          $facilities_data = "";
          while($fac_row = mysqli_fetch_assoc($fac_q)){
            $facilities_data .="<span class='badge rounded-pill bg-light text-dark text-wrap me-1 mb-1'>
              $fac_row[name]
            </span>";
          }

          // get thumbnail of image

          $room_thumb = ROOMS_IMG_PATH."thumbnail.jpg";
          $thumb_q = mysqli_query($con,"SELECT * FROM `room_images` 
            WHERE `room_id`='$room_data[id]' 
            AND `thumb`='1'");

          if(mysqli_num_rows($thumb_q)>0){
            $thumb_res = mysqli_fetch_assoc($thumb_q);
            $room_thumb = ROOMS_IMG_PATH.$thumb_res['image'];
          }

          $book_btn = "";

          if(!$settings_r['shutdown']){
            $login=0;
            if(isset($_SESSION['login']) && $_SESSION['login']==true){
              $login=1;
            }

            $book_btn = "<button onclick='checkLoginToBook($login,$room_data[id])' class='button-34' role='button'>Book Now</button>";
          }

          $rating_q = "SELECT AVG(rating) AS `avg_rating` FROM `rating_review`
            WHERE `room_id`='$room_data[id]' ORDER BY `sr_no` DESC LIMIT 20";

          $rating_res = mysqli_query($con,$rating_q);
          $rating_fetch = mysqli_fetch_assoc($rating_res);

          $rating_data = "";

          if($rating_fetch['avg_rating']!=NULL)
          {
            $rating_data = "<div class='rating mb-4'>
              <h6 class='mb-1'>Rating</h6>
              <span class='badge rounded-pill bg-light'>
            ";

            for($i=0; $i<$rating_fetch['avg_rating']; $i++){
              $rating_data .="<i class='bi bi-star-fill text-warning'></i> ";
            }

            $rating_data .= "</span>
              </div>
            ";
          }

          // print room card

          echo <<<data
            <div class="swiper-slide py-2">
              <div class="card border-0 shadow h-100 home-room-card" style="max-width: 350px; margin: auto;">
                <img src="$room_thumb" class="card-img-top">
                <div class="card-body">
                  <h5>$room_data[name]</h5>
                  <h6 class="mb-4">NPR$room_data[price] per night</h6>
                  <div class="features mb-4">
                    <h6 class="mb-1">Features</h6>
                    $features_data
                  </div>
                  <div class="facilities mb-4">
                    <h6 class="mb-1">Facilities</h6>
                    $facilities_data
                    <div class="small text-muted mt-2">$facility_count amenities included.</div>
                  </div>
                  <div class="guests mb-4">
                    <h6 class="mb-1">Guests</h6>
                    <span class="badge rounded-pill bg-light text-dark text-wrap">
                      $room_data[adult] Adults
                    </span>
                    <span class="badge rounded-pill bg-light text-dark text-wrap">
                      $room_data[children] Children
                    </span>
                  </div>
                  $rating_data
                  <div class="d-flex mb-2 home-room-actions">
                    $book_btn
                    <a href="room_details.php?id=$room_data[id]" class="button-34 button-34-plain" role="button">More Details</a>
                  </div>
                </div>
              </div>
            </div>
          data;

        }

      ?>
      </div>
      <div class="swiper-pagination rooms-pagination mt-2"></div>
    </div>

      <div class="text-center mt-5">
        <a href="rooms.php" class="btn btn-sm btn-outline-dark rounded-0 fw-bold shadow-none">View all rooms</a>
      </div>
  </div>

  <!-- Our Facilities -->

  <h2 class="mt-5 pt-4 mb-4 text-center fw-bold h-font"  style="color: #0f172a;">OUR FACILITIES</h2>

  <div class="container">
    <div class="row justify-content-evenly px-lg-0 px-md-0 px-5">
      <?php 
        $res = mysqli_query($con,"SELECT * FROM `facilities` ORDER BY `id` DESC LIMIT 5");
        $path = FACILITIES_IMG_PATH;

        while($row = mysqli_fetch_assoc($res)){
          echo<<<data
            <div class="col-lg-2 col-md-2 text-center bg-white rounded shadow py-4 my-3 facility-card">
              <img src="$path$row[icon]" width="60px">
              <h5 class="mt-3">$row[name]</h5>
            </div>
          data;
        }
      ?>

      <div class="col-lg-12 text-center mt-5">
        <a href="facilities.php" class="btn btn-sm btn-outline-dark rounded-0 fw-bold shadow-none">View all facilities</a>
      </div>
    </div>
  </div>

  <!-- Reviews -->

  <section class="home-reviews-section mt-5 pt-4">
    <div class="container">
      <div class="text-center mb-4">
        <h2 class="fw-bold h-font mb-1"  style="color: #0f172a;">Reviews</h2>
        <p class="text-muted small mb-0">Recent feedback from guests who stayed with us.</p>
      </div>

      <div class="swiper swiper-Reviews">
        <div class="swiper-wrapper mb-4">
          <?php

            $review_q = "SELECT rr.*,uc.name AS uname, uc.profile, r.name AS rname FROM `rating_review` rr
              INNER JOIN (
                SELECT MAX(sr_no) AS latest_sr
                FROM `rating_review`
                GROUP BY user_id, review
              ) dedupe ON rr.sr_no = dedupe.latest_sr
              INNER JOIN `user_cred` uc ON rr.user_id = uc.id
              INNER JOIN `rooms` r ON rr.room_id = r.id
              ORDER BY rr.`sr_no` DESC LIMIT 6";

            $review_res = mysqli_query($con,$review_q);
            $img_path = USERS_IMG_PATH;

            if(mysqli_num_rows($review_res)==0){
              echo '<div class="swiper-slide"><div class="review-card-empty text-center p-5"><h6 class="mb-1">No reviews yet</h6><p class="text-muted small mb-0">Once guests start sharing their experiences, you will see them here.</p></div></div>';
            }
            else
            {
              while($row = mysqli_fetch_assoc($review_res))
              {
                $stars = "<i class='bi bi-star-fill text-warning'></i>";
                for($i=1; $i<$row['rating']; $i++){
                  $stars .= " <i class='bi bi-star-fill text-warning'></i>";
                }

                $fallbackProfile = 'https://via.placeholder.com/44?text=User';
                $profile_img = $fallbackProfile;

                if(!empty($row['profile'])){
                  $candidate_path = $_SERVER['DOCUMENT_ROOT'].'/images/users/'.$row['profile'];
                  if(file_exists($candidate_path)){
                    $profile_img = $img_path.$row['profile'];
                  }
                }

                $created_on = '';
                if(!empty($row['datetime'])){
                  $created_on = date('M j, Y', strtotime($row['datetime']));
                }

                echo<<<slides
                  <div class="swiper-slide">
                    <article class="review-card bg-white shadow-sm p-4 h-100">
                      <div class="d-flex align-items-center mb-3">
                        <div class="me-3">
                          <img src="{$profile_img}" class="rounded-circle border" loading="lazy" width="44" height="44" style="object-fit:cover;" onerror="this.onerror=null;this.src='{$fallbackProfile}';">
                        </div>
                        <div class="flex-grow-1">
                          <h6 class="mb-0">$row[uname]</h6>
                          <small class="text-muted">Stayed in <span class="fw-semibold">$row[rname]</span></small><br>
                          <small class="text-muted">$created_on</small>
                        </div>
                        <div class="text-end ms-2">
                          <div class="badge bg-light text-dark px-2 py-1 small">
                            <span class="fw-semibold me-1">$row[rating].0</span>
                            <i class="bi bi-star-fill text-warning"></i>
                          </div>
                        </div>
                      </div>

                      <p class="review-text mb-3">
                        $row[review]
                      </p>
                      <div class="d-flex align-items-center justify-content-between mt-auto">
                        <div class="rating small text-warning">
                          $stars
                        </div>
                        <span class="badge bg-light text-muted border small">Verified guest</span>
                      </div>
                    </article>
                  </div>
                slides;
              }
            }
          
          ?>
        </div>
        <div class="swiper-pagination"></div>
      </div>

      <div class="text-center mt-4">
        <a href="about.php" class="btn btn-sm btn-outline-dark rounded-0 fw-bold shadow-none">Learn more about us</a>
      </div>
    </div>
  </section>

  <!-- Reach us -->

  <h2 class="mt-5 pt-4 mb-4 text-center fw-bold h-font" style="color: #0f172a;">REACH US</h2>

  <div class="container">
    <div class="row">
      <div class="col-lg-8 col-md-8 p-4 mb-lg-0 mb-3 bg-white rounded">
        <iframe class="w-100 rounded" height="320px" src="<?php echo $contact_r['iframe'] ?>" loading="lazy"></iframe>
      </div>
      <div class="col-lg-4 col-md-4">
        <div class="bg-white p-4 rounded mb-4">
          <h5>Call us</h5>
          <a href="tel: +<?php echo $contact_r['pn1'] ?>" class="d-inline-block mb-2 text-decoration-none text-dark">
            <i class="bi bi-telephone-fill"></i> +<?php echo $contact_r['pn1'] ?>
          </a>
          <br>
          <?php 
            if($contact_r['pn2']!=''){
              echo<<<data
                <a href="tel: +$contact_r[pn2]" class="d-inline-block text-decoration-none text-dark">
                  <i class="bi bi-telephone-fill"></i> +$contact_r[pn2]
                </a>
              data;
            }
          
          ?>
        </div>
        <div class="bg-white p-4 rounded mb-4">
          <h5>Follow us</h5>
          <?php 
            if($contact_r['tw']!=''){
              echo<<<data
                <a href="$contact_r[tw]" class="d-inline-block mb-3" title="X">
                  <span class="badge bg-light text-dark fs-6 p-2 d-inline-flex align-items-center">
                    <span class="social-x-logo">X</span>
                  </span>
                </a>
                <br>
              data;
            }
          ?>

          <a href="<?php echo $contact_r['fb'] ?>" class="d-inline-block mb-3">
            <span class="badge bg-light text-dark fs-6 p-2 d-inline-flex align-items-center"> 
            <i class="bi bi-facebook me-1"></i> Facebook
            </span>
          </a>
          <br>
          <a href="<?php echo $contact_r['insta'] ?>" class="d-inline-block">
            <span class="badge bg-light text-dark fs-6 p-2"> 
            <i class="bi bi-instagram me-1"></i> Instagram
            </span>
          </a>
        </div>
      </div>
    </div>
  </div>

  <!-- Password reset modal and code -->

  <div class="modal fade" id="recoveryModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <form id="recovery-form">
          <div class="modal-header">
            <h5 class="modal-title d-flex align-items-center">
              <i class="bi bi-shield-lock fs-3 me-2"></i> Set up New Password
            </h5>
          </div>
          <div class="modal-body">
            <div class="mb-4">
              <label class="form-label">New Password</label>
              <input type="password" name="pass" required class="form-control shadow-none">
              <input type="hidden" name="email">
              <input type="hidden" name="token">
            </div>
            <div class="mb-2 text-end">
              <button type="button" class="btn shadow-none me-2" data-bs-dismiss="modal">CANCEL</button>
              <button type="submit" class="btn btn-primary shadow-none">SUBMIT</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>


  <?php require('inc/footer.php'); ?>
  <!-- Upadated UI PArt -->


  <?php
  
    if(isset($_GET['account_recovery']))
    {
      $data = filteration($_GET);

      $t_date = date("Y-m-d");

      $query = select("SELECT * FROM `user_cred` WHERE `email`=? AND `token`=? AND `t_expire`=? LIMIT 1",
        [$data['email'],$data['token'],$t_date],'sss');

      if(mysqli_num_rows($query)==1)
      {
        echo<<<showModal
          <script>
            var myModal = document.getElementById('recoveryModal');

            myModal.querySelector("input[name='email']").value = '$data[email]';
            myModal.querySelector("input[name='token']").value = '$data[token]';

            var modal = bootstrap.Modal.getOrCreateInstance(myModal);
            modal.show();
          </script>
        showModal;
      }
      else{
        alert("error","Invalid or Expired Link !");
      }

    }

  ?>
  
  <script src="https://unpkg.com/swiper@7/swiper-bundle.min.js"></script>

  <script>
    try {
    var swiper = new Swiper(".swiper-container", {
      spaceBetween: 30,
      effect: "fade",
      loop: true,
      autoplay: {
        delay: 3500,
        disableOnInteraction: false,
      }
    });
    } catch (e) { console.warn("Swiper hero:", e); }

    try {
    var swiper = new Swiper(".swiper-Reviews", {
      spaceBetween: 20,
      loop: false,
      grabCursor: true,
      centeredSlides: false,
      slidesPerView: 1,
      speed: 700,
      autoplay: {
        delay: 3200,
        disableOnInteraction: false,
      },
      pagination: {
        el: ".swiper-pagination",
        clickable: true,
      },
      breakpoints: {
        640: {
          slidesPerView: 1,
        },
        768: {
          slidesPerView: 2,
        },
        1024: {
          slidesPerView: 3,
        },
      }
    });
    } catch (e) { console.warn("Swiper reviews:", e); }

    try {
    var roomSwiper = new Swiper(".swiper-Rooms", {
      spaceBetween: 20,
      loop: false,
      grabCursor: true,
      slidesPerView: 1,
      pagination: {
        el: ".rooms-pagination",
        clickable: true,
      },
      breakpoints: {
        640: {
          slidesPerView: 1,
        },
        768: {
          slidesPerView: 2,
        },
        1024: {
          slidesPerView: 3,
        },
      }
    });
    } catch (e) { console.warn("Swiper rooms:", e); }

    // recover account
    
    let recovery_form = document.getElementById('recovery-form');

    if (recovery_form) recovery_form.addEventListener('submit', (e)=>{
      e.preventDefault();

      let data = new FormData();

      data.append('email',recovery_form.elements['email'].value);
      data.append('token',recovery_form.elements['token'].value);
      data.append('pass',recovery_form.elements['pass'].value);
      data.append('recover_user','');

      var myModal = document.getElementById('recoveryModal');
      var modal = myModal ? bootstrap.Modal.getInstance(myModal) : null;
      if (modal) modal.hide();

      let xhr = new XMLHttpRequest();
      xhr.open("POST","ajax/login_register.php",true);

      xhr.onload = function(){
        if(this.responseText == 'failed'){
          alert('error',"Account reset failed!");
        }
        else{
          alert('success',"Account Reset Successful !");
          recovery_form.reset();
        }
      }

      xhr.send(data);
    });

  </script>

</body>
</html>
