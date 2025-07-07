<?php
require_once 'includes/db_config.php';
include 'includes/header.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'worker') {
    echo "<div class='container py-5'><p class='text-danger'>Pristup dozvoljen samo radnicima.</p></div>";
    include 'includes/footer.php';
    exit;
}

$message = "";
$reservation = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['code'])) {
    $code = trim($_POST['code'] ?? '');

    if ($code) {
        $stmt = $pdo->prepare("SELECT r.*, u.first_name, u.last_name, v.name, v.model FROM reservations r 
                               JOIN users u ON r.user_id = u.id 
                               JOIN vehicles v ON r.vehicle_id = v.id 
                               WHERE r.code = ?");
        $stmt->execute([$code]);
        $reservation = $stmt->fetch();

        if (!$reservation) {
            $message = "<div class='alert alert-danger'>Rezervacija sa unetim kodom nije pronađena.</div>";
        } elseif ($reservation['is_cancelled']) {
            $message = "<div class='alert alert-warning'>Ova rezervacija je otkazana.</div>";
            $reservation = null;
        }
    }
}

if (isset($_POST['action']) && isset($_POST['reservation_id'])) {
    $action = $_POST['action'];
    $id = (int) $_POST['reservation_id'];

    if (in_array($action, ['approve', 'reject'])) {
        $worker_id = $_SESSION['user']['id'] ?? 0;

        $log = $pdo->prepare("INSERT INTO worker_log (reservation_id, worker_id, type) VALUES (?, ?, ?)");
        $log->execute([$id, $worker_id, $action]);

        $message = "<div class='alert alert-success'>Rezervacija je uspešno " . ($action === 'approve' ? 'odobrena' : 'odbijena') . ".</div>";
        $reservation = null;
    }
}
?>

<div class="container py-5">
    <h2>Provera rezervacije po kodu</h2>
    <?php echo $message; ?>

    <form method="post" class="mb-4">
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Rezervacioni kod</label>
                <input type="text" name="code" class="form-control" required>
            </div>
            <div class="col-md-6 align-self-end">
                <button type="submit" class="btn btn-primary">Proveri rezervaciju</button>
            </div>
        </div>
    </form>

    <?php if ($reservation): ?>
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Rezervacija #<?php echo htmlspecialchars($reservation['id']); ?></h5>
                <p><strong>Korisnik:</strong> <?php echo htmlspecialchars($reservation['first_name'] . ' ' . $reservation['last_name']); ?></p>
                <p><strong>Vozilo:</strong> <?php echo htmlspecialchars($reservation['name'] . ' ' . $reservation['model']); ?></p>
                <p><strong>Period:</strong> <?php echo $reservation['start_datetime']; ?> &rarr; <?php echo $reservation['end_datetime']; ?></p>
                <form method="post" class="d-flex gap-2">
                    <input type="hidden" name="reservation_id" value="<?php echo $reservation['id']; ?>">
                    <button type="submit" name="action" value="approve" class="btn btn-success">Odobri</button>
                    <button type="submit" name="action" value="reject" class="btn btn-danger">Odbij</button>
                </form>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
