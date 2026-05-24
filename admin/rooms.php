<?php
  require('inc/essentials.php');
  require('inc/db_config.php');
  adminLogin();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Panel - Rooms</title>
  <?php require('inc/links.php'); ?>
</head>
<body class="bg-light">

  <?php require('inc/header.php'); ?>

  <div class="container-fluid" id="main-content">
    <div class="row">
      <div class="col-lg-10 ms-auto p-4 overflow-hidden">
        <h3 class="mb-4">ROOMS</h3>

        <div class="card border-0 shadow-sm mb-4">
          <div class="card-body">

            <p class="text-muted small mb-3">
              <strong>Booking limit</strong> is the max reservations for this room type on the same dates (saved in the database).
              Click the <i class="bi bi-door-open"></i> icon to <strong>release one booking at a time</strong> for that room after checkout, or clear an abandoned payment hold blocking the slot.
              You can also release from <a href="booking_records.php">Booking Records</a>.
            </p>

            <div class="text-end mb-4">
              <button type="button" class="btn btn-primary shadow-none d-inline-flex align-items-center gap-2 rounded-3 px-3 py-2 fw-semibold" data-bs-toggle="modal" data-bs-target="#add-room">
                <i class="bi bi-plus-lg" aria-hidden="true"></i>
                <span>Add room</span>
              </button>
            </div>

            <div class="table-responsive-lg" style="height: 450px; overflow-y: scroll;">
    <table class="table table-hover border text-center">
        <thead>
            <tr class="bg-dark text-light">
                <th scope="col">#</th>
                <th scope="col">Name</th>
                <th scope="col">Area</th>
                <th scope="col">Guests</th>
                <th scope="col">Price</th>
                <th scope="col">Booking limit</th>
                <th scope="col">Status</th>
                <th scope="col">Action</th>
            </tr>
        </thead>
        <tbody id="rooms-container">
        </tbody>
    </table>
</div>

          </div>
        </div>

      </div>
    </div>
  </div>
  

  <!-- Add room modal -->
  <div class="modal fade" id="add-room" data-bs-backdrop="static" data-bs-keyboard="true" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <form id="add_room_form" autocomplete="off">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Add Room</h5>
          </div>
          <div class="modal-body">
            <div class="row">
              <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Name</label>
                <input type="text" name="name" class="form-control shadow-none" required>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Area</label>
                <input type="number" min="1" name="area" class="form-control shadow-none" required>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Price</label>
                <input type="number" min="1" name="price" class="form-control shadow-none" required>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Booking limit</label>
                <input type="number" min="1" max="99" name="quantity" class="form-control shadow-none" required>
                <div class="form-text">Max bookings on the same dates (e.g. 3 for Premium Family Suite).</div>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Adult (Max.)</label>
                <input type="number" min="1" name="adult" class="form-control shadow-none" required>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Children (Max.)</label>
                <input type="number" min="1" name="children" class="form-control shadow-none" required>
              </div>
              <div class="col-12 mb-3">
                <label class="form-label fw-bold">Features</label>
                <div class="row">
                  <?php 
                    $res = selectAll('features');
                    while($opt = mysqli_fetch_assoc($res)){
                      echo"
                        <div class='col-md-3 mb-1'>
                          <label>
                            <input type='checkbox' name='features' value='$opt[id]' class='form-check-input shadow-none'>
                            $opt[name]
                          </label>
                        </div>
                      ";
                    }
                  ?>
                </div>
              </div>
              <div class="col-12 mb-3">
                <label class="form-label fw-bold">Facilities</label>
                <div class="row">
                  <?php 
                    $res = selectAll('facilities');
                    while($opt = mysqli_fetch_assoc($res)){
                      echo"
                        <div class='col-md-3 mb-1'>
                          <label>
                            <input type='checkbox' name='facilities' value='$opt[id]' class='form-check-input shadow-none'>
                            $opt[name]
                          </label>
                        </div>
                      ";
                    }
                  ?>
                </div>
              </div>
              <div class="col-12 mb-3">
                <label class="form-label fw-bold">Description</label>
                <textarea name="desc" rows="4" class="form-control shadow-none" required></textarea>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="reset" class="btn text-secondary shadow-none" data-bs-dismiss="modal">CANCEL</button>
            <button type="submit" class="btn custom-bg text-white shadow-none rounded-3 fw-semibold d-inline-flex align-items-center gap-2 px-3 py-2">SUBMIT</button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <!-- Edit room modal -->
  <div class="modal fade" id="edit-room" data-bs-backdrop="static" data-bs-keyboard="true" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <form id="edit_room_form" autocomplete="off">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Edit Room</h5>
          </div>
          <div class="modal-body">
            <div class="row">
              <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Name</label>
                <input type="text" name="name" class="form-control shadow-none" required>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Area</label>
                <input type="number" min="1" name="area" class="form-control shadow-none" required>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Price</label>
                <input type="number" min="1" name="price" class="form-control shadow-none" required>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Booking limit</label>
                <input type="number" min="1" max="99" name="quantity" class="form-control shadow-none" required>
                <div class="form-text">Max bookings on the same dates (e.g. 3 for Premium Family Suite).</div>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Adult (Max.)</label>
                <input type="number" min="1" name="adult" class="form-control shadow-none" required>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Children (Max.)</label>
                <input type="number" min="1" name="children" class="form-control shadow-none" required>
              </div>
              <div class="col-12 mb-3">
                <label class="form-label fw-bold">Features</label>
                <div class="row">
                  <?php 
                    $res = selectAll('features');
                    while($opt = mysqli_fetch_assoc($res)){
                      echo"
                        <div class='col-md-3 mb-1'>
                          <label>
                            <input type='checkbox' name='features' value='$opt[id]' class='form-check-input shadow-none'>
                            $opt[name]
                          </label>
                        </div>
                      ";
                    }
                  ?>
                </div>
              </div>
              <div class="col-12 mb-3">
                <label class="form-label fw-bold">Facilities</label>
                <div class="row">
                  <?php 
                    $res = selectAll('facilities');
                    while($opt = mysqli_fetch_assoc($res)){
                      echo"
                        <div class='col-md-3 mb-1'>
                          <label>
                            <input type='checkbox' name='facilities' value='$opt[id]' class='form-check-input shadow-none'>
                            $opt[name]
                          </label>
                        </div>
                      ";
                    }
                  ?>
                </div>
              </div>
              <div class="col-12 mb-3">
                <label class="form-label fw-bold">Description</label>
                <textarea name="desc" rows="4" class="form-control shadow-none" required></textarea>
              </div>
              <input type="hidden" name="room_id">
            </div>
          </div>
          <div class="modal-footer">
            <button type="reset" class="btn text-secondary shadow-none" data-bs-dismiss="modal">CANCEL</button>
            <button type="submit" class="btn custom-bg text-white shadow-none rounded-3 fw-semibold d-inline-flex align-items-center gap-2 px-3 py-2">SUBMIT</button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <!-- Manage room images modal -->
  <div class="modal fade" id="room-images" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Room Name</h5>
          <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div id="image-alert"></div>
          <div class="border-bottom border-3 pb-3 mb-3">
            <form id="add_image_form">
              <label class="form-label fw-bold">Add Image</label>
              <input type="file" name="image" accept=".jpg, .png, .webp, .jpeg" class="form-control shadow-none mb-3" required>
              <button type="submit" class="btn custom-bg text-white shadow-none rounded-3 fw-semibold d-inline-flex align-items-center gap-2 px-3 py-2">
                <i class="bi bi-cloud-upload" aria-hidden="true"></i>
                <span>Add image</span>
              </button>
              <input type="hidden" name="room_id">
            </form>
          </div>
          <div class="table-responsive-lg" style="height: 350px; overflow-y: scroll;">
            <table class="table table-hover border text-center">
              <thead>
                <tr class="bg-dark text-light sticky-top">
                  <th scope="col" width="60%">Image</th>
                  <th scope="col">Thumb</th>
                  <th scope="col">Delete</th>
                </tr>
              </thead>
              <tbody id="room-image-data">                 
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- View & release active bookings for a room -->
  <div class="modal fade" id="room-bookings-modal" data-bs-backdrop="static" data-bs-keyboard="true" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title d-flex align-items-center gap-2">
            <i class="bi bi-door-open text-primary"></i>
            <span>Release booking — <span id="release-modal-room-name">Room</span></span>
          </h5>
          <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div id="room-bookings-alert"></div>
          <div id="room-bookings-loader" class="text-center py-4 d-none">
            <div class="spinner-border text-primary" role="status"></div>
            <p class="small text-muted mt-2 mb-0">Loading bookings…</p>
          </div>
          <div id="room-bookings-content"></div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-light border shadow-none" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Manage 360° images modal -->
  <div class="modal fade" id="room-360-images" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">360° Images - <span id="room-name-title"></span></h5>
          <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div id="360-alert"></div>
          <div class="border-bottom border-3 pb-3 mb-3">
            <form id="add_360_form" enctype="multipart/form-data">
              <label class="form-label fw-bold">Add 360° Image Sequence</label>
              <div class="alert alert-info">
                <i class="bi bi-info-circle-fill"></i> Upload images (e.g., room1_001.jpg, room1_002.jpg, etc.)
              </div>
              <input type="file" name="360_images[]" accept=".jpg, .png, .webp, .jpeg" class="form-control shadow-none mb-3" multiple required>
              <button type="submit" class="btn custom-bg text-white shadow-none rounded-3 fw-semibold d-inline-flex align-items-center gap-2 px-3 py-2">
                <i class="bi bi-arrows-fullscreen" aria-hidden="true"></i>
                <span>Upload 360° images</span>
              </button>
              <input type="hidden" name="room_id">
            </form>
          </div>
          <div class="table-responsive-lg" style="height: 350px; overflow-y: scroll;">
            <table class="table table-hover border text-center">
              <thead>
                <tr class="bg-dark text-light sticky-top">
                  <th scope="col">Image</th>
                  <th scope="col">Sequence</th>
                  <th scope="col">Delete</th>
                </tr>
              </thead>
              <tbody id="room-360-image-data">                 
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>

  <?php require('inc/scripts.php'); ?>

  <script src="scripts/rooms.js"></script>
</body>
</html>