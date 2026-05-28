<?php
require('admin/inc/db_config.php');
require('admin/inc/essentials.php');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!(isset($_SESSION['login']) && $_SESSION['login'] == true)) {
    redirect('index.php');
}

if (isset($_GET['order_id']) && isset($_GET['amount'])) {
    $ORDER_ID   = $_GET['order_id'];
    $TXN_AMOUNT = number_format((float) $_GET['amount'], 2, '.', '');

    $merchant_code    = "EPAYTEST";
    $secret_key       = "8gBm/:&EnhH.1/q";
    $esewa_url        = "https://rc-epay.esewa.com.np/api/epay/main/v2/form";
    $transaction_uuid = $ORDER_ID;

    // Use APP_URL env var (set in Railway Variables) — fallback to auto-detect for XAMPP
    $app_url = getenv('APP_URL');
    if (!$app_url) {
        $scheme  = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $base    = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
        $app_url = $scheme . '://' . $_SERVER['HTTP_HOST'] . $base;
    }
    $app_url = rtrim($app_url, '/');

    $success_url = $app_url . "/esewa_success.php";
    $failure_url = $app_url . "/esewa_failure.php";

    error_log("eSewa success_url: " . $success_url);
    error_log("eSewa failure_url: " . $failure_url);

    $params = [
        'amount'                  => $TXN_AMOUNT,
        'tax_amount'              => '0',
        'total_amount'            => $TXN_AMOUNT,
        'transaction_uuid'        => $transaction_uuid,
        'product_code'            => $merchant_code,
        'product_service_charge'  => '0',
        'product_delivery_charge' => '0',
        'success_url'             => $success_url,
        'failure_url'             => $failure_url,
        'signed_field_names'      => 'total_amount,transaction_uuid,product_code',
    ];

    $string_to_sign = "total_amount=" . $params['total_amount'] .
                      ",transaction_uuid=" . $params['transaction_uuid'] .
                      ",product_code=" . $params['product_code'];

    $params['signature'] = base64_encode(hash_hmac('sha256', $string_to_sign, $secret_key, true));
?>
<!DOCTYPE html>
<html>
<head>
    <title>Redirecting to eSewa...</title>
    <style>
        .loading-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
            font-family: Arial, sans-serif;
        }
        .spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #3498db;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 1s linear infinite;
            margin-bottom: 20px;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="loading-container">
        <div class="spinner"></div>
        <p>Redirecting to eSewa payment gateway...</p>
    </div>
    <form action="<?php echo $esewa_url; ?>" method="POST" id="esewa_form">
        <?php foreach ($params as $key => $value) { ?>
            <input type="hidden" name="<?php echo $key; ?>" value="<?php echo htmlspecialchars($value); ?>">
        <?php } ?>
    </form>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                document.getElementById('esewa_form').submit();
            }, 1500);
        });
    </script>
</body>
</html>
<?php
} else {
    redirect('index.php');
}
?>