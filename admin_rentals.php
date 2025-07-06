<?php
require_once 'includes/db_config.php';
include 'includes/header.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo "<div class='container py-5'><p class='text-danger'>Pristup dozvoljen samo administratorima.</p></div>";
    include 'includes/footer.php';
    exit;
}

// Filtriranje po datumu i korisniku
$date_filter = $_GET['date'] ?? '';
$user_filter = $_GET['user'] ?? '';
$sort = in_array($_GET['sort'] ?? '', ['start_date', 'first_name', 'name']) ? $_GET['sort'] : 'start_date';

$query = "SELECT rentals.*, users.first_name, users.last_name, vehicles.name, vehicles.model 
          FROM rentals 
          JOIN users ON rentals.user_id = users.id 
          JOIN vehicles ON rentals.vehicle_id = vehicles.id";
$where = [];
$params = [];

if ($date_filter) {
    $where[] = "DATE(rentals.start_date) = ?";
    $params[] = $date_filter;
}
if ($user_filter) {
    $where[] = "users.id = ?";
    $params[] = $user_filter;
}

if (!empty($where)) {
    $query .= " WHERE " . implode(" AND ", $where);
}

$query .= " ORDER BY $sort DESC";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$rentals = $stmt->fetchAll();

// Lista korisnika za dropdown
$usersList = $pdo->query("SELECT id, first_name, last_name FROM users WHERE role = 'user' ORDER BY first_name")->fetchAll();
?>

<section class="container py-5">
    <h2>Pregled svih iznajmljivanja</h2>

    <form class="row mb-4" method="get">
        <div class="col-md-3">
            <label class="form-label">Filter po datumu</label>
            <input type="date" name="date" value="<?php echo htmlspecialchars($date_filter); ?>" class="form-control">
        </div>
        <div class="col-md-3">
            <label class="form-label">Filter po korisniku</label>
            <select name="user" class="form-select">
                <option value="">-- svi korisnici --</option>
                <?php foreach ($usersList as $u): ?>
                    <option value="<?php echo $u['id']; ?>" <?php if ($user_filter == $u['id']) echo 'selected'; ?>>
                        <?php echo htmlspecialchars($u['first_name'] . ' ' . $u['last_name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label">Sortiraj po</label>
            <select name="sort" class="form-select">
                <option value="start_date" <?php if ($sort === 'start_date') echo 'selected'; ?>>Datum početka</option>
                <option value="first_name" <?php if ($sort === 'first_name') echo 'selected'; ?>>Ime korisnika</option>
                <option value="name" <?php if ($sort === 'name') echo 'selected'; ?>>Naziv vozila</option>
            </select>
        </div>
        <div class="col-md-2 d-flex align-items-end">
            <button type="submit" class="btn btn-primary w-100">Primeni</button>
        </div>
    </form>

    <?php if (count($rentals) > 0): ?>
        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Korisnik</th>
                    <th>Vozilo</th>
                    <th>Početak</th>
                    <th>Kraj</th>
                    <th>Povratak</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($rentals as $r): ?>
                    <tr>
                        <td><?php echo $r['id']; ?></td>
                        <td><?php echo htmlspecialchars($r['first_name'] . ' ' . $r['last_name']); ?></td>
                        <td><?php echo htmlspecialchars($r['name'] . ' ' . $r['model']); ?></td>
                        <td><?php echo date('d.m.Y H:i', strtotime($r['start_date'])); ?></td>
                        <td><?php echo date('d.m.Y H:i', strtotime($r['end_date'])); ?></td>
                        <td>
                            <?php echo $r['return_time']
                                ? date('d.m.Y H:i', strtotime($r['return_time']))
                                : '<span class="text-muted">Još nije vraćeno</span>'; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p class="text-muted">Nema iznajmljivanja za prikaz.</p>
    <?php endif; ?>
</section>

<?php include 'includes/footer.php'; ?>
