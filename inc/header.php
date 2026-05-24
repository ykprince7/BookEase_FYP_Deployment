<nav id="nav-bar" class="navbar navbar-expand-lg navbar-light bg-white px-lg-3 py-lg-2 shadow-sm sticky-top">
  <div class="container-fluid">
    <a class="navbar-brand me-5 fw-bold fs-3 h-font site-wordmark" href="index.php"><?php echo preg_replace('/Ease$/', '<span class="wordmark-accent">Ease</span>', $settings_r['site_title']); ?></a>
    <button class="navbar-toggler shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link me-2" href="index.php">Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link me-2" href="rooms.php">Rooms</a>
        </li>
        <li class="nav-item">
          <a class="nav-link me-2" href="facilities.php">Facilities</a>
        </li>
        <li class="nav-item">
          <a class="nav-link me-2" href="contact.php">Contact us</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="about.php">About</a>
        </li>
        <li class="nav-item">
    <button type="button" class="nav-link" data-bs-toggle="modal" data-bs-target="#compareRoomsModal" style="border: none; background: transparent;">Compare Room</button>
</li>
      </ul>
      <div class="d-flex">
        <?php
          if(isset($_SESSION['login']) && $_SESSION['login'] == true) {
            $path = USERS_IMG_PATH;
            echo<<<data
              <div class="btn-group">
                <button type="button" class="btn btn-outline-primary shadow-none dropdown-toggle profile-dropdown-toggle" data-bs-toggle="dropdown" data-bs-display="static" aria-expanded="false">
                  <img src="$path$_SESSION[uPic]" onerror="this.onerror=null;this.src='{$path}default.svg';" style="width: 25px; height: 25px;" class="me-1 rounded-circle">
                  $_SESSION[uName]
                </button>
                <ul class="dropdown-menu dropdown-menu-lg-end">
                  <li><a class="dropdown-item" href="profile.php">Profile</a></li>
                  <li><a class="dropdown-item" href="bookings.php">Bookings</a></li>
                  <li><a class="dropdown-item" href="loyalty.php">Loyalty Program</a></li>
                  <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                </ul>
              </div>
            data;
          } else {
            echo<<<data
              <button type="button" class="btn btn-outline-primary shadow-none me-lg-3 me-2" data-bs-toggle="modal" data-bs-target="#loginModal">
                Login
              </button>
              <button type="button" class="btn btn-outline-primary shadow-none" data-bs-toggle="modal" data-bs-target="#registerModal">
                Register
              </button>
            data;
          }
        ?>
      </div>
    </div>
  </div>
</nav>
<div class="modal fade auth-modal" id="loginModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content border-0 shadow-lg">
      <form id="login-form">
        <div class="modal-header border-0 pb-2">
          <h5 class="modal-title d-flex align-items-center fw-bold">
            <span class="auth-modal-icon me-2"><i class="bi bi-person-circle"></i></span> Welcome back
          </h5>
          <button type="reset" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body pt-1">
          <p class="text-muted small mb-3">Login to continue booking, rewards, and account features.</p>
          <div class="mb-3">
            <label class="form-label fw-semibold">Email / Mobile</label>
            <input type="text" name="email_mob" required class="form-control shadow-none" placeholder="Enter email or mobile">
          </div>
          <div class="mb-4">
            <label class="form-label fw-semibold">Password</label>
            <input type="password" name="pass" required class="form-control shadow-none" placeholder="Enter password">
          </div>
          <div class="d-flex align-items-center justify-content-between mb-3">
            <button type="submit" class="btn btn-primary shadow-none px-4">Login</button>
            <button type="button" class="btn text-secondary text-decoration-none shadow-none p-0" data-bs-toggle="modal" data-bs-target="#forgotModal" data-bs-dismiss="modal">
              Forgot Password?
            </button>
          </div>
          <div class="auth-divider my-3"><span>or continue with</span></div>
          <div class="text-center">
            <div id="google-login-btn"></div>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="modal fade auth-modal" id="registerModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content border-0 shadow-lg">
      <form id="register-form">
        <div class="modal-header border-0 pb-2">
          <h5 class="modal-title d-flex align-items-center fw-bold">
            <span class="auth-modal-icon me-2"><i class="bi bi-person-lines-fill"></i></span> Create account
          </h5>
          <button type="reset" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body pt-1">
          <p class="text-muted small mb-3">Join BookEase to manage bookings, rewards, and profile details.</p>
          <div class="container-fluid">
            <div class="row">
              <div class="col-md-6 mb-3">
                <label class="form-label fw-semibold">Name</label>
                <input name="name" type="text" class="form-control shadow-none" placeholder="Full name" required>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label fw-semibold">Email</label>
                <input name="email" type="email" class="form-control shadow-none" placeholder="you@example.com" required>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label fw-semibold">Phone Number</label>
                <input name="phonenum" type="text" maxlength="10" pattern="[0-9]{10}" class="form-control shadow-none" placeholder="10-digit phone" required>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label fw-semibold">Picture <span class="text-muted fw-normal">(optional)</span></label>
                <input name="profile" type="file" accept=".jpg,.jpeg,.png,.webp" class="form-control shadow-none">
              </div>
              <div class="col-md-12 mb-3">
                <label class="form-label fw-semibold">Address</label>
                <textarea name="address" class="form-control shadow-none" rows="1" placeholder="Your address" required></textarea>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label fw-semibold">Pincode</label>
                <input name="pincode" type="number" class="form-control shadow-none" placeholder="Postal code" required>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label fw-semibold">Date of birth</label>
                <input name="dob" type="date" class="form-control shadow-none" required>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label fw-semibold">Password</label>
                <input name="pass" type="password" class="form-control shadow-none" placeholder="Minimum 6 characters" required>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label fw-semibold">Confirm Password</label>
                <input name="cpass" type="password" class="form-control shadow-none" placeholder="Re-enter password" required>
              </div>
            </div>
          </div>
          <div class="text-center mt-2">
            <button type="submit" class="btn btn-primary shadow-none px-4">Create account</button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="modal fade auth-modal" id="otpModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="otpModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content border-0 shadow-lg">
      <form id="otp-form">
        <div class="modal-header border-0 pb-2">
          <h5 class="modal-title d-flex align-items-center fw-bold">
            <span class="auth-modal-icon me-2"><i class="bi bi-shield-check"></i></span> Verify OTP
          </h5>
          <button type="reset" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body pt-1">
          <p class="text-muted mb-3">Enter the 6-digit OTP sent to your email.</p>
          <input type="hidden" name="email">
          <div class="mb-3">
            <label class="form-label fw-semibold">OTP</label>
            <input name="otp" type="text" inputmode="numeric" maxlength="6" pattern="[0-9]{6}" class="form-control shadow-none" placeholder="Enter 6-digit OTP" required>
          </div>
          <div class="text-end">
            <button type="button" class="btn shadow-none me-2" data-bs-dismiss="modal">CANCEL</button>
            <button type="submit" class="btn btn-primary shadow-none">VERIFY</button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="modal fade auth-modal" id="forgotModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content border-0 shadow-lg">
      <form id="forgot-form">
        <div class="modal-header border-0 pb-2">
          <h5 class="modal-title d-flex align-items-center fw-bold">
            <span class="auth-modal-icon me-2"><i class="bi bi-envelope-paper-heart"></i></span> Forgot Password
          </h5>
          <button type="reset" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body pt-1">
          <span class="badge rounded-pill bg-light text-dark mb-3 text-wrap lh-base">
            Note: A 6-digit OTP will be sent to your email to reset your password.
          </span>
          <div class="mb-4">
            <label class="form-label fw-semibold">Email</label>
            <input type="email" name="email" required class="form-control shadow-none" placeholder="you@example.com">
          </div>
          <div class="mb-2 text-end">
            <button type="button" class="btn shadow-none p-0 me-2" data-bs-toggle="modal" data-bs-target="#loginModal" data-bs-dismiss="modal">
              CANCEL
            </button>
            <button type="submit" class="btn btn-primary shadow-none">SEND OTP</button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="modal fade auth-modal" id="forgotResetModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="forgotResetModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content border-0 shadow-lg">
      <form id="forgot-reset-form">
        <div class="modal-header border-0 pb-2">
          <h5 class="modal-title d-flex align-items-center fw-bold">
            <span class="auth-modal-icon me-2"><i class="bi bi-key-fill"></i></span> Reset Password
          </h5>
          <button type="reset" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body pt-1">
          <input type="hidden" name="email">
          <div class="mb-3">
            <label class="form-label fw-semibold">OTP</label>
            <input type="text" name="otp" inputmode="numeric" maxlength="6" pattern="[0-9]{6}" class="form-control shadow-none" placeholder="Enter 6-digit OTP" required>
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold">New Password</label>
            <input type="password" name="pass" class="form-control shadow-none" minlength="6" placeholder="Minimum 6 characters" required>
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold">Confirm Password</label>
            <input type="password" name="cpass" class="form-control shadow-none" minlength="6" placeholder="Re-enter new password" required>
          </div>
          <div class="text-end">
            <button type="button" class="btn shadow-none me-2" data-bs-dismiss="modal">CANCEL</button>
            <button type="submit" class="btn btn-primary shadow-none">RESET PASSWORD</button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="modal fade" id="compareRoomsModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="compareRoomsModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-scrollable">
    <div class="modal-content border-0 shadow">
      <div class="modal-header border-0 pb-0">
        <h5 class="modal-title d-flex align-items-center fw-bold" id="compareRoomsModalLabel">
          <span class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center me-2" style="width:40px;height:40px;"><i class="bi bi-columns-gap"></i></span>
          Compare rooms
        </h5>
        <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body pt-2">
        <div class="container-fluid px-0" id="compare-rooms-list">
          <div id="room-selection-area"></div>
          <div id="comparison-area" style="display:none;"></div>
        </div>
        <p id="compare-rooms-error" class="text-danger small mt-2 mb-0" style="display:none;"></p>
      </div>
      <div class="modal-footer border-0 pt-0">
        <button type="button" class="btn btn-light border shadow-none" data-bs-dismiss="modal">Close</button>
        <button type="button" id="compare-rooms-btn" class="btn btn-primary shadow-none px-4" onclick="compareSelectedRooms()" disabled>Compare selected</button>
      </div>
    </div>
  </div>
</div>

<?php if($show_promo_banner && !empty($promo_banner_data)): ?>
<?php
  $b_title        = htmlspecialchars($promo_banner_data['title'],       ENT_QUOTES, 'UTF-8');
  $b_sub          = htmlspecialchars($promo_banner_data['subtitle'],    ENT_QUOTES, 'UTF-8');
  $b_desc         = htmlspecialchars($promo_banner_data['description'], ENT_QUOTES, 'UTF-8');
  $b_pts          = (int)$promo_banner_data['loyalty_points'];
  $b_can_claim    = !empty($banner_user_can_claim);
  $b_not_eligible = !empty($banner_user_not_eligible);
  $b_login_state  = $b_can_claim ? '1' : ($b_not_eligible ? '2' : '0');

  // Extended fields — with sensible fallbacks
  $b_offer_text  = !empty($promo_banner_data['offer_text'])  ? htmlspecialchars($promo_banner_data['offer_text'],  ENT_QUOTES, 'UTF-8') : $b_pts;
  $b_offer_label = !empty($promo_banner_data['offer_label']) ? htmlspecialchars($promo_banner_data['offer_label'], ENT_QUOTES, 'UTF-8') : 'LOYALTY POINTS';
  $b_badge_label = !empty($promo_banner_data['badge_label']) ? htmlspecialchars($promo_banner_data['badge_label'], ENT_QUOTES, 'UTF-8') : 'NEW MEMBER EXCLUSIVE';
  $b_cta_url     = !empty($promo_banner_data['cta_url'])     ? htmlspecialchars($promo_banner_data['cta_url'],     ENT_QUOTES, 'UTF-8') : '';
  $b_has_cta_url = $b_cta_url !== '';

  // CTA text: custom → fallback per state
  if (!empty($promo_banner_data['cta_text'])) {
    $b_cta_text = htmlspecialchars($promo_banner_data['cta_text'], ENT_QUOTES, 'UTF-8');
  } elseif ($b_has_cta_url) {
    $b_cta_text = 'Learn More';
  } elseif ($b_can_claim) {
    $b_cta_text = 'Claim ' . $b_pts . ' Points Now';
  } else {
    $b_cta_text = 'Login to Claim ' . $b_pts . ' Points';
  }

  // Image: custom uploaded OR fall back to default hotel slideshow
  $b_custom_img = !empty($promo_banner_data['bg_image']) ? BANNER_IMG_PATH . $promo_banner_data['bg_image'] : '';
  $b_default_imgs = [
    'https://images.unsplash.com/photo-1631049307264-da0ec9d70304?fit=crop&w=900&h=600&q=80',
    'https://images.unsplash.com/photo-1566073771259-6a8506099945?fit=crop&w=900&h=600&q=80',
    'https://images.unsplash.com/photo-1578683010236-d716f9a3f461?fit=crop&w=900&h=600&q=80',
    'https://images.unsplash.com/photo-1582719508461-905c673771fd?fit=crop&w=900&h=600&q=80',
  ];
  $b_images    = $b_custom_img ? [$b_custom_img] : $b_default_imgs;
  $b_imgs_json = json_encode($b_images);
?>
<!-- Promotional Banner Modal -->
<style>.pbm-slide{transition:opacity 1.1s ease-in-out;}</style>
<div class="modal fade" id="promoBannerModal" data-bs-backdrop="static" data-bs-keyboard="false"
     tabindex="-1" aria-hidden="true" data-login-state="<?php echo $b_login_state; ?>">
  <div class="modal-dialog modal-dialog-centered" style="max-width:min(940px,96vw);margin:auto;">
    <div class="modal-content border-0 overflow-hidden promo-banner-modal"
         style="border-radius:22px;box-shadow:0 40px 100px rgba(15,23,42,0.4);">
      <div style="display:flex;flex-direction:row;min-height:480px;">

        <!-- ══════════ LEFT panel ══════════ -->
        <?php if($b_custom_img): ?>
        <!-- Custom illustration image — show contained (no dark overlay) -->
        <div id="pbm-hero" style="flex:0 0 55%;position:relative;overflow:hidden;min-height:320px;background:#0d4a44;display:flex;align-items:center;justify-content:center;">
          <img src="<?php echo htmlspecialchars($b_custom_img, ENT_QUOTES, 'UTF-8'); ?>"
               alt="Rewards"
               style="width:100%;height:100%;object-fit:cover;object-position:center;display:block;">
        </div>

        <?php else: ?>
        <!-- Default hotel photo slideshow -->
        <div id="pbm-hero" style="flex:0 0 58%;position:relative;overflow:hidden;min-height:320px;">

          <?php foreach($b_images as $si => $bimg): ?>
          <div id="pbm-slide-<?php echo $si; ?>" class="pbm-slide position-absolute top-0 start-0 w-100 h-100"
               style="background-image:url('<?php echo htmlspecialchars($bimg,ENT_QUOTES,'UTF-8'); ?>');background-size:cover;background-position:center;opacity:<?php echo $si===0?'1':'0'; ?>;z-index:<?php echo $si===0?'1':'0'; ?>;"></div>
          <?php endforeach; ?>

          <div class="position-absolute top-0 start-0 w-100 h-100"
               style="background:linear-gradient(180deg,rgba(5,15,25,0.55) 0%,rgba(5,15,25,0.35) 35%,rgba(5,10,20,0.75) 70%,rgba(3,8,16,0.95) 100%);z-index:2;"></div>

          <div class="position-relative d-flex flex-column justify-content-between h-100 p-4 p-lg-5" style="z-index:3;min-height:320px;">

            <!-- Badge -->
            <div>
              <div class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill"
                   style="background:rgba(255,215,0,0.15);border:1px solid rgba(255,215,0,0.5);backdrop-filter:blur(8px);">
                <i class="bi bi-stars" style="color:#ffd700;font-size:13px;"></i>
                <span style="color:#ffd700;font-size:10px;font-weight:800;letter-spacing:2.5px;"><?php echo $b_badge_label; ?></span>
              </div>
            </div>

            <!-- Hero offer text -->
            <div class="text-center">
              <div style="font-size:clamp(4rem,9vw,6.5rem);font-weight:900;line-height:0.85;color:#ffd700;font-family:'Sora',sans-serif;
                          text-shadow:0 0 40px rgba(255,215,0,0.5),0 4px 20px rgba(0,0,0,0.7);">
                <?php echo $b_offer_text; ?>
              </div>
              <div style="font-size:1rem;font-weight:800;letter-spacing:4px;color:#fff;margin-top:8px;
                          text-shadow:0 2px 10px rgba(0,0,0,0.6);text-transform:uppercase;">
                <?php echo $b_offer_label; ?>
              </div>
              <?php if($b_pts > 0 && !$b_has_cta_url): ?>
              <div style="display:inline-block;margin-top:10px;padding:5px 14px;border-radius:20px;
                          background:rgba(255,255,255,0.12);border:1px solid rgba(255,255,255,0.25);
                          color:rgba(255,255,255,0.85);font-size:0.8rem;backdrop-filter:blur(6px);">
                <i class="bi bi-lightning-charge-fill me-1" style="color:#ffd700;"></i>
                Awarded instantly on your very first login
              </div>
              <?php endif; ?>
            </div>

            <!-- Perks row -->
            <div class="d-flex gap-2 justify-content-center flex-wrap">
              <div class="d-flex align-items-center gap-2 px-3 py-2 rounded-3"
                   style="background:rgba(255,255,255,0.1);backdrop-filter:blur(8px);border:1px solid rgba(255,255,255,0.18);">
                <i class="bi bi-building text-white" style="font-size:13px;"></i>
                <span class="text-white" style="font-size:11px;font-weight:700;">Luxury Stays</span>
              </div>
              <div class="d-flex align-items-center gap-2 px-3 py-2 rounded-3"
                   style="background:rgba(255,255,255,0.1);backdrop-filter:blur(8px);border:1px solid rgba(255,255,255,0.18);">
                <i class="bi bi-gift text-white" style="font-size:13px;"></i>
                <span class="text-white" style="font-size:11px;font-weight:700;">Instant Gift</span>
              </div>
              <div class="d-flex align-items-center gap-2 px-3 py-2 rounded-3"
                   style="background:rgba(255,255,255,0.1);backdrop-filter:blur(8px);border:1px solid rgba(255,255,255,0.18);">
                <i class="bi bi-percent text-white" style="font-size:13px;"></i>
                <span class="text-white" style="font-size:11px;font-weight:700;">Room Discounts</span>
              </div>
            </div>
          </div>
        </div>
        <?php endif; ?>
        <!-- /LEFT -->

        <!-- ══════════ RIGHT: Action panel ══════════ -->
        <div style="flex:1;background:#fff;display:flex;flex-direction:column;justify-content:space-between;padding:2rem 1.75rem 1.5rem;">

          <div>
            <div class="d-flex align-items-start justify-content-between mb-3">
              <div>
                <div style="font-size:0.65rem;letter-spacing:2.5px;color:#2d9c95;font-weight:800;text-transform:uppercase;">BookEase Exclusive</div>
                <h4 class="fw-bold mb-0 mt-1" style="font-size:1.15rem;color:#0f172a;line-height:1.3;"><?php echo $b_title; ?></h4>
              </div>
              <button type="button" id="promoBannerDismissBtn"
                      class="btn-close shadow-none flex-shrink-0 ms-2 mt-1" aria-label="Close" style="opacity:0.35;"></button>
            </div>

            <!-- Offer tile -->
            <div class="d-flex align-items-center gap-3 p-3 rounded-3 mb-3"
                 style="background:linear-gradient(135deg,rgba(45,156,149,0.07),rgba(45,156,149,0.03));border:1.5px solid rgba(45,156,149,0.18);">
              <div class="d-flex align-items-center justify-content-center rounded-circle flex-shrink-0"
                   style="width:50px;height:50px;background:linear-gradient(135deg,#2d9c95,#1d7a74);box-shadow:0 8px 20px rgba(45,156,149,0.35);font-size:22px;">🎁</div>
              <div>
                <div class="fw-bold" style="color:#0f172a;font-size:0.92rem;"><?php echo $b_offer_text; ?><?php if(!$b_has_cta_url && $b_pts > 0): ?> Free Loyalty Points<?php else: ?> <?php echo $b_offer_label; ?><?php endif; ?></div>
                <div class="text-muted" style="font-size:0.78rem;"><?php echo $b_sub ?: 'A special offer just for you'; ?></div>
              </div>
            </div>

            <p class="text-muted" style="font-size:0.83rem;line-height:1.65;margin-bottom:1rem;"><?php echo $b_desc; ?></p>
          </div>

          <!-- CTA section -->
          <div>
            <?php if($b_has_cta_url): ?>
            <!-- CTA URL mode — redirect button (no points claim) -->
            <a href="<?php echo $b_cta_url; ?>"
               class="btn w-100 fw-bold py-3 text-white shadow-none mb-2 d-block text-center"
               style="background:linear-gradient(135deg,#2d9c95,#1d7a74);border:none;border-radius:12px;font-size:0.97rem;text-decoration:none;">
              <i class="bi bi-arrow-right-circle-fill me-2"></i><?php echo $b_cta_text; ?>
            </a>

            <?php elseif($b_can_claim): ?>
            <!-- STATE 1: New member — eligible to claim points -->
            <button type="button" id="promoBannerClaimBtn"
                    class="btn w-100 fw-bold py-3 text-white shadow-none mb-2"
                    style="background:linear-gradient(135deg,#2d9c95,#1d7a74);border:none;border-radius:12px;font-size:0.97rem;">
              <span id="promoBannerClaimBtnText"><i class="bi bi-gift-fill me-2"></i><?php echo $b_cta_text; ?></span>
              <span id="promoBannerClaimBtnSpinner" class="spinner-border spinner-border-sm d-none" role="status"></span>
            </button>
            <div class="d-flex align-items-center justify-content-center gap-3 mb-3" style="font-size:0.72rem;color:#94a3b8;">
              <span><i class="bi bi-check-circle-fill me-1" style="color:#2d9c95;"></i>New members only</span>
              <span><i class="bi bi-check-circle-fill me-1" style="color:#2d9c95;"></i>One-time offer</span>
              <span><i class="bi bi-check-circle-fill me-1" style="color:#2d9c95;"></i>No expiry</span>
            </div>

            <?php elseif($b_not_eligible): ?>
            <!-- STATE 2: Existing member — informational only -->
            <div class="rounded-3 p-3 mb-3 text-center"
                 style="background:#fff7ed;border:1.5px solid #fed7aa;">
              <i class="bi bi-info-circle-fill mb-2 d-block" style="color:#f97316;font-size:1.4rem;"></i>
              <div class="fw-bold mb-1" style="color:#9a3412;font-size:0.88rem;">This offer is for new members only</div>
              <div style="color:#c2410c;font-size:0.78rem;line-height:1.5;">
                You registered before this promotion started.<br>
                You can still earn points by booking a room!
              </div>
            </div>
            <a href="rooms.php" class="btn w-100 fw-bold py-2 mb-2 shadow-none"
               style="background:linear-gradient(135deg,#2d9c95,#1d7a74);color:#fff;border:none;border-radius:12px;font-size:0.93rem;"
               data-bs-dismiss="modal">
              <i class="bi bi-building me-2"></i>Browse Our Rooms
            </a>

            <?php else: ?>
            <!-- STATE 3: Guest — not logged in -->
            <button type="button" id="promoBannerLoginBtn"
                    class="btn w-100 fw-bold py-3 text-white shadow-none mb-2"
                    data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#loginModal"
                    style="background:linear-gradient(135deg,#2d9c95,#1d7a74);border:none;border-radius:12px;font-size:0.97rem;">
              <i class="bi bi-box-arrow-in-right me-2"></i><?php echo $b_cta_text; ?>
            </button>
            <div class="text-center mb-3" style="font-size:0.78rem;color:#64748b;">
              New to BookEase?
              <button type="button" class="btn btn-link p-0 shadow-none fw-semibold"
                      style="font-size:0.78rem;color:#2d9c95;vertical-align:baseline;text-decoration:underline;text-underline-offset:2px;"
                      data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#registerModal">
                Register now — it's free!
              </button>
            </div>
            <div class="d-flex align-items-center justify-content-center gap-3 mb-3" style="font-size:0.72rem;color:#94a3b8;">
              <span><i class="bi bi-check-circle-fill me-1" style="color:#2d9c95;"></i>New members only</span>
              <span><i class="bi bi-check-circle-fill me-1" style="color:#2d9c95;"></i>One-time offer</span>
            </div>
            <?php endif; ?>

            <div class="text-center">
              <button type="button" id="promoBannerMaybeLaterBtn"
                      style="background:none;border:none;color:#94a3b8;font-size:0.78rem;cursor:pointer;text-decoration:underline;text-underline-offset:3px;">
                <?php echo $b_not_eligible ? 'Close' : 'Skip for now'; ?>
              </button>
            </div>
          </div>

        </div><!-- /RIGHT -->
      </div>
    </div>
  </div>
</div>

<script>
(function(){
  const imgs = <?php echo $b_imgs_json; ?>;
  if(imgs.length < 2) return;
  let cur = 0;
  setInterval(function(){
    const prev = document.getElementById('pbm-slide-' + cur);
    cur = (cur + 1) % imgs.length;
    const next = document.getElementById('pbm-slide-' + cur);
    if(!prev || !next) return;
    next.style.zIndex = 1; next.style.opacity = 1;
    setTimeout(function(){ prev.style.opacity = 0; prev.style.zIndex = 0; }, 80);
  }, 4800);
})();
</script>
<?php endif; ?>

<script>
let roomSelectionArray = [];

function syncCompareSelection() {
    roomSelectionArray = Array.from(document.querySelectorAll('.compare-room-checkbox:checked')).map(function (cb) {
        return parseInt(cb.value, 10);
    });
    let compareBtn = document.getElementById('compare-rooms-btn');
    compareBtn.disabled = roomSelectionArray.length < 1;
    let n = roomSelectionArray.length;
    compareBtn.textContent = n === 0 ? 'Compare selected' : (n === 1 ? 'Compare 1 room' : 'Compare 2 rooms');
}

function fetchRoomsForComparison() {
    let xhr = new XMLHttpRequest();
    xhr.open("GET", "admin/ajax/room_list.php?get_rooms_for_comparison=true", true);
    xhr.onload = function () {
        document.getElementById('room-selection-area').innerHTML = this.responseText;
        initializeRoomSelection();
    };
    xhr.send();
}

function initializeRoomSelection() {
    roomSelectionArray = [];
    let compareBtn = document.getElementById('compare-rooms-btn');
    let errorP = document.getElementById('compare-rooms-error');
    compareBtn.textContent = 'Compare selected';
    compareBtn.disabled = true;
    errorP.style.display = 'none';

    document.querySelectorAll('.compare-room-checkbox').forEach(function (checkbox) {
        checkbox.addEventListener('change', function () {
            if (this.checked) {
                let checked = document.querySelectorAll('.compare-room-checkbox:checked');
                if (checked.length > 2) {
                    this.checked = false;
                    errorP.textContent = 'You can compare at most two rooms. Uncheck one to pick another.';
                    errorP.style.display = 'block';
                    return;
                }
            }
            errorP.style.display = 'none';
            syncCompareSelection();
        });
    });
}

function resetCompareView() {
    document.getElementById('comparison-area').innerHTML = '';
    document.getElementById('comparison-area').style.display = 'none';
    document.getElementById('room-selection-area').style.display = 'block';
    let err = document.getElementById('compare-rooms-error');
    err.style.display = 'none';
    fetchRoomsForComparison();
}

function compareSelectedRooms() {
    let comparisonArea = document.getElementById('comparison-area');
    let roomSelectionArea = document.getElementById('room-selection-area');

    syncCompareSelection();
    if (roomSelectionArray.length < 1) {
        return false;
    }

    let xhr = new XMLHttpRequest();
    xhr.open("POST", "compare_rooms.php", true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    xhr.onload = function () {
        if (xhr.status === 200) {
            comparisonArea.innerHTML = xhr.responseText;
            roomSelectionArea.style.display = 'none';
            comparisonArea.style.display = 'block';
        }
    };

    xhr.send('room_ids=' + JSON.stringify(roomSelectionArray));
}

let compareRoomsModal = document.getElementById('compareRoomsModal');
compareRoomsModal.addEventListener('shown.bs.modal', function () {
    fetchRoomsForComparison();
});

compareRoomsModal.addEventListener('hidden.bs.modal', function () {
    let comparisonArea = document.getElementById('comparison-area');
    let roomSelectionArea = document.getElementById('room-selection-area');
    comparisonArea.innerHTML = '';
    comparisonArea.style.display = 'none';
    roomSelectionArea.style.display = 'block';
    roomSelectionArray = [];
    document.getElementById('compare-rooms-btn').disabled = true;
    document.getElementById('compare-rooms-btn').textContent = 'Compare selected';
    let err = document.getElementById('compare-rooms-error');
    err.style.display = 'none';
});
</script>
