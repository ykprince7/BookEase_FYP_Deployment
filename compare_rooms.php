<!-- Comparing Rooms with the help of the Room ID -->
<?php
require('admin/inc/db_config.php');
require('admin/inc/essentials.php');

if (!isset($_POST['room_ids'])) {
    echo "<div class='alert alert-danger mb-0'>No rooms selected.</div>";
    exit;
}

$room_ids = json_decode($_POST['room_ids'], true);
if (!is_array($room_ids)) {
    echo "<div class='alert alert-danger mb-0'>Invalid selection.</div>";
    exit;
}

$room_ids = array_values(array_unique(array_map('intval', $room_ids)));
$room_ids = array_slice($room_ids, 0, 2);

if (count($room_ids) < 1) {
    echo "<div class='alert alert-danger mb-0'>Select at least one room.</div>";
    exit;
}

$fea_stmt = $con->prepare("SELECT f.name FROM `features` f 
    INNER JOIN `room_features` rfea ON f.id = rfea.features_id 
    WHERE rfea.room_id = ? ORDER BY f.name ASC");
$fac_stmt = $con->prepare("SELECT f.name FROM `facilities` f 
    INNER JOIN `room_facilities` rfac ON f.id = rfac.facilities_id 
    WHERE rfac.room_id = ? ORDER BY f.name ASC");
$room_stmt = $con->prepare("SELECT * FROM `rooms` WHERE `id` = ? AND `status` = 1 AND `removed` = 0");

$rooms = [];

foreach ($room_ids as $room_id) {
    $room_stmt->bind_param('i', $room_id);
    $room_stmt->execute();
    $room_res = $room_stmt->get_result();
    if (!$room_res || $room_res->num_rows === 0) {
        continue;
    }
    $room_data = $room_res->fetch_assoc();

    $fea_stmt->bind_param('i', $room_id);
    $fea_stmt->execute();
    $fea_result = $fea_stmt->get_result();
    $features = [];
    while ($row = $fea_result->fetch_assoc()) {
        $features[] = $row['name'];
    }

    $fac_stmt->bind_param('i', $room_id);
    $fac_stmt->execute();
    $fac_result = $fac_stmt->get_result();
    $facilities = [];
    while ($row = $fac_result->fetch_assoc()) {
        $facilities[] = $row['name'];
    }

    $fallbackRoomThumb = 'https://via.placeholder.com/400x200?text=No+Image';
    $thumb = $fallbackRoomThumb;

    $thumb_q = mysqli_query($con, "SELECT `image` FROM `room_images` WHERE `room_id` = '" . (int) $room_data['id'] . "' AND `thumb` = 1 LIMIT 1");
    if ($thumb_q && mysqli_num_rows($thumb_q) > 0) {
        $tr = mysqli_fetch_assoc($thumb_q);
        if (!empty($tr['image'])) {
            $thumb = ROOMS_IMG_PATH . $tr['image'];
        }
    }
    $rooms[] = [
        'data' => $room_data,
        'features' => $features,
        'facilities' => $facilities,
        'thumb' => $thumb,
        'fallback_thumb' => $fallbackRoomThumb,
    ];
}

$fea_stmt->close();
$fac_stmt->close();
$room_stmt->close();

if (count($rooms) === 0) {
    echo "<div class='alert alert-warning mb-0'>No matching rooms found.</div>";
    exit;
}

function h($s)
{
    return htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8');
}

ob_start();
?>
<div class="compare-rooms-result">
  <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
    <button type="button" class="btn btn-outline-secondary btn-sm shadow-none" onclick="resetCompareView()">
      <i class="bi bi-arrow-left me-1"></i> Change selection
    </button>
    <span class="badge bg-light text-dark border"><?php echo count($rooms); ?> room<?php echo count($rooms) === 1 ? '' : 's'; ?> selected</span>
  </div>

  <div class="row g-3 compare-room-cards">
    <?php foreach ($rooms as $r) :
        $d = $r['data'];
        $name = h($d['name']);
        $price = number_format((float) $d['price'], 0, '.', ',');
        $area = h($d['area']);
        ?>
    <div class="col-md-6">
      <div class="card h-100 border-0 shadow-sm overflow-hidden compare-room-card">
        <div class="compare-room-img-wrap">
          <img src="<?php echo h($r['thumb']); ?>" class="w-100 compare-room-img" alt="<?php echo $name; ?>" onerror="this.onerror=null;this.src='<?php echo h($r['fallback_thumb']); ?>';">
          <div class="compare-room-img-overlay">
            <span class="badge bg-dark bg-opacity-75">NPR <?php echo $price; ?> / night</span>
          </div>
        </div>
        <div class="card-body">
          <h5 class="card-title mb-1"><?php echo $name; ?></h5>
          <p class="small text-muted mb-3">
            <i class="bi bi-rulers me-1"></i><?php echo $area; ?> sq. ft.
            <span class="mx-2">·</span>
            <i class="bi bi-people me-1"></i><?php echo (int) $d['adult']; ?> adults, <?php echo (int) $d['children']; ?> children
          </p>

          <div class="mb-3">
            <h6 class="text-uppercase small fw-bold text-secondary mb-2">Features</h6>
            <?php if (count($r['features'])) : ?>
              <div class="d-flex flex-wrap gap-1">
                <?php foreach ($r['features'] as $f) : ?>
                  <span class="badge rounded-pill bg-light text-dark border"><?php echo h($f); ?></span>
                <?php endforeach; ?>
              </div>
            <?php else : ?>
              <span class="text-muted small">—</span>
            <?php endif; ?>
          </div>

          <div>
            <h6 class="text-uppercase small fw-bold text-secondary mb-2">Facilities</h6>
            <?php if (count($r['facilities'])) : ?>
              <ul class="list-unstyled small compare-room-list mb-0">
                <?php foreach ($r['facilities'] as $f) : ?>
                  <li><i class="bi bi-check2-circle text-success me-1"></i><?php echo h($f); ?></li>
                <?php endforeach; ?>
              </ul>
            <?php else : ?>
              <span class="text-muted small">—</span>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>

  <?php
  $summary_html = '';
  $n = count($rooms);
  if ($n === 1) {
      $summary_html = '<p class="mb-0"><strong>Tip:</strong> Select a second room in the list to see a side-by-side comparison of price and amenities.</p>';
  } elseif ($n === 2) {
      $a = $rooms[0];
      $b = $rooms[1];
      $pa = (float) $a['data']['price'];
      $pb = (float) $b['data']['price'];
      $na = h($a['data']['name']);
      $nb = h($b['data']['name']);
      $fa = count($a['facilities']);
      $fb = count($b['facilities']);

      $lines = [];
      if ($pa < $pb) {
          $lines[] = "<strong>{$na}</strong> is lower priced (NPR " . number_format($pa, 0, '.', ',') . " vs NPR " . number_format($pb, 0, '.', ',') . ").";
      } elseif ($pb < $pa) {
          $lines[] = "<strong>{$nb}</strong> is lower priced (NPR " . number_format($pb, 0, '.', ',') . " vs NPR " . number_format($pa, 0, '.', ',') . ").";
      } else {
          $lines[] = "Both rooms are priced the same per night (NPR " . number_format($pa, 0, '.', ',') . ").";
      }

      if ($fa > $fb) {
          $lines[] = "<strong>{$na}</strong> lists more facilities ({$fa} vs {$fb}).";
      } elseif ($fb > $fa) {
          $lines[] = "<strong>{$nb}</strong> lists more facilities ({$fb} vs {$fa}).";
      } else {
          $lines[] = "Both list the same number of facilities ({$fa}).";
      }

      $lines[] = 'Higher rates usually reflect more space or premium amenities—pick what fits your budget and needs.';
      $lis = '';
      foreach ($lines as $line) {
          $lis .= '<li class="mb-1">' . $line . '</li>';
      }
      $summary_html = '<ul class="mb-0 ps-3">' . $lis . '</ul>';
  }
  ?>
  <div class="card border-0 bg-light mt-4 compare-summary-card">
    <div class="card-body">
      <h6 class="fw-bold mb-3"><i class="bi bi-info-circle me-2 text-primary"></i>Summary</h6>
      <div class="small text-secondary">
        <?php echo $summary_html; ?>
      </div>
    </div>
  </div>
</div>
<style>
.compare-room-img-wrap { position: relative; background: #e9ecef; }
.compare-room-img { height: 200px; object-fit: cover; display: block; }
.compare-room-img-overlay { position: absolute; left: 0; right: 0; bottom: 0; padding: 10px; background: linear-gradient(transparent, rgba(0,0,0,.55)); }
.compare-room-list li { margin-bottom: 4px; }
</style>
<?php
echo ob_get_clean();
