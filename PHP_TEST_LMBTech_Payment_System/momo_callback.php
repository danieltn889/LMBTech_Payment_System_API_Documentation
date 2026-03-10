<?php

// Fetch incoming POST data (the callback data sent by the payment provider)
$callbackData = json_decode(file_get_contents('php://input'), true);

// Check if the callback data is valid
if (isset($callbackData['reference_id'], $callbackData['transaction_id'], $callbackData['status'], $callbackData['action'])) {
    // Call the receiveCallback method to process the payment
    $response = $paymentSystem->receiveCallback($callbackData);

    // Send a response back to the payment provider to acknowledge the callback
    header('Content-Type: application/json');
    echo json_encode(['status' => true, 'message' => 'Callback received and processed', 'data' => $response, 'site_url' => "www.site.com"?? null]);
} else {
    // Invalid callback data
    header('HTTP/1.1 400 Bad Request');
    echo json_encode(['status' => false, 'message' => 'Invalid callback data', 'site_url' => "www.site.com"?? null]);
}
