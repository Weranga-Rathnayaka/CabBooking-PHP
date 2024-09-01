<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../vendor/autoload.php'; // Adjust the path as needed

function send_email($to, $subject, $body) {
    $mail = new PHPMailer(true);
    try {
        //Server settings
        $mail->isSMTP();                                            // Send using SMTP
        $mail->Host       = 'smtp.gmail.com';                     // Set the SMTP server to send through
        $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
        $mail->Username   = 'shenzhenbilianelectronic@gmail.com';               // SMTP username
        $mail->Password   = 'hnal zksk anbo rqpe';                  // SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;           // Enable TLS encryption
        $mail->Port       = 587;                                    // TCP port to connect to

        //Recipients
        $mail->setFrom('shenzhenbilianelectronic@gmail.com', 'You Drink We Drive');
        $mail->addAddress($to);                                     // Add a recipient

        // Content
        $mail->isHTML(true);                                        // Set email format to HTML
        $mail->Subject = $subject;
        $mail->Body    = $body;

        $mail->send();
        return ['status' => 'success'];
    } catch (Exception $e) {
        return ['status' => 'error', 'message' => $mail->ErrorInfo];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $status = $_POST['status'];
    $ref_code = $_POST['ref_code'];

    $pickup_zone = $_POST['pickup_zone'];
    $drop_zone = $_POST['drop_zone'];
    $driver_name = $_POST['driver_name'];
    $driver_contact = $_POST['driver_contact'];
    $firstname = $_POST['firstname'];
    $trackingLink = "https://forms.gle/UcastGpDJXr2zmnLA";

    if ($status == 1) {
        // Booking confirmed
        $subject = "Booking Confirmed";
        $body = "
            <div style=\"background-color:#f2f2f2;padding:20px;font-family:Arial,sans-serif;\">
                <div style=\"background-color:#fff;padding:20px;border-radius:10px;border:1px solid #ddd;max-width:600px;margin:auto;\">
                    <div style=\"text-align:center;margin-bottom:20px;\">
                        <img src=\"https://img.icons8.com/ios-filled/100/4caf50/checked-2.png\" alt=\"Booking Confirmed\" style=\"width:100px;height:auto;\" />
                    </div>
                    <p style=\"font-size:16px;color:#333;text-align:center;\">
                        Hi $firstname,<br><br>
                        Thank you for your booking. Your booking confirmed by driver. He will meet you soon.
                    </p>
                    <p style=\"margin:10px 0;font-size:24px;font-weight:bold;color:#4caf50;text-align:center;\">Booking Confirmed</p>
                    <div style=\"padding:15px;border:1px solid #ddd;margin:20px 0;background-color:#f9f9f9;border-radius:5px;\">
                        <p style=\"margin:0;line-height:1.6;color:#333;\">
                            <b>Ref. Code:</b> $ref_code<br><br>
                            <b>Customer Name:</b> $firstname<br><br>
                            <b>Pick up Location:</b> $pickup_zone<br><br>
                            <b>Drop off Location:</b> $drop_zone<br><br>
                            <b>Driver Name:</b> $driver_name<br><br>
                            <b>Driver Contact No:</b> $driver_contact<br>
                        </p>
                    </div>
                    <div style=\"text-align:center;margin:20px 0;\">
                        <a href=\"$trackingLink\" style=\"display:inline-block;padding:12px 25px;font-size:16px;color:#fff;background-color:#007bff;border-radius:5px;text-decoration:none;\">Track Order</a>
                    </div>
                    <div style=\"text-align:center;color:#888;margin-top:30px;\">
                        <b>You Drink We Drive</b><br>
                    </div>
                </div>
            </div>
            <style>
                @media only screen and (max-width: 600px) {
                    div[style*=\"padding:20px;\"] {
                        padding:10px !important;
                    }
                    p {
                        font-size:14px !important;
                    }
                    a[style*=\"padding:12px 25px;\"] {
                        padding:10px 20px !important;
                    }
                    img[alt=\"Order Confirmed\"] {
                        width:80px !important;
                    }
                }
            </style>
        ";
        $response = send_email($email, $subject, $body);
    } elseif ($status == 3) {
        // Booking dropped off
        $subject = "Your Ride Completed";
        $body = "
            <div style=\"background-color:#f2f2f2;padding:20px;font-family:Arial,sans-serif;\">
                <div style=\"background-color:#fff;padding:20px;border-radius:10px;border:1px solid #ddd;max-width:600px;margin:auto;\">
                    <div style=\"text-align:center;margin-bottom:20px;\">
                        <img src=\"https://img.icons8.com/ios-filled/100/4caf50/checked-2.png\" alt=\"Booking Confirmed\" style=\"width:100px;height:auto;\" />
                    </div>
                    <p style=\"font-size:16px;color:#333;text-align:center;\">
                        Hi $firstname,<br><br>
                        Thank you for your ride. Our system identified that you safely dropped off at your destination. It's time to put a review on us.<br>
                        Please click the button below to review us.
                    </p>
                    <p style=\"margin:10px 0;font-size:24px;font-weight:bold;color:#4caf50;text-align:center;\">Ride Completed</p>
                    <div style=\"padding:15px;border:1px solid #ddd;margin:20px 0;background-color:#f9f9f9;border-radius:5px;\">
                        <p style=\"margin:0;line-height:1.6;color:#333;\">
                            <b>Ref. Code:</b> $ref_code<br><br>
                            <b>Customer Name:</b> $firstname<br><br>
                            <b>Pick up Location:</b> $pickup_zone<br><br>
                            <b>Drop off Location:</b> $drop_zone<br><br>
                            <b>Driver Name:</b> $driver_name<br><br>
                        </p>
                    </div>
                    <div style=\"text-align:center;margin:20px 0;\">
                        <a href=\"$trackingLink\" style=\"display:inline-block;padding:12px 25px;font-size:16px;color:#fff;background-color:#007bff;border-radius:5px;text-decoration:none;\">Rate Us</a>
                    </div>
                    <div style=\"text-align:center;color:#888;margin-top:30px;\">
                        <b>You Drink We Drive</b><br>
                    </div>
                </div>
            </div>
            <style>
                @media only screen and (max-width: 600px) {
                    div[style*=\"padding:20px;\"] {
                        padding:10px !important;
                    }
                    p {
                        font-size:14px !important;
                    }
                    a[style*=\"padding:12px 25px;\"] {
                        padding:10px 20px !important;
                    }
                    img[alt=\"Order Confirmed\"] {
                        width:80px !important;
                    }
                }
            </style>
        ";
        $response = send_email($email, $subject, $body);
    } else {
        // No email needed for other statuses
        $response = ['status' => 'success', 'message' => 'No email sent for this status.'];
    }

    // echo json_encode($response);
    if ($response['status'] === 'success') {
        echo json_encode(['status' => 'success', 'message' => 'Email sent successfully.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to send email.']);
    }
}
?>
