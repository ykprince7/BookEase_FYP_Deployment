<?php
  require('inc/essentials.php');
  adminLogin();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Panel - Promotional Banner</title>
  <?php require('inc/links.php'); ?>
</head>
<body class="bg-light">

  <?php require('inc/header.php'); ?>

  <div class="container-fluid" id="main-content">
    <div class="row">
      <div class="col-lg-10 ms-auto p-4 overflow-hidden">
        <h3 class="mb-4">PROMOTIONAL BANNER</h3>

        <!-- Status card -->
        <div class="card border-0 shadow-sm mb-4">
          <div class="card-body">
            <div class="d-flex align-items-center justify-content-between mb-3">
              <div>
                <h5 class="card-title m-0">Banner Status</h5>
                <p class="text-muted small mb-0 mt-1">Toggle to show or hide the pop-up banner on the website.</p>
              </div>
              <div class="d-flex align-items-center gap-3">
                <div class="form-check form-switch mb-0">
                  <input class="form-check-input" type="checkbox" id="banner-toggle"
                         style="width:3rem;height:1.5rem;cursor:pointer;">
                </div>
                <button type="button"
                        class="btn btn-primary shadow-none d-inline-flex align-items-center gap-2 rounded-3 px-3 py-2 fw-semibold"
                        data-bs-toggle="modal" data-bs-target="#edit-banner-modal">
                  <i class="bi bi-pencil" aria-hidden="true"></i>
                  <span>Edit</span>
                </button>
              </div>
            </div>

            <div class="row g-3">
              <div class="col-md-4">
                <div class="rounded-3 p-3 text-center" style="background:rgba(45,156,149,0.08);border:1px solid rgba(45,156,149,0.2)">
                  <div class="fw-bold fs-4" id="stat-status" style="color:#2d9c95">—</div>
                  <div class="text-muted small">Status</div>
                </div>
              </div>
              <div class="col-md-4">
                <div class="rounded-3 p-3 text-center" style="background:rgba(45,156,149,0.08);border:1px solid rgba(45,156,149,0.2)">
                  <div class="fw-bold fs-4" id="stat-points" style="color:#2d9c95">—</div>
                  <div class="text-muted small">Points per Claim</div>
                </div>
              </div>
              <div class="col-md-4">
                <div class="rounded-3 p-3 text-center" style="background:rgba(45,156,149,0.08);border:1px solid rgba(45,156,149,0.2)">
                  <div class="fw-bold fs-4" id="stat-claims" style="color:#2d9c95">—</div>
                  <div class="text-muted small">Total Claims</div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Current settings card -->
        <div class="card border-0 shadow-sm mb-4">
          <div class="card-body">
            <h5 class="card-title mb-3">Current Settings</h5>
            <div class="row">
              <div class="col-md-6">
                <table class="table table-sm table-borderless small mb-0">
                  <tbody>
                    <tr>
                      <td class="text-muted fw-semibold" style="width:38%">Title</td>
                      <td id="preview-title">—</td>
                    </tr>
                    <tr>
                      <td class="text-muted fw-semibold">Subtitle</td>
                      <td id="preview-subtitle">—</td>
                    </tr>
                    <tr>
                      <td class="text-muted fw-semibold">Description</td>
                      <td id="preview-description" class="text-muted fst-italic">—</td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <div class="col-md-6">
                <table class="table table-sm table-borderless small mb-0">
                  <tbody>
                    <tr>
                      <td class="text-muted fw-semibold" style="width:38%">Points</td>
                      <td><span id="preview-points" class="fw-bold" style="color:#2d9c95">—</span></td>
                    </tr>
                    <tr>
                      <td class="text-muted fw-semibold">Expiry</td>
                      <td id="preview-expiry">—</td>
                    </tr>
                    <tr>
                      <td class="text-muted fw-semibold">Image</td>
                      <td>
                        <span id="preview-no-img" class="text-muted fst-italic">Default hotel photos</span>
                        <span id="preview-img-info" class="d-none text-success"><i class="bi bi-image me-1"></i>Custom image set</span>
                      </td>
                    </tr>
                  </tbody>
                </table>
                <div id="preview-img-container" class="d-none mt-2">
                  <img id="preview-img" src="" alt="Banner background"
                       class="img-fluid rounded-3 shadow-sm" style="max-height:120px;object-fit:cover;width:100%;">
                  <button type="button" id="remove-bg-btn" class="btn btn-danger shadow-none btn-sm d-inline-flex align-items-center gap-2 rounded-3 fw-semibold mt-2">
                    <i class="bi bi-trash" aria-hidden="true"></i>
                    <span>Remove Image</span>
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>


      </div>
    </div>
  </div>

  <!-- Edit Banner Modal -->
  <div class="modal fade" id="edit-banner-modal" data-bs-backdrop="static" data-bs-keyboard="true"
       tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <form id="edit_banner_form" enctype="multipart/form-data">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Edit Promotional Banner</h5>
          </div>
          <div class="modal-body">

            <!-- Hidden: keep occasion fixed to welcome_bonus -->
            <input type="hidden" name="occasion"         value="welcome_bonus">
            <input type="hidden" name="target_audience"  value="new_members">
            <input type="hidden" name="offer_text"       value="">
            <input type="hidden" name="offer_label"      value="LOYALTY POINTS">
            <input type="hidden" name="badge_label"      value="NEW MEMBER EXCLUSIVE">
            <input type="hidden" name="cta_text"         value="">
            <input type="hidden" name="cta_url"          value="">

            <div class="row">
              <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Title <span class="text-danger">*</span></label>
                <input type="text" name="title" id="edit_title" class="form-control shadow-none" required maxlength="255">
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Subtitle</label>
                <input type="text" name="subtitle" id="edit_subtitle" class="form-control shadow-none" maxlength="255">
              </div>

              <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Loyalty Points Reward <span class="text-danger">*</span></label>
                <input type="number" name="loyalty_points" id="edit_points" class="form-control shadow-none" min="1" required>
                <small class="text-muted">Points awarded to the user when they claim the banner.</small>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Expiry Date <span class="text-muted fw-normal">(optional)</span></label>
                <input type="date" name="expiry_date" id="edit_expiry_date" class="form-control shadow-none">
                <small class="text-muted">Banner auto-hides after this date. Leave blank for no expiry.</small>
              </div>

              <div class="col-12 mb-3">
                <label class="form-label fw-bold">Description</label>
                <textarea name="description" id="edit_description" class="form-control shadow-none" rows="4"></textarea>
              </div>

              <div class="col-12 mb-3">
                <label class="form-label fw-bold">Background Image <span class="text-muted fw-normal">(JPG/PNG/WEBP, max 2 MB)</span></label>
                <input type="file" name="bg_image" id="edit_bg_image" accept=".jpg,.jpeg,.png,.webp" class="form-control shadow-none">
                <small class="text-muted">Leave empty to keep the current image. Uses curated hotel photos when no custom image is set.</small>
              </div>
            </div>

          </div>
          <div class="modal-footer">
            <button type="button" class="btn text-secondary shadow-none" data-bs-dismiss="modal">CANCEL</button>
            <button type="submit" class="btn custom-bg text-white shadow-none rounded-3 fw-semibold d-inline-flex align-items-center gap-2 px-3 py-2">SUBMIT</button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <?php require('inc/scripts.php'); ?>
  <script src="scripts/banner.js"></script>

</body>
</html>
