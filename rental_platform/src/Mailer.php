<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

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
        $this->mail->Password = 'uxxzggzgmktbovig';
        $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $this->mail->Port = 587;

        $this->mail->setFrom('ayoubmogador2014@gmail.com', 'Rental Platform');
        $this->mail->isHTML(true);
    }

    public function sendBookingConfirmation(
        string $toEmail,
        string $userName,
        string $rentalTitle,
        string $startDate,
        string $endDate,
        float $totalPrice
    ): void {
        $this->mail->clearAddresses();
        $this->mail->addAddress($toEmail);

        $this->mail->Subject = 'Booking Confirmation';

        $this->mail->Body = "
            <h2>Booking Confirmed</h2>
            <p>Hello {$userName},</p>
            <p>Your booking for <strong>{$rentalTitle}</strong> is confirmed.</p>
            <p><strong>Dates:</strong> {$startDate} → {$endDate}</p>
            <p><strong>Total price:</strong> {$totalPrice}</p>
            <p>Thank you for using our platform.</p>
        ";

        $this->mail->send();
    }
}