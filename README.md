THIS IS COLLECT LMBTech Payment System - API Documentation
LMBTech Payment System - API Documentation
System Overview1
Authentication1
Making Payments (Collections)3
Sending Money (Payouts)7
Callback Implementation9
Error Handling12
Testing13
Quick Reference14


________________________________________
System Overview
The LMBTech Payment System allows you to:
Collect money from customers via Mobile Money (MTN MOMO) or Card 
Send money to customers (payouts)
Check payment status in real-time
Receive instant notifications via callbacks
Base URL: https://pay.lmbtech.rw/pay/config/api
________________________________________
Authentication
Every API request must include your credentials in the Authorization header. This ensures that only authorized applications can access the payment system.
How Authentication Works
Step 1: Get Your Credentials
You will receive two keys from LMBTech:
App Key: Identifies your application
Secret Key: Proves your identity (keep this secret!)
Step 2: Combine Your Keys
Combine your App Key and Secret Key with a colon (:) between them:
app_key:secret_key
Step 3: Encode Using Base64
Convert the combined string to Base64 format. Base64 encoding turns your credentials into a safe format for HTTP headers.
Step 4: Add to Request Header
Include the encoded string in your request header:
Authorization: Basic [base64_string]
________________________________________
Making Payments (Collections)
Use this to accept payments from customers.
Request Format
Method: POST
Content-Type: application/json
Endpoint: Base URL
FieldDescriptionExample
emailCustomer emailcustomer@example.com
nameCustomer full nameJohn Doe
payment_methodMTN_MOMO_RWA or cardMTN_MOMO_RWA
amountAmount in RWF1000
service_paidWhat they're paying forproduct_purchase
reference_idYour unique ID (see format below)ORDER-20260215-1234
callback_urlYour URL to receive notificationhttps://your-site.com/callback

actionMust be paypay
Required Fields
For Mobile Money Only
FieldDescriptionExample
payer_phoneCustomer phone+250785085214
For Card Payments Only
FieldDescriptionExample
card_redirect_urlWhere to send user for card paymenthttps://your-site.com/card-redirect

Complete Request Examples
Mobile Money Request:
json
{
    "email": "customer@example.com",
    "name": "John Doe",
    "payment_method": "MTN_MOMO_RWA",
    "amount": 1000,
    "payer_phone": "+250785085214",
    "service_paid": "order_123",
    "reference_id": "ORDER-20260215-1234",
    "callback_url": "https://your-site.com/callback",
    "action": "pay"
}
Card Payment Request:
json
{
    "email": "customer@example.com",
    "name": "John Doe",
    "payment_method": "card",
    "amount": 1000,
    "service_paid": "order_123",
    "reference_id": "ORDER-20260215-1234",
    "callback_url": "https://your-site.com/callback",
    "card_redirect_url": "https://your-site.com/card-redirect",
    "action": "pay"
}
Response Formats
Mobile Money Success Response:
json
{
    "status": "success",
    "data": {
        "id": 8067,
        "reference_id": "ORDER-20260215-1234",
        "amount": "1000.00",
        "status": "pending"
    },
    "message": "Payment initiated successfully"
}
Card Payment Success Response:
json
{
    "status": "success",
    "data": {
        "reference_id": "ORDER-20260215-1234",
        "redirect_url": "https://pay.lmbtech.rw/pay/pesapal/iframe.php?reference_id=ORDER-20260215-1234"
    },
    "message": "Redirect to card payment gateway"
}
Error Response:
json
{
    "status": "fail",
    "message": "Insufficient balance for payout"
}
What Happens Next
For Mobile Money:
1.Customer receives a payment request on their phone
2.They enter PIN to approve
3.Your callback URL receives notification when complete
For Card Payments:
1.User is redirected to the redirect_url from response
2.They enter card details on secure Pesapal page
3.After payment, they're redirected back to your callback_url
________________________________________
Sending Money (Payouts)
Use this to send money from your balance to customers.
Important Requirements
You must have sufficient balance in your account
Your account must have payout permissions enabled
Request Format
Method: POST
Content-Type: application/json
json
{
    "email": "your-account@example.com",
    "name": "Recipient Name",
    "payment_method": "MTN_MOMO_RWA",
    "amount": 500,
    "payer_phone": "+250785085214",
    "service_paid": "payout",
    "reference_id": "PAYOUT-20260215-5678",
    "callback_url": "https://your-site.com/callback",
    "action": "payout"
}
Response
json
{
    "status": "success",
    "data": {
        "reference_id": "PAYOUT-20260215-5678",
        "amount": "500.00",
        "status": "pending"
    },
    "message": "Payout initiated successfully"
}
________________________________________
Checking Payment Status
You can check the status of any payment using its reference ID.
Request Format
Method: GET
URL: Base URL with reference_id parameter
text
GET https://pay.lmbtech.rw/pay/config/api.php?reference_id=ORDER-20260215-1234
Response
json
{
    "status": "success",
    "data": {
        "id": 8067,
        "reference_id": "ORDER-20260215-1234",
        "transaction_id": "TXN-987654321",
        "amount": "1000.00",
        "status": "success",
        "payment_method": "MTN_MOMO_RWA",
        "payment_date": "2026-02-15 12:35:22"
    }
}
Status Meanings
StatusDescription
pendingTransaction started, waiting for customer action
successPayment completed successfully
failedPayment failed (insufficient funds, cancelled, etc.)
cancelledTransaction cancelled by user or system
________________________________________
Callback Implementation
This is the most important part of your integration. When a payment completes, the system sends a notification to your callback_url.
What You Need to Do
1.Create an endpoint (URL) that can receive HTTP requests
2.This endpoint must handle both:
oJSON data (for Mobile Money callbacks)
oForm data (for Card payment callbacks)
3.Process the data and update your database
4.Return a success response
Callback Data Formats
Mobile Money Callback (JSON):
json
{
    "reference_id": "ORDER-20260215-1234",
    "transaction_id": "TXN-987654321",
    "status": "success",
    "amount": "1000.00",
    "payment_method": "MTN_MOMO_RWA",
    "payer_phone": "+250785085214"
}
Card Payment Callback (Form Data)
Field: pesapal_merchant_reference = ORDER-20260215-1234
Field: pesapal_transaction_tracking_id = TXN-987654321
Field: pesapal_response_data = COMPLETED
Callback Handler Logic (Pseudo-code)
FUNCTION handle_callback(request):
    // Step 1: Determine callback type and extract data
    IF request has form data:
        reference_id = request.form["pesapal_merchant_reference"]
        transaction_id = request.form["pesapal_transaction_tracking_id"]
        response = request.form["pesapal_response_data"]
        
        IF response == "COMPLETED":
            status = "success"
        ELSE:
            status = "failed"
    
    ELSE IF request has JSON body:
        data = parse_json(request.body)
        reference_id = data["reference_id"]
        transaction_id = data["transaction_id"]
        status = data["status"]
    
    ELSE:
        RETURN error_response("Invalid callback data")
    
    // Step 2: Validate required data
    IF reference_id is empty OR transaction_id is empty:
        RETURN error_response("Missing required fields")
    
    // Step 3: Update your database
    database.execute(
        "UPDATE orders SET payment_status = ?, transaction_id = ? WHERE reference_id = ?",
        [status, transaction_id, reference_id]
    )
    
    // Step 4: Log for debugging
    write_to_log("Callback processed: " + reference_id + ", Status: " + status)
    
    // Step 5: Return success acknowledgment
    RETURN success_response("Callback processed")
Important Notes About Callbacks
Callbacks may be sent multiple times - Your handler must be idempotent (check if already processed)
Always validate data before updating your database
Return a 200 OK response quickly to acknowledge receipt
Log everything for debugging purposes
________________________________________
Card Payment Flow (Step by Step)
When a customer chooses to pay by card, here's the complete flow:
Step 1: Initiate Payment
Your system sends the card payment request (as shown above).
Step 2: Get Redirect URL
Response contains redirect_url:
https://pay.lmbtech.rw/pay/pesapal/iframe.php?reference_id=ORDER-20260215-1234
Step 3: Redirect Customer
Send the customer to this URL. They will see a secure payment page.
Step 4: Customer Enters Card Details
The iframe.php page:
1.Fetches payment details from your reference
2.Gets authentication from Pesapal
3.Displays the card payment form
Step 5: Payment Processing
Customer enters card details and completes payment on Pesapal's secure servers.
Step 6: Redirect Back
After payment, customer is redirected to your callback_url with the transaction details.
Step 7: Your Callback Handler
Your callback URL receives the form data and updates your database.
________________________________________
Error Handling
Common Error Responses
HTTP CodeError MessageWhat It MeansHow to Fix
401UnauthorizedInvalid API keysCheck your app_key and secret_key
402Insufficient balanceNot enough money for payoutCollect more payments first
400Invalid callback dataMalformed callbackCheck your callback implementation
404Reference not foundReference ID doesn't existVerify you're using correct reference
Error Response Format
json
{
    "status": "fail",
    "message": "Description of what went wrong"
}
________________________________________
Testing
Test Files Available
Card Payment Test: https://pay.lmbtech.rw/test_lmbtech_pay/test_card_api.php
Mobile Money Test: https://pay.lmbtech.rw/test_lmbtech_pay/test_momo_api.php
Test Credentials
text
App Key: app_68b06fca2067717563934188958
Secret Key: scrt_68b06fca206911756393418
Test Phone Number
+250785085214 (for MTN MOMO)
Test Amounts
Use small amounts for testing (e.g., 100, 200 RWF)
________________________________________
Quick Reference
API at a Glance
OperationMethodRequired Fields
Collect Money (MOMO)POSTemail, name, payment_method=MTN_MOMO_RWA, amount, payer_phone, service_paid, reference_id, callback_url, action=pay
Collect Money (Card)POSTemail, name, payment_method=card, amount, service_paid, reference_id, callback_url, card_redirect_url, action=pay
Send Money (Payout)POSTemail, name, payment_method=MTN_MOMO_RWA, amount, payer_phone, service_paid, reference_id, callback_url, action=payout
Check StatusGETreference_id parameter
Send SMSPOSTname, tel, message, action=sms
Reference ID Format
Always use unique IDs:
text
Format: [PREFIX]-[DATE]-[RANDOM]
Example: ORDER-20260215-1234
Phone Number Format
text
For Rwanda: +2507XXXXXXXX
Example: +250785085214
Important URLs
API Endpoint: https://pay.lmbtech.rw/pay/config/api.php
Card Payment Page: https://pay.lmbtech.rw/pay/pesapal/iframe.php
Support Email: support@lmbtech.rw
