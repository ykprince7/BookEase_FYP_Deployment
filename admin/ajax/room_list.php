<?php
require(__DIR__ .'../inc/db_config.php');
require(__DIR__ .'../inc/essentials.php');

if (isset($_GET['get_rooms_for_comparison'])) {
    $res = mysqli_query(
        $con,
        "SELECT * FROM `rooms` WHERE `status` = 1 AND `removed` = 0 ORDER BY `price` ASC, `id` ASC"
    );

    if (!$res || mysqli_num_rows($res) === 0) {
        echo "<p class='text-muted mb-0'>No rooms available for comparison.</p>";
        exit;
    }

    echo "<p class='small text-muted mb-3'>Select up to <strong>two</strong> rooms. Same names are distinguished by price, size, and room #.</p>";
    echo "<div class='row g-2'>";

    $i = 1;
    while ($row = mysqli_fetch_assoc($res)) {
        $rid = (int) $row['id'];
        $thumb = ROOMS_IMG_PATH . 'thumbnail.jpg';
        $img_q = mysqli_query(
            $con,
            "SELECT `image` FROM `room_images` WHERE `room_id` = '$rid' AND `thumb` = 1 LIMIT 1"
        );
        if ($img_q && mysqli_num_rows($img_q) > 0) {
            $img = mysqli_fetch_assoc($img_q);
            $thumb = ROOMS_IMG_PATH . $img['image'];
        }

        $name = htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8');
        $price = number_format((float) $row['price'], 0, '.', ',');
        $area = htmlspecialchars((string) $row['area'], ENT_QUOTES, 'UTF-8');
        $label = $name . ' · NPR ' . $price . ' · ' . $area . ' sq. ft. · #' . $rid;

        echo "
            <div class='col-md-6'>
                <div class='d-flex align-items-center border rounded p-2 h-100 bg-white compare-pick-row'>
                    <img src='" . htmlspecialchars($thumb, ENT_QUOTES, 'UTF-8') . "' alt='' class='rounded flex-shrink-0 compare-pick-thumb' width='56' height='56' style='object-fit:cover;'>
                    <div class='form-check ms-2 mb-0 flex-grow-1'>
                        <input type='checkbox' value='$rid' id='room-$i' class='form-check-input compare-room-checkbox shadow-none'>
                        <label class='form-check-label small' for='room-$i' style='cursor:pointer;'>$label</label>
                    </div>
                </div>
            </div>
        ";
        $i++;
    }

    echo '</div>';
}
