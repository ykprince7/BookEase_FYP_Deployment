<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once('/../inc/db_config.php');
require_once('/../inc/essentials.php');
require_once('/../../inc/room_availability.php');

date_default_timezone_set("Asia/Kathmandu");

adminLogin();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Check vendor autoload exists
$autoload = __DIR__ . '/../../vendor/autoload.php';

if (!file_exists($autoload)) {
    die("vendor/autoload.php not found");
}

require_once($autoload);

// ==================== MAILTRAP CONFIG ====================

function send_mail($to, $subject, $content)
{
    $mail = new PHPMailer(true);

    try {

        $mail->isSMTP();
        $mail->Host       = 'sandbox.smtp.mailtrap.io';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'f6f8a44d849cf8';
        $mail->Password   = '5979edcf8bb21d';
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


// ==================== GET BOOKINGS ====================

if(isset($_POST['get_bookings']))
{
    $frm_data = filteration($_POST);

    $limit = 10;

    $page = isset($frm_data['page']) ? (int)$frm_data['page'] : 1;

    if($page < 1){
        $page = 1;
    }

    $start = ($page - 1) * $limit;

    $search = isset($frm_data['search']) ? $frm_data['search'] : '';

    $search_term = "%".$search."%";

    $query = "SELECT 
                bo.*, 
                bd.*, 
                uc.name AS customer_name, 
                uc.phonenum AS user_phone,
                r.name AS room_name_db, 
                r.price AS room_price
              FROM `booking_order` bo
              LEFT JOIN `booking_details` bd 
                    ON bo.booking_id = bd.booking_id
              LEFT JOIN `user_cred` uc 
                    ON bo.user_id = uc.id
              LEFT JOIN `rooms` r 
                    ON bo.room_id = r.id
              WHERE (
                    bo.order_id LIKE ? 
                    OR bd.phonenum LIKE ? 
                    OR bd.user_name LIKE ?
                    OR uc.phonenum LIKE ?
                    OR uc.name LIKE ?
                    OR r.name LIKE ?
              )
              ORDER BY bo.booking_id DESC";

    $values = [
        $search_term,
        $search_term,
        $search_term,
        $search_term,
        $search_term,
        $search_term
    ];

    $res = select($query, $values, 'ssssss');

    if(!$res){
        die("Database query failed");
    }

    $total_rows = mysqli_num_rows($res);

    $limit_query = $query . " LIMIT $start, $limit";

    $limit_res = select($limit_query, $values, 'ssssss');

    if(!$limit_res){
        die("Limited query failed");
    }

    if($total_rows == 0)
    {
        echo json_encode([
            "table_data" => "<b>No Data Found!</b>",
            "pagination" => ""
        ]);
        exit;
    }

    $i = $start + 1;

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

        $user_name = isset($data['user_name'])
            ? $data['user_name']
            : (isset($data['customer_name']) ? $data['customer_name'] : 'N/A');

        $phonenum = isset($data['phonenum'])
            ? $data['phonenum']
            : (isset($data['user_phone']) ? $data['user_phone'] : 'N/A');

        $room_name = isset($data['room_name'])
            ? $data['room_name']
            : (isset($data['room_name_db']) ? $data['room_name_db'] : 'N/A');

        if(!empty($data['price'])){
            $price = "NPR ".$data['price'];
        }
        elseif(!empty($data['room_price'])){
            $price = "NPR ".$data['room_price'];
        }
        else{
            $price = "N/A";
        }

        // PHP 7 Compatible Status Badge

        $status = strtolower($data['booking_status']);

        if($status == 'booked'){
            $status_badge = "<span class='badge bg-success'>Booked</span>";
        }
        elseif($status == 'completed'){
            $status_badge = "<span class='badge bg-secondary'>Completed</span>";
        }
        elseif($status == 'cancelled'){
            $status_badge = "<span class='badge bg-danger'>Cancelled</span>";
        }
        else{
            $status_badge = "<span class='badge bg-warning'>{$data['booking_status']}</span>";
        }

        // Release Button

        $release_btn = "";

        if($data['booking_status'] == 'booked'){
            $release_btn = "
                <button 
                    onclick='release_room({$data['booking_id']})'
                    class='btn btn-sm btn-outline-success'>
                    Release
                </button>
            ";
        }

        $table_data .= "
        <tr>
            <td>$i</td>

            <td>
                <span class='badge bg-primary'>
                    #{$data['order_id']}
                </span><br>

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

            <td>
                {$status_badge}
            </td>

            <td>
                {$release_btn}
            </td>
        </tr>";

        $i++;
    }

    echo json_encode([
        "table_data" => $table_data,
        "pagination" => ""
    ]);

    exit;
}


// ==================== RELEASE BOOKING ====================

if(isset($_POST['release_booking']))
{
    $frm_data = filteration($_POST);

    $booking_id = (int)$frm_data['booking_id'];

    $update = update(
        "UPDATE `booking_order`
         SET `booking_status`='completed'
         WHERE `booking_id`=?",
        [$booking_id],
        'i'
    );

    echo $update ? 1 : 0;

    exit;
}

?>