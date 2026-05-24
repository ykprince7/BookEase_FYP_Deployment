<?php
require('inc/links.php');
require('inc/header.php');
require_once('inc/loyalty_points.php');

// Check if user is logged in and redirect to login page if not
if(!(isset($_SESSION['login']) && $_SESSION['login']==true)){
  redirect('index.php');
}

$user_id = $_SESSION['uId'];
awardMissingLoyaltyPointsForUser($user_id);
$points_balance = getLoyaltyPointsBalance($user_id);
$rewards = getAvailableRewards();
$transactions = getLoyaltyTransactions($user_id);
$user_vouchers = getUserVouchers($user_id);
$transaction_list = [];
while($t = mysqli_fetch_assoc($transactions)){
  $transaction_list[] = $t;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Loyalty Program - <?php echo $settings_r['site_title'] ?></title>
</head>
<body class="bg-light">
  <div class="container">
    <div class="row">
      <div class="col-12 my-5 mb-4 px-4">
        <h2 class="fw-bold">Loyalty Program</h2>
        <div style="font-size: 14px;">
          <a href="index.php" class="text-secondary text-decoration-none">HOME</a>
          <span class="text-secondary"> > </span>
          <a href="#" class="text-secondary text-decoration-none">LOYALTY PROGRAM</a>
        </div>
      </div>

      <!-- Points Balance -->
      <div class="col-lg-4 col-md-6 mb-4">
        <div class="card border-0 shadow-sm">
          <div class="card-body text-center">
            <h5 class="card-title">Your Points Balance</h5>
            <h2 class="text-primary mb-3"><?php echo $points_balance; ?></h2>
            <p class="text-muted">Earn 1 point for every NPR 10 spent</p>
          </div>
        </div>
      </div>

      <!-- Available Rewards for user -->
      <div class="col-lg-8 col-md-6 mb-4">
        <div class="card border-0 shadow-sm">
          <div class="card-body">
            <h5 class="card-title mb-4">Available Rewards</h5>
            <div class="row">
              <?php while($reward = mysqli_fetch_assoc($rewards)): ?>
                <div class="col-md-6 mb-3">
                  <div class="card h-100">
                    <div class="card-body">
                      <h6 class="card-title"><?php echo $reward['name']; ?></h6>
                      <p class="card-text"><?php echo $reward['description']; ?></p>
                      <p class="text-primary mb-3"><?php echo $reward['points_required']; ?> points required</p>
                      <?php if($points_balance >= $reward['points_required']): ?>
                        <button class="btn btn-primary btn-sm" onclick="redeemPoints(<?php echo $reward['id']; ?>)">
                          Redeem Now
                        </button>
                      <?php else: ?>
                        <button class="btn btn-secondary btn-sm" disabled>
                          Not Enough Points
                        </button>
                      <?php endif; ?>
                    </div>
                  </div>
                </div>
              <?php endwhile; ?>
            </div>
          </div>
        </div>
      </div>

      <!-- Transaction History -->
      <div class="col-12 mb-4">
        <div class="card border-0 shadow-sm">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <h5 class="card-title mb-0">Transaction History</h5>
              <?php if(count($transaction_list) > 5): ?>
              <button
                class="btn btn-outline-primary btn-sm"
                type="button"
                id="toggleMoreTransactionsBtn"
              >
                Show More
              </button>
              <?php endif; ?>
            </div>
            <div class="table-responsive">
              <table class="table table-hover">
                <thead>
                  <tr>
                    <th>Date</th>
                    <th>Type</th>
                    <th>Points</th>
                    <th>Description</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                    $initial_transactions = array_slice($transaction_list, 0, 5);
                    foreach($initial_transactions as $transaction):
                  ?>
                    <tr>
                      <td><?php echo date('M d, Y', strtotime($transaction['created_at'])); ?></td>
                      <td>
                        <span class="badge <?php echo $transaction['type'] == 'earn' ? 'bg-success' : 'bg-warning'; ?>">
                          <?php echo ucfirst($transaction['type']); ?>
                        </span>
                      </td>
                      <td><?php echo $transaction['points']; ?></td>
                      <td><?php echo $transaction['description']; ?></td>
                    </tr>
                  <?php endforeach; ?>
                  <?php if(count($transaction_list) > 5): ?>
                    <?php
                      $more_transactions = array_slice($transaction_list, 5);
                      foreach($more_transactions as $transaction):
                    ?>
                      <tr class="extra-transaction-row d-none">
                        <td><?php echo date('M d, Y', strtotime($transaction['created_at'])); ?></td>
                        <td>
                          <span class="badge <?php echo $transaction['type'] == 'earn' ? 'bg-success' : 'bg-warning'; ?>">
                            <?php echo ucfirst($transaction['type']); ?>
                          </span>
                        </td>
                        <td><?php echo $transaction['points']; ?></td>
                        <td><?php echo $transaction['description']; ?></td>
                      </tr>
                    <?php endforeach; ?>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      <!-- My Vouchers -->
      <div class="col-12 mb-4">
        <div class="card border-0 shadow-sm">
          <div class="card-body">
            <h5 class="card-title mb-4">My Vouchers</h5>
            <?php if(mysqli_num_rows($user_vouchers) > 0): ?>
              <div class="row">
                <?php while($voucher = mysqli_fetch_assoc($user_vouchers)): ?>
                  <div class="col-md-6 mb-3">
                    <div class="card h-100 border-primary">
                      <div class="card-body">
                        <h6 class="card-title text-primary"><?php echo htmlspecialchars($voucher['reward_name']); ?></h6>
                        <p class="card-text"><?php echo htmlspecialchars($voucher['discount_percent']); ?>% discount</p>
                        <p class="text-muted small">Expires: <?php echo date('M d, Y', strtotime($voucher['expires_at'])); ?></p>
                        <div class="input-group">
                          <input type="text" class="form-control" value="<?php echo htmlspecialchars($voucher['voucher_code']); ?>" readonly>
                          <button class="btn btn-outline-primary" onclick="copyToClipboard('<?php echo htmlspecialchars($voucher['voucher_code']); ?>')">Copy</button>
                        </div>
                        <a href="rooms.php" class="btn btn-primary btn-sm mt-2 w-100">Use in Booking</a>
                      </div>
                    </div>
                  </div>
                <?php endwhile; ?>
              </div>
            <?php else: ?>
              <div class="text-center text-muted py-4">
                <i class="bi bi-ticket-perforated fs-1 mb-2"></i>
                <p>No active vouchers</p>
                <p class="small">Redeem points above to get voucher codes for booking discounts</p>
              </div>
            <?php endif; ?>
          </div>
        </div>
      </div>

    </div>
  </div>

  <!-- Voucher Success Modal -->
  <div class="modal fade" id="voucherModal" tabindex="-1" aria-labelledby="voucherModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header bg-success text-white">
          <h5 class="modal-title" id="voucherModalLabel">
            <i class="bi bi-check-circle me-2"></i>Points Redeemed Successfully!
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div id="voucherModalContent">
            <!-- Content will be populated by JavaScript -->
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <a href="rooms.php" class="btn btn-primary">Go to Booking</a>
        </div>
      </div>
    </div>
  </div>

  <?php require('inc/footer.php'); ?>

  <script>
    function redeemPoints(rewardId) {
      if(confirm('Are you sure you want to redeem your points for this reward?')) {
        let xhr = new XMLHttpRequest();
        xhr.open("POST", "ajax/loyalty.php", true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        
        xhr.onload = function() {
          try {
            let data = JSON.parse(this.responseText);
            if(data.success) {
              // Show voucher in modal instead of alert
              showVoucherModal(data);
            } else {
              alert('error', data.message);
            }
          } catch (e) {
            alert('error', 'An error occurred while processing the request');
          }
        }
        
        xhr.onerror = function() {
          alert('error', 'Network error occurred');
        }
        
        xhr.send('redeem_points=' + rewardId);
      }
    }

    function showVoucherModal(data) {
      const modalContent = document.getElementById('voucherModalContent');
      const voucherCode = data.voucher_code;
      
      modalContent.innerHTML = `
        <div class="text-center mb-3">
          <i class="bi bi-ticket-perforated text-success" style="font-size: 3rem;"></i>
        </div>
        <p class="text-success fw-bold">${data.message}</p>
        <div class="alert alert-info">
          <strong>Use this voucher code during booking:</strong>
          <div class="input-group mt-2">
            <input type="text" class="form-control" value="${voucherCode}" readonly id="modalVoucherCodeInput">
            <button class="btn btn-outline-primary" onclick="copyModalVoucherCode()">
              <i class="bi bi-clipboard"></i> Copy
            </button>
          </div>
          <small class="text-muted mt-1 d-block">Valid for 30 days from now</small>
        </div>
        <div class="alert alert-light">
          <strong>What happens next?</strong>
          <ul class="mb-0 mt-2">
            <li>The voucher code has been added to your "My Vouchers" section below</li>
            <li>Use this code when booking a room to get your discount</li>
            <li>You can copy the code now or access it anytime from your vouchers</li>
          </ul>
        </div>
      `;
      
      // Show the modal
      const modal = new bootstrap.Modal(document.getElementById('voucherModal'));
      modal.show();
      
      // Refresh the page after modal is closed to show updated vouchers
      document.getElementById('voucherModal').addEventListener('hidden.bs.modal', function() {
        window.location.reload();
      });
    }

    function copyModalVoucherCode() {
      const input = document.getElementById('modalVoucherCodeInput');
      if (input) {
        input.select();
        document.execCommand('copy');
        
        // Show feedback
        const button = input.nextElementSibling;
        const originalText = button.innerHTML;
        button.innerHTML = '<i class="bi bi-check"></i> Copied!';
        button.classList.remove('btn-outline-primary');
        button.classList.add('btn-success');
        
        setTimeout(() => {
          button.innerHTML = originalText;
          button.classList.remove('btn-success');
          button.classList.add('btn-outline-primary');
        }, 2000);
      }
    }

    // Toggle additional transaction rows without adding layout gaps
    const extraTransactionRows = document.querySelectorAll('.extra-transaction-row');
    const toggleMoreTransactionsBtn = document.getElementById('toggleMoreTransactionsBtn');
    if (toggleMoreTransactionsBtn && extraTransactionRows.length > 0) {
      let expanded = false;
      toggleMoreTransactionsBtn.addEventListener('click', function() {
        expanded = !expanded;
        extraTransactionRows.forEach(function(row) {
          row.classList.toggle('d-none', !expanded);
        });
        toggleMoreTransactionsBtn.textContent = expanded ? 'Show Less' : 'Show More';
      });
    }
  </script>
</body>
</html> 