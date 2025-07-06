<?php
require_once 'includes/db_config.php';
include 'includes/header.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo "<div class='container py-5'><p class='text-danger'>Pristup dozvoljen samo administratorima.</p></div>";
    include 'includes/footer.php';
    exit;
}

$totalUsers = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'user'")->fetchColumn();
$totalVehicles = $pdo->query("SELECT COUNT(*) FROM vehicles")->fetchColumn();
$totalReservations = $pdo->query("SELECT COUNT(*) FROM reservations")->fetchColumn();
$totalReviews = $pdo->query("SELECT COUNT(*) FROM comments")->fetchColumn();

$topVehicles = $pdo->query("SELECT name, model, popularity FROM vehicles ORDER BY popularity DESC LIMIT 5")->fetchAll();
?>

<section class="container py-5">
    <h2 class="mb-4">Administratorski izveštaji</h2>

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-bg-primary">
                <div class="card-body">
                    <h5 class="card-title">Korisnika</h5>
                    <p class="display-6"><?php echo $totalUsers; ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-bg-success">
                <div class="card-body">
                    <h5 class="card-title">Vozila</h5>
                    <p class="display-6"><?php echo $totalVehicles; ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-bg-warning">
                <div class="card-body">
                    <h5 class="card-title">Rezervacija</h5>
                    <p class="display-6"><?php echo $totalReservations; ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-bg-info">
                <div class="card-body">
                    <h5 class="card-title">Recenzija</h5>
                    <p class="display-6"><?php echo $totalReviews; ?></p>
                </div>
            </div>
        </div>
    </div>

    <h4 class="mt-5">Top 5 najpopularnijih vozila</h4>
    <table class="table table-striped mt-3">
        <thead>
        <tr>
            <th>Naziv</th>
            <th>Model</th>
            <th>Popularnost</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($topVehicles as $v): ?>
            <tr>
                <td><?php echo htmlspecialchars($v['name']); ?></td>
                <td><?php echo htmlspecialchars($v['model']); ?></td>
                <td><?php echo $v['popularity']; ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</section>

<?php include 'includes/footer.php'; ?>
