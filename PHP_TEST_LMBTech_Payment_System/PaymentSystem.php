<?php
class PaymentSystem
{
    // API Configuration
    // private $api_url = "http://localhost/pay.lmbtech.rw/pay/config/api.php";
    // public $card_redirect_url = "http://localhost/pay.lmbtech.rw/pay/pesapal/iframe.php";
    private $api_url = "https://pay.lmbtech.rw/pay/config/api.php";
    public $card_redirect_url = "https://pay.lmbtech.rw/pay/pesapal/iframe.php";
    private $authHeader;

    public function __construct($app_key = null, $secret_key = null)
    {
        // Setup Authentication Header
        if (!$app_key && !$secret_key) {
            // Check session or use hardcoded fallback keys
            if (!empty($_SESSION['user']['app_key']) && !empty($_SESSION['user']['scret_key'])) {
                $this->authHeader = 'Authorization: Basic ' . base64_encode(
                    $_SESSION['user']['app_key'] . ':' . $_SESSION['user']['scret_key']
                );
            } else { 
                // Hardcoded fallback keys for testing
                $this->authHeader = 'Authorization: Basic ' . base64_encode(
                    'app_68b06fca2067717563934188958:scrt_68b06fca206911756393418'
                );
            }
        } else {
            $this->authHeader = 'Authorization: Basic ' . base64_encode(
                $app_key . ':' . $secret_key
            );
        }
    }

    // Function to make cURL API requests
    public function makeRequest($method, $data = [])
    {
        $ch = curl_init();

        // 1. Prepare JSON Payload
        $jsonData = json_encode($data);

        $options = [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                $this->authHeader,
                'Content-Type: application/json',       // ✅ Header says JSON
                'Content-Length: ' . strlen($jsonData)  // ✅ Good practice
            ]
        ];

        if ($method === "GET") {
            // GET requests use URL parameters
            $query = http_build_query($data);
            $options[CURLOPT_URL] = "{$this->api_url}?{$query}";
            // Remove Content-Type/Length for GET
            $options[CURLOPT_HTTPHEADER] = [$this->authHeader];
        } else {
            // POST/PUT/DELETE use the JSON Body
            $options[CURLOPT_URL] = $this->api_url;
            $options[CURLOPT_CUSTOMREQUEST] = $method;
            $options[CURLOPT_POSTFIELDS] = $jsonData;   // ✅ Body is now JSON
        }

        curl_setopt_array($ch, $options);
        $response = curl_exec($ch);
        
        // Debugging help: Check for cURL errors
        if ($response === false) {
             $error = curl_error($ch);
             curl_close($ch);
             return ["status" => false, "message" => "CURL Error: " . $error];
        }

        curl_close($ch);

        // Strip PHP warnings from response
        $response = preg_replace('/<br \/>[\s\S]*?<br \/>/', '', $response);

        $decoded = json_decode($response, true);
        if ($decoded === null && $response !== 'null') {
            return ["status" => false, "message" => "Invalid JSON response", "raw_response" => $response];
        }
        return $decoded;
    }
}

// Example usage
$paymentSystem = new PaymentSystem();
?>