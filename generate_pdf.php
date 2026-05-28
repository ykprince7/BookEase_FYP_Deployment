<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require('admin/inc/db_config.php');
require('admin/inc/essentials.php');
require('admin/inc/mpdf/vendor/autoload.php');

var_dump($_SESSION['login']);
var_dump(function_exists('userLogin'));
exit;

  if(isset($_GET['gen_pdf']) && isset($_GET['id']))
  {
    try {
      $frm_data = filteration($_GET);

      $query = "SELECT bo.*, bd.*, uc.email, uc.name AS customer_name, uc.phonenum AS user_phone,
                       r.name AS room_name_db, r.price AS room_price
                FROM `booking_order` bo
                LEFT JOIN `booking_details` bd ON bo.booking_id = bd.booking_id
                INNER JOIN `user_cred` uc ON bo.user_id = uc.id
                LEFT JOIN `rooms` r ON bo.room_id = r.id
                WHERE bo.booking_status IN ('booked', 'completed', 'cancelled', 'pending', 'payment failed', 'payment_failed')
                AND bo.booking_id = ?";

      $res = select($query, [$frm_data['id']], 'i');
      $total_rows = mysqli_num_rows($res);

      if($total_rows == 0){
        header('location: bookings.php');
        exit;
      }

      $data = mysqli_fetch_assoc($res);

      $date     = date("h:ia | d-m-Y", strtotime($data['datentime']));
      $checkin  = date("d-m-Y", strtotime($data['check_in']));
      $checkout = date("d-m-Y", strtotime($data['check_out']));

      $guest_name    = !empty($data['user_name'])  ? $data['user_name']   : (!empty($data['customer_name']) ? $data['customer_name'] : 'N/A');
      $guest_phone   = !empty($data['phonenum'])   ? $data['phonenum']    : (!empty($data['user_phone'])    ? $data['user_phone']    : 'N/A');
      $guest_address = !empty($data['address'])    ? $data['address']     : 'N/A';
      $room_name     = !empty($data['room_name'])  ? $data['room_name']   : (!empty($data['room_name_db'])  ? $data['room_name_db']  : 'N/A');
      $room_price    = !empty($data['price'])      ? $data['price']       : (!empty($data['room_price'])    ? $data['room_price']    : 'N/A');

      $styles = "
        <style>
          body { font-family: Arial, sans-serif; }
          .header { text-align: center; margin-bottom: 20px; }
          .header h1 { color: #2c3e50; margin: 0; }
          .header p { color: #7f8c8d; margin: 5px 0; }
          table { width: 100%; border-collapse: collapse; margin: 20px 0; }
          th, td { padding: 12px; border: 1px solid #ddd; }
          th { background-color: #f8f9fa; text-align: left; }
          .status { font-weight: bold; }
          .status.booked { color: #27ae60; }
          .status.cancelled { color: #e74c3c; }
          .status.failed, .status.payment_failed { color: #f39c12; }
          .footer { text-align: center; margin-top: 30px; font-size: 12px; color: #7f8c8d; }
        </style>
      ";

      $content = "
        $styles
        <div class='header'>
          <h1>BookEase</h1>
          <p>Booking Receipt</p>
        </div>

        <table>
          <tr><th colspan='2'>Booking Information</th></tr>
          <tr><td><strong>Order ID:</strong></td><td>{$data['order_id']}</td></tr>
          <tr><td><strong>Booking Date:</strong></td><td>$date</td></tr>
          <tr><td><strong>Status:</strong></td><td class='status " . strtolower($data['booking_status']) . "'>{$data['booking_status']}</td></tr>
        </table>

        <table>
          <tr><th colspan='2'>Guest Information</th></tr>
          <tr><td><strong>Name:</strong></td><td>$guest_name</td></tr>
          <tr><td><strong>Email:</strong></td><td>{$data['email']}</td></tr>
          <tr><td><strong>Phone:</strong></td><td>$guest_phone</td></tr>
          <tr><td><strong>Address:</strong></td><td>$guest_address</td></tr>
        </table>

        <table>
          <tr><th colspan='2'>Room Details</th></tr>
          <tr><td><strong>Room Name:</strong></td><td>$room_name</td></tr>
          <tr><td><strong>Rate:</strong></td><td>NPR $room_price per night</td></tr>
          <tr><td><strong>Check-in:</strong></td><td>$checkin</td></tr>
          <tr><td><strong>Check-out:</strong></td><td>$checkout</td></tr>
        </table>";

      if($data['booking_status'] == 'cancelled'){
        $refund = ($data['refund']) ? "Amount Refunded" : "Not Yet Refunded";
        $content .= "
          <table>
            <tr><th colspan='2'>Payment Information</th></tr>
            <tr><td><strong>Amount Paid:</strong></td><td>NPR {$data['trans_amt']}</td></tr>
            <tr><td><strong>Refund Status:</strong></td><td>$refund</td></tr>
          </table>";
      } else if($data['booking_status'] == 'payment failed' || $data['booking_status'] == 'payment_failed'){
        $content .= "
          <table>
            <tr><th colspan='2'>Payment Information</th></tr>
            <tr><td><strong>Transaction Amount:</strong></td><td>NPR {$data['trans_amt']}</td></tr>
            <tr><td><strong>Failure Response:</strong></td><td>{$data['trans_resp_msg']}</td></tr>
          </table>";
      } else {
        $content .= "
          <table>
            <tr><th colspan='2'>Payment Information</th></tr>
            <tr><td><strong>Amount Paid:</strong></td><td>{$data['trans_amt']} {$data['currency']}</td></tr>
          </table>";
      }

      $content .= "
        <div class='footer'>
          <p>Thank you for choosing BookEase</p>
          <p>This is a computer-generated receipt and does not require a signature</p>
        </div>";

      $mpdf = new \Mpdf\Mpdf([
        'margin_left'   => 15,
        'margin_right'  => 15,
        'margin_top'    => 15,
        'margin_bottom' => 15,
        'margin_header' => 10,
        'margin_footer' => 10
      ]);

      $mpdf->SetTitle("Booking Receipt - " . $data['order_id']);
      $mpdf->WriteHTML($content);
      $mpdf->Output($data['order_id'] . '.pdf', 'D');

    } catch (Exception $e) {
      error_log("PDF Generation Error: " . $e->getMessage());
      header('location: bookings.php');
      exit;
    }
  } else {
    header('location: bookings.php');
  }
?>