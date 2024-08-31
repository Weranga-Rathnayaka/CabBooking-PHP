<?php
require_once('./config.php');
require __DIR__ . '/vendor/autoload.php';
use Twilio\Rest\Client;

function sendCancellationSMS($details) {
  

    $client = new Client($account_sid, $auth_token);

    $message = "Booking Cancellation:
    Ref Code: {$details['ref_code']}
    Pickup Zone: {$details['pickup_zone']}
    Drop-off Zone: {$details['drop_zone']}
    Driver: {$details['driver_name']}
    Driver Contact: {$details['driver_contact']}
    Client: {$details['client']}
    Client Contact: {$details['contact']}
    Fee: LKR {$details['fee']}";

    try {
        $response = $client->messages->create(
            "+94779488546",
            [
                'from' => $twilio_number,
                'body' => $message
             
               
            ]
        );
        echo json_encode(["status" => "success", "message" => "SMS sent successfully."]);
    } catch (Exception $e) {
        echo json_encode(["status" => "error", "message" => $e->getMessage()]);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $details = json_decode($_POST['details'], true);
    sendCancellationSMS($details);
}
?>
