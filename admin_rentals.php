<?php
require_once 'includes/db_config.php';
include 'includes/header.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo "<div class='container py-5'><p class='text-danger'>Pristup dozvoljen samo administratorima.</p></div>";
    include 'includes/footer.php';
    exit;
}

$search = $_GET['search'] ?? '';
$sort = $_GET['sort'] ?? 'r.id';
$allowedSort = ['r.id', 'start_datetime', 'end_datetime', 'u.first_name', 'v.name'];
$sort = in_array($sort, $allowedSort) ? $sort : 'r.id';

$sql = "SELECT r.*, u.first_name, u.last_name, v.name AS vehicle_name, v.model FROM reservations r 
        JOIN users u ON r.user_id = u.id 
        JOIN vehicles v ON r.vehicle_id = v.id 
        WHERE u.first_name LIKE :search OR u.last_name LIKE :search OR v.name LIKE :search OR v.model LIKE :search 
        ORDER BY $sort DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute(['search' => "%$search%"]);
$reservations = $stmt->fetchAll();
?>

<section class="container py-5">
    <h2>Pregled svih rezervacija</h2>
    <form method="get" class="row g-3 mb-4">
        <div class="col-md-4">
            <input type="text" name="search" class="form-control" placeholder="Pretraga korisnika ili vozila" value="<?php echo htmlspecialchars($search); ?>">
        </div>
        <div class="col-md-3">
            <select name="sort" class="form-select">
                <option value="r.id">Sortiraj po...</option>
                <option value="start_datetime" <?php if ($sort === 'start_datetime') echo 'selected'; ?>>Početak</option>
                <option value="end_datetime" <?php if ($sort === 'end_datetime') echo 'selected'; ?>>Kraj</option>
                <option value="u.first_name" <?php if ($sort === 'u.first_name') echo 'selected'; ?>>Korisnik</option>
                <option value="v.name" <?php if ($sort === 'v.name') echo 'selected'; ?>>Vozilo</option>
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary">Primeni</button>
        </div>
    </form>

    <?php if (count($reservations) > 0): ?>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Korisnik</th>
                    <th>Vozilo</th>
                    <th>Početak</th>
                    <th>Kraj</th>
                    <th>Kod</th>
                    <th>Status</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($reservations as $r): ?>
                    <tr>
                        <td><?php echo $r['id']; ?></td>
                        <td><?php echo htmlspecialchars($r['first_name'] . ' ' . $r['last_name']); ?></td>
                        <td><?php echo htmlspecialchars($r['vehicle_name'] . ' ' . $r['model']); ?></td>
                        <td><?php echo $r['start_datetime']; ?></td>
                        <td><?php echo $r['end_datetime']; ?></td>
                        <td><?php echo $r['code']; ?></td>
                        <td><?php echo $r['is_cancelled'] ? '<span class="text-danger">Otkazana</span>' : 'Aktivna'; ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p class="text-muted">Nema rezervacija koje odgovaraju kriterijumima pretrage.</p>
    <?php endif; ?>
</section>

<?php include 'includes/footer.php'; ?>
