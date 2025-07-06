<?php
require_once 'includes/db_config.php';
session_start();

header('Content-Type: application/json');

if (!isset($_GET['vehicle_id']) || !isset($_GET['start']) || !isset($_GET['end'])) {
    echo json_encode(['available' => false, 'error' => 'Nedostaju parametri.']);
    exit;
}

$vehicle_id = (int) $_GET['vehicle_id'];
$start = $_GET['start'];
$end = $_GET['end'];

// Validacija formata datuma
if (!strtotime($start) || !strtotime($end) || strtotime($start) >= strtotime($end)) {
    echo json_encode(['available' => false, 'error' => 'Neispravni datumi.']);
    exit;
}

// Provera dostupnosti
$stmt = $pdo->prepare("SELECT COUNT(*) FROM reservations WHERE vehicle_id = ? AND is_cancelled = 0 AND (
    (start_datetime <= ? AND end_datetime > ?) OR
    (start_datetime < ? AND end_datetime >= ?) OR
    (start_datetime >= ? AND end_datetime <= ?)
)");

$stmt->execute([$vehicle_id, $start, $start, $end, $end, $start, $end]);
$count = $stmt->fetchColumn();

if ($count > 0) {
    echo json_encode(['available' => false]);
} else {
    echo json_encode(['available' => true]);
}
