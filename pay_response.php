<?php
require('admin/inc/db_config.php');
require('admin/inc/essentials.php');
require_once('inc/currency_helpers.php');
require_once('inc/loyalty_points.php');
require_once('vendor/autoload.php');

if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

\Stripe\Stripe::setApiKey('sk_test_51TDMJa4Gjr7oYQNHK8x9wqD6bGFms0RuSXhkrup4w9RqPeOJunbUWMkyadf1NinQNGNLZl7B8bQzXpz8Sl3cL9rC005ENdyRf2');

if (!(isset($_SESSION['login']) && $_SESSION['login'] == true)) {
  redirect('index.php');
}

$sessionId = isset($_GET['session_id']) ? $_GET['session_id'] : '';

try {
  if ($sessionId === '') {
    throw new \Exception('Missing payment session.');
  }

  $session = \Stripe\Checkout\Session::retrieve($sessionId);

  if ($session->payment_status == 'paid') {
    $orderId = $session->client_reference_id;

    $query = "SELECT `booking_id`, `user_id` FROM `booking_order` WHERE `order_id`=?";
    $booking_res = select($query, [$orderId], 's');

    if (!$booking_res) {
      echo '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Error</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"><link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,400;0,500;0,600;0,700;1,400&family=Sora:wght@600;700&display=swap" rel="stylesheet"><link rel="stylesheet" href="css/common.css"></head><body class="bg-light p-5"><div class="container text-center"><p class="text-danger">Something went wrong. Please contact support.</p><a href="index.php" class="btn btn-primary">Home</a></div></body></html>';
      exit;
    }

    if (mysqli_num_rows($booking_res) > 0) {
      $booking_fetch = mysqli_fetch_assoc($booking_res);

      $updateQuery = "UPDATE `booking_order` SET `booking_status`='booked', `trans_id`=?, `trans_status`='TXN_SUCCESS' WHERE `booking_id`=? AND (`booking_status`='pending' OR `booking_status` IS NULL)";
      $rowsUpdated = update($updateQuery, [$session->payment_intent, $booking_fetch['booking_id']], 'si');
      
      // Always send confirmation email for successful payments
      require_once __DIR__ . '/inc/booking_confirmation_mail.php';
      send_booking_payment_confirmation_email($con, (int) $booking_fetch['booking_id']);
      
      // Redeem pending loyalty points after successful payment
      if (!empty($_SESSION['pending_loyalty_reward'])) {
        $reward_info = $_SESSION['pending_loyalty_reward'];
        $result = redeemLoyaltyPoints($booking_fetch['user_id'], $reward_info['reward_id']);
        if ($result['success']) {
          // Update the notice to reflect actual redemption
          $_SESSION['loyalty_redeem_notice'] = 
            'You used ' . $reward_info['points_required'] . ' loyalty points on "' . $reward_info['reward_name'] . '" for a ' . rtrim(rtrim(number_format($reward_info['discount_percent'], 2, '.', ''), '0'), '.') . '% discount.';
        }
        unset($_SESSION['pending_loyalty_reward']);
      }
      
      // Earn loyalty points on successful payment (amount in NPR equivalent)
      $amount_paid_npr = ($session->currency === 'npr') ? 
        ($session->amount_total / 100) : 
        convertCurrency(($session->amount_total / 100), strtoupper($session->currency), 'NPR', $con);
      
      // Check if loyalty points have already been awarded for this booking
      $existing_points = select("SELECT COUNT(*) as count FROM loyalty_transactions WHERE booking_id = ? AND type = 'earn'", [$booking_fetch['booking_id']], 'i');
      $points_count = mysqli_fetch_assoc($existing_points)['count'];
      
      if ($points_count == 0) {
        addLoyaltyPoints($booking_fetch['user_id'], $booking_fetch['booking_id'], (float) $amount_paid_npr);
      }

      // Repair any other booked orders for this user missing earn transactions
      awardMissingLoyaltyPointsForUser($booking_fetch['user_id']);

      $settings_row = mysqli_fetch_assoc(select("SELECT `site_title` FROM `settings` WHERE `sr_no`=?", [1], 'i'));
      $siteTitle = $settings_row ? htmlspecialchars($settings_row['site_title'], ENT_QUOTES, 'UTF-8') : 'BookEase';
      $year = date('Y');

      $currency = strtoupper($session->currency ?? ($_SESSION['room']['currency'] ?? 'USD'));
      $amountPaid = number_format(($session->amount_total ?? 0) / 100, 2, '.', ',');
      $orderIdEsc = htmlspecialchars($orderId, ENT_QUOTES, 'UTF-8');
      $transIdEsc = htmlspecialchars((string) $session->payment_intent, ENT_QUOTES, 'UTF-8');

      $loyaltyNoticeHtml = '';
      if (!empty($_SESSION['loyalty_redeem_notice'])) {
        $loyaltyNoticeHtml = '<div class="alert alert-success border-0 mb-3 small"><i class="bi bi-gift-fill me-2"></i>' . htmlspecialchars($_SESSION['loyalty_redeem_notice'], ENT_QUOTES, 'UTF-8') . '</div>';
        unset($_SESSION['loyalty_redeem_notice']);
      }

      echo '<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Payment successful — ' . $siteTitle . '</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,400;0,500;0,600;0,700;1,400&family=Sora:wght@600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.1/font/bootstrap-icons.css">
  <link rel="stylesheet" href="css/common.css">
  <style>
    .pay-success-page { min-height: 100vh; display: flex; flex-direction: column; background: linear-gradient(165deg, #f8f9fa 0%, #e9ecef 45%, #f8f9fa 100%); }
    .pay-success-main { flex: 1 0 auto; }
    .pay-success-card { border: none; border-radius: 1rem; overflow: hidden; box-shadow: 0 0.5rem 2rem rgba(0,0,0,.08); }
    .pay-success-header { background: linear-gradient(135deg, #198754 0%, #157347 100%); color: #fff; padding: 2rem 1.5rem 1.5rem; text-align: center; }
    .pay-success-icon { width: 4rem; height: 4rem; border-radius: 50%; background: rgba(255,255,255,.2); display: inline-flex; align-items: center; justify-content: center; font-size: 2rem; margin-bottom: 1rem; }
    .pay-success-body { padding: 1.75rem; }
    .detail-row { display: flex; justify-content: space-between; align-items: baseline; gap: 1rem; padding: 0.65rem 0; border-bottom: 1px solid #eee; font-size: 0.95rem; }
    .detail-row:last-child { border-bottom: none; }
    .detail-label { color: #6c757d; font-weight: 500; }
    .detail-value { font-weight: 600; text-align: right; word-break: break-all; }
    .amount-highlight { font-size: 1.35rem; color: var(--teal_dark); font-weight: 700; }
    .pay-response-footer { flex-shrink: 0; margin-top: auto; background: linear-gradient(180deg, #1a1d23 0%, #121418 100%); border-top: 1px solid rgba(46, 193, 172, 0.35); box-shadow: 0 -4px 24px rgba(0,0,0,.12); }
    .pay-response-footer-top { padding: 1.25rem 0; }
    .pay-response-footer-brand { font-family: var(--font-heading); font-weight: 700; color: #fff; font-size: 1.1rem; letter-spacing: 0.02em; }
    .pay-response-footer-note { color: rgba(255,255,255,.55); font-size: 0.8rem; max-width: 22rem; }
    .pay-response-footer-links a { color: rgba(255,255,255,.65); text-decoration: none; font-size: 0.875rem; transition: color .2s; }
    .pay-response-footer-links a:hover { color: #2ec1ac; }
    .pay-response-footer-bottom { background: #0d0f12; border-top: 1px solid rgba(255,255,255,.06); padding: 0.85rem 0; }
    .pay-response-footer-bottom .sep { color: rgba(255,255,255,.25); margin: 0 .5rem; }
    .pay-response-stripe-badge { display: inline-flex; align-items: center; gap: .4rem; font-size: 0.75rem; color: rgba(255,255,255,.45); }
  </style>
</head>
<body class="pay-success-page">
  <main class="pay-success-main">
  <div class="container py-5">
    <div class="row justify-content-center">
      <div class="col-lg-7 col-md-9">
        <div class="card pay-success-card">
          <div class="pay-success-header">
            <div class="pay-success-icon"><i class="bi bi-check-lg"></i></div>
            <h1 class="h4 fw-bold mb-1">Payment successful</h1>
            <p class="mb-0 opacity-90 small">Your booking is confirmed. A summary is below.</p>
          </div>
          <div class="pay-success-body">
            ' . $loyaltyNoticeHtml . '
            <div class="detail-row">
              <span class="detail-label">Order reference</span>
              <span class="detail-value">' . $orderIdEsc . '</span>
            </div>
            <div class="detail-row">
              <span class="detail-label">Transaction ID</span>
              <span class="detail-value font-monospace small">' . $transIdEsc . '</span>
            </div>
            <div class="detail-row align-items-center">
              <span class="detail-label">Amount paid</span>
              <span class="detail-value amount-highlight">' . htmlspecialchars($currency, ENT_QUOTES, 'UTF-8') . ' ' . htmlspecialchars($amountPaid, ENT_QUOTES, 'UTF-8') . '</span>
            </div>
            <div class="alert alert-light border mt-3 mb-0 small text-muted">
              <i class="bi bi-info-circle me-1"></i>
              You can view full booking details and dates anytime from <strong>Bookings</strong>.
            </div>
            <div class="d-grid gap-2 d-sm-flex justify-content-sm-center mt-4">
              <a href="bookings.php" class="btn btn-primary px-4"><i class="bi bi-calendar-check me-2"></i>View my bookings</a>
              <a href="index.php" class="btn btn-outline-primary px-4">Back to home</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  </main>
  <footer class="pay-response-footer">
    <div class="container pay-response-footer-top">
      <div class="row align-items-center gy-3">
        <div class="col-md-5 text-center text-md-start">
          <div class="pay-response-footer-brand">' . $siteTitle . '</div>
          <p class="pay-response-footer-note mb-0 mt-1">Thank you for choosing us. Need help? We are here for you.</p>
        </div>
        <div class="col-md-4 text-center">
          <div class="pay-response-footer-links">
            <a href="index.php"><i class="bi bi-house-door me-1"></i>Home</a>
            <span class="sep text-white-50">·</span>
            <a href="bookings.php"><i class="bi bi-calendar3 me-1"></i>Bookings</a>
            <span class="sep text-white-50">·</span>
            <a href="contact.php"><i class="bi bi-envelope me-1"></i>Contact</a>
          </div>
        </div>
        <div class="col-md-3 text-center text-md-end">
          <span class="pay-response-stripe-badge"><i class="bi bi-lock-fill text-success"></i> Paid securely with Stripe</span>
        </div>
      </div>
    </div>
    <div class="pay-response-footer-bottom">
      <div class="container">
        <div class="d-flex flex-column flex-sm-row align-items-center justify-content-between gap-2 small">
          <span class="text-white-50">&copy; ' . $year . ' ' . $siteTitle . '. All rights reserved.</span>
          <span class="text-white-50" style="opacity:.6">Payment confirmation page</span>
        </div>
      </div>
    </div>
  </footer>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>';
    } else {
      echo '<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Booking not found</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"><link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,400;0,500;0,600;0,700;1,400&family=Sora:wght@600;700&display=swap" rel="stylesheet"><link rel="stylesheet" href="css/common.css"><link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.1/font/bootstrap-icons.css"></head><body class="bg-light"><div class="container py-5 text-center"><div class="card shadow-sm border-0 mx-auto" style="max-width:480px"><div class="card-body p-4"><i class="bi bi-exclamation-triangle text-warning display-4"></i><h1 class="h5 mt-3">Booking not found</h1><p class="text-muted small">We could not match this payment to a booking. Please contact support with your order ID.</p><a href="index.php" class="btn btn-primary">Home</a></div></div></div></body></html>';
    }
  } else {
    // Payment failed or cancelled - update booking status
    $orderId = $session->client_reference_id;
    $query = "SELECT `booking_id`, `user_id` FROM `booking_order` WHERE `order_id`=?";
    $booking_res = select($query, [$orderId], 's');
    
    if ($booking_res && mysqli_num_rows($booking_res) > 0) {
      $booking_fetch = mysqli_fetch_assoc($booking_res);
      $updateQuery = "UPDATE `booking_order` SET `booking_status`='payment_failed', `trans_status`='TXN_FAILURE', `trans_resp_msg`='Stripe payment failed or cancelled' WHERE `booking_id`=? AND (`booking_status`='pending' OR `booking_status` IS NULL)";
      update($updateQuery, [$booking_fetch['booking_id']], 'i');
    }
    
    // Clear pending loyalty reward since payment failed
    unset($_SESSION['pending_loyalty_reward']);
    unset($_SESSION['loyalty_redeem_notice']);
    
    echo '<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Payment failed</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"><link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,400;0,500;0,600;0,700;1,400&family=Sora:wght@600;700&display=swap" rel="stylesheet"><link rel="stylesheet" href="css/common.css"></head><body class="bg-light"><div class="container py-5 text-center"><div class="card shadow-sm border-0 mx-auto" style="max-width:480px"><div class="card-body p-4"><i class="bi bi-x-circle text-danger display-4"></i><h1 class="h5 mt-3">Payment Failed</h1><p class="text-muted small">Your payment was not completed. Please try again.</p><a href="bookings.php" class="btn btn-primary">Try Again</a></div></div></div></body></html>';
  }
} catch (\Stripe\Exception\ApiErrorException $e) {
  echo '<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><title>Error</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"><link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,400;0,500;0,600;0,700;1,400&family=Sora:wght@600;700&display=swap" rel="stylesheet"><link rel="stylesheet" href="css/common.css"></head><body class="bg-light p-5"><div class="container text-center"><p class="text-danger">' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . '</p><a href="index.php" class="btn btn-primary">Home</a></div></body></html>';
} catch (\Exception $e) {
  echo '<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><title>Error</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"><link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,400;0,500;0,600;0,700;1,400&family=Sora:wght@600;700&display=swap" rel="stylesheet"><link rel="stylesheet" href="css/common.css"></head><body class="bg-light p-5"><div class="container text-center"><p class="text-danger">' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . '</p><a href="index.php" class="btn btn-primary">Home</a></div></body></html>';
}
?>
