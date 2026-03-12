<?php
// Load your class
require_once 'PaymentSystem.php';

// Initialize the system
$paymentSystem = new PaymentSystem();

// echo "\n============================================\n";
// echo "🚀 STARTING FULL API TEST SUITE\n";
// echo "============================================\n";

// // ==================================================================
// // 🔄 AUTO-GENERATE CALLBACK URL
// // ==================================================================
// // 1. Check if HTTPS or HTTP (handle CLI case)
// $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || 
//             (!empty($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443)) ? "https://" : "http://";

// // 2. Get Host (e.g., localhost or domain.com)
// // If running in CLI, default to localhost, otherwise use server host
// $domainName = $_SERVER['HTTP_HOST'] ?? 'localhost';

// // 3. Get Current Directory (e.g., /pay.lmbtech.rw/pay/test)
// // For CLI, calculate relative path from document root
// if (php_sapi_name() === 'cli') {
//     // Hardcode the path for this specific test environment
//     $path = '/pay.lmbtech.rw/test_lmbtech_pay';
// } else {
//     $path = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
// }

// // 4. Combine to make the full URL
// $autoCallbackUrl = $protocol . $domainName . $path . "/momo_callback.php";

// echo "ℹ️  Callback URL set to: " . $autoCallbackUrl . "\n";


// // ==================================================================
// // 1️⃣ TEST PAY (Collection) - We will capture the ID here!
// // ==================================================================
// echo "\n🔵 [1] Testing POST Request (Create Payment)...\n";

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

// // --- 🔍 CAPTURE THE REFERENCE ID ---
// $capturedId = null;

// // Try to find the reference_id in common response locations
// if (isset($payResponse['data']['reference_id'])) {
//     $capturedId = $payResponse['data']['reference_id'];
// } elseif (isset($payResponse['reference_id'])) {
//     $capturedId = $payResponse['reference_id'];
// } elseif (isset($payResponse['refid'])) {
//     $capturedId = $payResponse['refid'];
// }

// // Fallback: If API didn't return it, use the one we sent (since we know it)
// if (!$capturedId && isset($payData['reference_id'])) {
//     $capturedId = $payData['reference_id'];
//     echo "⚠️ Warning: API didn't return reference_id in response. Using the ID we sent: $capturedId\n";
// }

// // ==================================================================
// // 2️⃣, 3️⃣, 4️⃣ DYNAMIC TESTS (Using Captured ID)
// // ==================================================================

// if ($capturedId) {
//     echo "\n✅ CAPTURED ID for Next Tests: " . $capturedId . "\n";

//     // ------------------------------------------------------------------
//     // 2️⃣ TEST GET (Retrieve)
//     // ------------------------------------------------------------------
//     echo "\n🟢 [2] Testing GET Request (Retrieve Payment)...\n";
//     $getResponse = $paymentSystem->makeRequest("GET", ['reference_id' => $capturedId]);
//     echo json_encode($getResponse, JSON_PRETTY_PRINT) . "\n";

//     // ------------------------------------------------------------------
//     // 3️⃣ TEST PUT (Update)
//     // ------------------------------------------------------------------
//     echo "\n🟠 [3] Testing PUT Request (Update Payment Status)...\n";
//     $updateData = [
//         'reference_id' => $capturedId,
//         'status' => 'success',
//         'transaction_id' => 'TRANS-' . mt_rand(10000, 99999), // Simulate a transaction ID
//         'action' => 'update'
//     ];
//     $putResponse = $paymentSystem->makeRequest("PUT", $updateData);
//     echo json_encode($putResponse, JSON_PRETTY_PRINT) . "\n";

//     // ------------------------------------------------------------------
//     // 4️⃣ TEST DELETE (Cancel)
//     // ------------------------------------------------------------------
//     echo "\n🔴 [4] Testing DELETE Request (Cancel Payment)...\n";
//     $deleteData = [
//         'reference_id' => $capturedId,
//         'action' => 'delete'
//     ];
//     $deleteResponse = $paymentSystem->makeRequest("DELETE", $deleteData);
//     echo json_encode($deleteResponse, JSON_PRETTY_PRINT) . "\n";

// } else {
//     echo "\n❌ Skipping GET/PUT/DELETE tests because Payment Creation failed or returned no ID.\n";
// }

// ==================================================================
// 5️⃣ TEST PAYOUT (Disbursement)
// ==================================================================
// echo "\n🔵 [5] Testing POST Request (Create Payout)...\n";

// $payoutData = [
//     'email' => 'danieltn889@gmail.com',
//     'name' => "Daniel Payout",
//     'payment_method' => "MTN_MOMO_RWA",
//     // ⚠️ NOTE: Amount must be covered by your user's successful payments balance
//     'amount' => 1500, 
//     'payer_phone' => "+250785085214",
//     'service_paid' => "test_payout",
//     'reference_id' => "REF-OUT-" . date('YmdHis') . "-" . mt_rand(1000, 9999),
//     // 👇 USING AUTO-GENERATED URL HERE
//     'callback_url' => $autoCallbackUrl,
//     'action' => "pay",
//     'debug' => true,
// ];

// $payoutResponse = $paymentSystem->makeRequest("POST", $payoutData);
// echo json_encode($payoutResponse, JSON_PRETTY_PRINT) . "\n";

// // ==================================================================
// // 6️⃣ TEST SMS (Optional)
// // ==================================================================
// echo "\n🟣 [6] Testing SMS Request...\n";
// $smsData = [
//     'name' => "Daniel",
//     'tel' => "0785085214",
//     'message' => "Hi Daniel, this is a test from your Payment System API.",
//     'action' => "sms",
// ];
// $smsResponse = $paymentSystem->makeRequest("POST", $smsData);
// echo json_encode($smsResponse, JSON_PRETTY_PRINT) . "\n";

echo "\n============================================\n";
echo "🏁 TEST SUITE COMPLETED\n";
echo "============================================\n";
?>