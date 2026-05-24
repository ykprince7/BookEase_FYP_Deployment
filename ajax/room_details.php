<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php require('inc/links.php'); ?>
    <!-- Add Pannellum CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/pannellum@2.5.6/build/pannellum.css">
    <title><?php echo $settings_r['site_title'] ?> - ROOM DETAILS</title>
</head>
<body class="bg-light">
    <?php require('inc/header.php'); ?>

    <?php
        if(!isset($_GET['id'])){
            redirect('rooms.php');
        }

        $data = filteration($_GET);
        $room_res = select("SELECT * FROM `rooms` WHERE `id`=? AND `status`=? AND `removed`=?",[$data['id'],1,0],'iii');
        if(mysqli_num_rows($room_res)==0){
            redirect('rooms.php');
        }

        $room_data = mysqli_fetch_assoc($room_res);
        require_once('inc/currency_helpers.php');
        $selected_currency = isset($_GET['currency']) ? $_GET['currency'] : 'NPR';
        $base_currency = 'NPR';
        $converted_price = formatCurrencyAmount(convertCurrency($room_data['price'], $base_currency, $selected_currency, $con));
    ?>
        
    <div class="container">
        <div class="row">
            <div class="col-12 my-5 mb-4 px-4">
                <h2 class="fw-bold"><?php echo $room_data['name'] ?></h2>
                <div style="font-size: 14px;">
                    <a href="index.php" class="text-secondary text-decoration-none">HOME</a>
                    <span class="text-secondary"> > </span>
                    <a href="rooms.php" class="text-secondary text-decoration-none">ROOMS</a>
                </div>
            </div>

            <div class="col-lg-7 col-md-12 px-4">
                <div id="roomCarousel" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-inner">
                        <?php
                            $room_img = ROOMS_IMG_PATH."thumbnail.jpg";
                            $img_q = mysqli_query($con,"SELECT * FROM `room_images` WHERE `room_id`='$room_data[id]'");
                            if(mysqli_num_rows($img_q)>0) {
                                $active_class = 'active';
                                while($img_res = mysqli_fetch_assoc($img_q)) {
                                    echo "<div class='carousel-item $active_class'>
                                            <img src='".ROOMS_IMG_PATH.$img_res['image']."' class='d-block w-100 rounded'>
                                          </div>";
                                    $active_class = '';
                                }
                            } else {
                                echo "<div class='carousel-item active'>
                                        <img src='$room_img' class='d-block w-100'>
                                      </div>";
                            }
                        ?>
                    </div>
                </div>
            </div>

            <div class="col-lg-5 col-md-12 px-4">
                <div class="card mb-4 border-0 shadow-sm rounded-3">
                    <div class="card-body">
                        <?php echo "<h4>$selected_currency $converted_price per night</h4>"; ?>

                        <?php
                            $rating_q = "SELECT AVG(rating) AS `avg_rating` FROM `rating_review` WHERE `room_id`='$room_data[id]' ORDER BY `sr_no` DESC LIMIT 20";
                            $rating_res = mysqli_query($con,$rating_q);
                            $rating_fetch = mysqli_fetch_assoc($rating_res);
                            $rating_data = "";
                            if($rating_fetch['avg_rating']!=NULL) {
                                for($i=0; $i < $rating_fetch['avg_rating']; $i++) {
                                    $rating_data .= "<i class='bi bi-star-fill text-warning'></i> ";
                                }
                            }
                            echo "<div class='mb-3'>$rating_data</div>";
                        ?>

                        <?php
                            $fea_q = mysqli_query($con,"SELECT f.name FROM `features` f
                                                      INNER JOIN `room_features` rfea ON f.id = rfea.features_id
                                                      WHERE rfea.room_id = '$room_data[id]'");
                            $features_data = "";
                            while($fea_row = mysqli_fetch_assoc($fea_q)) {
                                $features_data .= "<span class='badge rounded-pill bg-light text-dark text-wrap me-1 mb-1'>
                                                    $fea_row[name]
                                                  </span>";
                            }
                            echo "<div class='mb-3'><h6 class='mb-1'>Features</h6>$features_data</div>";
                        ?>

                        <?php
                            $fac_q = mysqli_query($con, "SELECT f.name FROM `facilities` f
                                                        INNER JOIN `room_facilities` rfac ON f.id = rfac.facilities_id
                                                        WHERE rfac.room_id = '$room_data[id]'");
                            $facilities_data = "";
                            while($fac_row = mysqli_fetch_assoc($fac_q)) {
                                $facilities_data .= "<span class='badge rounded-pill bg-light text-dark text-wrap me-1 mb-1'>
                                                    $fac_row[name]
                                                  </span>";
                            }
                            echo "<div class='mb-3'><h6 class='mb-1'>Facilities</h6>$facilities_data</div>";
                        ?>

                        <?php
                            echo "<div class='mb-3'>
                                    <h6 class='mb-1'>Guests</h6>
                                    <span class='badge rounded-pill bg-light text-dark text-wrap'>$room_data[adult] Adults</span>
                                    <span class='badge rounded-pill bg-light text-dark text-wrap'>$room_data[children] Children</span>
                                  </div>";
                        ?>

                        <?php
                            echo "<div class='mb-3'>
                                    <h6 class='mb-1'>Area</h6>
                                    <span class='badge rounded-pill bg-light text-dark text-wrap me-1 mb-1'>$room_data[area] sq. ft.</span>
                                  </div>";
                        ?>

                        <!-- Add 360° Images Button -->
                        <button type="button" class="btn btn-outline-success w-100 mb-2" data-bs-toggle="modal" data-bs-target="#room360Modal">
                            <i class="bi bi-arrow-repeat"></i> View 360° Images
                        </button>

                        <?php
                            if(!$settings_r['shutdown']) {
                                $login = isset($_SESSION['login']) && $_SESSION['login'] == true ? 1 : 0;
                                echo "<button onclick='checkLoginToBook($login,$room_data[id])' class='btn w-100 text-white custom-bg shadow-none mb-1'>Book Now</button>";
                            }
                        ?>
                    </div>
                </div>
            </div>

            <div class="col-12 mt-4 px-4">
                <div class="mb-5">
                    <h5>Description</h5>
                    <p><?php echo $room_data['description'] ?></p>
                </div>
                <div>
                    <h5 class="mb-3">Reviews & Ratings</h5>
                    <?php
                        $review_q = "SELECT rr.*, uc.name AS uname, uc.profile, r.name AS rname FROM `rating_review` rr
                                    INNER JOIN `user_cred` uc ON rr.user_id = uc.id
                                    INNER JOIN `rooms` r ON rr.room_id = r.id
                                    WHERE rr.room_id = '$room_data[id]'
                                    ORDER BY `sr_no` DESC LIMIT 15";
                        $review_res = mysqli_query($con,$review_q);
                        $img_path = USERS_IMG_PATH;
                        $fallback_profile_url = 'https://via.placeholder.com/30?text=U';

                        if(mysqli_num_rows($review_res)==0) {
                            echo 'No reviews yet!';
                        } else {
                            while($row = mysqli_fetch_assoc($review_res)) {
                                $stars = "<i class='bi bi-star-fill text-warning'></i> ";
                                for($i=1; $i<$row['rating']; $i++) {
                                    $stars .= " <i class='bi bi-star-fill text-warning'></i>";
                                }

                                $profile_img = !empty($row['profile']) ? $img_path.$row['profile'] : $fallback_profile_url;

                                echo "<div class='mb-4'>
                                        <div class='d-flex align-items-center mb-2'>
                                            <img src='$profile_img' class='rounded-circle' loading='lazy' width='30px' onerror=\"this.onerror=null;this.src='$fallback_profile_url';\" >
                                            <h6 class='m-0 ms-2'>$row[uname]</h6>
                                        </div>
                                        <p class='mb-1'>$row[review]</p>
                                        <div>$stars</div>
                                      </div>";
                            }
                        }
                    ?>
                </div>
            </div>
        </div>
    </div>

    <!-- 360° Images Modal with Pannellum -->
<div class="modal fade" id="room360Modal" tabindex="-1" aria-labelledby="room360ModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="room360ModalLabel">360° Images for <?php echo $room_data['name']; ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="panorama" style="width: 100%; height: 400px;"></div>
                <?php
                    $query_360 = "SELECT * FROM `room_360_images` WHERE `room_id`=? ORDER BY `id` DESC";
                    $res_360 = select($query_360, [$room_data['id']], 'i');
                    if (mysqli_num_rows($res_360) > 0) {
                        $row_360 = mysqli_fetch_assoc($res_360);
                        $img_path_360 = 'images/360/' . rawurlencode($row_360['image']);
                        echo "<script>var panoramaImage = '$img_path_360';</script>";
                    } else {
                        echo '<p>No 360° images available for this room.</p>';
                        echo "<script>var panoramaImage = null;</script>";
                    }
                ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

    <?php require('inc/footer.php'); ?>

    <!-- Add Pannellum JS -->
    <script src="https://cdn.jsdelivr.net/npm/pannellum@2.5.6/build/pannellum.js"></script>
    <script>
        let panoramaViewer = null;
        const room360Modal = document.getElementById('room360Modal');
        const panoramaDiv = document.getElementById('panorama');

        room360Modal.addEventListener('shown.bs.modal', function () {
            if (!panoramaImage) return;

            panoramaDiv.innerHTML = '';
            if (panoramaViewer) {
                try { panoramaViewer.destroy(); } catch (e) {}
                panoramaViewer = null;
            }

            panoramaViewer = pannellum.viewer('panorama', {
                type: 'equirectangular',
                panorama: panoramaImage,
                autoLoad: true,
                autoRotate: -2,
                compass: true,
                mouseZoom: true,
                draggable: true,
                showControls: true
            });
        });

        room360Modal.addEventListener('hidden.bs.modal', function () {
            if (panoramaViewer) {
                try { panoramaViewer.destroy(); } catch (e) {}
                panoramaViewer = null;
            }
            panoramaDiv.innerHTML = '';
        });
    </script>

    <?php require_once('inc/currency_helpers.php'); ?>

    <script>
    function checkLoginToBook(login, room_id) {
        // Get currency from URL
        const urlParams = new URLSearchParams(window.location.search);
        const selectedCurrency = urlParams.get('currency') || 'NPR';
        if (!login) {
            window.location.href = 'login.php';
        } else {
            window.location.href = 'confirm_booking.php?id=' + room_id + '&currency=' + selectedCurrency;
        }
    }
    </script>
</body>
</html>