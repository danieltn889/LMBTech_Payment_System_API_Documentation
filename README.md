# LMBTech Payment System API Documentation

This repository contains documentation and test implementations for the LMBTech Payment System API, which provides payment processing capabilities for card and mobile money transactions.

## Project Structure

```
LMBTech_Payment_System_API_Documentation/
├── JS_TEST_LMBTech_Payment_System/
│   └── lmbtech_full_test.js          # JavaScript examples for API integration
├── PHP_TEST_LMBTech_Payment_System/
│   ├── PaymentSystem.php             # PHP class for API interactions
│   ├── test_card_api.php             # PHP test script for card payments
│   ├── test_momo_api.php             # PHP test script for mobile money payments
│   ├── card_callback.php             # Callback handler for card payment responses
│   └── momo_callback.php             # Callback handler for mobile money responses
└── README.md                         # This documentation file
```

## Overview

The LMBTech Payment System API allows merchants to accept payments through:
- **Card Payments**: Visa, Mastercard, and other card types via Pesapal integration
- **Mobile Money**: MTN Mobile Money and other mobile payment methods

## Authentication

All API requests require authentication using Basic Auth with your app key and secret key:

```
Authorization: Basic base64_encode(app_key:secret_key)
```

## PHP Implementation

### PaymentSystem Class

The `PaymentSystem.php` class provides a convenient wrapper for making API requests:

```php
require_once 'PaymentSystem.php';

$paymentSystem = new PaymentSystem($app_key, $secret_key);

// Make a payment request
$response = $paymentSystem->makeRequest('POST', [
    'action' => 'pay',
    'amount' => 1000,
    'payment_method' => 'card', // or 'momo'
    'email' => 'customer@example.com',
    'name' => 'Customer Name',
    'service_paid' => 'Your Service',
    'reference_id' => 'unique-reference-123',
    'callback_url' => 'https://yourdomain.com/callback'
]);
```

### Test Scripts

- `test_card_api.php`: Demonstrates card payment flow
- `test_momo_api.php`: Demonstrates mobile money payment flow

Run these scripts to test the integration:

```bash
php test_card_api.php
php test_momo_api.php
```

## JavaScript Implementation

The `lmbtech_full_test.js` file contains JavaScript examples using the Fetch API:

```javascript
const API_URL = 'https://pay.lmbtech.rw/pay/config/api.php';
const credentials = btoa(APP_KEY + ':' + SECRET_KEY);

const headers = {
  'Authorization': 'Basic ' + credentials,
  'Content-Type': 'application/json'
};

async function makePayment() {
  const response = await fetch(API_URL, {
    method: 'POST',
    headers: headers,
    body: JSON.stringify({
      action: 'pay',
      amount: 1000,
      payer_phone: '+250785085214',
      service_paid: 'Test Payment',
      email: 'test@example.com',
      name: 'Test User'
    })
  });
  
  const result = await response.json();
  console.log(result);
}
```

## Callback Handling

The API uses callbacks to notify your application of payment status:

- `card_callback.php`: Handles card payment callbacks from Pesapal
- `momo_callback.php`: Handles mobile money payment callbacks

Callbacks receive payment status updates and should be configured with HTTPS URLs for security.

## API Endpoints

- **Base URL**: `https://pay.lmbtech.rw/pay/config/api.php`
- **Card Redirect**: `https://pay.lmbtech.rw/pay/pesapal/iframe.php`

## Payment Flow

1. **Initiate Payment**: Send payment request with amount, customer details, and callback URL
2. **Redirect Customer**: For card payments, redirect to payment processor
3. **Process Payment**: Customer completes payment on processor's site
4. **Receive Callback**: API sends status update to your callback URL
5. **Verify Status**: Check payment status using reference ID

## Error Handling

The API returns structured responses with status and message fields. Always check the `status` field in responses:

```php
if ($response['status']) {
    // Payment successful
    echo "Payment completed: " . $response['message'];
} else {
    // Payment failed
    echo "Payment failed: " . $response['message'];
}
```

## Support

For API documentation and support, contact LMBTech support team.

## License

This documentation and test code is provided as-is for integration reference.