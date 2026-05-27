<?php 
  require_once(__DIR__ . '/../inc/db_config.php');
  require_once(__DIR__ . '/../inc/essentials.php');
  require_once(__DIR__ . '/../../inc/room_availability.php');
  date_default_timezone_set("Asia/Kathmandu");
  adminLogin();

  if(isset($_POST['get_bookings']))
  {
    $frm_data = filteration($_POST);

    $limit = 10;
    $page = isset($frm_data['page']) ? (int)$frm_data['page'] : 1;
    if($page < 1){
      $page = 1;
  }
    $start = ($page-1) * $limit;

    $query = "SELECT bo.*, bd.*, uc.name AS customer_name, uc.phonenum AS user_phone, r.name AS room_name_db, r.price AS room_price FROM `booking_order` bo
      LEFT JOIN `booking_details` bd ON bo.booking_id = bd.booking_id
      LEFT JOIN `user_cred` uc ON bo.user_id = uc.id
      LEFT JOIN `rooms` r ON bo.room_id = r.id
      WHERE ((bo.booking_status='booked')
      OR (bo.booking_status='completed')
      OR (bo.booking_status='cancelled' AND bo.refund=1)
      OR (bo.booking_status='payment failed')
      OR (bo.booking_status='payment_failed')
      OR (bo.booking_status='pending')) 
      AND (bo.order_id LIKE ? OR bd.phonenum LIKE ? OR bd.user_name LIKE ? OR uc.phonenum LIKE ? OR uc.name LIKE ? OR r.name LIKE ?) 
      ORDER BY bo.booking_id DESC";

$search = isset($frm_data['search']) ? $frm_data['search'] : '';
$search_term = "%{$search}%";
    $res = select($query,[$search_term,$search_term,$search_term,$search_term,$search_term,$search_term],'ssssss');
    
    $limit_query = $query ." LIMIT $start,$limit";
    $limit_res = select($limit_query,[$search_term,$search_term,$search_term,$search_term,$search_term,$search_term],'ssssss');

    $total_rows = mysqli_num_rows($res);

    if($total_rows==0){
      $output = json_encode(["table_data"=>"<b>No Data Found!</b>", "pagination"=>'']);
      echo $output;
      exit;
    }

    $i=$start+1;
    $table_data = "";

    while($data = mysqli_fetch_assoc($limit_res))
    {
        $date = !empty($data['datentime']) 
            ? date("d-m-Y", strtotime($data['datentime'])) 
            : 'N/A';
    
        $checkin = !empty($data['check_in']) 
            ? date("d-m-Y", strtotime($data['check_in'])) 
            : 'N/A';
    
        $checkout = !empty($data['check_out']) 
            ? date("d-m-Y", strtotime($data['check_out'])) 
            : 'N/A';
    
        $user_name = !empty($data['user_name']) 
            ? $data['user_name'] 
            : (!empty($data['customer_name']) ? $data['customer_name'] : 'N/A');
    
        $phonenum = !empty($data['phonenum']) 
            ? $data['phonenum'] 
            : (!empty($data['user_phone']) ? $data['user_phone'] : 'N/A');
    
        $room_name = !empty($data['room_name']) 
            ? $data['room_name'] 
            : (!empty($data['room_name_db']) ? $data['room_name_db'] : 'N/A');
    
        $price = !empty($data['price']) 
            ? 'NPR '.$data['price'] 
            : (!empty($data['room_price']) ? 'NPR '.$data['room_price'] : 'N/A');
    
        $amount = !empty($data['trans_amt']) 
            ? $data['trans_amt'] 
            : 'N/A';
    
        $currency = !empty($data['currency']) 
            ? $data['currency'] 
            : '';
    
        // STATUS BADGE
    
        $status = strtolower(trim($data['booking_status']));
    
        if ($status == 'booked') {
    
            $status_badge = "
                <span class='badge bg-success'>
                    Checked In
                </span>
            ";
    
        } 
        elseif ($status == 'completed') {
    
            $status_badge = "
                <span class='badge bg-secondary'>
                    Released
                </span>
            ";
    
        } 
        elseif ($status == 'cancelled') {
    
            $status_badge = "
                <span class='badge bg-danger'>
                    Cancelled
                </span>
            ";
    
        } 
        elseif ($status == 'pending') {
    
            $status_badge = "
                <span class='badge bg-warning text-dark'>
                    Pending
                </span>
            ";
    
        } 
        else {
    
            $status_badge = "
                <span class='badge bg-info text-dark'>
                    {$data['booking_status']}
                </span>
            ";
        }
    
        // RELEASE BUTTON
    
        $release_btn = "";
    
        if ($status == 'booked') {
    
            $release_btn = "
                <button 
                    type='button'
                    onclick='release_room({$data['booking_id']})'
                    class='btn btn-admin-icon btn-admin-icon--neutral shadow-none'
                    title='Release room — free slot for new bookings'>
    
                    <i class='bi bi-door-open' aria-hidden='true'></i>
    
                </button>
            ";
        }
    
        // TABLE ROW
    
        $table_data .= "
    
        <tr>
    
            <td>$i</td>
    
            <td>
    
                <span class='badge bg-primary'>
                    Order: {$data['order_id']}
                </span>
    
                <br>
    
                <b>Name:</b> {$user_name}
    
                <br>
    
                <b>Phone:</b> {$phonenum}
    
            </td>
    
            <td>
    
                <b>Room:</b> {$room_name}
    
                <br>
    
                <b>Price:</b> {$price}
    
            </td>
    
            <td>
    
                <b>Check-in:</b> {$checkin}
    
                <br>
    
                <b>Check-out:</b> {$checkout}
    
                <br>
    
                <b>Paid:</b> {$amount} {$currency}
    
                <br>
    
                <b>Booked:</b> {$date}
    
            </td>
    
            <td>
                {$status_badge}
            </td>
    
            <td>
    
                <div class='admin-action-group'>
    
                    <button 
                        type='button'
                        onclick='download({$data['booking_id']})'
                        class='btn btn-admin-icon btn-admin-icon--success shadow-none'
                        title='Download PDF'>
    
                        <i class='bi bi-download' aria-hidden='true'></i>
    
                    </button>
    
                    {$release_btn}
    
                </div>
    
            </td>
    
        </tr>
    
        ";
    
        $i++;
    }

    $pagination = "";

    if($total_rows>$limit)
    {
      $total_pages = ceil($total_rows/$limit); 

      if($page!=1){
        $pagination .="<li class='page-item'>
          <button onclick='change_page(1)' class='page-link shadow-none'>First</button>
        </li>";
      }

      $disabled = ($page==1) ? "disabled" : "";
      $prev= $page-1;
      $pagination .="<li class='page-item $disabled'>
        <button onclick='change_page($prev)' class='page-link shadow-none'>Prev</button>
      </li>";


      $disabled = ($page==$total_pages) ? "disabled" : "";
      $next = $page+1;
      $pagination .="<li class='page-item $disabled'>
        <button onclick='change_page($next)' class='page-link shadow-none'>Next</button>
      </li>";

      if($page!=$total_pages){
        $pagination .="<li class='page-item'>
          <button onclick='change_page($total_pages)' class='page-link shadow-none'>Last</button>
        </li>";
      }

    }

    $output = json_encode(["table_data"=>$table_data,"pagination"=>$pagination]);

    echo $output;
  }

  if (isset($_POST['release_booking']))
  {
    $frm_data = filteration($_POST);
    $booking_id = (int) $frm_data['booking_id'];

    $check = select(
      "SELECT booking_id, room_id, check_in, check_out, booking_status FROM booking_order
       WHERE booking_id=? LIMIT 1",
      [$booking_id], 'i'
    );

    if (mysqli_num_rows($check) === 0) {
      echo json_encode(['success' => false, 'message' => 'Booking not found.']);
      exit;
    }

    $booking_row = mysqli_fetch_assoc($check);
    $status = strtolower(trim($booking_row['booking_status'] ?? 'pending'));

    if (in_array($status, ['completed', 'cancelled'], true)) {
      echo json_encode(['success' => true, 'message' => 'Booking was already released.']);
      exit;
    }

    update("UPDATE booking_order SET booking_status='completed' WHERE booking_id=?", [$booking_id], 'i');

    cancelUnpaidBookingsForDateRange(
      (int) $booking_row['room_id'],
      $booking_row['check_in'],
      $booking_row['check_out'],
      $con
    );

    echo json_encode(['success' => true, 'message' => 'Booking released. Users can now book this room for those dates.']);
  }

?>