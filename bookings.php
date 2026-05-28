<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <?php require('inc/links.php'); ?>
  <title><?php echo htmlspecialchars($settings_r['site_title'], ENT_QUOTES, 'UTF-8'); ?> - My bookings</title>
  <style>
    .bookings-hero {
      background: linear-gradient(135deg, #f8fafb 0%, #eef6f4 50%, #f0f4f8 100%);
      border-radius: 1rem;
      padding: 2rem 1.5rem;
      margin-bottom: 0.5rem;
      border: 1px solid rgba(46, 193, 172, 0.15);
    }
    .bookings-hero h2 { font-family: var(--font-heading, 'Outfit', sans-serif); letter-spacing: -0.02em; }
    .booking-card {
      border: none;
      border-radius: 1rem;
      overflow: hidden;
      box-shadow: 0 0.35rem 1.25rem rgba(0,0,0,.06);
      transition: transform .2s ease, box-shadow .2s ease;
      height: 100%;
      display: flex;
      flex-direction: column;
    }
    .booking-card:hover {
      transform: translateY(-3px);
      box-shadow: 0 0.65rem 2rem rgba(0,0,0,.1);
    }
    .booking-card-img-wrap {
      position: relative;
      height: 200px;
      background: linear-gradient(180deg, #e9ecef 0%, #dee2e6 100%);
    }
    .booking-card-img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }
    .booking-card-img-overlay {
      position: absolute;
      inset: 0;
      background: linear-gradient(to top, rgba(0,0,0,.65) 0%, transparent 55%);
      pointer-events: none;
    }
    .booking-card-title-overlay {
      position: absolute;
      left: 0; right: 0; bottom: 0;
      padding: 1rem 1.1rem;
      color: #fff;
    }
    .booking-card-title-overlay h3 {
      font-size: 1.15rem;
      font-weight: 700;
      margin: 0;
      text-shadow: 0 1px 8px rgba(0,0,0,.35);
    }
    .booking-meta {
      font-size: 0.875rem;
      color: #5c6570;
      max-height: 0;
      overflow: hidden;
      transition: max-height 0.3s ease;
    }
    .booking-meta.show {
      max-height: 500px;
    }
    .booking-meta i {
      color: var(--teal, #2ec1ac);
      width: 1.25rem;
    }
    .booking-card-body { padding: 1.25rem; flex: 1; display: flex; flex-direction: column; }
    .booking-card-actions {
      margin-top: auto;
      padding-top: 1rem;
      border-top: 1px solid #f0f2f4;
      display: flex;
      flex-wrap: wrap;
      align-items: center;
      gap: 0.5rem;
    }
    .booking-card-actions .btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 0.35rem;
      line-height: 1.25;
      padding-top: 0.4rem;
      padding-bottom: 0.4rem;
      vertical-align: middle;
    }
    .booking-card-actions .btn .bi {
      line-height: 1;
      font-size: 1em;
      vertical-align: 0;
      margin-top: 0;
    }
    .booking-card-actions .btn:hover,
    .booking-card-actions .btn:focus,
    .booking-card-actions .custom-bg:hover {
      transform: none;
    }
    .status-pill { font-size: 0.75rem; font-weight: 600; letter-spacing: 0.02em; padding: 0.35em 0.75em; }
    .bookings-empty {
      text-align: center;
      padding: 3rem 1.5rem;
      background: #fff;
      border-radius: 1rem;
      border: 1px dashed #dee2e6;
    }
    .booking-details-toggle {
      background: none;
      border: none;
      padding: 0.5rem 0;
      font-weight: 600;
      color: var(--teal, #2ec1ac);
      cursor: pointer;
      display: flex;
      align-items: center;
      gap: 0.5rem;
      transition: color 0.2s ease;
    }
    .booking-details-toggle:hover {
      color: var(--teal_hover, #249e8c);
    }
    .booking-details-toggle i {
      transition: transform 0.3s ease;
    }
    .booking-details-toggle i.rotated {
      transform: rotate(180deg);
    }
    h2{
      color:#0f172a !important
    }
  </style>
</head>
<body class="bg-light">

  <?php 
    require('inc/header.php'); 

    if(!(isset($_SESSION['login']) && $_SESSION['login']==true)){
      redirect('index.php');
    }
  ?>

  <div class="container py-4 py-lg-5">
    <div class="bookings-hero mb-4">
      <div class="row align-items-center g-3">
        <div class="col-lg-8">
          <h2 class="fw-bold h-font mb-2">My bookings</h2>
          <p class="text-muted mb-0 small">View your stays, download receipts, and manage upcoming reservations.</p>
        </div>
        <div class="col-lg-4 text-lg-end">
          <a href="rooms.php" class="btn custom-bg text-white shadow-none px-4 rounded-pill">
            <i class="bi bi-plus-lg me-1"></i> Book a room
          </a>
        </div>
      </div>
      <div class="h-line bg-dark mt-3 mb-0" style="margin-left:0;"></div>
    </div>

    <div class="row g-4">
      <?php 
        $query = "SELECT bo.*, bd.*,
          (SELECT ri.image FROM `room_images` ri WHERE ri.room_id = bo.room_id AND ri.thumb = 1 LIMIT 1) AS thumb_image
          FROM `booking_order` bo
          INNER JOIN `booking_details` bd ON bo.booking_id = bd.booking_id
          WHERE ((bo.booking_status='booked') 
          OR (bo.booking_status='cancelled')
          OR (bo.booking_status='payment failed')) 
          AND (bo.user_id=?)
          ORDER BY bo.booking_id DESC";

        $result = select($query, [$_SESSION['uId']], 'i');
        $has_rows = ($result && mysqli_num_rows($result) > 0);

        if (!$has_rows) {
          echo '<div class="col-12">
            <div class="bookings-empty">
              <i class="bi bi-calendar-x display-4 text-muted mb-3 d-block" style="opacity:.5;"></i>
              <h5 class="fw-bold text-secondary">No bookings yet</h5>
              <p class="text-muted small mb-4">When you complete a reservation, it will appear here with the room photo and details.</p>
              <a href="rooms.php" class="btn custom-bg text-white shadow-none rounded-pill px-4">Browse rooms</a>
            </div>
          </div>';
        } else {
          while ($data = mysqli_fetch_assoc($result)) {
            $date = date("d M Y", strtotime($data['datentime']));
            $checkin = date("d M Y", strtotime($data['check_in']));
            $checkout = date("d M Y", strtotime($data['check_out']));

            $thumb_file = !empty($data['thumb_image']) ? $data['thumb_image'] : 'thumbnail.jpg';
            $img_src = ROOMS_IMG_PATH . $thumb_file;

            $room_name = htmlspecialchars($data['room_name'], ENT_QUOTES, 'UTF-8');
            $currency = htmlspecialchars(strtoupper($data['currency'] ?? 'NPR'), ENT_QUOTES, 'UTF-8');
            $trans_amt = is_numeric($data['trans_amt']) ? number_format((float) $data['trans_amt'], 2) : htmlspecialchars((string) $data['trans_amt'], ENT_QUOTES, 'UTF-8');
            $price_night = is_numeric($data['price']) ? number_format((float) $data['price'], 2) : htmlspecialchars((string) $data['price'], ENT_QUOTES, 'UTF-8');
            $order_id_esc = htmlspecialchars($data['order_id'], ENT_QUOTES, 'UTF-8');
            $bid = (int) $data['booking_id'];
            $rid = (int) $data['room_id'];

            $status_bg = 'bg-secondary';
            $status_label = ucfirst(htmlspecialchars($data['booking_status'], ENT_QUOTES, 'UTF-8'));
            $btn = '';

            if ($data['booking_status'] == 'booked') {
              $status_bg = 'bg-success';
              $btn = "<a href='generate_pdf.php?gen_pdf&id=$bid' class='btn btn-outline-dark btn-sm rounded-pill shadow-none d-inline-flex align-items-center justify-content-center'><i class='bi bi-download'></i><span>Receipt</span></a>";
              if ($data['arrival'] == 1 && $data['rate_review'] == 0) {
                  $btn .= "<button type='button' onclick='review_room($bid,$rid)' data-bs-toggle='modal' data-bs-target='#reviewModal' class='btn custom-bg text-white btn-sm rounded-pill shadow-none d-inline-flex align-items-center justify-content-center'><i class='bi bi-star'></i><span>Rate stay</span></button>";
              }
              if ($data['arrival'] == 0) {
                  $btn .= "<button onclick='cancel_booking($bid)' type='button' class='btn btn-outline-danger btn-sm rounded-pill shadow-none d-inline-flex align-items-center justify-content-center'><i class='bi bi-x-lg'></i><span>Cancel</span></button>";
              }
          } else {
                $btn = "<button onclick='cancel_booking($bid)' type='button' class='btn btn-outline-danger btn-sm rounded-pill shadow-none d-inline-flex align-items-center justify-content-center'><i class='bi bi-x-lg'></i><span>Cancel</span></button>";
              }
            } else if ($data['booking_status'] == 'cancelled') {
              $status_bg = 'bg-danger';
              if ($data['refund'] == 0) {
                $btn = "<span class='badge bg-warning text-dark rounded-pill'>Refund in process</span>";
              } else {
                $btn = "<a href='admin/generate_pdf.php?gen_pdf&id=$bid' class='btn btn-outline-dark btn-sm rounded-pill shadow-none d-inline-flex align-items-center justify-content-center'><i class='bi bi-download'></i><span>Receipt</span></a>";
              }
            } else {
              $status_bg = 'bg-warning text-dark';
              $btn = "<a href='generate_pdf.php?gen_pdf&id=$bid' class='btn btn-outline-dark btn-sm rounded-pill shadow-none d-inline-flex align-items-center justify-content-center'><i class='bi bi-download'></i><span>Receipt</span></a>";
            }

            echo <<<HTML
            <div class="col-lg-6 col-xl-4">
              <article class="card booking-card">
                <div class="booking-card-img-wrap">
                  <img src="$img_src" class="booking-card-img" alt="$room_name">
                  <div class="booking-card-img-overlay"></div>
                  <div class="booking-card-title-overlay">
                    <h3 class="h-font">$room_name</h3>
                    <div class="small opacity-90">NPR {$price_night} / night</div>
                  </div>
                </div>
                <div class="booking-card-body">
                  <div class="d-flex justify-content-between align-items-start mb-3">
                    <span class="badge {$status_bg} status-pill rounded-pill">{$status_label}</span>
                    <span class="small text-muted"><i class="bi bi-clock me-1"></i>{$date}</span>
                  </div>
                  <button type="button" class="booking-details-toggle" onclick="toggleBookingDetails(this)">
                    <i class="bi bi-chevron-down"></i>
                    <span>View booking details</span>
                  </button>
                  <div class="booking-meta">
                    <div class="d-flex align-items-start gap-2 mb-2">
                      <i class="bi bi-calendar2-check mt-1"></i>
                      <div><strong class="text-dark">Check-in</strong><br>{$checkin}</div>
                    </div>
                    <div class="d-flex align-items-start gap-2 mb-2">
                      <i class="bi bi-calendar2-x mt-1"></i>
                      <div><strong class="text-dark">Check-out</strong><br>{$checkout}</div>
                    </div>
                    <div class="d-flex align-items-start gap-2 mb-2">
                      <i class="bi bi-currency-exchange mt-1"></i>
                      <div><strong class="text-dark">Paid</strong><br>{$currency} {$trans_amt}</div>
                    </div>
                    <div class="d-flex align-items-start gap-2">
                      <i class="bi bi-hash mt-1"></i>
                      <div><strong class="text-dark">Order</strong><br><span class="font-monospace small">{$order_id_esc}</span></div>
                    </div>
                  </div>
                  <div class="booking-card-actions">{$btn}</div>
                </div>
              </article>
            </div>
HTML;
          }
        }
      ?>
    </div>
  </div>

  <div class="modal fade" id="reviewModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="reviewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content border-0 shadow">
        <form id="review-form">
          <div class="modal-header border-0 pb-0">
            <h5 class="modal-title d-flex align-items-center fw-bold h-font" id="reviewModalLabel">
              <span class="rounded-circle custom-bg text-white d-inline-flex align-items-center justify-content-center me-2" style="width:40px;height:40px;"><i class="bi bi-chat-square-heart-fill"></i></span>
              Rate your stay
            </h5>
            <button type="reset" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body pt-2">
            <div class="mb-3">
              <label class="form-label fw-semibold d-block mb-1">Rating</label>
              <div class="review-stars d-flex align-items-center mb-1" data-selected="0">
                <span class="star-btn me-1" data-value="1" role="button" tabindex="0" aria-label="1 star"><i class="bi bi-star"></i></span>
                <span class="star-btn me-1" data-value="2" role="button" tabindex="0" aria-label="2 stars"><i class="bi bi-star"></i></span>
                <span class="star-btn me-1" data-value="3" role="button" tabindex="0" aria-label="3 stars"><i class="bi bi-star"></i></span>
                <span class="star-btn me-1" data-value="4" role="button" tabindex="0" aria-label="4 stars"><i class="bi bi-star"></i></span>
                <span class="star-btn" data-value="5" role="button" tabindex="0" aria-label="5 stars"><i class="bi bi-star"></i></span>
              </div>
              <div class="small text-muted" id="rating-label">Select a rating</div>
              <input type="hidden" name="rating" value="0">
            </div>
            <div class="mb-4">
              <label class="form-label fw-semibold">Your review</label>
              <textarea name="review" rows="4" required class="form-control shadow-none" placeholder="Tell us about your experience…"></textarea>
            </div>
            <input type="hidden" name="booking_id">
            <input type="hidden" name="room_id">
            <div class="text-end">
              <button type="button" class="btn btn-light border me-2 rounded-pill" data-bs-dismiss="modal">Cancel</button>
              <button type="submit" class="btn custom-bg text-white shadow-none rounded-pill px-4">Submit review</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>

  <?php 
    if(isset($_GET['cancel_status'])){
      alert('success','Booking Cancelled!');
    }  
    else if(isset($_GET['review_status'])){
      alert('success','Thank you for rating & review!');
    }  
  ?>

  <?php require('inc/footer.php'); ?>

  <script>
    function cancel_booking(id)
    {
      if(confirm('Are you sure to cancel booking?'))
      {        
        let xhr = new XMLHttpRequest();
        xhr.open("POST","ajax/cancel_booking.php",true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

        xhr.onload = function(){
          if(this.responseText==1){
            window.location.href="bookings.php?cancel_status=true";
          }
          else{
            alert('error','Cancellation Failed!');
          }
        }

        xhr.send('cancel_booking&id='+id);
      }
    }

    let review_form = document.getElementById('review-form');

    function review_room(bid,rid){
      review_form.elements['booking_id'].value = bid;
      review_form.elements['room_id'].value = rid;
      // reset stars to "no rating" on open
      const starsWrapper = document.querySelector('.review-stars');
      if (starsWrapper) {
        const hiddenRating = review_form.elements['rating'];
        const label = document.getElementById('rating-label');
        const starBtns = starsWrapper.querySelectorAll('.star-btn');
        hiddenRating.value = 0;
        starsWrapper.dataset.selected = '0';
        starBtns.forEach(btn => {
          btn.classList.remove('is-filled');
          const icon = btn.querySelector('.bi');
          if (icon) {
            icon.className = 'bi bi-star';
          }
        });
        if (label) label.textContent = 'Select a rating';
      }
    }

    // interactive star rating
    (function initStarRating(){
      const starsWrapper = document.querySelector('.review-stars');
      if (!starsWrapper) return;

      const starBtns = starsWrapper.querySelectorAll('.star-btn');
      const hiddenRating = review_form.elements['rating'];
      const label = document.getElementById('rating-label');

      const ratingTexts = {
        1: 'Bad',
        2: 'Poor',
        3: 'Okay',
        4: 'Good',
        5: 'Excellent'
      };

      function updateVisual(selected) {
        starBtns.forEach(btn => {
          const val = parseInt(btn.dataset.value, 10);
          const filled = selected > 0 && val <= selected;
          btn.classList.toggle('is-filled', filled);
          const icon = btn.querySelector('.bi');
          if (icon) {
            icon.className = filled ? 'bi bi-star-fill' : 'bi bi-star';
          }
        });
        if (label) {
          label.textContent = selected ? (ratingTexts[selected] || (selected + ' stars')) : 'Select a rating';
        }
      }

      starsWrapper.addEventListener('mouseleave', () => {
        const selected = parseInt(starsWrapper.dataset.selected || hiddenRating.value || '0', 10);
        updateVisual(selected);
      });

      starBtns.forEach(btn => {
        const val = parseInt(btn.dataset.value, 10);

        btn.addEventListener('mouseenter', () => {
          updateVisual(val);
        });

        btn.addEventListener('click', () => {
          starsWrapper.dataset.selected = String(val);
          hiddenRating.value = val;
          updateVisual(val);
        });

        btn.addEventListener('keydown', (e) => {
          if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            starsWrapper.dataset.selected = String(val);
            hiddenRating.value = val;
            updateVisual(val);
          }
        });
      });

      // initial state (no rating yet)
      updateVisual(parseInt(hiddenRating.value || '0',10));
    })();

    review_form.addEventListener('submit',function(e){
      e.preventDefault();

      let data = new FormData();

      data.append('review_form','');
      data.append('rating',review_form.elements['rating'].value);
      data.append('review',review_form.elements['review'].value);
      data.append('booking_id',review_form.elements['booking_id'].value);
      data.append('room_id',review_form.elements['room_id'].value);

      let xhr = new XMLHttpRequest();
      xhr.open("POST","ajax/review_room.php",true);

      xhr.onload = function()
      {
        const res = (this.responseText || '').trim();

        if(res === '1')
        {
          window.location.href = 'bookings.php?review_status=true';
        }
        else if(res === 'already_reviewed'){
          var myModal = document.getElementById('reviewModal');
          var modal = bootstrap.Modal.getInstance(myModal);
          modal.hide();
          alert('error','You have already submitted a review for this booking.');
        }
        else{
          var myModal = document.getElementById('reviewModal');
          var modal = bootstrap.Modal.getInstance(myModal);
          modal.hide();
  
          alert('error',"Rating & Review Failed!");
        }
      }

      xhr.send(data);
    })

    // Toggle booking details (expand/collapse)
    function toggleBookingDetails(button) {
      const detailsDiv = button.nextElementSibling;
      const icon = button.querySelector('i');
      const text = button.querySelector('span');
      
      detailsDiv.classList.toggle('show');
      icon.classList.toggle('rotated');
      
      if (detailsDiv.classList.contains('show')) {
        text.textContent = 'Hide booking details';
      } else {
        text.textContent = 'View booking details';
      }
    }

  </script>

</body>
</html>
