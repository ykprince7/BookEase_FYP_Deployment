<?php 
require_once('admin/inc/db_config.php');
require_once('admin/inc/essentials.php');
require_once('inc/currency_helpers.php');
require_once('inc/loyalty_points.php');
require_once('inc/room_availability.php');

// Include Stripe's PHP library
require_once('vendor/autoload.php');

date_default_timezone_set("Asia/Kathmandu");

session_start();

if (!(isset($_SESSION['login']) && $_SESSION['login'] == true)) {
  redirect('index.php');
}

\Stripe\Stripe::setApiKey('sk_test_51TDMJa4Gjr7oYQNHK8x9wqD6bGFms0RuSXhkrup4w9RqPeOJunbUWMkyadf1NinQNGNLZl7B8bQzXpz8Sl3cL9rC005ENdyRf2');

if (isset($_POST['pay_now'])) {
  $payment_method = $_POST['payment_method']; // Get selected payment method
  $CUST_ID = $_SESSION['uId'];
  $selected_currency = $_SESSION['room']['currency'];
  $base_currency = 'NPR';
  $ORDER_ID = 'ORD_' . $_SESSION['uId'] . random_int(11111, 9999999);

  $paramList = filteration($_POST);
  if (preg_match('/^[0-9]{10}$/', $paramList['phonenum']) !== 1) {
    redirect('confirm_booking.php?id=' . $_SESSION['room']['id'] . '&currency=' . $selected_currency . '&error=invalid_phone');
  }

  $room_id = (int) $_SESSION['room']['id'];
  $availability = checkRoomDateAvailability(
    $room_id,
    $paramList['checkin'],
    $paramList['checkout'],
    $con
  );
  if (!$availability['available']) {
    redirect('confirm_booking.php?id=' . $room_id . '&currency=' . $selected_currency . '&error=fully_booked');
  }

  // Validate payment method (both available for every currency)
  if (!in_array($payment_method, ['stripe', 'eSewa'], true)) {
    redirect('confirm_booking.php?id=' . $_SESSION['room']['id'] . '&currency=' . $selected_currency . '&error=invalid_payment');
  }

  // Total stay amount in NPR (from session, set when dates were confirmed)
  $npr_total = (float) $_SESSION['room']['payment'];

  // Store loyalty reward info for later redemption (after successful payment)
  if (!empty($_POST['loyalty_reward'])) {
    $reward_id = (int) $_POST['loyalty_reward'];
    if ($reward_id > 0) {
      // Get reward details without redeeming yet
      $query = "SELECT * FROM loyalty_rewards WHERE id = ? AND is_active = 1";
      $reward_result = select($query, [$reward_id], 'i');
      
      if ($reward = mysqli_fetch_assoc($reward_result)) {
        // Check if user has enough points (but don't deduct yet)
        $current_points = getLoyaltyPointsBalance($CUST_ID);
        if ($current_points >= $reward['points_required']) {
          // Store reward info in session for later redemption
          $_SESSION['pending_loyalty_reward'] = [
            'reward_id' => $reward_id,
            'points_required' => $reward['points_required'],
            'discount_percent' => (float) $reward['discount_percent'],
            'reward_name' => $reward['name']
          ];
          
          // Calculate discounted amount for payment
          $d = (float) $reward['discount_percent'];
          if ($d > 0 && $d <= 100) {
            $npr_total = round($npr_total * (1 - $d / 100), 2);
            if ($npr_total < 0) {
              $npr_total = 0;
            }
          }
          
          $_SESSION['loyalty_redeem_notice'] =
            'You will use ' . $reward['points_required'] . ' loyalty points on "' . $reward['name'] . '" for a ' . rtrim(rtrim(number_format($d, 2, '.', ''), '0'), '.') . '% discount after successful payment.';
        } else {
          redirect('confirm_booking.php?id=' . $_SESSION['room']['id'] . '&currency=' . $selected_currency . '&error=insufficient_points');
        }
      } else {
        redirect('confirm_booking.php?id=' . $_SESSION['room']['id'] . '&currency=' . $selected_currency . '&error=invalid_reward');
      }
    }
  }
  // Handle voucher code (alternative to loyalty rewards)
  elseif (!empty($_POST['voucher_code'])) {
    $voucher_code = trim(strtoupper($_POST['voucher_code']));
    $voucher = validateVoucher($voucher_code, $CUST_ID);
    
    if ($voucher) {
      // Store voucher info for later use (after successful payment)
      $_SESSION['pending_voucher'] = [
        'voucher_code' => $voucher_code,
        'discount_percent' => (float) $voucher['discount_percent'],
        'reward_name' => $voucher['reward_name']
      ];
      
      // Calculate discounted amount for payment
      $d = (float) $voucher['discount_percent'];
      if ($d > 0 && $d <= 100) {
        $npr_total = round($npr_total * (1 - $d / 100), 2);
        if ($npr_total < 0) {
          $npr_total = 0;
        }
      }
      
      $_SESSION['voucher_notice'] =
        'You will use voucher "' . $voucher_code . '" for a ' . rtrim(rtrim(number_format($d, 2, '.', ''), '0'), '.') . '% discount.';
    } else {
      redirect('confirm_booking.php?id=' . $_SESSION['room']['id'] . '&currency=' . $selected_currency . '&error=invalid_voucher');
    }
  } else {
    unset($_SESSION['pending_loyalty_reward']);
    unset($_SESSION['loyalty_redeem_notice']);
  }

  // Calculate charge amount in the currency used for payment
  if ($payment_method === 'stripe') {
    $final_amount = (float) convertCurrency($npr_total, $base_currency, $selected_currency, $con);
    $final_currency = $selected_currency;
  } else {
    $final_amount = $npr_total;
    $final_currency = 'NPR';
  }

  if ($final_amount <= 0) {
    redirect('confirm_booking.php?id=' . $_SESSION['room']['id'] . '&currency=' . $selected_currency . '&error=invalid_amount');
  }

  $query1 = "INSERT INTO `booking_order` (`user_id`, `room_id`, `check_in`, `check_out`, `order_id`, `trans_amt`, `currency`) VALUES (?,?,?,?,?,?,?)";
  insert($query1, [$CUST_ID, $_SESSION['room']['id'], $paramList['checkin'], $paramList['checkout'], $ORDER_ID, $final_amount, $final_currency], 'issssds');
  
  $booking_id = mysqli_insert_id($con);

  $query2 = "INSERT INTO `booking_details` (`booking_id`, `room_name`, `price`, `total_pay`, `user_name`, `phonenum`, `address`) VALUES (?,?,?,?,?,?,?)";
  insert($query2, [$booking_id, $_SESSION['room']['name'], $_SESSION['room']['price'], $final_amount, $paramList['name'], $paramList['phonenum'], $paramList['address']], 'issssss');

  // Note: Loyalty points will be earned only after successful payment confirmation

  if ($payment_method === 'stripe') {
    // Stripe Payment
    $converted_amount = $final_amount;
    $unit_cents = (int) round($converted_amount * 100);
    if ($unit_cents < 1) {
      $unit_cents = 1;
    }
    $checkoutSession = \Stripe\Checkout\Session::create([
      'payment_method_types' => ['card'],
      'line_items' => [
        [
          'price_data' => [
            'currency' => strtolower($selected_currency),
            'product_data' => [
              'name' => $_SESSION['room']['name'],
            ],
            'unit_amount' => $unit_cents,
          ],
          'quantity' => 1,
        ],
      ],
      'mode' => 'payment',
      'client_reference_id' => $ORDER_ID,
      'success_url' => 'http://localhost/BookEase/pay_response.php?session_id={CHECKOUT_SESSION_ID}',
      'cancel_url' => 'http://localhost/BookEase/pay_status.php?status=failed',
    ]);

    header("Location: " . $checkoutSession->url);
    exit();
  } elseif ($payment_method === 'eSewa') {
    // eSewa Payment (amount in NPR)
    header("Location: esewa_payment.php?order_id=$ORDER_ID&amount=" . urlencode((string) $final_amount));
    exit();
  }
}
?>
