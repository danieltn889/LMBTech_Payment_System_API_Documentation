// LMBTech Payment API - Code Examples

// JavaScript (Fetch API) Example
const API_URL = 'https://pay.lmbtech.rw/pay/config/api.php';
const APP_KEY = 'your_app_key';
const SECRET_KEY = 'your_secret_key';

// Create auth header
const credentials = btoa(APP_KEY + ':' + SECRET_KEY);
const headers = {
  'Authorization': 'Basic ' + credentials,
  'Content-Type': 'application/json'
};

// Make payment
async function makePayment() {
  const payload = {
    action: 'pay',
    amount: 1000,
    payer_phone: '+250785085214',
    service_paid: 'Test Payment',
    email: 'test@example.com',
    name: 'Test User'
  };

  try {
    const response = await fetch(API_URL, {
      method: 'POST',
      headers: headers,
      body: JSON.stringify(payload)
    });
    
    const result = await response.json();
    console.log(result);
  } catch (error) {
    console.error('Error:', error);
  }
}

// Check status
async function checkStatus(referenceId) {
  try {
    const response = await fetch(`${API_URL}?reference_id=${referenceId}`, {
      headers: headers
    });
    
    const result = await response.json();
    console.log(result);
  } catch (error) {
    console.error('Error:', error);
  }
}

// PHP (cURL) Example
/*
<?php
$apiUrl = 'https://pay.lmbtech.rw/pay/config/api.php';
$appKey = 'your_app_key';
$secretKey = 'your_secret_key';

// Create auth header
$credentials = base64_encode($appKey . ':' . $secretKey);
$headers = [
  'Authorization: Basic ' . $credentials,
  'Content-Type: application/json'
];

// Make payment
function makePayment($amount, $phone, $service) {
  global $apiUrl, $headers;
  
  $payload = [
    'action' => 'pay',
    'amount' => $amount,
    'payer_phone' => $phone,
    'service_paid' => $service,
    'email' => 'customer@example.com',
    'name' => 'Customer Name'
  ];
  
  $ch = curl_init($apiUrl);
  curl_setopt($ch, CURLOPT_POST, true);
  curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
  curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  
  $response = curl_exec($ch);
  curl_close($ch);
  
  return json_decode($response, true);
}

// Check status
function checkStatus($referenceId) {
  global $apiUrl, $headers;
  
  $ch = curl_init($apiUrl . '?reference_id=' . $referenceId);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  
  $response = curl_exec($ch);
  curl_close($ch);
  
  return json_decode($response, true);
}
?>
*/
