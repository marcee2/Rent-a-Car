<?php
require_once '../includes/db_config.php';

$stmt = $pdo->query("SELECT * FROM vehicles ORDER BY popularity DESC LIMIT 10");
$vehicles = $stmt->fetchAll();
echo json_encode($vehicles);
