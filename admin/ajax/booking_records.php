<?php 
ob_start();
require_once('/../inc/db_config.php');
require_once('/../inc/essentials.php');
require_once('/../../inc/room_availability.php');

date_default_timezone_set("Asia/Kathmandu");
adminLogin();

if(isset($_POST['get_bookings']))
{
    $frm_data = filteration($_POST);

    $limit = 10;
    $page = max(1, (int)($frm_data['page'] ?? 1));
    $start = ($page - 1) * $limit;

    $search = trim($frm_data['search'] ?? '');
    $search_term = "%{$search}%";

    $query = "SELECT bo.*, bd.*, uc.name AS customer_name, uc.phonenum AS user_phone, 
                     r.name AS room_name_db, r.price AS room_price 
              FROM `booking_order` bo
              LEFT JOIN `booking_details` bd ON bo.booking_id = bd.booking_id
              LEFT JOIN `user_cred` uc ON bo.user_id = uc.id
              LEFT JOIN `rooms` r ON bo.room_id = r.id
              WHERE bo.booking_status IN ('booked', 'completed', 'pending', 'cancelled')
              AND (bo.order_id LIKE ? OR bd.phonenum LIKE ? OR bd.user_name LIKE ? 
                   OR uc.phonenum LIKE ? OR uc.name LIKE ? OR r.name LIKE ?) 
              ORDER BY bo.booking_id DESC";

    $values = [$search_term, $search_term, $search_term, $search_term, $search_term, $search_term];

    $res = select($query, $values, 'ssssss');
    $total_rows = mysqli_num_rows($res);

    if($total_rows == 0){
        echo json_encode(["table_data" => "<tr><td colspan='6' class='text-center'>No Data Found!</td></tr>", "pagination" => ""]);
        exit;
    }

    $limit_query = $query . " LIMIT ?, ?";
    $limit_res = select($limit_query, array_merge($values, [$start, $limit]), 'ssssssii');

    $i = $start + 1;
    $table_data = "";

    while($data = mysqli_fetch_assoc($limit_res))
    {
        $checkin   = !empty($data['check_in']) ? date("d-m-Y", strtotime($data['check_in'])) : 'N/A';
        $checkout  = !empty($data['check_out']) ? date("d-m-Y", strtotime($data['check_out'])) : 'N/A';

        $user_name = $data['user_name'] ?? $data['customer_name'] ?? 'N/A';
        $phone     = $data['phonenum'] ?? $data['user_phone'] ?? 'N/A';
        $room_name = $data['room_name'] ?? $data['room_name_db'] ?? 'N/A';
        $price     = !empty($data['price']) ? 'NPR '.$data['price'] : 
                     (!empty($data['room_price']) ? 'NPR '.$data['room_price'] : 'N/A');

        $status = strtolower(trim($data['booking_status'] ?? ''));

        if($status == 'booked'){
            $status_badge = "<span class='badge bg-success'>Booked</span>";
            $release_btn  = "<button onclick='release_room({$data['booking_id']})' class='btn btn-sm btn-outline-success'>Release</button>";
        } elseif($status == 'completed'){
            $status_badge = "<span class='badge bg-secondary'>Released</span>";
            $release_btn  = "";
        } elseif($status == 'pending'){
            $status_badge = "<span class='badge bg-warning text-dark'>Pending</span>";
            $release_btn  = "";
        } else {
            $status_badge = "<span class='badge bg-info'>".htmlspecialchars($data['booking_status'])."</span>";
            $release_btn  = "";
        }

        $table_data .= "
        <tr>
            <td>{$i}</td>
            <td>
                <span class='badge bg-primary'>#{$data['order_id']}</span><br>
                <b>{$user_name}</b><br>
                {$phone}
            </td>
            <td>
                <b>{$room_name}</b><br>
                {$price}
            </td>
            <td>
                <b>In:</b> {$checkin}<br>
                <b>Out:</b> {$checkout}
            </td>
            <td>{$status_badge}</td>
            <td>{$release_btn}</td>
        </tr>";

        $i++;
    }

    echo json_encode(["table_data" => $table_data, "pagination" => ""]);
    exit;
}

// Release Booking
if(isset($_POST['release_booking']))
{
    $booking_id = (int)($_POST['booking_id'] ?? 0);

    $check = select("SELECT booking_status FROM booking_order WHERE booking_id=?", [$booking_id], 'i');

    if(mysqli_num_rows($check) == 0){
        echo json_encode(['success' => false, 'message' => 'Booking not found']);
        exit;
    }

    update("UPDATE booking_order SET booking_status='completed' WHERE booking_id=?", [$booking_id], 'i');
    ob_clean(); // Discard any junk output before sending JSON
    echo json_encode(['success' => true, 'message' => 'Room released successfully!']);
    exit;
}
?>