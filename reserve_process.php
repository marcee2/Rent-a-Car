<?php
require_once 'includes/db_config.php';
require_once 'PHPMailer/src/PHPMailer.php';
require_once 'PHPMailer/src/SMTP.php';
require_once 'PHPMailer/src/Exception.php';
session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (!isset($_SESSION['user']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'user'){
    $_SESSION['reservation_error'] = 'Morate biti ulogovani kao korisnik da biste rezervisali vozilo.';
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user']['id'];
$user_email = $_SESSION['user']['email'] ?? '';
$user_name = $_SESSION['user']['first_name'] ?? '';

$vehicle_id = (int) ($_POST['vehicle_id'] ?? 0);
$start = $_POST['start_date'] ?? '';
$end = $_POST['end_date'] ?? '';

if (!$start || !$end || strtotime($start) >= strtotime($end)) {
    $_SESSION['reservation_error'] = 'Datum početka mora biti pre datuma kraja.';
    header("Location: vehicle.php?id=$vehicle_id");
    exit;
}

$diff_seconds = strtotime($end) - strtotime($start);
$hours = $diff_seconds / 3600;

if ($hours < 12 || $hours % 12 !== 0) {
    $_SESSION['reservation_error'] = 'Rezervacija mora trajati najmanje 12 sati i u koracima od 12h (12h, 24h, 36h...).';
    header("Location: vehicle.php?id=$vehicle_id");
    exit;
}

$stmt = $pdo->prepare("SELECT COUNT(*) FROM reservations WHERE vehicle_id = ? AND is_cancelled = 0 AND (
    (start_datetime <= ? AND end_datetime > ?) OR
    (start_datetime < ? AND end_datetime >= ?) OR
    (start_datetime >= ? AND end_datetime <= ?)
)");
$stmt->execute([$vehicle_id, $start, $start, $end, $end, $start, $end]);

$count = $stmt->fetchColumn();

if ($count > 0) {
    $_SESSION['reservation_error'] = 'Vozilo je već rezervisano u tom periodu.';
    header("Location: vehicle.php?id=$vehicle_id");
    exit;
}

$code = bin2hex(random_bytes(5));
$insert = $pdo->prepare("INSERT INTO reservations (user_id, vehicle_id, start_datetime, end_datetime, code, is_cancelled) 
                         VALUES (?, ?, ?, ?, ?, 0)");
$success = $insert->execute([$user_id, $vehicle_id, $start, $end, $code]);

if ($success) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'marcetic.nikola05@gmail.com';
        $mail->Password = 'vbpc pita ovnt ymeu';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('marcetic.nikola05@gmail.com', 'Rent a Car SU');
        $mail->addAddress($user_email, $user_name);
        $mail->isHTML(true);
        $mail->Subject = 'Potvrda rezervacije vozila';
        $mail->Body = "<h3>Uspešno ste rezervisali vozilo!</h3>
                        <p>Termin: <strong>$start</strong> do <strong>$end</strong></p>
                        <p>Vaš rezervacioni kod: <strong>$code</strong></p>
                        <p>Ovaj kod će vam biti potreban prilikom preuzimanja vozila i ostavljanja komentara nakon povratka.</p>
                        <p>Hvala što koristite našu uslugu.</p>";
        $mail->send();
    } catch (Exception $e) {
        error_log("Greška pri slanju e-maila: {$mail->ErrorInfo}");
    }

    $_SESSION['reservation_success'] = "Uspešno ste rezervisali vozilo! Kod: $code";
    header("Location: vehicle.php?id=$vehicle_id");
    exit;
} else {
    $_SESSION['reservation_error'] = 'Greška prilikom upisa rezervacije.';
    header("Location: vehicle.php?id=$vehicle_id");
    exit;
}
