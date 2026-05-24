<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <?php require('inc/links.php'); ?>
  <!-- Add Pannellum CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/pannellum@2.5.6/build/pannellum.css">
  <title><?php echo $settings_r['site_title'] ?> - CONFIRM BOOKING</title>
</head>
<body class="bg-light">

  <?php require('inc/header.php'); ?>

  <?php 
    require_once('inc/currency_helpers.php');

    /*
      Check room id from url is present or not
      Shutdown mode is active or not
      User is logged in or not
    */

    if(!isset($_GET['id']) || $settings_r['shutdown']==true){
      redirect('rooms.php');
    }
    else if(!(isset($_SESSION['login']) && $_SESSION['login']==true)){
      redirect('rooms.php');
    }

    // filter and get room and user data

    $data = filteration($_GET);

    $room_res = select("SELECT * FROM `rooms` WHERE `id`=? AND `status`=? AND `removed`=?",[$data['id'],1,0],'iii');

    if(mysqli_num_rows($room_res)==0){
      redirect('rooms.php');
    }

    $room_data = mysqli_fetch_assoc($room_res);

    // Fetch 360° image for this room
    $img_360_res = select("SELECT `image` FROM `room_360_images` WHERE `room_id`=? ORDER BY `id` DESC LIMIT 1", [$room_data['id']], "i");
    $img_360_data = mysqli_fetch_assoc($img_360_res);
    $room_360_image = $img_360_data ? 'images/360/' . rawurlencode($img_360_data['image']) : null;

    // Get selected currency from URL or default to NPR
    $selected_currency = isset($_GET['currency']) ? $_GET['currency'] : 'NPR';
    $base_currency = 'NPR';
    $converted_price = formatCurrencyAmount(convertCurrency($room_data['price'], $base_currency, $selected_currency, $con));

    $_SESSION['room'] = [
      "id" => $room_data['id'],
      "name" => $room_data['name'],
      "price" => $room_data['price'],
      "payment" => null,
      "available" => false,
      "currency" => $selected_currency
    ];


    $user_res = select("SELECT * FROM `user_cred` WHERE `id`=? LIMIT 1", [$_SESSION['uId']], "i");
    $user_data = mysqli_fetch_assoc($user_res);

    require_once('inc/loyalty_points.php');
    $points_balance = getLoyaltyPointsBalance($_SESSION['uId']);
    $rewards_q = getAvailableRewards();
    $rewards_list = [];
    while ($r = mysqli_fetch_assoc($rewards_q)) {
      if ($points_balance >= $r['points_required']) {
        $rewards_list[] = $r;
      }
    }

    $cb_book_min_in = date('Y-m-d');
    $cb_book_min_out = date('Y-m-d', strtotime('+1 day'));

  ?>



  <div class="container">
    <div class="row">

      <div class="col-12 my-5 mb-4 px-4">
        <h2 class="fw-bold">CONFIRM BOOKING</h2>
        <div style="font-size: 14px;">
          <a href="index.php" class="text-secondary text-decoration-none">HOME</a>
          <span class="text-secondary"> > </span>
          <a href="rooms.php" class="text-secondary text-decoration-none">ROOMS</a>
          <span class="text-secondary"> > </span>
          <a href="#" class="text-secondary text-decoration-none">CONFIRM</a>
        </div>
      </div>

      <div class="col-lg-7 col-md-12 px-4">
        <?php 

          $room_thumb = ROOMS_IMG_PATH."thumbnail.jpg";
          $thumb_q = mysqli_query($con,"SELECT * FROM `room_images` 
            WHERE `room_id`='$room_data[id]' 
            AND `thumb`='1'");

          if(mysqli_num_rows($thumb_q)>0){
            $thumb_res = mysqli_fetch_assoc($thumb_q);
            $room_thumb = ROOMS_IMG_PATH.$thumb_res['image'];
          }

          echo<<<data
            <div class="card p-3 shadow-sm rounded">
              <img src="$room_thumb" class="img-fluid rounded mb-3">
              <h5>$room_data[name]</h5>
              <h6>$selected_currency $converted_price per night</h6>
              <button type="button" class="btn btn-outline-success mt-3 w-100 shadow-none" data-bs-toggle="modal" data-bs-target="#room360Modal">
                <i class="bi bi-arrow-repeat"></i> View 360° Room
              </button>
            </div>
          data;

        ?>
      </div>

      <div class="col-lg-5 col-md-12 px-4">
        <div class="card mb-4 border-0 shadow-sm rounded-3">
          <div class="card-body">
            <form action="pay_now.php" method="POST" id="booking_form" data-booking-today="<?php echo htmlspecialchars($cb_book_min_in, ENT_QUOTES, 'UTF-8'); ?>" data-room-id="<?php echo (int) $room_data['id']; ?>">
              <input type="hidden" name="room_id" value="<?php echo (int) $room_data['id']; ?>">
              <h6 class="mb-3">BOOKING DETAILS</h6>
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label class="form-label">Name</label>
                  <input name="name" type="text" value="<?php echo $user_data['name'] ?>" class="form-control shadow-none" required>
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Phone Number</label>
                  <input name="phonenum" type="text" maxlength="10" pattern="[0-9]{10}" value="<?php echo $user_data['phonenum'] ?>" class="form-control shadow-none" required>
                </div>
                <div class="col-md-12 mb-3">
                  <label class="form-label">Address</label>
                  <textarea name="address" class="form-control shadow-none" rows="1" required><?php echo $user_data['address'] ?></textarea>
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Check-in</label>
                  <input name="checkin" id="confirm_booking_checkin" onchange="check_availability()" type="date" class="form-control shadow-none" required autocomplete="off"
                    min="<?php echo htmlspecialchars($cb_book_min_in, ENT_QUOTES, 'UTF-8'); ?>">
                </div>
                <div class="col-md-6 mb-4">
                  <label class="form-label">Check-out</label>
                  <input name="checkout" id="confirm_booking_checkout" onchange="check_availability()" type="date" class="form-control shadow-none" required autocomplete="off"
                    min="<?php echo htmlspecialchars($cb_book_min_out, ENT_QUOTES, 'UTF-8'); ?>">
                </div>
                <div class="col-12 mb-3">
                  <?php if(isset($_GET['error']) && $_GET['error'] == 'fully_booked'): ?>
                    <div class="alert alert-danger mb-2 d-flex align-items-start gap-2">
                      <i class="bi bi-calendar-x flex-shrink-0 mt-1"></i>
                      <span>This room is <strong>fully booked</strong> for your selected dates. Please choose different dates or another room.</span>
                    </div>
                  <?php endif; ?>
                  <?php if(isset($_GET['error']) && $_GET['error'] == 'invalid_payment'): ?>
                    <div class="alert alert-danger mb-2">Please choose a valid payment method (Card or eSewa).</div>
                  <?php endif; ?>
                  <?php if(isset($_GET['error']) && $_GET['error'] == 'invalid_phone'): ?>
                    <div class="alert alert-danger mb-2">Phone number must be exactly 10 digits.</div>
                  <?php endif; ?>
                  <?php if(isset($_GET['error']) && $_GET['error'] == 'loyalty_redeem_failed'): ?>
                    <div class="alert alert-danger mb-2">Could not apply that loyalty reward. Check your points balance or choose another option.</div>
                  <?php endif; ?>
                  <?php if(isset($_GET['error']) && $_GET['error'] == 'insufficient_points'): ?>
                    <div class="alert alert-danger mb-2">You don't have enough loyalty points for that reward.</div>
                  <?php endif; ?>
                  <?php if(isset($_GET['error']) && $_GET['error'] == 'invalid_reward'): ?>
                    <div class="alert alert-danger mb-2">Invalid loyalty reward selected.</div>
                  <?php endif; ?>
                  <?php if(isset($_GET['error']) && $_GET['error'] == 'invalid_voucher'): ?>
                    <div class="alert alert-danger mb-2">Invalid, expired, or already used voucher code.</div>
                  <?php endif; ?>
                  <?php if (count($rewards_list) > 0): ?>
                <div class="col-12 mb-3">
                  <label class="form-label fw-bold"><i class="bi bi-gift me-1 text-primary"></i> Loyalty reward (optional)</label>
                  <select name="loyalty_reward" id="loyalty_reward" class="form-select shadow-none">
                    <option value="">No Reward</option>
                    <?php foreach ($rewards_list as $rw): ?>
                      <option value="<?php echo (int) $rw['id']; ?>"
                        data-discount="<?php echo htmlspecialchars((string) $rw['discount_percent'], ENT_QUOTES, 'UTF-8'); ?>"
                        data-points="<?php echo (int) $rw['points_required']; ?>"
                        data-name="<?php echo htmlspecialchars($rw['name'], ENT_QUOTES, 'UTF-8'); ?>">
                        <?php echo htmlspecialchars($rw['name']); ?> — <?php echo htmlspecialchars((string) $rw['discount_percent']); ?>% off (<?php echo (int) $rw['points_required']; ?> pts) — Use points now
                      </option>
                    <?php endforeach; ?>
                  </select>
                  <div class="form-text">You have <strong><?php echo (int) $points_balance; ?></strong> points. Points are deducted when you pay, after the discount is applied to your stay total.</div>
                  <div id="loyalty_preview" class="mt-2"></div>
                </div>
                  <?php endif; ?>

                  <!-- Voucher Code Section -->
                  <div class="col-12 mb-3">
                    <label class="form-label fw-bold"><i class="bi bi-ticket-perforated me-1 text-success"></i> Voucher Code (optional)</label>
                    <div class="input-group">
                      <input type="text" name="voucher_code" id="voucher_code" class="form-control shadow-none" placeholder="Enter voucher code" maxlength="20">
                      <button type="button" id="apply_voucher" class="btn btn-outline-success">Apply</button>
                    </div>
                    <div class="form-text">Use voucher codes from your loyalty program for additional discounts</div>
                    <div id="voucher_preview" class="mt-2"></div>
                  </div>
                  <div class="col-12 mb-3">
                    <label class="form-label fw-bold"><i class="bi bi-wallet2 me-1 text-primary"></i> Payment method</label>
                    <select name="payment_method" class="form-select shadow-none" required>
                      <option value="stripe">Card</option>
                      <option value="eSewa">eSewa</option>
                    </select>
                    <div class="form-text">
                      <?php if($selected_currency !== 'NPR'): ?>
                        Card is charged in <?php echo htmlspecialchars($selected_currency, ENT_QUOTES, 'UTF-8'); ?>.
                        eSewa uses the NPR equivalent of your total.
                      <?php else: ?>
                        Pay with card or eSewa wallet.
                      <?php endif; ?>
                    </div>
                  </div>
                </div>
                <div class="col-12">
                  <div class="spinner-border text-info mb-3 d-none" id="info_loader" role="status">
                    <span class="visually-hidden">Loading...</span>
                  </div>

                  <h6 class="mb-3 text-danger" id="pay_info">Provide check-in & check-out date !</h6>

                  <button name="pay_now" class="btn w-100 text-white custom-bg shadow-none mb-1" disabled>Pay Now</button>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>

    </div>
  </div>

  <!-- 360° Room Modal -->
  <div class="modal fade" id="room360Modal" tabindex="-1" aria-labelledby="room360Label" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="room360Label">360° Room View - <?php echo htmlspecialchars($room_data['name']); ?></h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div id="panorama" style="width: 100%; height: 500px;"></div>
        </div>
      </div>
    </div>
  </div>

  <?php require('inc/footer.php'); ?>
  
  <!-- Pannellum JavaScript -->
  <script src="https://cdn.jsdelivr.net/npm/pannellum@2.5.6/build/pannellum.js"></script>
  
  <script>
    // 360° Room Image Path
    const room360Image = <?php echo $room_360_image ? "'" . $room_360_image . "'" : "null"; ?>;
    let panorama_viewer = null;

    // Initialize Pannellum when modal is shown
    document.getElementById('room360Modal').addEventListener('shown.bs.modal', function() {
      if (room360Image && !panorama_viewer) {
        try {
          panorama_viewer = pannellum.viewer('panorama', {
            'type': 'equirectangular',
            'panorama': room360Image,
            'autoLoad': true,
            'orientationOnByDefault': false,
            'compass': true,
            'autoRotate': 2,
            'mouseZoom': true,
            'controlsEnabled': true,
            'showControls': true
          });
        } catch (err) {
          console.error('Error initializing Pannellum:', err);
          document.getElementById('panorama').innerHTML = '<div class="alert alert-warning">Unable to load 360° view</div>';
        }
      }
    });

    // Clean up when modal is hidden
    document.getElementById('room360Modal').addEventListener('hidden.bs.modal', function() {
      if (panorama_viewer) {
        try {
          panorama_viewer.destroy();
          panorama_viewer = null;
        } catch (err) {
          console.error('Error destroying Pannellum:', err);
        }
      }
    });
  </script>

  <script>
    let booking_form = document.getElementById('booking_form');
    let info_loader = document.getElementById('info_loader');
    let pay_info = document.getElementById('pay_info');
    window.lastPaymentAmount = null;
    window.lastCurrency = null;
    window.lastDays = null;

    function parseAmount(val) {
      return parseFloat(String(val).replace(/,/g, '')) || 0;
    }

    function formatAmount(num) {
      return parseAmount(num).toFixed(2);
    }

    function getActiveDiscount() {
      let sel = document.getElementById('loyalty_reward');
      if (sel && sel.value) {
        let opt = sel.options[sel.selectedIndex];
        return {
          percent: parseFloat(opt.getAttribute('data-discount') || '0'),
          label: (opt.getAttribute('data-name') || 'Loyalty reward') + ' (' + (opt.getAttribute('data-discount') || '0') + '% off)',
          points: opt.getAttribute('data-points') || '',
          type: 'loyalty'
        };
      }
      if (appliedVoucher) {
        return {
          percent: parseFloat(appliedVoucher.discount) || 0,
          label: appliedVoucher.name + ' voucher (' + appliedVoucher.discount + '% off)',
          type: 'voucher'
        };
      }
      return { percent: 0, label: '', type: '' };
    }

    function updatePaymentDisplay() {
      let preview = document.getElementById('loyalty_preview');
      let voucherPreview = document.getElementById('voucher_preview');

      if (window.lastPaymentAmount == null || window.lastDays == null) {
        if (preview) preview.innerHTML = '';
        return;
      }

      let base = parseAmount(window.lastPaymentAmount);
      let discount = getActiveDiscount();
      let pct = Math.min(100, Math.max(0, discount.percent));
      let discountAmt = pct > 0 ? base * (pct / 100) : 0;
      let finalAmt = Math.max(0, base - discountAmt);
      let cur = window.lastCurrency || '';

      let html = 'No. of Days: <strong>' + window.lastDays + '</strong><br>';

      if (pct > 0) {
        html += '<span class="text-muted small">Subtotal: ' + cur + ' ' + formatAmount(base) + '</span><br>';
        html += '<span class="text-success small">Discount (' + pct + '%): −' + cur + ' ' + formatAmount(discountAmt) + '</span><br>';
        html += 'Total Amount to Pay: <strong class="text-dark">' + cur + ' ' + formatAmount(finalAmt) + '</strong>';
        pay_info.classList.replace('text-danger', 'text-dark');
      } else {
        html += 'Total Amount to Pay: <strong>' + cur + ' ' + formatAmount(base) + '</strong>';
        pay_info.classList.replace('text-danger', 'text-dark');
      }

      pay_info.innerHTML = html;

      if (preview) {
        if (discount.type === 'loyalty' && discount.points) {
          preview.innerHTML = '<div class="alert alert-info py-2 px-3 mb-0 small"><i class="bi bi-gift me-1"></i> ' +
            discount.points + ' loyalty points will be used when you pay.</div>';
        } else {
          preview.innerHTML = '';
        }
      }

      if (voucherPreview && appliedVoucher && discount.type === 'voucher') {
        voucherPreview.innerHTML = '<div class="alert alert-success py-2 px-3 mb-0 small"><i class="bi bi-check-circle me-1"></i> ' +
          'Voucher <strong>' + escapeHtml(appliedVoucher.code) + '</strong> applied — ' + appliedVoucher.discount + '% off</div>';
      }
    }

    function updateLoyaltyPreview() {
      updatePaymentDisplay();
    }

    function escapeHtml(s) {
      let d = document.createElement('div');
      d.textContent = s;
      return d.innerHTML;
    }

    let lr = document.getElementById('loyalty_reward');
    if (lr) lr.addEventListener('change', updateLoyaltyPreview);

    // Voucher functionality
    let appliedVoucher = null;

    document.getElementById('apply_voucher').addEventListener('click', function() {
      let voucherCode = document.getElementById('voucher_code').value.trim().toUpperCase();
      let voucherPreview = document.getElementById('voucher_preview');
      
      if (!voucherCode) {
        voucherPreview.innerHTML = '<div class="alert alert-warning py-2 px-3 mb-0 small">Please enter a voucher code</div>';
        return;
      }

      // Check if loyalty reward is also selected
      let loyaltyReward = document.getElementById('loyalty_reward');
      if (loyaltyReward && loyaltyReward.value) {
        voucherPreview.innerHTML = '<div class="alert alert-warning py-2 px-3 mb-0 small">Cannot use voucher code with loyalty reward. Please choose one option.</div>';
        return;
      }

      // Send AJAX request to validate voucher
      let xhr = new XMLHttpRequest();
      xhr.open("POST", "ajax/validate_voucher.php", true);
      xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
      
      xhr.onload = function() {
        try {
          let data = JSON.parse(this.responseText);
          if (data.success) {
            appliedVoucher = {
              code: voucherCode,
              discount: data.discount_percent,
              name: data.reward_name
            };
            updateLoyaltyPreview();
          } else {
            voucherPreview.innerHTML = '<div class="alert alert-danger py-2 px-3 mb-0 small"><i class="bi bi-x-circle me-1"></i> ' + data.message + '</div>';
            appliedVoucher = null;
            updatePaymentDisplay();
          }
        } catch (e) {
          voucherPreview.innerHTML = '<div class="alert alert-danger py-2 px-3 mb-0 small">Error validating voucher</div>';
          appliedVoucher = null;
        }
      };
      
      xhr.send('voucher_code=' + encodeURIComponent(voucherCode));
    });

    // Clear voucher when loyalty reward is selected
    if (lr) {
      lr.addEventListener('change', function() {
        if (this.value) {
          document.getElementById('voucher_code').value = '';
          document.getElementById('voucher_preview').innerHTML = '';
          appliedVoucher = null;
        }
      });
    }

    function check_availability()
    {
      let checkin_val = booking_form.elements['checkin'].value;
      let checkout_val = booking_form.elements['checkout'].value;

      booking_form.elements['pay_now'].setAttribute('disabled',true);

      if(checkin_val!='' && checkout_val!='')
      {
        pay_info.classList.add('d-none');
        pay_info.classList.replace('text-dark','text-danger');
        info_loader.classList.remove('d-none');

        let data = new FormData();

        data.append('check_availability','');
        data.append('check_in',checkin_val);
        data.append('check_out',checkout_val);
        data.append('room_id', booking_form.getAttribute('data-room-id') || booking_form.elements['room_id']?.value || '');

        let xhr = new XMLHttpRequest();
        xhr.open("POST","ajax/confirm_booking.php",true);

        xhr.onload = function()
        {
          let data = JSON.parse(this.responseText);

          if(data.status == 'check_in_out_equal'){
            pay_info.innerText = "You cannot check-out on the same day!";
          }
          else if(data.status == 'check_out_earlier'){
            pay_info.innerText = "Check-out date is earlier than check-in date!";
          }
          else if(data.status == 'check_in_earlier'){
            pay_info.innerText = "Check-in date is earlier than today's date!";
          }
          else if(data.status == 'unavailable'){
            pay_info.innerHTML = '<div class="d-flex align-items-start gap-2 mb-0">' +
              '<i class="bi bi-calendar-x text-danger flex-shrink-0" style="font-size:1.15rem;line-height:1.5;"></i>' +
              '<span>This room is <strong>fully booked</strong> for the selected dates. Please try different dates or another room.</span></div>';
            pay_info.classList.replace('text-dark', 'text-danger');
          }
          else if(data.status == 'error'){
            pay_info.innerText = data.message || 'Could not check availability.';
            pay_info.classList.replace('text-dark', 'text-danger');
          }
          else{
            booking_form.elements['pay_now'].removeAttribute('disabled');
            window.lastPaymentAmount = data.payment;
            window.lastCurrency = data.currency;
            window.lastDays = data.days;
            updatePaymentDisplay();
          }

          pay_info.classList.remove('d-none');
          info_loader.classList.add('d-none');
        }

        xhr.send(data);
      }
    }

    (function initConfirmBookingDateLimits() {
      var checkin = document.getElementById('confirm_booking_checkin');
      var checkout = document.getElementById('confirm_booking_checkout');
      if (!booking_form || !checkin || !checkout) return;

      function addDaysISO(iso, n) {
        var parts = iso.split('-').map(Number);
        var dt = new Date(parts[0], parts[1] - 1, parts[2] + n);
        var y = dt.getFullYear();
        var m = String(dt.getMonth() + 1).padStart(2, '0');
        var d = String(dt.getDate()).padStart(2, '0');
        return y + '-' + m + '-' + d;
      }

      function todayISO() {
        var t = booking_form.getAttribute('data-booking-today');
        if (t && /^\d{4}-\d{2}-\d{2}$/.test(t)) return t;
        var now = new Date();
        return now.getFullYear() + '-' + String(now.getMonth() + 1).padStart(2, '0') + '-' + String(now.getDate()).padStart(2, '0');
      }

      function clampConfirmBookingDates() {
        var today = todayISO();
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

      function onCheckinDateAdjusted() {
        clampConfirmBookingDates();
        check_availability();
      }

      if (!checkin.dataset.bookingRangeBound) {
        checkin.dataset.bookingRangeBound = '1';
        checkin.addEventListener('change', onCheckinDateAdjusted);
        checkin.addEventListener('input', onCheckinDateAdjusted);
        checkout.addEventListener('input', function () {
          clampConfirmBookingDates();
          check_availability();
        });
      }

      function runClamp() {
        clampConfirmBookingDates();
        if (checkin.value && checkout.value) check_availability();
      }

      if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', runClamp);
      else runClamp();
      window.addEventListener('load', runClamp);
      setTimeout(runClamp, 300);
      setTimeout(runClamp, 1500);
    })();

    booking_form.addEventListener('submit', function(e){
      let phone = booking_form.elements['phonenum'].value.trim();
      if(!/^[0-9]{10}$/.test(phone)){
        e.preventDefault();
        alert('error',"Phone number must be exactly 10 digits!");
      }
    });
  </script>

</body>
</html>