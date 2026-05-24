<?php
/**
 * Exchange Rate Updater
 * Fetches live rates from Open Exchange Rates (base: USD) and upserts them
 * into the exchange_rates table.
 *
 * Run via Windows Task Scheduler every hour using cron/run_cron.bat.
 * The API key below is the real active key for this project.
 */
require(__DIR__ . '/../admin/inc/db_config.php');

function fetchExchangeRates() {
    $api_key = '89c6b23c0ec94eaa8f011a54df9f80b9';
    $url = "https://openexchangerates.org/api/latest.json?app_id={$api_key}";
    $response = @file_get_contents($url);

    if ($response === false) {
        throw new Exception('Failed to fetch exchange rates from API. Check network or allow_url_fopen.');
    }

    $decoded = json_decode($response, true);
    if (!is_array($decoded)) {
        throw new Exception('Invalid JSON response from API.');
    }

    if (!empty($decoded['error'])) {
        $msg = $decoded['description'] ?? 'Unknown API error.';
        throw new Exception('API error: ' . $msg);
    }

    if (empty($decoded['rates']) || !is_array($decoded['rates'])) {
        throw new Exception('No rates received from API.');
    }

    return [
        'rates'     => $decoded['rates'],
        'timestamp' => $decoded['timestamp'] ?? time(),
    ];
}

function updateExchangeRates(array $rates, $con) {
    $current_time = date('Y-m-d H:i:s');
    $updated_count = 0;

    $query = "INSERT INTO `exchange_rates` (`currency`, `rate`, `last_updated`)
              VALUES (?, ?, ?)
              ON DUPLICATE KEY UPDATE `rate` = VALUES(`rate`), `last_updated` = VALUES(`last_updated`)";
    $stmt = $con->prepare($query);
    if (!$stmt) {
        throw new Exception('Failed to prepare DB statement: ' . $con->error);
    }

    foreach ($rates as $currency => $rate) {
        $stmt->bind_param('sds', $currency, $rate, $current_time);
        if (!$stmt->execute()) {
            throw new Exception('DB execute failed for ' . $currency . ': ' . $stmt->error);
        }
        $updated_count++;
    }

    $stmt->close();
    return $updated_count;
}

try {
    $data  = fetchExchangeRates();
    $count = updateExchangeRates($data['rates'], $con);
    $api_ts = date('Y-m-d H:i:s', $data['timestamp']);
    echo "Exchange rates updated successfully. Rows processed: {$count}. API data as of: {$api_ts}";
} catch (Exception $e) {
    http_response_code(500);
    echo "Exchange rate update failed: " . $e->getMessage();
}
