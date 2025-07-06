<?php
require_once 'includes/db_config.php';
include 'includes/header.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'worker') {
    header("Location: index.php");
    exit;
}

$worker_id = $_SESSION['user']['id'] ?? 0;
$message = '';

// Evidencija preuzimanja
if (isset($_GET['pickup'])) {
    $reservation_id = (int) $_GET['pickup'];
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM worker_log WHERE reservation_id = ? AND type = 'pickup'");
    $stmt->execute([$reservation_id]);
    if ($stmt->fetchColumn() == 0) {
        $stmt = $pdo->prepare("INSERT INTO worker_log (reservation_id, worker_id, type) VALUES (?, ?, 'pickup')");
        $stmt->execute([$reservation_id, $worker_id]);
        $message = "<p class='text-success'>Preuzimanje je evidentirano.</p>";
    }
}

// Evidencija povratka
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['return_reservation'])) {
    $reservation_id = (int) $_POST['return_reservation'];
    $condition = trim($_POST['vehicle_condition']);
    $damage = trim($_POST['damage_report']);

    // Upis u returns
    $stmt = $pdo->prepare("INSERT INTO returns (reservation_id, return_time, vehicle_condition, damage_report) VALUES (?, NOW(), ?, ?)");
    $stmt->execute([$reservation_id, $condition, $damage]);

    // Log povratka
    $stmt = $pdo->prepare("INSERT INTO worker_log (reservation_id, worker_id, type) VALUES (?, ?, 'return')");
    $stmt->execute([$reservation_id, $worker_id]);

    $message = "<p class='text-success'>Povratak je evidentiran.</p>";
}

// Rezervacije za prikaz
$reservations = $pdo->query("SELECT r.*, u.first_name, u.last_name, v.name, v.model
    FROM reservations r
    JOIN users u ON r.user_id = u.id
    JOIN vehicles v ON r.vehicle_id = v.id
    WHERE r.is_cancelled = 0 AND r.start_datetime >= NOW()
    ORDER BY r.start_datetime ASC")->fetchAll();

// Dohvati koje su već evidentirane
$logged_pickups = $pdo->query("SELECT reservation_id FROM worker_log WHERE type = 'pickup'")->fetchAll(PDO::FETCH_COLUMN);
$logged_returns = $pdo->query("SELECT reservation_id FROM worker_log WHERE type = 'return'")->fetchAll(PDO::FETCH_COLUMN);
?>

<section class="container py-5">
    <h2>Pregled rezervacija</h2>
    <?php echo $message; ?>

    <?php if (count($reservations) > 0): ?>
        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead class="table-light">
                <tr>
                    <th>Korisnik</th>
                    <th>Vozilo</th>
                    <th>Termin</th>
                    <th>Kod</th>
                    <th>Preuzimanje</th>
                    <th>Povratak</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($reservations as $r): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($r['first_name'] . ' ' . $r['last_name']); ?></td>
                        <td><?php echo htmlspecialchars($r['name'] . ' ' . $r['model']); ?></td>
                        <td><?php echo date('d.m.Y H:i', strtotime($r['start_datetime'])) . ' - ' . date('d.m.Y H:i', strtotime($r['end_datetime'])); ?></td>
                        <td><?php echo htmlspecialchars($r['code']); ?></td>
                        <td>
                            <?php if (in_array($r['id'], $logged_pickups)): ?>
                                <span class="text-success">Evidentirano</span>
                            <?php else: ?>
                                <a href="?pickup=<?php echo $r['id']; ?>" class="btn btn-sm btn-outline-primary">Evidentiraj</a>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if (in_array($r['id'], $logged_returns)): ?>
                                <span class="text-success">Evidentirano</span>
                            <?php else: ?>
                                <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#returnModal<?php echo $r['id']; ?>">Evidentiraj</button>

                                <!-- Modal -->
                                <div class="modal fade" id="returnModal<?php echo $r['id']; ?>" tabindex="-1">
                                    <div class="modal-dialog">
                                        <form method="post" class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Evidencija povratka</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <input type="hidden" name="return_reservation" value="<?php echo $r['id']; ?>">
                                                <div class="mb-3">
                                                    <label class="form-label">Stanje vozila</label>
                                                    <textarea name="vehicle_condition" class="form-control" required></textarea>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Izveštaj o šteti (ako postoji)</label>
                                                    <textarea name="damage_report" class="form-control"></textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="submit" class="btn btn-success">Sačuvaj</button>
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Otkaži</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p class="text-muted">Nema aktivnih rezervacija.</p>
    <?php endif; ?>
</section>

<?php include 'includes/footer.php'; ?>
