<?php
require_once 'includes/db_config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'worker') {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['rental_id'])) {
    $rental_id = (int) $_POST['rental_id'];

    // Proveri da li postoji i status je pending
    $stmt = $pdo->prepare("SELECT * FROM rentals WHERE id = ? AND status = 'pending'");
    $stmt->execute([$rental_id]);

    if ($stmt->rowCount() === 1) {
        // Ažuriraj status u 'approved'
        $update = $pdo->prepare("UPDATE rentals SET status = 'approved' WHERE id = ?");
        $update->execute([$rental_id]);
    }
}

header("Location: worker_dashboard.php");
exit;
