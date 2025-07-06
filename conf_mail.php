<?php
require_once 'includes/db_config.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    $_SESSION['reservation_error'] = 'Morate biti ulogovani kao korisnik da biste rezervisali vozilo.';
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $vehicle_id = (int) ($_POST['vehicle_id'] ?? 0);
    $start = $_POST['start_date'] ?? '';
    $end = $_POST['end_date'] ?? '';

    if (!$start || !$end || strtotime($start) >= strtotime($end)) {
        $_SESSION['reservation_error'] = 'Datum početka mora biti pre datuma kraja.';
        header("Location: vehicle.php?id=$vehicle_id");
        exit;
    }

    $duration = (strtotime($end) - strtotime($start)) / 3600;
    if ($duration < 12 || $duration % 12 !== 0) {
        $_SESSION['reservation_error'] = 'Rezervacija mora trajati najmanje 12h i u koracima od 12h (npr. 12h, 24h, 36h).';
        header("Location: vehicle.php?id=$vehicle_id");
        exit;
    }

    // Proveri da li je vozilo već rezervisano
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

    $code = strtoupper(bin2hex(random_bytes(4)));
    $insert = $pdo->prepare("INSERT INTO reservations (user_id, vehicle_id, start_datetime, end_datetime, code, is_cancelled) 
                             VALUES (?, ?, ?, ?, ?, 0)");
    $insert->execute([$user_id, $vehicle_id, $start, $end, $code]);

    // Pošalji e-mail korisniku
    require_once 'PHPMailer/src/PHPMailer.php';
    require_once 'PHPMailer/src/SMTP.php';
    require_once 'PHPMailer/src/Exception.php';
    use PHPMailer\src\PHPMailer;

    $stmt = $pdo->prepare("SELECT email, first_name FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();

    if ($user) {
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'marcetic.nikola05@gmail.com';
            $mail->Password = 'oxnq wlcl tmgp acwp';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('marcetic.nikola05@gmail.com', 'Rent a Car SU');
            $mail->addAddress($user['email'], $user['first_name']);

            $mail->isHTML(true);
            $mail->Subject = 'Potvrda rezervacije vozila';

            $mail->Body = "<p>Poštovani {$user['first_name']},</p>
                            <p>Vaša rezervacija vozila je uspešna.</p>
                            <p>Rezervacioni kod: <strong>$code</strong></p>
                            <p>Prikažite ovaj kod prilikom dolaska po vozilo.</p>
                            <p>S poštovanjem,<br>Rent a Car SU tim</p>";
            $mail->send();
        } catch (Exception $e) {
        }
    }

    $_SESSION['reservation_success'] = 'Uspešno ste rezervisali vozilo! Kod rezervacije je poslat na e-mail.';
    header("Location: vehicle.php?id=$vehicle_id");
    exit;
} else {
    header('Location: index.php');
    exit;
}
