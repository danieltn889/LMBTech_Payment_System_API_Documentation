<?php
// Log file
$logFile = 'callback_log.txt';
file_put_contents($logFile, date('Y-m-d H:i:s') . " - Callback received\n", FILE_APPEND);

// Include the lmbtech class
require_once '../pay/config/lmbtech.php';

// Create an instance
$lmbtech = new LmbTechPaymentSystem();

$reference_id = '';
$transaction_id = '';
$status = '';

if (!empty($_POST)) {
    // Pesapal callback (form data)
    file_put_contents($logFile, "Form data: " . json_encode($_POST) . "\n", FILE_APPEND);
    $reference_id = $_POST['pesapal_merchant_reference'] ?? '';
    $transaction_id = $_POST['pesapal_transaction_tracking_id'] ?? '';
    $response_data = $_POST['pesapal_response_data'] ?? '';
    $status = (strtolower($response_data) === 'completed' || strtolower($response_data) === 'success') ? 'success' : 'failed';
} else {
    // Momo callback (JSON)
    $json = file_get_contents('php://input');
    file_put_contents($logFile, "JSON data: " . $json . "\n", FILE_APPEND);
    $callbackData = json_decode($json, true);
    if ($callbackData) {
        $reference_id = $callbackData['reference_id'] ?? '';
        $transaction_id = $callbackData['transaction_id'] ?? '';
        $status = $callbackData['status'] ?? '';
    }
}

file_put_contents($logFile, "Extracted: ref=$reference_id, txn=$transaction_id, status=$status\n", FILE_APPEND);

if (empty($reference_id) || empty($transaction_id)) {
    file_put_contents($logFile, "Invalid data\n", FILE_APPEND);
    echo json_encode(['status' => false, 'message' => 'Invalid callback data']);
    exit;
}

$response = $lmbtech->updatePaymentRecord($reference_id, $transaction_id, $status);

file_put_contents($logFile, "Update response: " . json_encode($response) . "\n", FILE_APPEND);

echo json_encode($response);
