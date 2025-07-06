<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Iznajmljivanje vozila</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="index.php">Rent a Car SU</a>
        <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#nav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="nav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a href="vehicles.php" class="nav-link">Top 10 Vozila</a></li>

                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                    <li class="nav-item"><a class="nav-link" href="admin_dashboard.php">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
                    <li class="nav-item"><a class="nav-link" href="admin_reports.php">Statistika</a></li>

                <?php elseif (isset($_SESSION['role']) && $_SESSION['role'] === 'worker'): ?>
                    <li class="nav-item"><a class="nav-link" href="worker_dashboard.php">Worker Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="worker_check.php">Provera iznajmljivanja vozila</a></li>
                    <li class="nav-item"><a class="nav-link" href="worker_history.php">Istorija</a></li>
                    <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>

                <?php elseif (isset($_SESSION['role']) && $_SESSION['role'] === 'user'): ?>
                    <li class="nav-item"><a class="nav-link" href="my_reservations.php">Moje rezervacije</a></li>
                    <li class="nav-item"><a href="all.php" class="nav-link">Svi Automobili</a></li>
                    <li class="nav-item"><a class="nav-link" href="reviews.php">Recenzije</a></li>
                    <li class="nav-item"><a class="nav-link" href="profile.php">Profile</a></li>
                    <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>

                <?php else: ?>
                    <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
                    <li class="nav-item"><a class="nav-link" href="register.php">Register</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
