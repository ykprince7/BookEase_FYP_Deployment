<?php 
  require(__DIR__ .'../inc/db_config.php');
  require(__DIR__ .'../inc/essentials.php');
  adminLogin();

  if(isset($_POST['add_room']))
  {
    $features = filteration(json_decode($_POST['features']));
    $facilities = filteration(json_decode($_POST['facilities']));

    $frm_data = filteration($_POST);
    $flag = 0;

    $q1 = "INSERT INTO rooms (name, area, price, quantity, adult, children, description) VALUES (?,?,?,?,?,?,?)";
    $values = [$frm_data['name'],$frm_data['area'],$frm_data['price'],$frm_data['quantity'],$frm_data['adult'],$frm_data['children'],$frm_data['desc']];

    if(insert($q1,$values,'siiiiis')){
      $flag = 1;
    }
    
    $room_id = mysqli_insert_id($con);

    $q2 = "INSERT INTO room_facilities(room_id, facilities_id) VALUES (?,?)";
    if($stmt = mysqli_prepare($con,$q2))
    {
      foreach($facilities as $f){
        mysqli_stmt_bind_param($stmt,'ii',$room_id,$f);
        mysqli_stmt_execute($stmt);
      }
      mysqli_stmt_close($stmt);
    }
    else{
      $flag = 0;
      die('query cannot be prepared - insert');
    }

    $q3 = "INSERT INTO room_features(room_id, features_id) VALUES (?,?)";
    if($stmt = mysqli_prepare($con,$q3))
    {
      foreach($features as $f){
        mysqli_stmt_bind_param($stmt,'ii',$room_id,$f);
        mysqli_stmt_execute($stmt);
      }
      mysqli_stmt_close($stmt);
    }
    else{
      $flag = 0;
      die('query cannot be prepared - insert');
    }
    
    if($flag){
      echo 1;
    }
    else{
      echo 0;
    }
  }

  if(isset($_POST['get_all_rooms']))
  {
    $res = select("SELECT * FROM rooms WHERE removed=?",[0],'i');
    $i=1;

    $data = "";

    while($row = mysqli_fetch_assoc($res))
    {
      $room_name_attr = htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8');

      if($row['status']==1){
        $status = "<button type='button' onclick='toggle_status($row[id],0)' class='btn btn-status-toggle btn-status-toggle--on shadow-none'>Active</button>";
      }
      else{
        $status = "<button type='button' onclick='toggle_status($row[id],1)' class='btn btn-status-toggle btn-status-toggle--off shadow-none'>Inactive</button>";
      }

      $data.=" 
        <tr class='align-middle'>
          <td>$i</td>
          <td>$row[name]</td>
          <td>$row[area] sq. ft.</td>
          <td>
            <span class='badge rounded-pill bg-light text-dark'>
              Adult: $row[adult]
            </span><br>
            <span class='badge rounded-pill bg-light text-dark'>
              Children: $row[children]
            </span>
          </td>
          <td>NPR$row[price]</td>
          <td>
            <div class='d-flex align-items-center justify-content-center gap-1 flex-wrap'>
              <button type='button' class='btn btn-sm btn-light border shadow-none px-2' onclick='change_room_limit($row[id], -1)' title='Decrease limit' aria-label='Decrease booking limit'>−</button>
              <input type='number' min='1' max='99' class='form-control form-control-sm shadow-none text-center room-limit-input' style='width:4rem;' id='room-limit-$row[id]' value='$row[quantity]' data-room-id='$row[id]' onchange='save_room_limit($row[id], this.value)'>
              <button type='button' class='btn btn-sm btn-light border shadow-none px-2' onclick='change_room_limit($row[id], 1)' title='Increase limit' aria-label='Increase booking limit'>+</button>
            </div>
          </td>
          <td>$status</td>
          <td>
            <div class='admin-action-group'>
              <button type='button' onclick='edit_details($row[id])' class='btn btn-admin-icon btn-admin-icon--edit shadow-none' data-bs-toggle='modal' data-bs-target='#edit-room' title='Edit room'>
                <i class='bi bi-pencil' aria-hidden='true'></i>
              </button>
              <button type='button' onclick=\"room_images($row[id],'$row[name]')\" class='btn btn-admin-icon btn-admin-icon--media shadow-none' data-bs-toggle='modal' data-bs-target='#room-images' title='Manage photos'>
                <i class='bi bi-images' aria-hidden='true'></i>
              </button>
              <button type='button' class='btn btn-admin-icon btn-admin-icon--neutral shadow-none release-bookings-btn' data-bs-toggle='modal' data-bs-target='#room-bookings-modal' data-room-id='$row[id]' data-room-name='$room_name_attr' title='View &amp; release bookings'>
                <i class='bi bi-door-open' aria-hidden='true'></i>
              </button>
              <button type='button' onclick=\"view360Images($row[id],'$row[name]')\" class='btn btn-admin-icon btn-admin-icon--360 shadow-none' data-bs-toggle='modal' data-bs-target='#room-360-images' title='360° tour'>
                <i class='bi bi-arrows-fullscreen' aria-hidden='true'></i>
              </button>
              <button type='button' onclick='remove_room($row[id])' class='btn btn-admin-icon btn-admin-icon--danger shadow-none' title='Remove room'>
                <i class='bi bi-trash3' aria-hidden='true'></i>
              </button>
            </div>
          </td>
        </tr>
      ";
      $i++;
    }

    echo $data;
  }

  if(isset($_POST['get_room']))
  {
    $frm_data = filteration($_POST);

    $res1 = select("SELECT * FROM rooms WHERE id=?",[$frm_data['get_room']],'i');
    $res2 = select("SELECT * FROM room_features WHERE room_id=?",[$frm_data['get_room']],'i');
    $res3 = select("SELECT * FROM room_facilities WHERE room_id=?",[$frm_data['get_room']],'i');

    $roomdata = mysqli_fetch_assoc($res1);
    $features = [];
    $facilities = [];

    if(mysqli_num_rows($res2)>0)
    {
      while($row = mysqli_fetch_assoc($res2)){
        array_push($features,$row['features_id']);
      }
    }

    if(mysqli_num_rows($res3)>0)
    {
      while($row = mysqli_fetch_assoc($res3)){
        array_push($facilities,$row['facilities_id']);
      }
    }

    $data = ["roomdata" => $roomdata, "features" => $features, "facilities" => $facilities];
    
    $data = json_encode($data);

    echo $data;
  }

  if(isset($_POST['edit_room']))
  {
    $features = filteration(json_decode($_POST['features']));
    $facilities = filteration(json_decode($_POST['facilities']));

    $frm_data = filteration($_POST);
    $flag = 0;

    $q1 = "UPDATE rooms SET name=?,area=?,price=?,quantity=?,
      adult=?,children=?,description=? WHERE id=?";
    $values = [$frm_data['name'],$frm_data['area'],$frm_data['price'],$frm_data['quantity'],$frm_data['adult'],$frm_data['children'],$frm_data['desc'],$frm_data['room_id']];
    
    if(update($q1,$values,'siiiiisi')){
      $flag = 1;
    }

    $del_features = delete("DELETE FROM room_features WHERE room_id=?", [$frm_data['room_id']],'i');
    $del_facilities = delete("DELETE FROM room_facilities WHERE room_id=?", [$frm_data['room_id']],'i');

    if(!($del_facilities && $del_features)){
      $flag = 0;
    }

    $q2 = "INSERT INTO room_facilities(room_id, facilities_id) VALUES (?,?)";
    if($stmt = mysqli_prepare($con,$q2))
    {
      foreach($facilities as $f){
        mysqli_stmt_bind_param($stmt,'ii',$frm_data['room_id'],$f);
        mysqli_stmt_execute($stmt);
      }
      $flag = 1;
      mysqli_stmt_close($stmt);
    }
    else{
      $flag = 0;
      die('query cannot be prepared - insert');
    }

    $q3 = "INSERT INTO room_features(room_id, features_id) VALUES (?,?)";
    if($stmt = mysqli_prepare($con,$q3))
    {
      foreach($features as $f){
        mysqli_stmt_bind_param($stmt,'ii',$frm_data['room_id'],$f);
        mysqli_stmt_execute($stmt);
      }
      $flag = 1;
      mysqli_stmt_close($stmt);
    }
    else{
      $flag = 0;
      die('query cannot be prepared - insert');
    }
    
    if($flag){
      echo 1;
    }
    else{
      echo 0;
    }
  }

  if (isset($_POST['update_room_limit'])) {
    $frm_data = filteration($_POST);
    $room_id  = (int) ($frm_data['room_id'] ?? 0);
    $quantity = max(1, min(99, (int) ($frm_data['quantity'] ?? 1)));

    if ($room_id <= 0) {
      echo json_encode(['success' => false, 'message' => 'Invalid room.']);
      exit;
    }

    $res = update("UPDATE `rooms` SET `quantity`=? WHERE `id`=? AND `removed`=0", [$quantity, $room_id], 'ii');

    if ($res !== false && $res >= 0) {
      echo json_encode(['success' => true, 'quantity' => $quantity, 'message' => 'Booking limit updated.']);
    } else {
      echo json_encode(['success' => false, 'message' => 'Update failed.']);
    }
    exit;
  }

  if(isset($_POST['toggle_status']))
  {
    $frm_data = filteration($_POST);

    $q = "UPDATE rooms SET status=? WHERE id=?";
    $v = [$frm_data['value'],$frm_data['toggle_status']];

    if(update($q,$v,'ii')){
      echo 1;
    }
    else{
      echo 0;
    }
  }

  if(isset($_POST['add_image']))
  {
    $frm_data = filteration($_POST);

    $img_r = uploadImage($_FILES['image'],ROOMS_FOLDER);

    if($img_r == 'inv_img'){
      echo $img_r;
    }
    else if($img_r == 'inv_size'){
      echo $img_r;
    }
    else if($img_r == 'upd_failed'){
      echo $img_r;
    }
    else{
      $q = "INSERT INTO room_images(room_id, image) VALUES (?,?)";
      $values = [$frm_data['room_id'],$img_r];
      $res = insert($q,$values,'is');
      echo $res;
    }
  }

  if(isset($_POST['get_room_images']))
  {
    $frm_data = filteration($_POST);
    $res = select("SELECT * FROM room_images WHERE room_id=?",[$frm_data['get_room_images']],'i');

    $path = ROOMS_IMG_PATH;

    while($row = mysqli_fetch_assoc($res))
    {
      if($row['thumb']==1){
        $thumb_btn = "<i class='bi bi-check-lg text-light bg-success px-2 py-1 rounded fs-5'></i>";
      }
      else{
        $thumb_btn = "<button type='button' onclick='thumb_image($row[sr_no],$row[room_id])' class='btn btn-admin-icon btn-admin-icon--neutral shadow-none' title='Set as thumbnail'>
          <i class='bi bi-image' aria-hidden='true'></i>
        </button>";
      }

      echo<<<data
        <tr class='align-middle'>
          <td><img src='$path$row[image]' class='img-fluid'></td>
          <td>$thumb_btn</td>
          <td>
            <button type='button' onclick='rem_image($row[sr_no],$row[room_id])' class='btn btn-admin-icon btn-admin-icon--danger shadow-none' title='Delete image'>
              <i class='bi bi-trash3' aria-hidden='true'></i>
            </button>
          </td>
        </tr>
      data;
    }
  }

  if(isset($_POST['rem_image']))
  {
    $frm_data = filteration($_POST);

    $values = [$frm_data['image_id'],$frm_data['room_id']];

    $pre_q = "SELECT * FROM room_images WHERE sr_no=? AND room_id=?";
    $res = select($pre_q,$values,'ii');
    $img = mysqli_fetch_assoc($res);

    if(deleteImage($img['image'],ROOMS_FOLDER)){
      $q = "DELETE FROM room_images WHERE sr_no=? AND room_id=?";
      $res = delete($q,$values,'ii');
      echo $res;
    }
    else{
      echo 0;
    }
  }

  if(isset($_POST['thumb_image']))
  {
    $frm_data = filteration($_POST);

    $pre_q = "UPDATE room_images SET thumb=? WHERE room_id=?";
    $pre_v = [0,$frm_data['room_id']];
    $pre_res = update($pre_q,$pre_v,'ii');

    $q = "UPDATE room_images SET thumb=? WHERE sr_no=? AND room_id=?";
    $v = [1,$frm_data['image_id'],$frm_data['room_id']];
    $res = update($q,$v,'iii');

    echo $res;
  }

  if (isset($_POST['remove_room'])) {
    $frm_data = filteration($_POST);
    error_log("Removing room with ID: " . $frm_data['room_id']); // Debugging

    $res2 = delete("DELETE FROM room_images WHERE room_id=?", [$frm_data['room_id']], 'i');
    $res3 = delete("DELETE FROM room_features WHERE room_id=?", [$frm_data['room_id']], 'i');
    $res4 = delete("DELETE FROM room_facilities WHERE room_id=?", [$frm_data['room_id']], 'i');
    $res5 = delete("DELETE FROM room_360_images WHERE room_id=?", [$frm_data['room_id']], 'i');
    $res6 = update("UPDATE rooms SET removed=? WHERE id=?", [1, $frm_data['room_id']], 'ii');

    error_log("Query Results: res2=$res2, res3=$res3, res4=$res4, res5=$res5, res6=$res6"); // Debugging

    if ($res2 && $res3 && $res4 && $res5 && $res6) {
        echo 1;
    } else {
        echo 0;
    }
  }

  /* 360° Image Management Functions */
  if(isset($_POST['get_360_images']))
  {
    $frm_data = filteration($_POST);
    $res = select("SELECT * FROM `room_360_images` WHERE `room_id`=? ORDER BY `image` ASC", [$frm_data['room_id']], 'i');

    $path = ROOMS_360_PATH; // Updated from 360 to ROOMS_360_PATH
    $i = 1;

    while($row = mysqli_fetch_assoc($res))
    {
        echo<<<data
        <tr class='align-middle'>
            <td><img src='$path$row[image]' class='img-fluid' style='max-height: 100px;'></td>
            <td>Image $i</td>
            <td>
                <button type='button' onclick='delete360Image($row[id],$row[room_id])' class='btn btn-admin-icon btn-admin-icon--danger shadow-none' title='Delete 360° frame'>
                    <i class='bi bi-trash3' aria-hidden='true'></i>
                </button>
            </td>
        </tr>
        data;
        $i++;
    }
  }

  if(isset($_POST['add_360_images']))
{
    $frm_data = filteration($_POST);
    $room_id = $frm_data['room_id'];
    $flag = 0;

    if(!is_dir(ROOMS_360_FULL_PATH)) {
        mkdir(ROOMS_360_FULL_PATH, 0755, true);
    }

    if(!empty($_FILES['360_images']['name'][0])) {
        foreach($_FILES['360_images']['tmp_name'] as $key => $tmp_name){
            if($_FILES['360_images']['error'][$key] === 0){
                if($_FILES['360_images']['size'][$key] <= 20*1024*1024){
                    $img_ext = pathinfo($_FILES['360_images']['name'][$key], PATHINFO_EXTENSION);
                    $img_ext = strtolower($img_ext);

                    $allowed_ext = ['jpg', 'jpeg', 'png', 'webp'];
                    if(in_array($img_ext, $allowed_ext)){
                        $new_img_name = "360_" . $room_id . "_" . uniqid("", true) . ".jpg"; // Always save as JPG
                        $img_upload_path = ROOMS_360_FULL_PATH . $new_img_name;

                        move_uploaded_file($tmp_name, $img_upload_path);

                        $q = "INSERT INTO room_360_images(room_id, image) VALUES (?,?)";
                        $values = [$room_id, $new_img_name];
                        $res = insert($q, $values, 'is');
                        if($res == 1) $flag = 1;
                    }
                }
            }
        }
    }

    echo $flag ? 1 : 0;
}

  if(isset($_POST['delete_360_image']))
  {
    $frm_data = filteration($_POST);
    $img_res = select("SELECT image FROM room_360_images WHERE id=? AND room_id=?", [$frm_data['img_id'], $frm_data['room_id']], 'ii');
    $img_row = mysqli_fetch_assoc($img_res);

    if(deleteImage($img_row['image'], ROOMS_360_FOLDER)){
      $res = delete("DELETE FROM room_360_images WHERE id=? AND room_id=?", [$frm_data['img_id'], $frm_data['room_id']], 'ii');
      echo $res;
    }
    else{
      echo 0;
    }
  }

  if (isset($_POST['get_room_bookings'])) {
    require_once('../../inc/room_availability.php');

    $frm_data = filteration($_POST);
    $room_id = (int) ($frm_data['room_id'] ?? 0);
    $booking_id = (int) ($frm_data['booking_id'] ?? 0);
    $nav = $frm_data['nav'] ?? '';

    if ($room_id <= 0) {
      echo json_encode(['success' => false, 'html' => '<p class="text-danger mb-0">Invalid room.</p>']);
      exit;
    }

    $room_res = select("SELECT `name`, `quantity` FROM `rooms` WHERE `id`=? AND `removed`=0 LIMIT 1", [$room_id], 'i');
    if (!$room_res || mysqli_num_rows($room_res) === 0) {
      echo json_encode(['success' => false, 'html' => '<p class="text-danger mb-0">Room not found.</p>']);
      exit;
    }
    $room_row = mysqli_fetch_assoc($room_res);

    $all_ids = [];
    $ids_res = select(
      "SELECT `booking_id` FROM `booking_order`
       WHERE `room_id`=? AND `booking_status` IN ('booked', 'pending')
       ORDER BY FIELD(`booking_status`, 'booked', 'pending'), `check_out` ASC, `booking_id` ASC",
      [$room_id], 'i'
    );
    while ($id_row = mysqli_fetch_assoc($ids_res)) {
      $all_ids[] = (int) $id_row['booking_id'];
    }

    $pick_id = 0;
    if (count($all_ids) > 0) {
      if ($nav === 'next' && $booking_id > 0) {
        $idx = array_search($booking_id, $all_ids, true);
        $pick_id = ($idx !== false && isset($all_ids[$idx + 1])) ? $all_ids[$idx + 1] : $all_ids[0];
      } elseif ($nav === 'prev' && $booking_id > 0) {
        $idx = array_search($booking_id, $all_ids, true);
        $pick_id = ($idx !== false && $idx > 0) ? $all_ids[$idx - 1] : $all_ids[count($all_ids) - 1];
      } elseif ($booking_id > 0 && in_array($booking_id, $all_ids, true)) {
        $pick_id = $booking_id;
      } else {
        $pick_id = $all_ids[0];
      }
    }

    if ($pick_id <= 0) {
      $html = "<div class='text-center py-4 text-muted'>
        <i class='bi bi-check-circle fs-2 d-block mb-2 text-success'></i>
        <p class='mb-0'>No active bookings for <strong>" . htmlspecialchars($room_row['name'], ENT_QUOTES, 'UTF-8') . "</strong>.</p>
        <p class='small mt-2 mb-0'>All slots are available for new reservations.</p>
      </div>";
      echo json_encode([
        'success' => true,
        'room_name' => $room_row['name'],
        'html' => $html,
        'booking_id' => 0,
        'total' => 0,
      ]);
      exit;
    }

    $one = select(
      "SELECT bo.booking_id, bo.check_in, bo.check_out, bo.booking_status, bo.arrival, bo.datentime,
              bd.user_name AS bd_user_name, bd.phonenum AS bd_phone, bd.room_no,
              uc.name AS uc_name, uc.phonenum AS uc_phone
       FROM `booking_order` bo
       LEFT JOIN `booking_details` bd ON bo.booking_id = bd.booking_id
       LEFT JOIN `user_cred` uc ON bo.user_id = uc.id
       WHERE bo.booking_id=? AND bo.room_id=?
       LIMIT 1",
      [$pick_id, $room_id], 'ii'
    );
    $b = mysqli_fetch_assoc($one);

    $guest_name = trim($b['bd_user_name'] ?? '') ?: trim($b['uc_name'] ?? '') ?: 'Guest';
    $guest_phone = trim($b['bd_phone'] ?? '') ?: trim($b['uc_phone'] ?? '') ?: '—';

    $checkin  = date('d M Y', strtotime($b['check_in']));
    $checkout = date('d M Y', strtotime($b['check_out']));
    $guest    = htmlspecialchars($guest_name, ENT_QUOTES, 'UTF-8');
    $phone    = htmlspecialchars($guest_phone, ENT_QUOTES, 'UTF-8');
    $room_no  = $b['room_no'] ? htmlspecialchars($b['room_no'], ENT_QUOTES, 'UTF-8') : 'Not assigned';
    $bid      = (int) $b['booking_id'];
    $today    = date('Y-m-d');
    $checkout_passed = ($b['check_out'] <= $today);

    $idx = array_search($bid, $all_ids, true);
    $total = count($all_ids);
    $position = ($idx !== false) ? ($idx + 1) : 1;

    $prev_attr = ($idx === false || $idx <= 0) ? 'disabled' : '';
    $next_attr = ($idx === false || $idx >= $total - 1) ? 'disabled' : '';
    $nav_html = '';
    if ($total > 1) {
      $nav_html = "<div class='d-flex justify-content-between align-items-center mb-3'>
        <button type='button' class='btn btn-sm btn-light border shadow-none' {$prev_attr}
          data-release-nav='prev' data-room-id='{$room_id}' data-booking-id='{$bid}'>
          <i class='bi bi-chevron-left'></i> Previous
        </button>
        <span class='small text-muted'>Booking {$position} of {$total}</span>
        <button type='button' class='btn btn-sm btn-light border shadow-none' {$next_attr}
          data-release-nav='next' data-room-id='{$room_id}' data-booking-id='{$bid}'>
          Next <i class='bi bi-chevron-right'></i>
        </button>
      </div>";
    }

    $status_key = strtolower(trim($b['booking_status'] ?? 'pending'));
    if ($status_key === 'booked') {
      $status_badge = "<span class='badge bg-success'>Confirmed stay</span>";
      $date_note = $checkout_passed
        ? "<span class='text-success small'><i class='bi bi-check2-circle me-1'></i>Checkout date has passed — safe to release.</span>"
        : "<span class='text-warning small'><i class='bi bi-exclamation-circle me-1'></i>Checkout is in the future — release only if the guest has left early.</span>";
    } else {
      $status_badge = "<span class='badge bg-warning text-dark'>Unpaid / payment not completed</span>";
      $date_note = "<span class='text-muted small'>This booking does <strong>not</strong> count toward the room limit until payment succeeds. Release it to remove the unpaid record.</span>";
    }
    $action = "<button type='button' data-release-action='release' data-booking-id='{$bid}'
                 class='btn custom-bg text-white shadow-none d-inline-flex align-items-center gap-2 px-4 py-2'>
                 <i class='bi bi-door-open'></i> Release booking
               </button>";

    $avail = checkRoomDateAvailability($room_id, $b['check_in'], $b['check_out'], $con);
    $slot_info = "Active confirmed bookings on these dates: <strong>{$avail['used']}</strong> (room limit set in admin: {$avail['limit']})";

    $booking_list_html = '';
    $list_res = select(
      "SELECT bo.booking_id, bo.booking_status, bo.check_in, bo.check_out,
              COALESCE(NULLIF(TRIM(bd.user_name), ''), NULLIF(TRIM(uc.name), ''), 'Guest') AS guest_name,
              (LOWER(TRIM(bo.booking_status)) = 'booked') AS blocks_slot
       FROM `booking_order` bo
       LEFT JOIN `booking_details` bd ON bo.booking_id = bd.booking_id
       LEFT JOIN `user_cred` uc ON bo.user_id = uc.id
       WHERE bo.room_id=? AND bo.booking_status IN ('booked', 'pending')
       ORDER BY blocks_slot DESC, FIELD(bo.booking_status, 'booked', 'pending'), bo.check_out ASC, bo.booking_id ASC",
      [$room_id], 'i'
    );
    $list_items = '';
    while ($item = mysqli_fetch_assoc($list_res)) {
      $item_id = (int) $item['booking_id'];
      $item_guest = htmlspecialchars($item['guest_name'], ENT_QUOTES, 'UTF-8');
      $item_in  = date('d M Y', strtotime($item['check_in']));
      $item_out = date('d M Y', strtotime($item['check_out']));
      $blocks = (int) ($item['blocks_slot'] ?? 0);
      $item_badge = $item['booking_status'] === 'booked'
        ? ($blocks ? "<span class='badge bg-danger'>Uses slot</span>" : "<span class='badge bg-secondary'>Confirmed (ended)</span>")
        : "<span class='badge bg-warning text-dark'>Unpaid</span>";
      $active = ($item_id === $bid) ? ' active' : '';
      $list_items .= "<button type='button' class='list-group-item list-group-item-action d-flex justify-content-between align-items-start gap-2 text-start{$active}'
        data-select-booking='{$item_id}' data-room-id='{$room_id}'>
        <span>
          <span class='fw-semibold d-block'>{$item_guest}</span>
          <span class='small text-muted'>{$item_in} – {$item_out}</span>
        </span>
        <span class='flex-shrink-0'>{$item_badge}</span>
      </button>";
    }
    if ($list_items !== '') {
      $booking_list_html = "<div class='col-md-5 mb-3 mb-md-0'>
        <p class='small fw-semibold mb-2'>Active bookings for this room ({$total}) — click any to view</p>
        <div class='list-group shadow-sm release-booking-list' style='max-height:320px;overflow-y:auto;'>{$list_items}</div>
      </div>";
    }

    $html = "<div class='row g-3'>
      {$booking_list_html}
      <div class='" . ($booking_list_html !== '' ? 'col-md-7' : 'col-12') . "'>
        {$nav_html}
        <div class='card border shadow-sm'>
          <div class='card-body'>
            <div class='d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3'>
              <h6 class='mb-0 fw-bold'>" . htmlspecialchars($room_row['name'], ENT_QUOTES, 'UTF-8') . "</h6>
              {$status_badge}
            </div>
            <p class='small text-muted mb-3'>{$slot_info}</p>
            <p class='small fw-semibold text-muted mb-2'>Selected booking</p>
            <dl class='row mb-0 small'>
              <dt class='col-sm-4 text-muted'>Guest</dt>
              <dd class='col-sm-8 fw-semibold mb-2'>{$guest}</dd>
              <dt class='col-sm-4 text-muted'>Phone</dt>
              <dd class='col-sm-8 mb-2'>{$phone}</dd>
              <dt class='col-sm-4 text-muted'>Check-in</dt>
              <dd class='col-sm-8 mb-2'>{$checkin}</dd>
              <dt class='col-sm-4 text-muted'>Check-out</dt>
              <dd class='col-sm-8 mb-2'>{$checkout}</dd>
              <dt class='col-sm-4 text-muted'>Room number</dt>
              <dd class='col-sm-8 mb-2'>{$room_no}</dd>
            </dl>
            <div class='mt-3 mb-3'>{$date_note}</div>
            <div>{$action}</div>
          </div>
        </div>
      </div>
    </div>";

    echo json_encode([
      'success' => true,
      'room_name' => $room_row['name'],
      'html' => $html,
      'booking_id' => $bid,
      'total' => $total,
    ]);
    exit;
  }

  if (isset($_POST['cancel_pending_booking'])) {
    $_POST['release_booking'] = '1';
  }

  if (isset($_POST['release_booking'])) {
    require_once('../../inc/room_availability.php');

    $frm_data = filteration($_POST);
    $booking_id = (int) ($frm_data['booking_id'] ?? 0);

    if ($booking_id <= 0) {
      echo json_encode(['success' => false, 'message' => 'Invalid booking.']);
      exit;
    }

    $check = select(
      "SELECT booking_id, room_id, check_in, check_out, booking_status FROM booking_order
       WHERE booking_id=? LIMIT 1",
      [$booking_id], 'i'
    );

    if (!$check || mysqli_num_rows($check) === 0) {
      echo json_encode(['success' => false, 'message' => 'Booking not found.']);
      exit;
    }

    $booking_row = mysqli_fetch_assoc($check);
    $status = strtolower(trim($booking_row['booking_status'] ?? 'pending'));

    if (in_array($status, ['completed', 'cancelled'], true)) {
      echo json_encode([
        'success' => true,
        'message' => 'This booking was already released. Users can book these dates.',
        'room_id' => (int) $booking_row['room_id'],
      ]);
      exit;
    }

    $releasable = ['booked', 'pending', 'payment_failed', 'payment failed', ''];
    if (!in_array($status, $releasable, true)) {
      echo json_encode([
        'success' => false,
        'message' => 'Cannot release booking with status: ' . $booking_row['booking_status'],
      ]);
      exit;
    }

    update(
      "UPDATE `booking_order` SET `booking_status`='completed' WHERE `booking_id`=?",
      [$booking_id], 'i'
    );

    $verify = select(
      "SELECT booking_status FROM booking_order WHERE booking_id=? LIMIT 1",
      [$booking_id], 'i'
    );
    $verified_status = strtolower(trim(mysqli_fetch_assoc($verify)['booking_status'] ?? ''));

    if ($verified_status !== 'completed') {
      echo json_encode(['success' => false, 'message' => 'Could not release booking. Please try again.']);
      exit;
    }

    cancelUnpaidBookingsForDateRange(
      (int) $booking_row['room_id'],
      $booking_row['check_in'],
      $booking_row['check_out'],
      $con
    );

    echo json_encode([
      'success' => true,
      'message' => 'Room released. Users can now book this room for those dates.',
      'room_id' => (int) $booking_row['room_id'],
    ]);
    exit;
  }
?>