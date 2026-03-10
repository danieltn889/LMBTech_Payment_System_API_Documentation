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
