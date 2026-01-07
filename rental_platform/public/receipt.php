<?php
require_once "../config/autoload.php";

session_start();

use Dompdf\Dompdf;

if (!isset($_SESSION['user_id'], $_GET['id'])) {
    die("Access denied");
}

$db = new Database();
$pdo = $db->getConnection();

$stmt = $pdo->prepare("
    SELECT b.*, r.title, u.full_name
    FROM bookings b
    JOIN rentals r ON r.id = b.rental_id
    JOIN users u ON u.id = b.user_id
    WHERE b.id = :id AND b.user_id = :user_id
");
$stmt->execute([
    'id' => $_GET['id'],
    'user_id' => $_SESSION['user_id']
]);

$booking = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$booking) {
    die("Not allowed");
}

$html = "
<h1>Booking Receipt</h1>
<p>Name: {$booking['full_name']}</p>
<p>Rental: {$booking['title']}</p>
<p>Dates: {$booking['start_date']} → {$booking['end_date']}</p>
<p>Total: {$booking['total_price']}</p>
";

$pdf = new Dompdf();
$pdf->loadHtml($html);
$pdf->render();
$pdf->stream("receipt.pdf", ["Attachment" => true]);
