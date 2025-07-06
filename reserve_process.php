<?php
require_once 'includes/db_config.php';
include 'includes/header.php';

if (!isset($_SESSION['user']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'user'){
    $_SESSION['profile_error'] = 'Morate biti ulogovani kao korisnik da biste pristupili profilu.';
    header("Location: login.php");
    exit;
}

    $user_id = $_SESSION['user']['id'];
    $vehicle_id = (int) ($_POST['vehicle_id'] ?? 0);
    $start = $_POST['start_date'] ?? '';
    $end = $_POST['end_date'] ?? '';

    if (!$start || !$end || strtotime($start) >= strtotime($end)) {
        echo "<div class='container py-5'><p class='text-danger'>Datum početka mora biti pre datuma kraja.</p></div>";
        exit;
    }

    $diff_seconds = strtotime($end) - strtotime($start);
    $hours = $diff_seconds / 3600;

    if ($hours < 12 || $hours % 12 !== 0) {
        echo "<div class='container py-5'><p class='text-danger'>Rezervacija mora trajati najmanje 12 sati i u koracima od 12h (12h, 24h, 36h...).</p></div>";
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
        echo "<div class='container py-5'><p class='text-danger'>Vozilo je već rezervisano u tom periodu.</p></div>";
        exit;
    }

    $code = bin2hex(random_bytes(5));
    $insert = $pdo->prepare("INSERT INTO reservations (user_id, vehicle_id, start_datetime, end_datetime, code, is_cancelled) 
                             VALUES (?, ?, ?, ?, ?, 0)");
    $success = $insert->execute([$user_id, $vehicle_id, $start, $end, $code]);

    if ($success) {
        require_once 'conf_mail.php';
        echo "<div class='container py-5'><p class='text-success'>Uspešno ste rezervisali vozilo! Kod: <strong>$code</strong></p></div>";
    } else {
        echo "<div class='container py-5'><p class='text-danger'>Greška prilikom upisa rezervacije.</p></div>";
    }


