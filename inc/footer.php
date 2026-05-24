<div class="container-fluid bg-white mt-5">
  <div class="row">
    <div class="col-lg-4 p-4">
      <h3 class="h-font fw-bold fs-3 mb-2 site-wordmark footer-wordmark"><?php echo preg_replace('/Ease$/', '<span class="wordmark-accent">Ease</span>', $settings_r['site_title']); ?></h3>
      <p>
        <?php echo $settings_r['site_about'] ?>
      </p>
    </div>
    <div class="col-lg-4 p-4">
      <h5 class="mb-3">Links</h5>
      <a href="index.php" class="d-inline-block mb-2 text-dark text-decoration-none">Home</a> <br>
      <a href="rooms.php" class="d-inline-block mb-2 text-dark text-decoration-none">Rooms</a> <br>
      <a href="facilities.php" class="d-inline-block mb-2 text-dark text-decoration-none">Facilities</a> <br>
      <a href="contact.php" class="d-inline-block mb-2 text-dark text-decoration-none">Contact us</a> <br>
      <a href="about.php" class="d-inline-block mb-2 text-dark text-decoration-none">About</a>
    </div>
    <div class="col-lg-4 p-4">
        <h5 class="mb-3">Follow us</h5>
        <?php 
          if($contact_r['tw']!=''){
            echo<<<data
              <a href="$contact_r[tw]" class="d-inline-block text-dark text-decoration-none mb-2">
                <i class="bi bi-twitter-x me-1"></i> X
              </a><br>
            data;
          }
        ?>
        <a href="<?php echo $contact_r['fb'] ?>" class="d-inline-block text-dark text-decoration-none mb-2">
          <i class="bi bi-facebook me-1"></i> Facebook
        </a><br>
        <a href="<?php echo $contact_r['insta'] ?>" class="d-inline-block text-dark text-decoration-none">
          <i class="bi bi-instagram me-1"></i> Instagram
        </a><br>
    </div>
  </div>
</div>

<h6 class="text-center bg-dark text-white p-3 m-0">&copy; 2026 BookEase. All rights reserved.</h6>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<?php require_once(__DIR__.'/google_config.php'); ?>
<script src="https://accounts.google.com/gsi/client" async defer></script>

<script>
  const nativeAlert = window.alert ? window.alert.bind(window) : function(){};

  function getAlertHost(position='body'){
    if(position !== 'body'){
      const target = document.getElementById(position);
      if(target) return target;
    }

    let host = document.getElementById('global-alert-host');
    if(!host){
      host = document.createElement('div');
      host.id = 'global-alert-host';
      host.className = 'custom-alert-host';
      host.style.position = 'fixed';
      host.style.top = '84px';
      host.style.right = '20px';
      host.style.zIndex = '1200';
      host.style.width = 'min(360px, calc(100vw - 24px))';
      host.style.display = 'flex';
      host.style.flexDirection = 'column';
      host.style.gap = '10px';
      document.body.appendChild(host);
    }
    return host;
  }

  function dismissAlert(alertNode){
    if(!alertNode) return;
    alertNode.classList.add('is-leaving');
    setTimeout(()=>{
      if(alertNode.parentNode){
        alertNode.remove();
      }
    }, 260);
  }

  function alert(type,msg,position='body')
  {
    try{
      // Backward compatibility for calls like alert("some message")
      if(typeof msg === 'undefined'){
        msg = String(type !== undefined && type !== null ? type : '');
        type = 'error';
      }

      const safeType = type === 'success' ? 'success' : 'error';
      const icon = safeType === 'success' ? 'bi-check-circle-fill' : 'bi-exclamation-octagon-fill';
      const host = getAlertHost(position);
      if(!host) return nativeAlert(msg);

      const alertNode = document.createElement('div');
      alertNode.className = `custom-alert-toast custom-alert-${safeType}`;
      alertNode.setAttribute('role', 'alert');
      alertNode.style.background = 'rgba(255,255,255,0.98)';
      alertNode.style.border = '1px solid rgba(148,163,184,.25)';
      alertNode.style.borderRadius = '14px';
      alertNode.style.boxShadow = '0 16px 36px rgba(15,23,42,.16)';
      alertNode.style.overflow = 'hidden';
      alertNode.innerHTML = `
        <div class="custom-alert-content">
          <i class="bi ${icon} custom-alert-icon" aria-hidden="true"></i>
          <div class="custom-alert-message">${msg}</div>
          <button type="button" class="btn-close custom-alert-close" aria-label="Close"></button>
        </div>
        <div class="custom-alert-progress"></div>
      `;

      host.appendChild(alertNode);
      setTimeout(()=> alertNode.classList.add('is-visible'), 10);

      const closeBtn = alertNode.querySelector('.custom-alert-close');
      if(closeBtn){
        closeBtn.addEventListener('click', ()=> dismissAlert(alertNode));
      }

      setTimeout(()=> dismissAlert(alertNode), 3600);
    }
    catch(err){
      nativeAlert(typeof msg !== 'undefined' ? String(msg) : 'Something went wrong.');
    }
  }

  function remAlert(){
    const alertNode = document.querySelector('.custom-alert-toast');
    dismissAlert(alertNode);
  }

  function setActive()
  {
    let navbar = document.getElementById('nav-bar');
    let a_tags = navbar.getElementsByTagName('a');

    for(i=0; i<a_tags.length; i++)
    {
      let file = a_tags[i].href.split('/').pop();
      let file_name = file.split('.')[0];

      if(document.location.href.indexOf(file_name) >= 0){
        a_tags[i].classList.add('active');
      }

    }
  }

  function isTenDigitPhone(phone){
    // Remove non-numeric characters and check length
    let cleanPhone = phone.replace(/\D/g, '');
    return cleanPhone.length === 10 && /^[0-9]{10}$/.test(cleanPhone);
  }

  function isAtLeast18(dob){
    if(!dob) return false;
    let date = new Date(dob);
    if(isNaN(date.getTime())) return false;

    let today = new Date();
    let adultDate = new Date(today.getFullYear() - 18, today.getMonth(), today.getDate());
    return date <= adultDate;
  }

  function initSiteDatePickers(){
    if(typeof flatpickr === 'undefined') return;

    document.querySelectorAll('input[type="date"]').forEach((inputEl)=>{
      if(inputEl.dataset.fpInit === '1') return;

      const initialValue = inputEl.value || '';
      const minDate = inputEl.getAttribute('min') || null;
      const maxDate = inputEl.getAttribute('max') || null;

      // Avoid native date UI conflicts and use one consistent picker.
      inputEl.setAttribute('type', 'text');
      inputEl.setAttribute('autocomplete', 'off');
      if(!inputEl.getAttribute('placeholder')){
        inputEl.setAttribute('placeholder', 'YYYY-MM-DD');
      }

      flatpickr(inputEl, {
        dateFormat: 'Y-m-d',
        allowInput: true,
        minDate: minDate,
        maxDate: maxDate,
        defaultDate: initialValue || null
      });

      inputEl.dataset.fpInit = '1';
    });
  }

  function setRegistrationMaxDob(){
    let dobInput = document.querySelector('#register-form input[name="dob"]');
    if(!dobInput) return;
    let today = new Date();
    let maxDate = new Date(today.getFullYear() - 18, today.getMonth(), today.getDate());
    let maxDateString = maxDate.toISOString().split('T')[0];
    dobInput.setAttribute('max', maxDateString);

    if(dobInput._flatpickr){
      dobInput._flatpickr.set('maxDate', maxDateString);
    }
  }

  initSiteDatePickers();
  setRegistrationMaxDob();

  let register_form = document.getElementById('register-form');

  register_form.addEventListener('submit', (e)=>{
    e.preventDefault();

    let phone = register_form.elements['phonenum'].value;
    if(!isTenDigitPhone(phone)){
      alert('error',"Phone number must be exactly 10 digits!");
      return;
    }

    let dob = register_form.elements['dob'].value;
    if(!isAtLeast18(dob)){
      alert('error',"You must be at least 18 years old to register.");
      return;
    }

    let data = new FormData();

    data.append('name',register_form.elements['name'].value);
    data.append('email',register_form.elements['email'].value);
    data.append('phonenum',register_form.elements['phonenum'].value);
    data.append('address',register_form.elements['address'].value);
    data.append('pincode',register_form.elements['pincode'].value);
    data.append('dob',register_form.elements['dob'].value);
    data.append('pass',register_form.elements['pass'].value);
    data.append('cpass',register_form.elements['cpass'].value);
    const profileFile = register_form.elements['profile'].files[0];
    if (profileFile) {
      data.append('profile', profileFile);
    }
    data.append('register','');

    var myModal = document.getElementById('registerModal');
    var modal = bootstrap.Modal.getInstance(myModal);
    modal.hide();

    let xhr = new XMLHttpRequest();
    xhr.open("POST","ajax/login_register.php",true);

    xhr.onload = function(){
      if(this.responseText == 'pass_mismatch'){
        alert('error',"Password Mismatch!");
      }
      else if(this.responseText == 'email_already'){
        alert('error',"Email is already registered!");
      }
      else if(this.responseText == 'phone_already'){
        alert('error',"Phone number is already registered!");
      }
      else if(this.responseText == 'phone_invalid'){
        alert('error',"Phone number must contain exactly 10 digits (numbers only)!");
      }
      else if(this.responseText == 'age_invalid'){
        alert('error',"You must be at least 18 years old to register.");
      }
      else if(this.responseText == 'inv_img'){
        alert('error',"Only JPG, WEBP & PNG images are allowed!");
      }
      else if(this.responseText == 'upd_failed'){
        alert('error',"Image upload failed!");
      }
      else if(this.responseText == 'mail_failed'){
        alert('error',"Cannot send OTP email! Server down!");
      }
      else if(this.responseText == 'ins_failed'){
        alert('error',"Registration failed! Server down!");
      }
      else{
        let regEmail = register_form.elements['email'].value;
        alert('success',"OTP sent to your email!");
        register_form.reset();
        let otpModal = document.getElementById('otpModal');
        if(otpModal){
          let emailInput = otpModal.querySelector("input[name='email']");
          if(emailInput) emailInput.value = regEmail;

          // Small delay helps when Bootstrap is still finishing the register modal hide animation.
          setTimeout(() => {
            try{
              let inst = bootstrap.Modal.getInstance(otpModal);
              if(!inst) inst = new bootstrap.Modal(otpModal, {backdrop: 'static', keyboard: false});
              inst.show();
              let otpInput = otpModal.querySelector("input[name='otp']");
              if(otpInput) otpInput.focus();

              // Verify Bootstrap actually switched the modal to "shown" state.
              setTimeout(() => {
                const isShown = otpModal.classList.contains('show') && otpModal.getAttribute('aria-hidden') !== 'true';
                if(!isShown){
                  alert('error', 'OTP popup did not open (Bootstrap did not show the modal).');
                }
              }, 320);
            }catch(err){
              alert('error', 'OTP popup failed to open.');
            }
          }, 250);
        }
      }
    }

    xhr.send(data);
  });

  let otp_form = document.getElementById('otp-form');

  if(otp_form){
    otp_form.addEventListener('submit', (e)=>{
      e.preventDefault();

      let email = otp_form.elements['email'].value;
      let otp = otp_form.elements['otp'].value.trim();

      if(!email){
        alert('error',"Email is missing! Please register again.");
        return;
      }

      if(!/^[0-9]{6}$/.test(otp)){
        alert('error',"OTP must be exactly 6 digits!");
        return;
      }

      let data = new FormData();
      data.append('email', email);
      data.append('otp', otp);
      data.append('verify_otp','');

      let xhr = new XMLHttpRequest();
      xhr.open("POST","ajax/login_register.php",true);

      xhr.onload = function(){
        if(this.responseText == 'inv_email'){
          alert('error',"Invalid email! Please register again.");
        }
        else if(this.responseText == 'already_verified'){
          alert('success',"Account already verified!");
          otp_form.reset();
          let otpModal = document.getElementById('otpModal');
          if(otpModal){
            let inst = bootstrap.Modal.getInstance(otpModal);
            if(!inst) inst = new bootstrap.Modal(otpModal, {backdrop: 'static', keyboard: false});
            inst.hide();
          }
        }
        else if(this.responseText == 'otp_expired'){
          alert('error',"OTP expired! Please register again.");
        }
        else if(this.responseText == 'otp_invalid'){
          alert('error',"Invalid OTP! Please try again.");
        }
        else if(this.responseText == 'otp_invalid_format'){
          alert('error',"OTP format invalid!");
        }
        else if(this.responseText == 'otp_verify_failed'){
          alert('error',"OTP verification failed! Try again.");
        }
        else{
          alert('success',"Account verified successfully! Log in to receive your welcome loyalty points.");
          otp_form.reset();
          let otpModal = document.getElementById('otpModal');
          if(otpModal){
            let inst = bootstrap.Modal.getInstance(otpModal);
            if(!inst) inst = new bootstrap.Modal(otpModal, {backdrop: 'static', keyboard: false});
            inst.hide();
          }
        }
      }

      xhr.send(data);
    });
  }

  let login_form = document.getElementById('login-form');

  login_form.addEventListener('submit', (e)=>{
    e.preventDefault();

    let data = new FormData();

    data.append('email_mob',login_form.elements['email_mob'].value);
    data.append('pass',login_form.elements['pass'].value);
    data.append('login','');

    var myModal = document.getElementById('loginModal');
    var modal = bootstrap.Modal.getInstance(myModal);
    modal.hide();

    let xhr = new XMLHttpRequest();
    xhr.open("POST","ajax/login_register.php",true);

    xhr.onload = function(){
      if(this.responseText == 'inv_email_mob'){
        alert('error',"Invalid Email or Mobile Number!");
      }
      else if(this.responseText == 'not_verified'){
        alert('error',"Email is not verified!");
      }
      else if(this.responseText == 'inactive'){
        alert('error',"Account Suspended! Please contact Admin.");
      }
      else if(this.responseText == 'invalid_pass'){
        alert('error',"Incorrect Password!");
      }
      else{
        let fileurl = window.location.href.split('/').pop().split('?').shift();
        if(fileurl == 'room_details.php'){
          window.location = window.location.href;
        }
        else{
          window.location = window.location.pathname;
        }
      }
    }

    xhr.send(data);
  });

  let forgot_form = document.getElementById('forgot-form');

  if(forgot_form){
    forgot_form.addEventListener('submit', (e)=>{
      e.preventDefault();

      let email = forgot_form.elements['email'].value.trim();
      if(email === ''){
        alert('error', "Please enter your email.");
        return;
      }

      let data = new FormData();
      data.append('email', email);
      data.append('forgot_pass','');

      let xhr = new XMLHttpRequest();
      xhr.open("POST","ajax/login_register.php",true);

      xhr.onload = function(){
        if(this.responseText == 'inv_email'){
          alert('error',"Invalid Email !");
        }
        else if(this.responseText == 'not_verified'){
          alert('error',"Email is not verified! Please contact Admin");
        }
        else if(this.responseText == 'inactive'){
          alert('error',"Account Suspended! Please contact Admin.");
        }
        else if(this.responseText == 'mail_failed'){
          alert('error',"Cannot send email. Server Down!");
        }
        else if(this.responseText == 'upd_failed'){
          alert('error',"Password reset OTP failed. Server down!");
        }
        else{
          alert('success',"OTP sent to your email! Enter it below to reset your password.");
          forgot_form.reset();

          let forgotModalEl = document.getElementById('forgotModal');
          let forgotModal = bootstrap.Modal.getInstance(forgotModalEl) || new bootstrap.Modal(forgotModalEl, {backdrop:'static', keyboard:false});
          forgotModal.hide();

          let forgotResetModalEl = document.getElementById('forgotResetModal');
          if(forgotResetModalEl){
            let emailInput = forgotResetModalEl.querySelector("input[name='email']");
            if(emailInput) emailInput.value = email;

            let inst = bootstrap.Modal.getInstance(forgotResetModalEl) || new bootstrap.Modal(forgotResetModalEl, {backdrop:'static', keyboard:false});
            inst.show();
            let otpInput = forgotResetModalEl.querySelector("input[name='otp']");
            if(otpInput) otpInput.focus();
          }
        }
      }

      xhr.send(data);
    });
  }

  let forgot_reset_form = document.getElementById('forgot-reset-form');

  if(forgot_reset_form){
    forgot_reset_form.addEventListener('submit', (e)=>{
      e.preventDefault();

      let email = forgot_reset_form.elements['email'].value.trim();
      let otp = forgot_reset_form.elements['otp'].value.trim();
      let pass = forgot_reset_form.elements['pass'].value;
      let cpass = forgot_reset_form.elements['cpass'].value;

      if(email === ''){
        alert('error',"Email is missing. Restart the reset flow.");
        return;
      }
      if(!/^[0-9]{6}$/.test(otp)){
        alert('error',"OTP must be exactly 6 digits!");
        return;
      }
      if(pass.length < 6){
        alert('error',"Password must be at least 6 characters.");
        return;
      }
      if(pass !== cpass){
        alert('error',"Passwords do not match!");
        return;
      }

      let data = new FormData();
      data.append('email', email);
      data.append('otp', otp);
      data.append('pass', pass);
      data.append('recover_user','');

      let xhr = new XMLHttpRequest();
      xhr.open("POST","ajax/login_register.php",true);

      xhr.onload = function(){
        if(this.responseText == 'inv_email'){
          alert('error',"Invalid Email! Please restart the reset flow.");
        }
        else if(this.responseText == 'otp_invalid_format'){
          alert('error',"OTP format invalid!");
        }
        else if(this.responseText == 'otp_invalid'){
          alert('error',"Invalid OTP! Please try again.");
        }
        else if(this.responseText == 'otp_expired'){
          alert('error',"OTP expired! Request a new one.");
        }
        else if(this.responseText == 'failed'){
          alert('error',"Password reset failed. Please try again.");
        }
        else{
          alert('success',"Password reset successful! You can now log in.");
          forgot_reset_form.reset();
          let forgotResetModalEl = document.getElementById('forgotResetModal');
          let inst = bootstrap.Modal.getInstance(forgotResetModalEl) || new bootstrap.Modal(forgotResetModalEl, {backdrop:'static', keyboard:false});
          inst.hide();
        }
      }

      xhr.send(data);
    });
  }

  function checkLoginToBook(status,room_id){
    if(status){
      window.location.href='confirm_booking.php?id='+room_id;
    }
    else{
      alert('error','Please login to book room!');
    }
  }

  const GOOGLE_CLIENT_ID = <?php echo json_encode(GOOGLE_CLIENT_ID); ?>;

  function handleGoogleCredentialResponse(response) {
    if (!response || !response.credential) {
      alert('error', 'Google login failed. Please try again.');
      return;
    }

    let data = new FormData();
    data.append('google_auth', '');
    data.append('credential', response.credential);

    let xhr = new XMLHttpRequest();
    xhr.open("POST","ajax/login_register.php",true);

    xhr.onload = function(){
      if(this.responseText == 'google_not_configured'){
        alert('error',"Google login is not configured yet.");
      }
      else if(this.responseText == 'google_invalid'){
        alert('error',"Invalid Google login response.");
      }
      else if(this.responseText == 'inactive'){
        alert('error',"Account Suspended! Please contact Admin.");
      }
      else if(this.responseText == 'ins_failed'){
        alert('error',"Google login failed! Please try again.");
      }
      else{
        window.location = window.location.pathname;
      }
    };

    xhr.send(data);
  }

  function initializeGoogleLogin(){
    const btnContainer = document.getElementById('google-login-btn');
    if(!btnContainer){
      return;
    }

    if(!GOOGLE_CLIENT_ID){
      btnContainer.innerHTML = '<small class="text-muted">Google login is unavailable right now.</small>';
      return;
    }

    if(!window.google || !window.google.accounts || !window.google.accounts.id){
      setTimeout(initializeGoogleLogin, 500);
      return;
    }

    window.google.accounts.id.initialize({
      client_id: GOOGLE_CLIENT_ID,
      callback: handleGoogleCredentialResponse
    });

    btnContainer.innerHTML = '';
    window.google.accounts.id.renderButton(btnContainer, {
      type: 'standard',
      theme: 'outline',
      size: 'large',
      width: 280,
      text: 'signin_with'
    });
  }

  setActive();
  initializeGoogleLogin();

  <?php if (!empty($welcome_bonus_points)): ?>
  alert('success', 'Welcome! <?php echo (int) $welcome_bonus_points; ?> loyalty points have been added to your account!');
  <?php endif; ?>

  // ── Promotional Banner ──────────────────────────────────────
  (function () {
    const bannerEl = document.getElementById('promoBannerModal');
    if (!bannerEl) return;

    const DISMISS_KEY  = 'bookease_banner_dismissed';
    // loginState: '0' = guest, '1' = new member can claim, '2' = existing member
    const loginState   = bannerEl.dataset.loginState || '0';
    const isGuest      = loginState === '0';

    // All users: if dismissed this session, don't show again until next browser session.
    // sessionStorage is wiped when the tab/browser closes, so banner returns on a new visit.
    if (sessionStorage.getItem(DISMISS_KEY) === '1') {
      bannerEl.remove();
      return;
    }

    const bannerModal = new bootstrap.Modal(bannerEl, { backdrop: 'static', keyboard: false });
    setTimeout(function () { bannerModal.show(); }, 800);

    const claimBtn   = document.getElementById('promoBannerClaimBtn');
    const claimText  = document.getElementById('promoBannerClaimBtnText');
    const claimSpin  = document.getElementById('promoBannerClaimBtnSpinner');
    const dismissBtn = document.getElementById('promoBannerDismissBtn');
    const laterBtn   = document.getElementById('promoBannerMaybeLaterBtn');

    // Logged-in claim flow
    if (claimBtn) {
      claimBtn.addEventListener('click', function () {
        claimBtn.disabled = true;
        if (claimText) claimText.classList.add('d-none');
        if (claimSpin) claimSpin.classList.remove('d-none');

        const fd = new FormData();
        fd.append('claim_banner', '');
        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'ajax/banner.php', true);
        xhr.onload = function () {
          claimBtn.disabled = false;
          if (claimText) claimText.classList.remove('d-none');
          if (claimSpin) claimSpin.classList.add('d-none');

          const res = this.responseText.trim();
          try {
            const data = JSON.parse(res);
            if (data.status === 'success') {
              bannerModal.hide();
              alert('success', 'Congratulations! ' + data.points + ' loyalty points have been added to your account!');
              return;
            }
          } catch (e) { /* not JSON */ }

          if (res === 'already_claimed') {
            bannerModal.hide();
          } else if (res === 'not_eligible') {
            bannerModal.hide();
            alert('error', 'This offer is for new members only.');
          } else {
            alert('error', 'Could not claim points. Please try again.');
          }
        };
        xhr.send(fd);
      });
    }

    // Dismiss: store in sessionStorage for all users so navigating away doesn't re-open it
    function dismissBanner() {
      sessionStorage.setItem(DISMISS_KEY, '1');
      bannerModal.hide();
    }

    if (dismissBtn) dismissBtn.addEventListener('click', dismissBanner);
    if (laterBtn)   laterBtn.addEventListener('click', dismissBanner);
  })();
  // ── End Promotional Banner ───────────────────────────────────

</script>