<?php
session_start();
require_once 'includes/db_config.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);

    if ($stmt->rowCount() == 1) {
        $user = $stmt->fetch();

        if ((int)$user['is_active'] !== 1) {
            $_SESSION['error'] = "Nalog nije aktiviran. Proverite svoj e-mail.";
            header("Location: login.php");
            exit;
        }

        if (password_verify($password, $user['password'])) {
            // Session set — sve korisničke informacije
            $_SESSION['user'] = [
                'id' => $user['id'],
                'email' => $user['email'],
                'first_name' => $user['first_name'],
                'last_name' => $user['last_name'],
                'id_number' => $user['id_number'],
                'driver_license' => $user['driver_license'],
                'phone' => $user['phone']
            ];
            $_SESSION['role'] = $user['role'] ?? 'user';

            // Redirekcija po ulozi
            if ($user['role'] === 'admin') {
                header("Location: index.php");
                exit;
            } elseif ($user['role'] === 'worker') {
                header("Location: index.php");
                exit;
            } else {
                header("Location: index.php");
                exit;
            }
        } else {
            $_SESSION['error'] = "Pogrešna lozinka.";
            header("Location: login.php");
            exit;
        }
    } else {
        $_SESSION['error'] = "Ne postoji korisnik sa ovom e-mail adresom.";
        header("Location: login.php");
        exit;
    }
} else {
    header("Location: login.php");
    exit;
}
