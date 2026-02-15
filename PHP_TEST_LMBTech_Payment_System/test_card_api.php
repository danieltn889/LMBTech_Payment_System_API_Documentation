<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Load your class
require_once 'PaymentSystem.php';

// Initialize the system
$app_key = "your app key"; // Replace with your actual app key
$secret_key = "your secret key"; // Replace with your actual secret key
$paymentSystem = new PaymentSystem($app_key, $secret_key);

echo "\n============================================\n";
echo "🚀 STARTING CARD PAYMENT API TEST\n";
echo "============================================\n";

// ==================================================================
// 🔄 AUTO-GENERATE CALLBACK URL
// ==================================================================
// 1. Check if HTTPS or HTTP
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443)) ? "https://" : "http://";

// 2. Get Host (e.g., localhost or domain.com)
// If running in CLI, default to localhost, otherwise use server host
$domainName = 'localhost';

// 3. Get Current Directory (e.g., /pay.lmbtech.rw/pay/test)
$path = '/pay.lmbtech.rw/test_lmbtech_pay';

// 4. Combine to make the full URL
$autoCallbackUrl = $protocol . $domainName . $path . "/card_callback.php";

echo "ℹ️  Callback URL set to: " . $autoCallbackUrl . "\n";
echo "ℹ️  Callback URL set to: " . $autoCallbackUrl . "\n";


// ==================================================================
// 1️⃣ TEST CARD PAYMENT (Collection)
// ==================================================================
echo "\n🔵 [1] Testing POST Request (Create Card Payment)...\n";

$payData = [
    'email' => 'danieltn889@gmail.com',
    'name' => "Daniel Card Payment",
    'payment_method' => "card",
    'amount' => 150, // Amount for card payment
    'service_paid' => "test_card_pay",
    'reference_id' => "REF-CARD-" . date('YmdHis') . "-" . mt_rand(1000, 9999),
    'callback_url' => $autoCallbackUrl,
    'card_redirect_url' => $paymentSystem->card_redirect_url,
    'action' => "pay",
]; 

$payResponse = $paymentSystem->makeRequest("POST", $payData);

// Output the response
echo json_encode($payResponse, JSON_PRETTY_PRINT);

// If successful and has redirect, note it
if (isset($payResponse['redirect_url'])) {
    echo "\n🔗 Redirect URL: " . $payResponse['redirect_url'] . "\n";
    echo "📝 To complete the card payment, visit the redirect URL and enter card details.\n";
}

// ==================================================================
// 2️⃣ TEST GET REQUEST (Retrieve Payment Status)
// ==================================================================
if (isset($payResponse['data']['reference_id'])) {
    echo "\n🟢 [2] Testing GET Request (Retrieve Payment Status)...\n";
    $getResponse = $paymentSystem->makeRequest("GET", ['reference_id' => $payResponse['data']['reference_id']]);
    echo json_encode($getResponse, JSON_PRETTY_PRINT);
} else {
    echo "\n🟢 [2] Skipping GET test - No reference ID from POST\n";
}

// NOTE: Callback simulation removed to ensure status only updates on real payment completion.
// To test callback, manually trigger it after completing payment on Pesapal.

echo "\n============================================\n";
echo "✅ CARD PAYMENT TEST COMPLETED\n";
echo "============================================\n";
?>