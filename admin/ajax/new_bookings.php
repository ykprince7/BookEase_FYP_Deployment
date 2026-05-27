<?php
require_once(__DIR__ . '/../inc/db_config.php');
require_once(__DIR__ . '/../inc/essentials.php');
require_once(__DIR__ . '/../../inc/room_availability.php');

date_default_timezone_set("Asia/Kathmandu");
adminLogin();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once(__DIR__ . '/../../vendor/autoload.php');

// ==================== MAILTRAP CONFIG ====================
function send_mail($to, $subject, $content) 
{
    $mail = new PHPMailer(true);
    
    try {
        $mail->isSMTP();
        $mail->Host       = 'sandbox.smtp.mailtrap.io';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'f6f8a44d849cf8';
        $mail->Password   = '5979edcf8bb21d';           // Your Mailtrap Password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 2525;

        $mail->setFrom('bookease.noreply69@gmail.com', 'BookEase');
        $mail->addAddress($to);

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $content;
        $mail->AltBody = strip_tags($content);

        return $mail->send();
    } catch (Exception $e) {
        error_log("Mail Error: " . $mail->ErrorInfo);
        return false;
    }
}
// ========================================================

// Get Bookings
if(isset($_POST['get_bookings']))
{
    $frm_data = filteration($_POST);

    $limit = 10;
    $page = (int)$frm_data['page'];
    $start = ($page - 1) * $limit;

    $query = "SELECT bo.*, bd.*, uc.name AS customer_name, uc.phonenum AS user_phone, 
                     r.name AS room_name_db, r.price AS room_price 
              FROM `booking_order` bo
              LEFT JOIN `booking_details` bd ON bo.booking_id = bd.booking_id
              LEFT JOIN `user_cred` uc ON bo.user_id = uc.id
              LEFT JOIN `rooms` r ON bo.room_id = r.id
              WHERE (bo.order_id LIKE ? OR bd.phonenum LIKE ? OR bd.user_name LIKE ? 
                 OR uc.phonenum LIKE ? OR uc.name LIKE ? OR r.name LIKE ?) 
              ORDER BY bo.booking_id DESC";

    $search_term = "%" . $frm_data['search'] . "%";
    
    $res = select($query, [$search_term, $search_term, $search_term, $search_term, $search_term, $search_term], 'ssssss');
    
    $limit_query = $query . " LIMIT $start, $limit";
    $limit_res = select($limit_query, [$search_term, $search_term, $search_term, $search_term, $search_term, $search_term], 'ssssss');

    $total_rows = mysqli_num_rows($res);

    if($total_rows == 0){
        echo json_encode(["table_data" => "<b>No Data Found!</b>", "pagination" => '']);
        exit;
    }

    $i = $start + 1;
    $table_data = "";

    while($data = mysqli_fetch_assoc($limit_res))
    {
        $date = date("d-m-Y", strtotime($data['datentime']));
        $checkin  = !empty($data['check_in'])  ? date("d-m-Y", strtotime($data['check_in']))  : 'N/A';
        $checkout = !empty($data['check_out']) ? date("d-m-Y", strtotime($data['check_out'])) : 'N/A';

        $user_name = $data['user_name'] ?? $data['customer_name'] ?? 'N/A';
        $phonenum  = $data['phonenum'] ?? $data['user_phone'] ?? 'N/A';
        $room_name = $data['room_name'] ?? $data['room_name_db'] ?? 'N/A';
        $price     = !empty($data['price']) ? 'NPR '.$data['price'] : (!empty($data['room_price']) ? 'NPR '.$data['room_price'] : 'N/A');

        $status_badge = match(strtolower($data['booking_status'] ?? '')) {
            'booked'     => "<span class='badge bg-success'>Booked</span>",
            'completed'  => "<span class='badge bg-secondary'>Completed</span>",
            'cancelled'  => "<span class='badge bg-danger'>Cancelled</span>",
            default      => "<span class='badge bg-warning'>".$data['booking_status']."</span>"
        };

        $release_btn = ($data['booking_status'] === 'booked') 
            ? "<button onclick='release_room({$data['booking_id']})' class='btn btn-sm btn-outline-success'>Release</button>" 
            : "";

        $table_data .= "
        <tr>
            <td>$i</td>
            <td>
                <span class='badge bg-primary'>#{$data['order_id']}</span><br>
                <b>{$user_name}</b><br>
                {$phonenum}
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
    $frm_data = filteration($_POST);
    $booking_id = (int)$frm_data['booking_id'];

    $update = update("UPDATE `booking_order` SET `booking_status`='completed' WHERE `booking_id`=?", [$booking_id], 'i');

    echo $update ? 1 : 0;
    exit;
}
?>