<?php
require_once 'includes/db_config.php';
include 'includes/header.php';

// Provera pristupa
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo "<div class='container py-5'><p class='text-danger'>Pristup dozvoljen samo administratorima.</p></div>";
    include 'includes/footer.php';
    exit;
}

// Statistika
$totalUsers = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'user'")->fetchColumn();
$totalWorkers = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'worker'")->fetchColumn();
$totalVehicles = $pdo->query("SELECT COUNT(*) FROM vehicles")->fetchColumn();
$totalRentals = $pdo->query("SELECT COUNT(*) FROM rentals")->fetchColumn();
?>

<section class="container py-5">
    <h2 class="mb-4">Administratorski panel</h2>

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-white bg-primary mb-3">
                <div class="card-body">
                    <h5 class="card-title">Korisnici</h5>
                    <p class="card-text fs-4"><?php echo $totalUsers; ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-info mb-3">
                <div class="card-body">
                    <h5 class="card-title">Radnici</h5>
                    <p class="card-text fs-4"><?php echo $totalWorkers; ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-success mb-3">
                <div class="card-body">
                    <h5 class="card-title">Vozila</h5>
                    <p class="card-text fs-4"><?php echo $totalVehicles; ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-secondary mb-3">
                <div class="card-body">
                    <h5 class="card-title">Rezervacije</h5>
                    <p class="card-text fs-4"><?php echo $totalRentals; ?></p>
                </div>
            </div>
        </div>
    </div>

    <h4>Upravljanje</h4>
    <ul class="list-group">
        <li class="list-group-item"><a href="admin_users.php">✔️ Korisnici</a></li>
        <li class="list-group-item"><a href="admin_workers.php">👷 Radnici</a></li>
        <li class="list-group-item"><a href="admin_vehicles.php">🚗 Vozila</a></li>
        <li class="list-group-item"><a href="admin_rentals.php">📄 Sve rezervacije</a></li>
    </ul>
</section>

<?php include 'includes/footer.php'; ?>
