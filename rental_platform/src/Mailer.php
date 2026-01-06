<?php

use PHPMailer\PHPMailer\PHPMailer;

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
