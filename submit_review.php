<?php
require_once 'db_config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    $_SESSION['review_error'] = 'Morate biti prijavljeni kao korisnik da biste ostavili recenziju.';
    header('Location: vehicles.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$vehicle_id = $_POST['vehicle_id'] ?? null;
$rating = (int) ($_POST['rating'] ?? 0);
$content = trim($_POST['content'] ?? '');


if (!$vehicle_id || $rating < 1 || $rating > 5 || empty($content)) {
    $_SESSION['review_error'] = 'Popunite sva polja ispravno.';
    header("Location: vehicle.php?id=$vehicle_id");
    exit;
}


$stmt = $pdo->prepare("SELECT COUNT(*) FROM reservations WHERE user_id = ? AND vehicle_id = ? AND is_cancelled = 0 AND end_datetime <= NOW()");
$stmt->execute([$user_id, $vehicle_id]);

if ($stmt->fetchColumn() == 0) {
    $_SESSION['review_error'] = 'Recenziju možete ostaviti samo nakon završenog iznajmljivanja vozila.';
    header("Location: vehicle.php?id=$vehicle_id");
    exit;
}


$insert = $pdo->prepare("INSERT INTO comments (user_id, vehicle_id, rating, content, created_at) VALUES (?, ?, ?, ?, NOW())");
$insert->execute([$user_id, $vehicle_id, $rating, $content]);

$_SESSION['review_success'] = 'Uspešno ste ostavili recenziju!';
header("Location: vehicle.php?id=$vehicle_id");
exit;
