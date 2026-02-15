<?php
// Load your class
require_once 'PaymentSystem.php';

// Initialize the system
$app_key = "your app key"; // Replace with your actual app key
$secret_key = "your secret key"; // Replace with your actual secret key
$paymentSystem = new PaymentSystem($app_key, $secret_key);

echo "\n============================================\n";
echo "🚀 STARTING FULL API TEST SUITE\n";
echo "============================================\n";

$autoCallbackUrl = "https://pay.lmbtech.rw/pay/test/callback.php"; // Hardcoded for test

$payData = [
    'email' => 'danieltn889@gmail.com',
    'name' => "Daniel Payment",
    'payment_method' => "MTN_MOMO_RWA",
    // Note: Amount is 110 to be realistic. Ensure this user has balance for Payout later!
    'amount' => 1000, 
    'payer_phone' => "+250785085214",
    'service_paid' => "test_pay",
    'reference_id' => "REF-IN-" . date('YmdHis') . "-" . mt_rand(1000, 9999),
    // 👇 USING AUTO-GENERATED URL HERE
    'callback_url' => $autoCallbackUrl, 
    'action' => "pay",
];

$payResponse = $paymentSystem->makeRequest("POST", $payData);
echo json_encode($payResponse, JSON_PRETTY_PRINT) . "\n";

// --- 🔍 CAPTURE THE REFERENCE ID ---
$capturedId = null;

// Try to find the reference_id in common response locations
if (isset($payResponse['data']['reference_id'])) {
    $capturedId = $payResponse['data']['reference_id'];
} elseif (isset($payResponse['reference_id'])) {
    $capturedId = $payResponse['reference_id'];
} elseif (isset($payResponse['refid'])) {
    $capturedId = $payResponse['refid'];
}

// Fallback: If API didn't return it, use the one we sent (since we know it)
if (!$capturedId && isset($payData['reference_id'])) {
    $capturedId = $payData['reference_id'];
    echo "⚠️ Warning: API didn't return reference_id in response. Using the ID we sent: $capturedId\n";
}

// ==================================================================
// 2️⃣, 3️⃣, 4️⃣ DYNAMIC TESTS (Using Captured ID)
// ==================================================================

if ($capturedId) {
    echo "\n✅ CAPTURED ID for Next Tests: " . $capturedId . "\n";

    // ------------------------------------------------------------------
    // 2️⃣ TEST GET (Retrieve)
    // ------------------------------------------------------------------
    echo "\n🟢 [2] Testing GET Request (Retrieve Payment)...\n";
    $getResponse = $paymentSystem->makeRequest("GET", ['reference_id' => $capturedId]);
    echo json_encode($getResponse, JSON_PRETTY_PRINT) . "\n";


} else {
    echo "\n❌ Skipping GET/PUT/DELETE tests because Payment Creation failed or returned no ID.\n";
}

echo "\n============================================\n";
echo "🏁 TEST SUITE COMPLETED\n";
echo "============================================\n";
?>