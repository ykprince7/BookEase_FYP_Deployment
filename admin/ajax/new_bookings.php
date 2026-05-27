<?php
require(__DIR__ . '/../inc/essentials.php');
require(__DIR__ . '/../inc/db_config.php');
adminLogin();

header('Content-Type: application/json');

// GET BOOKINGS
if (isset($_POST['get_bookings'])) {
    $search = isset($_POST['search']) ? trim($_POST['search']) : '';
    $search = mysqli_real_escape_string($con, $search);

    $query = "SELECT * FROM bookings WHERE status = 'new'";
    if ($search !== '') {
        $query .= " AND (user_name LIKE '%$search%' OR room_type LIKE '%$search%')";
    }

    $result = mysqli_query($con, $query);
    $html = '';
    $i = 1;

    while ($row = mysqli_fetch_assoc($result)) {
        $html .= "<tr>
            <td>{$i}</td>
            <td>{$row['user_name']}</td>
            <td>{$row['room_type']}</td>
            <td>{$row['booking_date']}</td>
            <td>
                <button onclick=\"assign_room({$row['id']})\" 
                    class='btn btn-sm btn-primary' 
                    data-bs-toggle='modal' 
                    data-bs-target='#assign-room'>Assign Room</button>
                <button onclick=\"cancel_booking({$row['id']})\" 
                    class='btn btn-sm btn-danger'>Cancel</button>
            </td>
        </tr>";
        $i++;
    }

    if ($html === '') {
        $html = "<tr><td colspan='5' class='text-center py-4'>No Data Found!</td></tr>";
    }

    echo json_encode(['table_data' => $html]);
    exit;
}

// ASSIGN ROOM
if (isset($_POST['assign_room'])) {
    $room_no    = mysqli_real_escape_string($con, $_POST['room_no']);
    $booking_id = mysqli_real_escape_string($con, $_POST['booking_id']);

    $query = "UPDATE bookings SET room_no = '$room_no', status = 'assigned' WHERE id = '$booking_id'";
    $result = mysqli_query($con, $query);

    echo $result ? "1" : "0";
    exit;
}

// CANCEL BOOKING
if (isset($_POST['cancel_booking'])) {
    $booking_id = mysqli_real_escape_string($con, $_POST['booking_id']);

    $query = "UPDATE bookings SET status = 'cancelled' WHERE id = '$booking_id'";
    $result = mysqli_query($con, $query);

    echo $result ? "1" : "0";
    exit;
}

echo json_encode(['error' => 'Invalid request']);
exit;