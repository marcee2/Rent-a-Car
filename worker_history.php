<?php
require_once 'includes/db_config.php';
include 'includes/header.php';

// Provera pristupa
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'worker') {
    echo "<div class='container py-5'><p class='text-danger'>Pristup dozvoljen samo radnicima.</p></div>";
    include 'includes/footer.php';
    exit;
}

// Filtracija po datumu (opciono)
$filter_start = $_GET['start'] ?? '';
$filter_end = $_GET['end'] ?? '';

$params = [];
$where = "r.status = 'completed'";

if ($filter_start && $filter_end) {
    $where .= " AND r.return_time BETWEEN ? AND ?";
    $params[] = $filter_start . " 00:00:00";
    $params[] = $filter_end . " 23:59:59";
}

// Dohvatanje istorije
$stmt = $pdo->prepare("
    SELECT r.*, u.first_name, u.last_name, v.name, v.model
    FROM rentals r
    JOIN users u ON r.user_id = u.id
    JOIN vehicles v ON r.vehicle_id = v.id
    WHERE $where
    ORDER BY r.return_time DESC
");
$stmt->execute($params);
$rows = $stmt->fetchAll();
?>

<section class="container py-5">
    <h2>Istorija iznajmljivanja (vraćena vozila)</h2>

    <form method="get" class="row g-3 mb-4">
        <div class="col-md-4">
            <label class="form-label">Od datuma:</label>
            <input type="date" name="start" value="<?php echo htmlspecialchars($filter_start); ?>" class="form-control">
        </div>
        <div class="col-md-4">
            <label class="form-label">Do datuma:</label>
            <input type="date" name="end" value="<?php echo htmlspecialchars($filter_end); ?>" class="form-control">
        </div>
        <div class="col-md-4 d-flex align-items-end">
            <button type="submit" class="btn btn-primary me-2">Filtriraj</button>
            <a href="worker_history.php" class="btn btn-secondary">Poništi</a>
        </div>
    </form>

    <?php if (count($rows) > 0): ?>
        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead class="table-light">
                <tr>
                    <th>Vozilo</th>
                    <th>Korisnik</th>
                    <th>Datum iznajmljivanja</th>
                    <th>Datum povratka</th>
                    <th>Napomene</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($rows as $r): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($r['name'] . ' ' . $r['model']); ?></td>
                        <td><?php echo htmlspecialchars($r['first_name'] . ' ' . $r['last_name']); ?></td>
                        <td><?php echo $r['start_date']; ?> do <?php echo $r['end_date']; ?></td>
                        <td><?php echo $r['return_time'] ? date('d.m.Y H:i', strtotime($r['return_time'])) : '-'; ?></td>
                        <td><?php echo $r['return_notes'] ? nl2br(htmlspecialchars($r['return_notes'])) : '<em>Nema beleške</em>'; ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p class="text-muted">Nema vraćenih vozila za prikaz u zadatom periodu.</p>
    <?php endif; ?>
</section>

<?php include 'includes/footer.php'; ?>
