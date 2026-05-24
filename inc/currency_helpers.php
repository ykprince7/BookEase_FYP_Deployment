<?php
/**
 * Shared currency conversion helpers.
 * Include this file instead of duplicating these functions across pages.
 * All rates in the exchange_rates table are stored relative to USD
 * (Open Exchange Rates free tier, base = USD).
 */

if (!function_exists('getExchangeRateFromDB')) {
    function getExchangeRateFromDB($currency, $con) {
        $query = "SELECT `rate` FROM `exchange_rates` WHERE `currency` = ?";
        $stmt = $con->prepare($query);
        $stmt->bind_param('s', $currency);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return $row ? (float) $row['rate'] : null;
    }
}

/**
 * Convert $amount from $from_currency to $to_currency using DB rates.
 * Returns a plain float so callers can format as needed.
 * Falls back to the original amount if either rate is missing.
 */
if (!function_exists('convertCurrency')) {
    function convertCurrency($amount, $from_currency, $to_currency, $con) {
        if ($from_currency === $to_currency) {
            return (float) $amount;
        }

        $from_rate = getExchangeRateFromDB($from_currency, $con);
        $to_rate   = getExchangeRateFromDB($to_currency,   $con);

        if ($from_rate && $to_rate) {
            return (float) ($amount * ($to_rate / $from_rate));
        }

        return (float) $amount;
    }
}

/**
 * Format a converted amount for display (2 decimal places, no thousands separator).
 * Use this for payment amounts that must not contain commas.
 */
if (!function_exists('formatCurrencyAmount')) {
    function formatCurrencyAmount($amount, $decimals = 2) {
        return number_format((float) $amount, $decimals, '.', '');
    }
}
