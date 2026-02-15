# LMBTech Payment API Documentation

## Overview
The LMBTech Payment API allows you to integrate payment collection, disbursement (payout), SMS sending, and payment status checking into your applications. The API supports multiple payment methods including MTN Mobile Money (Rwanda), card payments via Pesapal, and other providers.

## Base URL
```
https://pay.lmbtech.rw/pay/config/api.php
```

## Authentication
All API requests require HTTP Basic Authentication using your App Key and Secret Key.

### Headers
```
Authorization: Basic <base64-encoded-credentials>
Content-Type: application/json
```

### Creating Credentials
1. Combine your App Key and Secret Key with a colon: `app_key:secret_key`
2. Base64 encode the result
3. Include in Authorization header as `Basic <encoded-string>`

**Example:**
```javascript
const appKey = 'app_68b06fca2067717563934188958';
const secretKey = 'scrt_68b06fca206911756393418';
const credentials = btoa(appKey + ':' + secretKey);
const authHeader = 'Basic ' + credentials;
```

## API Endpoints

### 1. Make a Payment (Collection)
Collect payments from customers via mobile money or card.

**Method:** `POST`  
**Action:** `pay`

#### Required Fields
- `amount` (number): Payment amount in RWF (minimum 100)
- `payer_phone` (string): Customer's phone number (for mobile money)
- `service_paid` (string): Description of the service
- `action`: `"pay"`

#### Optional Fields
- `email` (string): Customer email
- `name` (string): Customer name
- `callback_url` (string): URL to receive payment notifications
- `reference_id` (string): Custom reference ID (auto-generated if not provided)
- `payment_method` (string): `"MTN_MOMO_RWA"` (default) or `"card"`

#### Card Payment Additional Fields
For card payments, also include:
- `card_redirect_url` (string): URL where users are redirected after payment

#### Example Request
```json
{
  "action": "pay",
  "amount": 1000,
  "payer_phone": "+250785085214",
  "service_paid": "Service Payment",
  "email": "customer@example.com",
  "name": "John Doe",
  "callback_url": "https://your-site.com/callback",
  "reference_id": "PAY-20240215-123456-789"
}
```

#### Card Payment Example
```json
{
  "action": "pay",
  "amount": 5000,
  "service_paid": "Premium Service",
  "email": "customer@example.com",
  "name": "Jane Doe",
  "payment_method": "card",
  "card_redirect_url": "https://your-site.com/card-complete",
  "callback_url": "https://your-site.com/callback"
}
```

### 2. Send Payout (Disbursement)
Send money to recipients via mobile money.

**Method:** `POST`  
**Action:** `payout`

#### Required Fields
- `amount` (number): Payout amount in RWF (minimum 100)
- `payer_phone` (string): Recipient's phone number
- `service_paid` (string): Description of the payout
- `action`: `"payout"`

#### Optional Fields
- `email` (string): Your account email
- `name` (string): Recipient name
- `callback_url` (string): URL to receive payout notifications
- `reference_id` (string): Custom reference ID (auto-generated if not provided)

#### Example Request
```json
{
  "action": "payout",
  "amount": 2500,
  "payer_phone": "+250785085214",
  "service_paid": "Salary Payment",
  "email": "youraccount@example.com",
  "name": "Employee Name",
  "callback_url": "https://your-site.com/callback",
  "reference_id": "POUT-20240215-123456-789"
}
```

### 3. Send SMS
Send SMS messages to recipients.

**Method:** `POST`  
**Action:** `sms`

#### Required Fields
- `tel` (string): Recipient phone number (07XXXXXXXX format)
- `message` (string): SMS content
- `action`: `"sms"`

#### Optional Fields
- `name` (string): Sender name (default: "LMBTECH")

#### Example Request
```json
{
  "action": "sms",
  "tel": "0785085214",
  "message": "Your payment has been received successfully.",
  "name": "MyCompany"
}
```

### 4. Check Payment Status
Check the status of any payment or payout using the reference ID.

**Method:** `GET`  
**Query Parameter:** `reference_id`

#### Example Request
```
GET https://pay.lmbtech.rw/pay/config/api.php?reference_id=PAY-20240215-123456-789
```

## Response Format
All responses are in JSON format with the following structure:

### Success Response
```json
{
  "status": "success",
  "message": "Operation completed successfully",
  "data": {
    // Additional data depending on the operation
  }
}
```

### Error Response
```json
{
  "status": "fail",
  "message": "Error description"
}
```

### Payment Response Examples

#### Mobile Money Payment Success
```json
{
  "status": "success",
  "message": "Payment initiated successfully",
  "data": {
    "reference_id": "PAY-20240215-123456-789",
    "amount": 1000,
    "status": "pending"
  }
}
```

#### Card Payment Success
```json
{
  "status": "success",
  "message": "Redirect to card payment gateway to complete payment",
  "data": {
    "reference_id": "PAY-20240215-123456-789",
    "amount": 5000,
    "payment_method": "card",
    "redirect_url": "https://your-site.com/card-redirect?reference_id=PAY-20240215-123456-789&amount=5000&payer_phone=%2B250785085214&full_name=John+Doe&callback_url=https%3A//your-site.com/callback"
  }
}
```

#### Status Check Response
```json
{
  "status": "success",
  "message": "Payment status fetched successfully",
  "payment_status": "success",
  "refid": "PAY-20240215-123456-789",
  "data": {
    "id": 123,
    "amount": 1000,
    "status": "success",
    "reference_id": "PAY-20240215-123456-789",
    "transaction_id": "TXN-123456",
    "name": "John Doe",
    // ... other payment details
  }
}
```

#### SMS Response
```json
{
  "status": "success",
  "message": "Message sent successfully. Used 1 SMS credit(s).",
  "data": {
    // SMS provider response data
  },
  "remainingSms": 99
}
```

## Status Values
- `pending`: Payment/payout initiated, awaiting completion
- `success`: Payment/payout completed successfully
- `fail`: Payment/payout failed
- `error`: System error occurred

## Phone Number Formats
- **MTN Mobile Money:** `+2507XXXXXXXX` or `07XXXXXXXX`
- **SMS:** `07XXXXXXXX` (without country code)

## Amount Limits
- **Minimum payment:** 100 RWF
- **Maximum payment:** Varies by payment method and user limits

## Callbacks
For payments and payouts, you can provide a `callback_url` to receive real-time notifications when the transaction status changes. The API will POST JSON data to your callback URL.

### Callback Payload Example
```json
{
  "reference_id": "PAY-20240215-123456-789",
  "status": "success",
  "amount": 1000,
  "transaction_id": "TXN-123456",
  "payment_method": "MTN_MOMO_RWA"
}
```

## Implementing Callbacks

Callbacks are essential for receiving real-time notifications about payment status changes. You need to implement callback handlers for both mobile money (MOMO) and card payments. Below are language-agnostic implementation guides with pseudocode examples.

### Mobile Money (MOMO) Callback Implementation

For MTN Mobile Money payments, the callback is sent as JSON POST data to your specified `callback_url`.

#### General Implementation Steps

1. **Receive the HTTP POST request** with JSON body
2. **Parse the JSON data** to extract callback information
3. **Validate the callback data** (check required fields)
4. **Process the callback** (update your database, send notifications, etc.)
5. **Respond with success** to acknowledge receipt

#### Pseudocode Example

```
function handleMomoCallback(request):
    # Step 1: Get raw POST data
    rawData = request.getBody()
    
    # Step 2: Parse JSON
    try:
        callbackData = JSON.parse(rawData)
    except:
        return errorResponse("Invalid JSON data")
    
    # Step 3: Validate required fields
    if not (callbackData.has('reference_id') and 
            callbackData.has('transaction_id') and 
            callbackData.has('status') and 
            callbackData.has('action')):
        return errorResponse("Missing required fields", 400)
    
    # Step 4: Process the callback
    result = processPaymentCallback(callbackData)
    
    # Step 5: Return success response
    return jsonResponse({"status": true, "message": "Callback processed"})

function processPaymentCallback(data):
    # Update your database with the new status
    updatePaymentRecord(data.reference_id, data.transaction_id, data.status)
    
    # Send notifications if needed
    if data.status == "success":
        sendSuccessNotification(data.reference_id)
    
    # Log the callback
    logCallback(data)
    
    return true
```

**Key Points:**
- Always validate the incoming data
- Use the raw request body to read JSON data
- Respond with HTTP 200 and JSON to acknowledge receipt
- Log all callbacks for debugging
- Process the callback asynchronously if needed

### Card Payment Callback Implementation

Card payments via Pesapal send callbacks as form POST data (not JSON).

#### General Implementation Steps

1. **Receive the HTTP POST request** with form data
2. **Extract form parameters** from the request
3. **Map provider status** to your internal status values
4. **Validate the data** (check required fields)
5. **Process the callback** (update records, send notifications)
6. **Respond with success** to acknowledge receipt

#### Pseudocode Example

```
function handleCardCallback(request):
    # Step 1: Check if form data exists
    if request.hasFormData():
        # Pesapal callback (form data)
        reference_id = request.getFormParam('pesapal_merchant_reference') or ''
        transaction_id = request.getFormParam('pesapal_transaction_tracking_id') or ''
        response_data = request.getFormParam('pesapal_response_data') or ''
        
        # Map Pesapal status to your system status
        status = mapPesapalStatus(response_data)
    else:
        # Fallback for JSON format if needed
        rawData = request.getBody()
        try:
            callbackData = JSON.parse(rawData)
            reference_id = callbackData.reference_id or ''
            transaction_id = callbackData.transaction_id or ''
            status = callbackData.status or ''
        except:
            return errorResponse("Invalid data format")
    
    # Step 2: Log the callback
    logCallback("Card callback received: ref=" + reference_id + ", txn=" + transaction_id + ", status=" + status)
    
    # Step 3: Validate data
    if not reference_id or not transaction_id:
        return errorResponse("Missing required data", 400)
    
    # Step 4: Process the callback
    result = updatePaymentRecord(reference_id, transaction_id, status)
    
    # Step 5: Return response
    return jsonResponse(result)

function mapPesapalStatus(response_data):
    lower_response = response_data.toLowerCase()
    if lower_response == 'completed' or lower_response == 'success':
        return 'success'
    else:
        return 'failed'
```

**Key Points:**
- Card callbacks come as form data, not JSON
- Pesapal uses specific field names like `pesapal_merchant_reference`
- Map external status values to your internal status system
- Always log callbacks for audit trails
- Validate data before processing
- Handle both success and failure statuses

### General Callback Best Practices

1. **Security:** Verify the callback source if possible (IP whitelisting, signature verification)
2. **Idempotency:** Ensure processing the same callback multiple times doesn't cause issues
3. **Logging:** Log all incoming callbacks with timestamps
4. **Error Handling:** Respond appropriately to invalid data
5. **Asynchronous Processing:** For heavy operations, queue the processing
6. **Testing:** Test callbacks with tools like ngrok for local development
7. **Status Mapping:** Map provider-specific statuses to your system's statuses
8. **Confirmation:** Always confirm receipt with proper HTTP responses

### Testing Callbacks Locally

For local development, use tools like ngrok to expose your local server:

```bash
# Install ngrok
npm install -g ngrok

# Expose local server
ngrok http 80

# Use the ngrok URL as callback_url in your API requests
```

Then test with cURL:

```bash
# Test MOMO callback
curl -X POST http://your-ngrok-url.com/momo_callback \
  -H "Content-Type: application/json" \
  -d '{"reference_id":"TEST-123","transaction_id":"TXN-456","status":"success","action":"pay"}'

# Test card callback
curl -X POST http://your-ngrok-url.com/card_callback \
  -d "pesapal_merchant_reference=TEST-123&pesapal_transaction_tracking_id=TXN-456&pesapal_response_data=COMPLETED"
```

## Error Handling
Common HTTP status codes:
- `200`: Success
- `401`: Authentication failed (invalid credentials)
- `400`: Bad request (missing/invalid parameters)
- `500`: Internal server error

## Rate Limits
- SMS: Limited by your account balance
- Payments: Subject to payment provider limits
- API calls: Generally unlimited, but monitor for abuse

## Testing
Use the provided test credentials:
- **App Key:** `app_68b06fca2067717563934188958`
- **Secret Key:** `scrt_68b06fca206911756393418`

## CORS Issues
If you're testing from a local HTML file, you'll encounter CORS errors because browsers block requests from `file://` protocol to `https://` domains. To test properly:

1. Serve your HTML file from a local web server (e.g., using `python -m http.server`)
2. Or deploy to a web server
3. Or use a tool like Postman/cURL for API testing

## Support
For technical support or questions about the API, contact the LMBTech support team.</content>
<parameter name="filePath">t:\My Server\NewXampp\htdocs\pay.lmbtech.rw\API_DOCUMENTATION.md#   L M B T e c h _ P a y m e n t _ S y s t e m _ A P I _ D o c u m e n t a t i o n 
 
 