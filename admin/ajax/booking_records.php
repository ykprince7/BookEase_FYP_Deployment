<?php
ob_start();
require_once(__DIR__ . '/../inc/db_config.php');
require_once(__DIR__ . '/../inc/essentials.php');

date_default_timezone_set("Asia/Kathmandu");
adminLogin();

if (isset($_POST['get_bookings'])) {
    $frm_data = filteration($_POST);

    $limit = 10;
    $page  = max(1, (int)($frm_data['page'] ?? 1));
    $start = ($page - 1) * $limit;

    $search      = trim($frm_data['search'] ?? '');
    $search_term = "%{$search}%";

    // Count total rows first (no LIMIT)
    $count_query = "SELECT COUNT(*) as total FROM `booking_order` bo
                    LEFT JOIN `user_cred` uc ON bo.user_id = uc.id
                    LEFT JOIN `rooms` r ON bo.room_id = r.id
                    WHERE bo.booking_status IN ('booked','completed','pending','cancelled')
                    AND (bo.order_id LIKE ? OR uc.phonenum LIKE ? OR uc.name LIKE ? OR r.name LIKE ?)";

    $count_res = select($count_query, [$search_term, $search_term, $search_term, $search_term], 'ssss');
    $count_row = mysqli_fetch_assoc($count_res);
    $total_rows = (int)($count_row['total'] ?? 0);

    if ($total_rows == 0) {
        echo json_encode(["table_data" => "<tr><td colspan='6' class='text-center py-4'>No Data Found!</td></tr>", "pagination" => ""]);
        exit;
    }

    // Fetch paginated rows — no JOIN to booking_details to avoid broken JOIN
    $query = "SELECT bo.*, uc.name AS customer_name, uc.phonenum AS user_phone,
                     r.name AS room_name_db, r.price AS room_price
              FROM `booking_order` bo
              LEFT JOIN `user_cred` uc ON bo.user_id = uc.id
              LEFT JOIN `rooms` r ON bo.room_id = r.id
              WHERE bo.booking_status IN ('booked','completed','pending','cancelled')
              AND (bo.order_id LIKE ? OR uc.phonenum LIKE ? OR uc.name LIKE ? OR r.name LIKE ?)
              ORDER BY bo.booking_id DESC
              LIMIT ?, ?";

    $res = select($query, [$search_term, $search_term, $search_term, $search_term, $start, $limit], 'ssssii');

    if (!$res) {
        echo json_encode(["table_data" => "<tr><td colspan='6' class='text-center py-4'>Query failed.</td></tr>", "pagination" => ""]);
        exit;
    }

    $i          = $start + 1;
    $table_data = "";

    while ($data = mysqli_fetch_assoc($res)) {
        $checkin   = !empty($data['check_in'])  ? date("d-m-Y", strtotime($data['check_in']))  : 'N/A';
        $checkout  = !empty($data['check_out']) ? date("d-m-Y", strtotime($data['check_out'])) : 'N/A';
        $user_name = !empty($data['customer_name']) ? htmlspecialchars($data['customer_name']) : 'N/A';
        $phone     = !empty($data['user_phone'])    ? htmlspecialchars($data['user_phone'])     : 'N/A';
        $room_name = !empty($data['room_name_db'])  ? htmlspecialchars($data['room_name_db'])  : 'N/A';
        $price     = !empty($data['room_price'])    ? 'NPR ' . $data['room_price']             : 'N/A';
        $trans_amt = !empty($data['trans_amt'])     ? 'NPR ' . $data['trans_amt']              : 'N/A';

        $status = strtolower(trim($data['booking_status'] ?? ''));

        if ($status == 'booked') {
            $status_badge = "<span class='badge bg-success'>Booked</span>";
            $action_btn   = "<button onclick='release_room({$data['booking_id']})' class='btn btn-sm btn-outline-success'>Release</button>";
        } elseif ($status == 'completed') {
            $status_badge = "<span class='badge bg-secondary'>Released</span>";
            $action_btn   = "";
        } elseif ($status == 'pending') {
            $status_badge = "<span class='badge bg-warning text-dark'>Pending</span>";
            $action_btn   = "";
        } elseif ($status == 'cancelled') {
            $status_badge = "<span class='badge bg-danger'>Cancelled</span>";
            $action_btn   = "";
        } else {
            $status_badge = "<span class='badge bg-info'>" . htmlspecialchars($data['booking_status']) . "</span>";
            $action_btn   = "";
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
                <b>Out:</b> {$checkout}<br>
                <b>Paid:</b> {$trans_amt}
            </td>
            <td>{$status_badge}</td>
            <td>{$action_btn}</td>
        </tr>";

        $i++;
    }

    // Build pagination
    $total_pages = ceil($total_rows / $limit);
    $pagination  = "";
    if ($total_pages > 1) {
        for ($p = 1; $p <= $total_pages; $p++) {
            $active      = ($p == $page) ? "active" : "";
            $pagination .= "<li class='page-item {$active}'>
                <a class='page-link' href='#' onclick='get_bookings(document.getElementById(\"search_input\").value, {$p}); return false;'>{$p}</a>
            </li>";
        }
    }

    ob_clean();
    echo json_encode(["table_data" => $table_data, "pagination" => $pagination]);
    exit;
}

// Release Booking
if (isset($_POST['release_booking'])) {
    $booking_id = (int)($_POST['booking_id'] ?? 0);

    $check = select("SELECT booking_status FROM booking_order WHERE booking_id=?", [$booking_id], 'i');

    if (mysqli_num_rows($check) == 0) {
        ob_clean();
        echo json_encode(['success' => false, 'message' => 'Booking not found']);
        exit;
    }

    update("UPDATE booking_order SET booking_status='completed' WHERE booking_id=?", [$booking_id], 'i');
    ob_clean();
    echo json_encode(['success' => true, 'message' => 'Room released successfully!']);
    exit;
}
?>