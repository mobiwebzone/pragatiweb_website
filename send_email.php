<?php
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', 'email_error.log');
error_reporting(E_ALL);
header('Content-Type: application/json');

require 'vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

set_error_handler(function ($severity, $message, $file, $line) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => "Server Error: $message in $file on line $line"]);
    exit;
});

// Sanitize and validate inputs
$name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
$institution_name = filter_input(INPUT_POST, 'institution_name', FILTER_SANITIZE_STRING);
$city = filter_input(INPUT_POST, 'city', FILTER_SANITIZE_STRING) ?? '';
$message = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_STRING) ?? '';
$mobileno = filter_input(INPUT_POST, 'mobileno', FILTER_SANITIZE_STRING) ?? filter_input(INPUT_POST, 'mobileno', FILTER_SANITIZE_STRING);
$product_type = filter_input(INPUT_POST, 'product_type', FILTER_SANITIZE_STRING);
// $country_id = filter_input(INPUT_POST, 'country_id', FILTER_SANITIZE_STRING);
// $country_name = filter_input(INPUT_POST, 'country_name', FILTER_SANITIZE_STRING);

// Validate required fields
$missing_fields = [];
if (!$name) $missing_fields[] = 'name';
if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) $missing_fields[] = 'email';
if (!$institution_name) $missing_fields[] = 'institution_name';
if (!$mobileno) $missing_fields[] = 'mobileno';
if (!$product_type) $missing_fields[] = 'product_type';

if (!empty($missing_fields)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Missing or invalid required fields: ' . implode(', ', $missing_fields)]);
    exit;
}

// Log POST data for debugging
file_put_contents('email_debug.log', date('Y-m-d H:i:s') . " - POST data:\n" . print_r($_POST, true) . "\n", FILE_APPEND);

$mail = new PHPMailer(true);
try {
    $mail->SMTPDebug = 2;
    $mail->Debugoutput = function($str, $level) {
        file_put_contents('email_debug.log', date('Y-m-d H:i:s') . " - [$level] $str\n", FILE_APPEND);
    };

    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'info.pragatiweb@gmail.com';
    $mail->Password = 'yyxj hopx ycbo unog'; // Verify App Password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    // Email to user
    $mail->setFrom('info.pragatiweb@gmail.com', 'PragatiWeb');
    $mail->addAddress($email);
    $mail->Subject = 'Thank You for Contacting Us';
    $mail->isHTML(true);
    $mail->Body = '
    <div style="font-family: Arial, Helvetica, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; background-color: #f4f4f4;">
        <div style="background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); padding: 20px;">
            <div style="background-color: #007bff; color: #ffffff; padding: 15px; text-align: center; border-radius: 8px 8px 0 0;">
                <h1 style="margin: 0; font-size: 24px;">Thank You, ' . htmlspecialchars($name) . '!</h1>
            </div>
            <div style="padding: 20px; color: #333333;">
                <p style="font-size: 16px; line-height: 1.6;">Thank you for reaching out to us! We have received your message and appreciate you taking the time to get in touch.</p>
                <p style="font-size: 16px; line-height: 1.6;">Our team will connect with you at the earliest opportunity.</p>
            </div>
            <div style="text-align: center; padding: 10px; border-top: 1px solid #eeeeee;">
                <p style="font-size: 14px; color: #666666; margin: 0;">Best regards,<br><strong>Team PragatiWeb</strong></p>
                <p style="font-size: 12px; color: #999999; margin-top: 10px;">© ' . date('Y') . ' PragatiWeb. All rights reserved.</p>
            </div>
        </div>
    </div>';
    $mail->AltBody = "Dear $name,\n\nThank you for reaching out! We have received your message.\n\nOur Team will connect at the earliest\n\nBest regards,\nTeam PragatiWeb";
    $mail->send();

    // Email to admin
    $mail->clearAddresses();
    $mail->addAddress('info.pragatiweb@gmail.com');
    $mail->Subject = "New Contact Form Submission from $name";
    $mail->Body = '
    <div style="font-family: Arial, Helvetica, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; background-color: #f4f4f4;">
        <div style="background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); padding: 20px;">
            <div style="background-color: #28a745; color: #ffffff; padding: 15px; text-align: center; border-radius: 8px 8px 0 0;">
                <h1 style="margin: 0; font-size: 24px;">New Contact Form Submission</h1>
            </div>
            <div style="padding: 20px; color: #333333;">
                <h2 style="font-size: 18px; color: #333333; margin-top: 0;">Submission Details</h2>
                <table style="width: 100%; border-collapse: collapse;">
                    <tr style="background-color: #f9f9f9;">
                        <td style="padding: 10px; font-weight: bold; border: 1px solid #dddddd;">Name</td>
                        <td style="padding: 10px; border: 1px solid #dddddd;">' . htmlspecialchars($name) . '</td>
                    </tr>
                    <tr>
                        <td style="padding: 10px; font-weight: bold; border: 1px solid #dddddd;">Mobile No</td>
                        <td style="padding: 10px; border: 1px solid #dddddd;">' . htmlspecialchars($mobileno) . '</td>
                    </tr>
                    <tr style="background-color: #f9f9f9;">
                        <td style="padding: 10px; font-weight: bold; border: 1px solid #dddddd;">Email</td>
                        <td style="padding: 10px; border: 1px solid #dddddd;">' . htmlspecialchars($email) . '</td>
                    </tr>
                    <tr>
                        <td style="padding: 10px; font-weight: bold; border: 1px solid #dddddd;">Product Type</td>
                        <td style="padding: 10px; border: 1px solid #dddddd;">' . htmlspecialchars($product_type) . '</td>
                    </tr>
                    <tr style="background-color: #f9f9f9;">
                        <td style="padding: 10px; font-weight: bold; border: 1px solid #dddddd;">Institution</td>
                        <td style="padding: 10px; border: 1px solid #dddddd;">' . htmlspecialchars($institution_name) . '</td>
                    </tr>
                    <tr>
                        <td style="padding: 10px; font-weight: bold; border: 1px solid #dddddd;">City</td>
                        <td style="padding: 10px; border: 1px solid #dddddd;">' . htmlspecialchars($city) . '</td>
                    </tr>
                   
                    <tr>
                        <td style="padding: 10px; font-weight: bold; border: 1px solid #dddddd;">Message</td>
                        <td style="padding: 10px; border: 1px solid #dddddd;">' . htmlspecialchars($message) . '</td>
                    </tr>
                </table>
            </div>
            <div style="text-align: center; padding: 10px; border-top: 1px solid #eeeeee;">
                <p style="font-size: 12px; color: #999999; margin: 0;">Transparency is key! We\'re here to answer your questions.</p>
                <p style="font-size: 12px; color: #999999; margin-top: 10px;">© ' . date('Y') . ' PragatiWeb. All rights reserved.</p>
            </div>
        </div>
    </div>';
    $mail->AltBody = "New submission:\nName: $name\nMobile No: $mobileno\nEmail: $email\nProduct Type: $product_type\nInstitution: $institution_name\nCity: $city\nMessage: $message";
    $mail->send();

    echo json_encode(['success' => true, 'message' => 'Email sent successfully']);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => "Mailer Error: {$mail->ErrorInfo}"]);
}
exit;
?>