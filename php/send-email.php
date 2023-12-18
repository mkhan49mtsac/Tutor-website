<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Add debugging line
error_log('Received POST request: ' . print_r($_POST, true));

require 'C:/Users/mkhan/Desktop/tutor website/php/Exception.php';
require 'C:/Users/mkhan/Desktop/tutor website/php/PHPMailer.php';
require 'C:/Users/mkhan/Desktop/tutor website/php/SMTP.php';
require 'config.php'; // Include the configuration file

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate user input
    $name = isset($_POST['name']) ? htmlspecialchars(trim($_POST['name'])) : '';
    $email = isset($_POST['email']) ? htmlspecialchars(trim($_POST['email'])) : '';
    $subject = isset($_POST['subject']) ? htmlspecialchars(trim($_POST['subject'])) : '';
    $message = isset($_POST['message']) ? htmlspecialchars(trim($_POST['message'])) : '';

    // Set Message for notification and user email
    $notificationMessage = "You have a new contact form submission from the tutoring website:\n\n";
    $notificationMessage .= "Name: $name\n";
    $notificationMessage .= "Email: $email\n";
    $notificationMessage .= "Subject: $subject\n";
    $notificationMessage .= "Message: $message\n";

    $userMessage = "Dear $name,\n\nThank you for reaching out! I have received your message and will get back to you as soon as possible. In the meantime, feel free to explore the course materials, and if you have any specific questions or concerns, don't hesitate to let me know.\n\nBest Regards, \nMohammed Khan\nFull Stack Developer | CA | USA";

    try {
        // Send notification email
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host       = $config['smtpHost'];
        $mail->SMTPAuth   = true;
        $mail->Username   = $config['smtpUsername'];
        $mail->Password   = $config['smtpPassword'];
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;
        $mail->setFrom($config['smtpUsername'], 'Your Full Name');
        $mail->addAddress($config['to']);
        $mail->isHTML(false);
        $mail->Subject = "New Contact Form Submission";
        $mail->Body    = $notificationMessage;

        if (!$mail->send()) {
            error_log('Error sending notification email: ' . $mail->ErrorInfo);
            throw new Exception('Error sending notification email.');
        }

        // Send user confirmation email
        $mailUser = new PHPMailer(true);
        $mailUser->isSMTP();
        $mailUser->Host       = $config['smtpHost'];
        $mailUser->SMTPAuth   = true;
        $mailUser->Username   = $config['smtpUsername'];
        $mailUser->Password   = $config['smtpPassword'];
        $mailUser->SMTPSecure = 'tls';
        $mailUser->Port       = 587;
        $mailUser->setFrom($config['smtpUsername'], 'Your Full Name');
        $mailUser->addAddress($email);
        $mailUser->isHTML(false);
        $mailUser->Subject = "Thank you for your message";
        $mailUser->Body    = $userMessage;

        if (!$mailUser->send()) {
            error_log('Error sending user confirmation email: ' . $mailUser->ErrorInfo);
            throw new Exception('Error sending user confirmation email.');
        }

        echo 'Thank you! Your message has been successfully sent.';
    } catch (Exception $e) {
        error_log('Something went wrong: ' . $e->getMessage());
        echo 'Something went wrong. Please try again.';
    }
}
?>
