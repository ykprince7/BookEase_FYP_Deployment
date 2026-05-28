<?php
require('admin/inc/db_config.php');
require('admin/inc/essentials.php');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// User auth check
userLogin();

require('admin/inc/mpdf/vendor/autoload.php'); // forward slashes

if(isset($_GET['gen_pdf']) && isset($_GET['id'])) {
    try {
        $bid = (int) $_GET['id']; // safe integer cast, no filteration needed

        $query = "SELECT bo.*, bd.*, uc.email 
                  FROM `booking_order` bo
                  INNER JOIN `booking_details` bd ON bo.booking_id = bd.booking_id
                  INNER JOIN `user_cred` uc ON bo.user_id = uc.id
                  WHERE ((bo.booking_status='booked' AND bo.arrival=1) 
                  OR (bo.booking_status='cancelled' AND bo.refund=1)
                  OR (bo.booking_status='payment failed')) 
                  AND bo.booking_id = ?
                  AND bo.user_id = ?";

        $res = select($query, [$bid, $_SESSION['uId']], 'ii');

        if(mysqli_num_rows($res) == 0) {
            header('Location: bookings.php');
            exit;
        }

        $data = mysqli_fetch_assoc($res);

        $date     = date("h:ia | d-m-Y", strtotime($data['datentime']));
        $checkin  = date("d-m-Y", strtotime($data['check_in']));
        $checkout = date("d-m-Y", strtotime($data['check_out']));

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
          .footer { text-align: center; margin-top: 30px; font-size: 12px; color: #7f8c8d; }
        </style>";

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
          <tr><td><strong>Status:</strong></td><td>{$data['booking_status']}</td></tr>
        </table>
        <table>
          <tr><th colspan='2'>Guest Information</th></tr>
          <tr><td><strong>Name:</strong></td><td>{$data['user_name']}</td></tr>
          <tr><td><strong>Email:</strong></td><td>{$data['email']}</td></tr>
          <tr><td><strong>Phone:</strong></td><td>{$data['phonenum']}</td></tr>
          <tr><td><strong>Address:</strong></td><td>{$data['address']}</td></tr>
        </table>
        <table>
          <tr><th colspan='2'>Room Details</th></tr>
          <tr><td><strong>Room Name:</strong></td><td>{$data['room_name']}</td></tr>
          <tr><td><strong>Rate:</strong></td><td>NPR {$data['price']} per night</td></tr>
          <tr><td><strong>Check-in:</strong></td><td>$checkin</td></tr>
          <tr><td><strong>Check-out:</strong></td><td>$checkout</td></tr>
        </table>";

        if($data['booking_status'] == 'cancelled') {
            $refund = $data['refund'] ? "Amount Refunded" : "Not Yet Refunded";
            $content .= "
            <table>
              <tr><th colspan='2'>Payment Information</th></tr>
              <tr><td><strong>Amount Paid:</strong></td><td>NPR {$data['trans_amt']}</td></tr>
              <tr><td><strong>Refund Status:</strong></td><td>$refund</td></tr>
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
        ]);

        $mpdf->SetTitle("Booking Receipt - " . $data['order_id']);
        $mpdf->WriteHTML($content);
        $mpdf->Output($data['order_id'] . '.pdf', 'D');

    } catch (Exception $e) {
        error_log("PDF Generation Error: " . $e->getMessage());
        header('Location: bookings.php');
        exit;
    }
} else {
    header('Location: bookings.php');
    exit;
}
?>