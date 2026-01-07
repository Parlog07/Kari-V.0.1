<?php


require_once __DIR__ . '/PHPMailer/PHPMailer.php';
require_once __DIR__ . '/PHPMailer/SMTP.php';
require_once __DIR__ . '/PHPMailer/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// function sendOTPEmail(string $toEmail, string $otp): bool
// {
//     $mail = new PHPMailer(true);

//     try {
//         // SMTP CONFIG


//         // SSL FIX FOR LARAGON / WINDOWS
//         $mail->SMTPOptions = [
//             'ssl' => [
//                 'verify_peer' => false,
//                 'verify_peer_name' => false,
//                 'allow_self_signed' => true,
//             ],
//         ];

//         // EMAIL CONTENT
//         $mail->setFrom('ayoubmogador2014@gmail.com', 'Smart Wallet');
//         $mail->addAddress($toEmail);

//         $mail->isHTML(true);
//         $mail->Subject = 'Your Smart Wallet OTP Code';
//         $mail->Body = "
//             <h2>Your OTP Code</h2>
//             <p>Your verification code is:</p>
//             <h1 style='letter-spacing:4px;'>$otp</h1>
//             <p>This code expires in 5 minutes.</p>
//         ";
//         $mail->AltBody = "Your OTP code is: $otp (expires in 5 minutes)";

//         $mail->send();
//         return true;

//     } catch (Exception $e) {
//         error_log('MAIL ERROR: ' . $mail->ErrorInfo);
//         return false;
//     }
// }


class Mailer
{
    private PHPMailer $mail;

    public function __construct()
    {
        $this->mail = new PHPMailer(true);

        
        $this->mail->isSMTP();
        $this->mail->Host = 'smtp.gmail.com';
        $this->mail->SMTPAuth = true;
        $this->mail->Username = 'ayoubmogador2014@gmail.com';
        $this->mail->Password = 'uxxzggzgmktbovig'; // Gmail App Password
        $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $this->mail->Port = 587;

        $this->mail->setFrom('ayoubmogador2014@gmail.com', 'Rental Platform');
        $this->mail->isHTML(true);

        $this->mail->SMTPOptions = [
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true,
            ],
        ];
    }

    public function sendBookingConfirmation($to, $name, $title, $start, $end, $price)
    {
        $this->mail->clearAddresses();
        $this->mail->addAddress($to);
        $this->mail->Subject = 'Booking Confirmation';
        $this->mail->Body = "
            <h2>Booking Confirmed</h2>
            <p>Hello $name</p>
            <p>Rental: <strong>$title</strong></p>
            <p>Dates: $start → $end</p>
            <p>Total: $price</p>
        ";
        $this->mail->send();
    }

    public function sendBookingCancellation($to, $name, $title, $start, $end)
    {
        $this->mail->clearAddresses();
        $this->mail->addAddress($to);
        $this->mail->Subject = 'Booking Cancelled';
        $this->mail->Body = "
            <h2>Booking Cancelled</h2>
            <p>Hello $name</p>
            <p>Rental: <strong>$title</strong></p>
            <p>Dates: $start → $end</p>
        ";
        $this->mail->send();
    }
} 
