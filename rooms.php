<?php 
  session_start();
  require('inc/links.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo $settings_r['site_title']; ?> - ROOMS</title>
  <style>
    #rooms-data .room-list-card{
      border-radius: 16px;
      border: 1px solid rgba(148, 163, 184, 0.2);
      overflow: hidden;
      position: relative;
      transform: none;
      transition: box-shadow .3s ease, border-color .3s ease;
    }

    #rooms-data .room-list-card::before{
      content: "";
      position: absolute;
      inset: 0;
      border-radius: inherit;
      border: 1px solid rgba(45, 156, 149, 0.0);
      transition: border-color .3s ease;
      pointer-events: none;
      z-index: 2;
    }

    #rooms-data .room-list-card:hover{
      box-shadow: 0 18px 34px rgba(15, 23, 42, 0.14) !important;
      border-color: rgba(45, 156, 149, 0.35);
    }

    #rooms-data .room-list-card:hover::before{
      border-color: rgba(45, 156, 149, 0.35);
    }

    #rooms-data .room-list-card .room-thumb{
      transition: transform .55s cubic-bezier(.22,.61,.36,1), filter .4s ease;
      transform-origin: center;
    }

    #rooms-data .room-list-card:hover .room-thumb{
      transform: scale(1.05);
      filter: saturate(1.05) contrast(1.02);
    }

    /* Keep stacked CTA buttons clean: no color spill in gap */
    #rooms-data .book-now-btn,
    #rooms-data .room-details-btn{
      isolation: isolate;
      box-shadow: none !important;
    }

    #rooms-data .book-now-btn:hover,
    #rooms-data .room-details-btn:hover{
      transform: translateY(-1px) scale(1.005);
      box-shadow: 0 6px 10px rgba(15, 23, 42, 0.1) !important;
      filter: none;
    }

    @media (max-width: 767.98px){
      #rooms-data .room-list-card:hover{
        box-shadow: 0 12px 24px rgba(15, 23, 42, 0.12) !important;
      }
    }

    /* Sidebar currency: custom dropdown (no native select) */
    .currency-filter-dropdown .dropdown-toggle::after {
      display: none;
    }
    .currency-filter-dropdown .dropdown-toggle {
      text-align: left;
      border-color: rgba(148, 163, 184, 0.45) !important;
      transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }
    .currency-filter-dropdown .dropdown-toggle:hover,
    .currency-filter-dropdown .dropdown-toggle:focus {
      border-color: var(--teal, #2ec1ac) !important;
      box-shadow: 0 0 0 0.15rem rgba(46, 193, 172, 0.2);
    }
    .currency-filter-dropdown .dropdown-menu {
      z-index: 1025;
    }
    .currency-filter-dropdown .dropdown-item {
      border-radius: 0.5rem;
      margin: 0 0.35rem;
      padding: 0.55rem 0.75rem;
      font-weight: 500;
    }
    .currency-filter-dropdown .dropdown-item:hover,
    .currency-filter-dropdown .dropdown-item:focus {
      background: rgba(46, 193, 172, 0.1);
      color: #0f172a;
    }
    .currency-filter-dropdown .dropdown-item.active {
      background: rgba(46, 193, 172, 0.18);
      color: #0f172a;
      font-weight: 600;
    }
    .currency-filter-dropdown .dropdown-item .currency-code {
      font-variant-numeric: tabular-nums;
      min-width: 2.75rem;
      display: inline-block;
    }
  </style>
</head>
<body class="bg-light">

  <?php require('inc/header.php'); ?>

  <?php 
    // Default values for checkin, checkout, adult, and children
    $checkin_default = "";
    $checkout_default = "";
    $adult_default = "";
    $children_default = "";

    if (isset($_GET['check_availability'])) {
      $frm_data = filteration($_GET);
      $checkin_default = $frm_data['checkin'];
      $checkout_default = $frm_data['checkout'];
      $adult_default = $frm_data['adult'];
      $children_default = $frm_data['children'];
    }
  ?>

  <div class="my-5 px-4">
    <h2 class="fw-bold h-font text-center">OUR ROOMS</h2>
    <div class="h-line bg-dark"></div>
  </div>

  <div class="container-fluid">
    <div class="row">

      <div class="col-lg-3 col-md-12 mb-lg-0 mb-4 ps-4">
        <nav class="navbar navbar-expand-lg navbar-light bg-white rounded shadow">
          <div class="container-fluid flex-lg-column align-items-stretch">
            <h4 class="mt-2">FILTERS</h4>
            <button class="navbar-toggler shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#filterDropdown" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
              <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse flex-column align-items-stretch mt-2" id="filterDropdown">
              
              <!-- Check availablity -->
              <div class="border bg-light p-3 rounded mb-3">
                <h5 class="d-flex align-items-center justify-content-between mb-3" style="font-size: 18px;">
                  <span>CHECK AVAILABILITY</span>
                  <button id="chk_avail_btn" onclick="chk_avail_clear()" class="btn shadow-none btn-sm text-secondary d-none">Reset</button>
                </h5>
                <label class="form-label">Check-in</label>
                <input type="date" class="form-control shadow-none mb-3" value="<?php echo $checkin_default ?>" id="checkin" onchange="chk_avail_filter()">
                <label class="form-label">Check-out</label>
                <input type="date" class="form-control shadow-none" value="<?php echo $checkout_default ?>"  id="checkout" onchange="chk_avail_filter()">
              </div>

              <!-- Facilities -->
              <div class="border bg-light p-3 rounded mb-3">
                <h5 class="d-flex align-items-center justify-content-between mb-3" style="font-size: 18px;">
                  <span>FACILITIES</span>
                  <button id="facilities_btn" onclick="facilities_clear()" class="btn shadow-none btn-sm text-secondary d-none">Reset</button>
                </h5>
                <?php 
                  $facilities_q = selectAll('facilities');
                  while($row = mysqli_fetch_assoc($facilities_q)) {
                    echo<<<facilities
                      <div class="mb-2">
                        <input type="checkbox" onclick="fetch_rooms()" name="facilities" value="$row[id]" class="form-check-input shadow-none me-1" id="$row[id]">
                        <label class="form-check-label" for="$row[id]">$row[name]</label>
                      </div>
                    facilities;
                  }
                ?>
              </div>

              <!-- Guests -->
              <div class="border bg-light p-3 rounded mb-3">
                <h5 class="d-flex align-items-center justify-content-between mb-3" style="font-size: 18px;">
                  <span>GUESTS</span>
                  <button id="guests_btn" onclick="guests_clear()" class="btn shadow-none btn-sm text-secondary d-none">Reset</button>
                </h5>
                <div class="d-flex">
                  <div class="me-3">
                    <label class="form-label">Adults</label>
                    <input type="number" min="1" id="adults" value="<?php echo $adult_default ?>" oninput="guests_filter()" class="form-control shadow-none">                 
                  </div>
                  <div>
                    <label class="form-label">Children</label>
                    <input type="number" min="1" id="children" value="<?php echo $children_default ?>" oninput="guests_filter()" class="form-control shadow-none">                 
                  </div>
                </div>
              </div>

              <!-- Currency -->
              <div class="border bg-light p-3 rounded mb-3">
                <h5 class="d-flex align-items-center justify-content-between mb-3" style="font-size: 18px;">
                  <span class="d-flex align-items-center gap-2">
                    <i class="bi bi-currency-exchange text-primary"></i>
                    Currency
                  </span>
                </h5>
                <input type="hidden" id="currency-selector" value="NPR">
                <div class="dropdown currency-filter-dropdown w-100">
                  <button type="button" class="btn btn-light border shadow-sm dropdown-toggle w-100 d-flex align-items-center justify-content-between rounded-3 py-2 px-3 fw-semibold text-dark" data-bs-toggle="dropdown" data-bs-display="static" aria-expanded="false" id="currencyDropdownBtn">
                    <span class="d-flex align-items-center gap-2 min-w-0 text-start">
                      <i class="bi bi-wallet2 text-primary flex-shrink-0"></i>
                      <span id="currency-dropdown-label" class="text-truncate">NPR · Nepalese Rupee</span>
                    </span>
                    <i class="bi bi-chevron-expand text-muted flex-shrink-0 ms-2 small"></i>
                  </button>
                  <ul class="dropdown-menu shadow border-0 rounded-3 py-2 w-100 mt-1" aria-labelledby="currencyDropdownBtn">
                    <li>
                      <button type="button" class="dropdown-item d-flex align-items-center justify-content-between active" data-currency-value="NPR" data-currency-label="NPR · Nepalese Rupee">
                        <span><span class="currency-code text-primary">NPR</span> Nepalese Rupee</span>
                        <i class="bi bi-check2 text-primary currency-check" aria-hidden="true"></i>
                      </button>
                    </li>
                    <li>
                      <button type="button" class="dropdown-item d-flex align-items-center justify-content-between" data-currency-value="USD" data-currency-label="USD · US Dollar">
                        <span><span class="currency-code text-primary">USD</span> US Dollar</span>
                        <i class="bi bi-check2 text-primary d-none currency-check" aria-hidden="true"></i>
                      </button>
                    </li>
                    <li>
                      <button type="button" class="dropdown-item d-flex align-items-center justify-content-between" data-currency-value="INR" data-currency-label="INR · Indian Rupee">
                        <span><span class="currency-code text-primary">INR</span> Indian Rupee</span>
                        <i class="bi bi-check2 text-primary d-none currency-check" aria-hidden="true"></i>
                      </button>
                    </li>
                    <li>
                      <button type="button" class="dropdown-item d-flex align-items-center justify-content-between" data-currency-value="EUR" data-currency-label="EUR · Euro">
                        <span><span class="currency-code text-primary">EUR</span> Euro</span>
                        <i class="bi bi-check2 text-primary d-none currency-check" aria-hidden="true"></i>
                      </button>
                    </li>
                  </ul>
                </div>
                <p class="form-text small text-muted mb-0 mt-2">Prices reload in the currency you pick.</p>
              </div>

            </div>
          </div>
        </nav>
      </div>

      <div class="col-lg-9 col-md-12 px-4" id="rooms-data">
        <!-- Room details will appear here -->
      </div>

    </div>
  </div>

  <script>
    let rooms_data = document.getElementById('rooms-data');
    let checkin = document.getElementById('checkin');
    let checkout = document.getElementById('checkout');
    let chk_avail_btn = document.getElementById('chk_avail_btn');

    let adults = document.getElementById('adults');
    let children = document.getElementById('children');
    let guests_btn = document.getElementById('guests_btn');
    
    let facilities_btn = document.getElementById('facilities_btn');

    // Currency (hidden value + Bootstrap dropdown)
    let currencySelector = document.getElementById('currency-selector');
    let currencyLabel = document.getElementById('currency-dropdown-label');

    document.querySelectorAll('.currency-filter-dropdown [data-currency-value]').forEach(function (item) {
      item.addEventListener('click', function () {
        var v = this.getAttribute('data-currency-value');
        var label = this.getAttribute('data-currency-label');
        if (!v || !currencySelector) return;
        currencySelector.value = v;
        if (currencyLabel && label) currencyLabel.textContent = label;
        document.querySelectorAll('.currency-filter-dropdown [data-currency-value]').forEach(function (btn) {
          btn.classList.toggle('active', btn === item);
          var ch = btn.querySelector('.currency-check');
          if (ch) ch.classList.toggle('d-none', btn !== item);
        });
        fetch_rooms();
      });
    });
    
    // Fetch Room Data
    function fetch_rooms() {
        let chk_avail = {
            checkin: checkin.value,
            checkout: checkout.value
        };

        let selectedCurrency = currencySelector.value;
        let facility_list = {"facilities":[]};

        let get_facilities = document.querySelectorAll('[name="facilities"]:checked');
        if(get_facilities.length > 0) {
            get_facilities.forEach((facility) => {
                facility_list.facilities.push(facility.value);
            });
            facilities_btn.classList.remove('d-none');
        } else {
            facilities_btn.classList.add('d-none');
        }

        facility_list = JSON.stringify(facility_list);

        let xhr = new XMLHttpRequest();
        xhr.open("POST", "ajax/rooms.php", true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

        xhr.onprogress = function() {
            rooms_data.innerHTML = `<div class="spinner-border text-info mb-3 d-block mx-auto" id="loader" role="status">
              <span class="visually-hidden">Loading...</span>
            </div>`;
        }

        xhr.onload = function() {
            rooms_data.innerHTML = this.responseText;
        }

        let params = new URLSearchParams();
        params.append('fetch_rooms', '1');
        params.append('checkin', chk_avail.checkin);
        params.append('checkout', chk_avail.checkout);
        params.append('adults', adults.value);
        params.append('children', children.value);
        params.append('currency', selectedCurrency);
        params.append('facility_list', facility_list);

        xhr.send(params.toString());
    }

    // Handle Check Availability Filter
    function chk_avail_filter() {
        if(checkin.value != '' && checkout.value != '') {
            fetch_rooms();
            chk_avail_btn.classList.remove('d-none');
        }
    }

    // Handle Guests Filter
    function guests_filter() {
        if(adults.value > 0 || children.value > 0) {
            fetch_rooms();
            guests_btn.classList.remove('d-none');
        }
    }

    // Handle Clear Filters
    function chk_avail_clear() {
        checkin.value = '';
        checkout.value = '';
        chk_avail_btn.classList.add('d-none');
        fetch_rooms();
    }

    function guests_clear() {
        adults.value = '';
        children.value = '';
        guests_btn.classList.add('d-none');
        fetch_rooms();
    }

    function facilities_clear() {
        let get_facilities = document.querySelectorAll('[name="facilities"]:checked');
        get_facilities.forEach((facility) => {
            facility.checked = false;
        });
        facilities_btn.classList.add('d-none');
        fetch_rooms();
    }

    // Initialize on page load
    window.onload = function() {
        fetch_rooms();
    };

    document.addEventListener('click', function(e) {
        if (e.target && e.target.classList.contains('book-now-btn')) {
            let login = e.target.getAttribute('data-login');
            let room_id = e.target.getAttribute('data-room-id');
            let selectedCurrency = document.getElementById('currency-selector').value;
            
            if (login === '0' || !login) {
                let loginModalEl = document.getElementById('loginModal');
                if (loginModalEl && typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                    let loginModal = bootstrap.Modal.getOrCreateInstance(loginModalEl);
                    loginModal.show();
                } else {
                    alert('error', 'Please login to book room!');
                }
            } else {
                window.location.href = 'confirm_booking.php?id=' + room_id + '&currency=' + selectedCurrency;
            }
        }
    });
  </script>

  <?php require('inc/footer.php'); ?>

</body>
</html>
<?php die(); ?>
<!--
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BookEase - Rooms</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <style>
        :root {
            --primary-color: #2c5aa0;
            --secondary-color: #d4af37;
            --accent-color: #e31837;
            --light-bg: #f8f9fa;
            --dark-text: #333;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: var(--dark-text);
            background-color: #f5f7fa;
        }
        
        /* Navigation */
        .navbar {
            background-color: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 15px 0;
        }
        
        .navbar-brand {
            font-weight: 700;
            color: var(--primary-color);
            font-size: 1.8rem;
            position: relative;
            transition: transform .25s ease, color .25s ease;
        }

        .navbar-brand::after{
            content: "";
            position: absolute;
            left: 0;
            bottom: -3px;
            width: 100%;
            height: 2px;
            background: linear-gradient(90deg, var(--primary-color), #5E5DF0);
            transform: scaleX(0);
            transform-origin: left;
            transition: transform .25s ease;
        }

        .navbar-brand:hover,
        .navbar-brand:focus{
            color: #5E5DF0;
            transform: translateY(-1px);
        }

        .navbar-brand:hover::after,
        .navbar-brand:focus::after{
            transform: scaleX(1);
        }
        
        .nav-link {
            font-weight: 500;
            color: var(--dark-text);
            padding: 8px 15px !important;
        }
        
        .nav-link:hover, .nav-link.active {
            color: var(--primary-color);
        }
        
        .btn-login {
            background-color: var(--primary-color);
            color: white;
            padding: 8px 20px;
            border-radius: 5px;
            border: none;
        }
        
        .btn-login:hover {
            background-color: #1e3d6f;
            color: white;
        }
        
        /* Page Header */
        .page-header {
            background: linear-gradient(rgba(44, 90, 160, 0.9), rgba(44, 90, 160, 0.9)), url('https://images.unsplash.com/photo-1631049307264-da0ec9d70304?ixlib=rb-4.0.3&auto=format&fit=crop&w=1470&q=80');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 80px 0;
            margin-bottom: 50px;
            text-align: center;
        }
        
        .page-header h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 20px;
        }
        
        /* Main Content */
        .main-content {
            padding: 0 0 50px 0;
        }
        
        /* Filters Section */
        .filters-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 3px 15px rgba(0,0,0,0.08);
            padding: 25px;
            margin-bottom: 30px;
            position: sticky;
            top: 20px;
        }
        
        .filters-card h3 {
            color: var(--primary-color);
            font-size: 1.4rem;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #eee;
        }
        
        .filter-section {
            margin-bottom: 30px;
        }
        
        .filter-section h4 {
            font-size: 1.1rem;
            color: #555;
            margin-bottom: 15px;
        }
        
        .form-control, .form-select {
            border-radius: 5px;
            padding: 10px 15px;
            border: 1px solid #ddd;
            margin-bottom: 15px;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(44, 90, 160, 0.25);
        }
        
        .form-check-label {
            cursor: pointer;
        }
        
        .form-check-input:checked {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-apply {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 5px;
            font-weight: 600;
            width: 100%;
            transition: all 0.3s;
        }
        
        .btn-apply:hover {
            background-color: #1e3d6f;
        }
        
        /* Room Cards */
        .room-card {
            position: relative;
            background: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 18px 40px rgba(15, 23, 42, 0.08);
            margin-bottom: 30px;
            transition: transform 0.35s ease, box-shadow 0.35s ease, border-color 0.35s ease;
            transform: translateY(0) scale(1);
            animation: room-card-enter 0.85s ease both;
            will-change: transform, box-shadow, opacity;
            height: 100%;
        }
        
        .room-card:hover {
            transform: translateY(-12px) scale(1.02);
            box-shadow: 0 28px 68px rgba(15, 23, 42, 0.18);
            border-color: rgba(52, 144, 220, 0.12);
        }
        
        .list-group .room-card:nth-child(1),
        .row .room-card:nth-child(1) {
            animation-delay: 0.1s;
        }
        
        .list-group .room-card:nth-child(2),
        .row .room-card:nth-child(2) {
            animation-delay: 0.18s;
        }
        
        .list-group .room-card:nth-child(3),
        .row .room-card:nth-child(3) {
            animation-delay: 0.26s;
        }
        
        @keyframes room-card-enter {
            0% {
                opacity: 0;
                transform: translateY(20px) scale(0.98);
            }
            100% {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }
        
        .room-image {
            height: 200px;
            background-size: cover;
            background-position: center;
        }
        
        .room-details {
            padding: 25px;
        }
        
        .room-title {
            color: var(--primary-color);
            font-size: 1.4rem;
            font-weight: 600;
            margin-bottom: 15px;
        }
        
        .room-price {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--secondary-color);
            margin-bottom: 20px;
        }
        
        .room-features, .room-facilities, .room-guests {
            margin-bottom: 20px;
        }
        
        .room-features h5, .room-facilities h5, .room-guests h5 {
            font-size: 1rem;
            color: #555;
            margin-bottom: 10px;
            font-weight: 600;
        }
        
        .feature-badge, .facility-badge, .guest-badge {
            display: inline-block;
            background: #f0f4f8;
            padding: 5px 12px;
            margin: 3px;
            border-radius: 20px;
            font-size: 0.85rem;
            color: #555;
        }
        
        .facility-badge {
            background: #e8f4ff;
            color: var(--primary-color);
        }
        
        .guest-badge {
            background: #fff8e8;
            color: #d4af37;
        }
        
        .room-actions {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }
        
        /* Button 34 (match homepage) */
        .button-34 {
            background: #5E5DF0;
            border-radius: 999px;
            box-shadow: #5E5DF0 0 10px 20px -10px;
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

        .button-34:hover {
            transform: translateY(-1px);
            filter: brightness(1.04);
            box-shadow: #5E5DF0 0 14px 26px -12px;
        }

        .button-34:focus {
            outline: 0 solid transparent;
            box-shadow: 0 0 0 .2rem rgba(94, 93, 240, 0.35);
        }

        .button-34.button-34-plain {
            background: transparent;
            color: #5E5DF0;
            border: 2px solid #5E5DF0;
            box-shadow: rgba(94, 93, 240, 0.25) 0 10px 20px -14px;
        }

        .button-34.button-34-plain:hover {
            background: rgba(94, 93, 240, 0.08);
            box-shadow: rgba(94, 93, 240, 0.25) 0 14px 26px -16px;
        }

        .room-actions .button-34{
            flex: 1 1 0;
            min-height: 40px;
            width: 100%;
            white-space: nowrap;
        }

        .btn-book {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 10px 25px;
            border-radius: 5px;
            font-weight: 600;
            flex: 1;
            transition: all 0.3s;
        }
        
        .btn-book:hover {
            background-color: #1e3d6f;
            color: white;
        }
        
        .btn-details {
            background-color: transparent;
            color: var(--primary-color);
            border: 1px solid var(--primary-color);
            padding: 10px 25px;
            border-radius: 5px;
            font-weight: 600;
            flex: 1;
            transition: all 0.3s;
        }
        
        .btn-details:hover {
            background-color: var(--primary-color);
            color: white;
        }
        
        /* Footer */
        .footer {
            background-color: #222;
            color: white;
            padding: 50px 0 20px;
            margin-top: 50px;
        }
        
        .footer h5 {
            color: var(--secondary-color);
            margin-bottom: 20px;
        }
        
        .social-links a {
            color: white;
            font-size: 1.2rem;
            margin-right: 15px;
        }
        
        .copyright {
            border-top: 1px solid #444;
            padding-top: 20px;
            margin-top: 30px;
            text-align: center;
            color: #aaa;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .page-header {
                padding: 50px 0;
            }
            
            .page-header h1 {
                font-size: 2rem;
            }
            
            .filters-card {
                position: static;
                margin-bottom: 30px;
            }
            
            .room-actions {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container">
            <a class="navbar-brand" href="homepage.php">BookEase</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="homepage.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="rooms.php">Rooms</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="facilities.php">Facilities</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="contact.php">Contact us</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="about.php">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="compare.php">Compare Room</a>
                    </li>
                    <li class="nav-item ms-2">
                        <a class="btn btn-login" href="login.php">Login</a>
                    </li>
                    <li class="nav-item ms-2">
                        <a class="btn btn-outline-primary" href="register.php">Register</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Page Header -->
    <section class="page-header">
        <div class="container">
            <h1>OUR ROOMS</h1>
            <p class="lead">Choose from our selection of comfortable and well-equipped rooms</p>
        </div>
    </section>

    <!-- Main Content -->
    <div class="container main-content">
        <div class="row">
            <!-- Filters Column -->
            <div class="col-lg-3">
                <div class="filters-card">
                    <h3>FILTERS</h3>
                    
                    <!-- Check Availability -->
                    <div class="filter-section">
                        <h4>CHECK AVAILABILITY</h4>
                        <div class="mb-3">
                            <label for="checkin" class="form-label">Check-in</label>
                            <input type="date" class="form-control" id="checkin" name="checkin">
                        </div>
                        <div class="mb-3">
                            <label for="checkout" class="form-label">Check-out</label>
                            <input type="date" class="form-control" id="checkout" name="checkout">
                        </div>
                    </div>
                    
                    <!-- Facilities -->
                    <div class="filter-section">
                        <h4>FACILITIES</h4>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="ac" checked>
                            <label class="form-check-label" for="ac">
                                Air Conditioner
                            </label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="wifi" checked>
                            <label class="form-check-label" for="wifi">
                                WiFi
                            </label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="tv" checked>
                            <label class="form-check-label" for="tv">
                                Television(TV)
                            </label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="spa">
                            <label class="form-check-label" for="spa">
                                SPA
                            </label>
                        </div>
                    </div>
                    
                    <!-- Price Range -->
                    <div class="filter-section">
                        <h4>PRICE RANGE</h4>
                        <div class="mb-3">
                            <label for="priceRange" class="form-label">Max Price: <span id="priceValue">5000</span> NPR</label>
                            <input type="range" class="form-range" id="priceRange" min="1000" max="5000" step="500" value="5000">
                        </div>
                    </div>
                    
                    <!-- Guests -->
                    <div class="filter-section">
                        <h4>GUESTS</h4>
                        <div class="mb-3">
                            <label for="adults" class="form-label">Adults</label>
                            <select class="form-select" id="adults">
                                <option value="any">Any</option>
                                <option value="1">1 Adult</option>
                                <option value="2" selected>2 Adults</option>
                                <option value="3">3 Adults</option>
                                <option value="4">4+ Adults</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="children" class="form-label">Children</label>
                            <select class="form-select" id="children">
                                <option value="any">Any</option>
                                <option value="0">0 Children</option>
                                <option value="1">1 Child</option>
                                <option value="2" selected>2 Children</option>
                            </select>
                        </div>
                    </div>
                    
                    <button class="btn btn-apply" id="applyFilters">Apply Filters</button>
                </div>
            </div>
            
            <!-- Rooms Column -->
            <div class="col-lg-9">
                <div class="row" id="roomList">
                    <!-- Simple Room -->
                    <div class="col-md-6 col-lg-4 room-item" data-ac="false" data-wifi="true" data-tv="true" data-price="1000" data-adults="2" data-children="1">
                        <div class="room-card">
                            <div class="room-image" style="background-image: url('https://images.unsplash.com/photo-1586023492125-27b2c045efd7?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80');"></div>
                            <div class="room-details">
                                <h3 class="room-title">Simple</h3>
                                <div class="room-price">NPR 1,000.00 per night</div>
                                
                                <div class="room-features">
                                    <h5>Features</h5>
                                    <div>
                                        <span class="feature-badge">bedroom</span>
                                        <span class="feature-badge">balcony</span>
                                        <span class="feature-badge">kitchen</span>
                                        <span class="feature-badge">sofa</span>
                                    </div>
                                </div>
                                
                                <div class="room-facilities">
                                    <h5>Facilities</h5>
                                    <div>
                                        <span class="facility-badge">WIFI</span>
                                        <span class="facility-badge">Television(TV)</span>
                                        <span class="facility-badge">Heater</span>
                                        <span class="facility-badge">Geyser</span>
                                    </div>
                                </div>
                                
                                <div class="room-guests">
                                    <h5>Guests</h5>
                                    <div>
                                        <span class="guest-badge">2 Adults</span>
                                        <span class="guest-badge">1 Child</span>
                                    </div>
                                </div>
                                
                                <div class="room-actions">
                                    <button class="button-34" role="button" onclick="bookRoom('Simple')">Book Now</button>
                                    <button class="button-34 button-34-plain" role="button" onclick="showDetails('Simple')">More details</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Better Room -->
                    <div class="col-md-6 col-lg-4 room-item" data-ac="true" data-wifi="true" data-tv="true" data-price="2000" data-adults="3" data-children="2">
                        <div class="room-card">
                            <div class="room-image" style="background-image: url('https://images.unsplash.com/photo-1611892440504-42a792e24d32?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80');"></div>
                            <div class="room-details">
                                <h3 class="room-title">Better</h3>
                                <div class="room-price">NPR 2,000.00 per night</div>
                                
                                <div class="room-features">
                                    <h5>Features</h5>
                                    <div>
                                        <span class="feature-badge">bedroom</span>
                                        <span class="feature-badge">balcony</span>
                                        <span class="feature-badge">sofa</span>
                                    </div>
                                </div>
                                
                                <div class="room-facilities">
                                    <h5>Facilities</h5>
                                    <div>
                                        <span class="facility-badge">Air Conditioner</span>
                                        <span class="facility-badge">WiFi</span>
                                        <span class="facility-badge">Television(TV)</span>
                                    </div>
                                </div>
                                
                                <div class="room-guests">
                                    <h5>Guests</h5>
                                    <div>
                                        <span class="guest-badge">3 Adults</span>
                                        <span class="guest-badge">2 Children</span>
                                    </div>
                                </div>
                                
                                <div class="room-actions">
                                    <button class="button-34" role="button" onclick="bookRoom('Better')">Book Now</button>
                                    <button class="button-34 button-34-plain" role="button" onclick="showDetails('Better')">More details</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Standard Room (Additional) -->
                    <div class="col-md-6 col-lg-4 room-item" data-ac="true" data-wifi="true" data-tv="true" data-spa="true" data-price="3000" data-adults="2" data-children="2">
                        <div class="room-card">
                            <div class="room-image" style="background-image: url('https://images.unsplash.com/photo-1615873968403-89e068629265?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80');"></div>
                            <div class="room-details">
                                <h3 class="room-title">Standard</h3>
                                <div class="room-price">NPR 3,000.00 per night</div>
                                
                                <div class="room-features">
                                    <h5>Features</h5>
                                    <div>
                                        <span class="feature-badge">bedroom</span>
                                        <span class="feature-badge">balcony</span>
                                        <span class="feature-badge">sofa</span>
                                        <span class="feature-badge">dressing table</span>
                                    </div>
                                </div>
                                
                                <div class="room-facilities">
                                    <h5>Facilities</h5>
                                    <div>
                                        <span class="facility-badge">Air Conditioner</span>
                                        <span class="facility-badge">WiFi</span>
                                        <span class="facility-badge">Television(TV)</span>
                                        <span class="facility-badge">SPA Access</span>
                                        <span class="facility-badge">Mini Bar</span>
                                    </div>
                                </div>
                                
                                <div class="room-guests">
                                    <h5>Guests</h5>
                                    <div>
                                        <span class="guest-badge">2 Adults</span>
                                        <span class="guest-badge">2 Children</span>
                                    </div>
                                </div>
                                
                                <div class="room-actions">
                                    <button class="button-34" role="button" onclick="bookRoom('Standard')">Book Now</button>
                                    <button class="button-34 button-34-plain" role="button" onclick="showDetails('Standard')">More details</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- No Results Message -->
                <div id="noResults" class="text-center" style="display: none;">
                    <div class="alert alert-info">
                        <h4>No rooms found matching your criteria</h4>
                        <p>Try adjusting your filters to see more options.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h5>BookEase</h5>
                    <p>Experience luxury and comfort in the heart of Nepal. Your perfect stay awaits.</p>
                </div>
                <div class="col-md-4">
                    <h5>Quick Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="homepage.php" class="text-light text-decoration-none">Home</a></li>
                        <li><a href="rooms.php" class="text-light text-decoration-none">Rooms</a></li>
                        <li><a href="facilities.php" class="text-light text-decoration-none">Facilities</a></li>
                        <li><a href="contact.php" class="text-light text-decoration-none">Contact Us</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h5>Connect With Us</h5>
                    <div class="social-links">
                        <a href="#"><i class="bi bi-facebook"></i></a>
                        <a href="#"><i class="bi bi-twitter"></i></a>
                        <a href="#"><i class="bi bi-instagram"></i></a>
                        <a href="#"><i class="bi bi-whatsapp"></i></a>
                    </div>
                </div>
            </div>
            <div class="copyright">
                <p>&copy; 2024 BookEase. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Set min date for check-in to today
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('checkin').min = today;
        
        // Update checkout min date based on check-in
        document.getElementById('checkin').addEventListener('change', function() {
            const checkout = document.getElementById('checkout');
            checkout.min = this.value;
            
            // If checkout is before new checkin, reset it
            if (checkout.value && checkout.value < this.value) {
                checkout.value = this.value;
            }
        });
        
        // Price range slider
        const priceRange = document.getElementById('priceRange');
        const priceValue = document.getElementById('priceValue');
        
        priceRange.addEventListener('input', function() {
            priceValue.textContent = this.value;
        });
        
        // Filter functionality
        document.getElementById('applyFilters').addEventListener('click', function() {
            applyFilters();
        });
        
        function applyFilters() {
            const acChecked = document.getElementById('ac').checked;
            const wifiChecked = document.getElementById('wifi').checked;
            const tvChecked = document.getElementById('tv').checked;
            const spaChecked = document.getElementById('spa').checked;
            const maxPrice = parseInt(priceRange.value);
            const adults = document.getElementById('adults').value;
            const children = document.getElementById('children').value;
            
            const roomItems = document.querySelectorAll('.room-item');
            let visibleCount = 0;
            
            roomItems.forEach(item => {
                const hasAC = item.getAttribute('data-ac') === 'true';
                const hasWifi = item.getAttribute('data-wifi') === 'true';
                const hasTV = item.getAttribute('data-tv') === 'true';
                const hasSpa = item.getAttribute('data-spa') === 'true';
                const roomPrice = parseInt(item.getAttribute('data-price'));
                const roomAdults = item.getAttribute('data-adults');
                const roomChildren = item.getAttribute('data-children');
                
                let show = true;
                
                // Check facilities
                if (acChecked && !hasAC) show = false;
                if (wifiChecked && !hasWifi) show = false;
                if (tvChecked && !hasTV) show = false;
                if (spaChecked && !hasSpa) show = false;
                
                // Check price
                if (roomPrice > maxPrice) show = false;
                
                // Check guests
                if (adults !== 'any') {
                    if (parseInt(adults) > parseInt(roomAdults)) show = false;
                }
                
                if (children !== 'any') {
                    if (parseInt(children) > parseInt(roomChildren)) show = false;
                }
                
                if (show) {
                    item.style.display = 'block';
                    visibleCount++;
                } else {
                    item.style.display = 'none';
                }
            });
            
            // Show/hide no results message
            const noResults = document.getElementById('noResults');
            if (visibleCount === 0) {
                noResults.style.display = 'block';
            } else {
                noResults.style.display = 'none';
            }
        }
        
        // Room booking function
        function bookRoom(roomType) {
            const checkin = document.getElementById('checkin').value;
            const checkout = document.getElementById('checkout').value;
            
            if (!checkin || !checkout) {
                alert('Please select check-in and check-out dates first');
                return;
            }
            
            // In a real application, you would redirect to booking page or show modal
            window.location.href = `booking.php?room=${encodeURIComponent(roomType)}&checkin=${checkin}&checkout=${checkout}`;
        }
        
        // Show room details
        function showDetails(roomType) {
            // In a real application, you would show a modal or redirect to room details page
            alert(`Showing details for ${roomType} room. This would open a detailed view or modal.`);
        }
        
        // Initialize filters on page load
        document.addEventListener('DOMContentLoaded', function() {
            applyFilters();
        });
    </script>
</body>
</html>
-->
