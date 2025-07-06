<?php
require_once 'includes/db_config.php';
include 'includes/header.php';

// Provera pristupa
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo "<div class='container py-5'><p class='text-danger'>Pristup dozvoljen samo administratorima.</p></div>";
    include 'includes/footer.php';
    exit;
}

$message = "";

// Deaktivacija korisnika
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['deactivate_id'])) {
    $deactivate_id = (int) $_POST['deactivate_id'];

    $stmt = $pdo->prepare("UPDATE users SET is_active = 0 WHERE id = ? AND role = 'user'");
    $stmt->execute([$deactivate_id]);
    $message = "<p class='text-success'>Korisniku je onemogućen pristup sistemu.</p>";
}

// Dohvatanje svih korisnika (role = user)
$stmt = $pdo->query("SELECT * FROM users WHERE role = 'user'");
$users = $stmt->fetchAll();
?>

<section class="container py-5">
    <h2>Upravljanje korisnicima</h2>

    <?php if ($message) echo "<div class='my-3'>$message</div>"; ?>

    <?php if (count($users) > 0): ?>
        <div class="table-responsive mt-4">
            <table class="table table-bordered align-middle">
                <thead class="table-light">
                <tr>
                    <th>Ime</th>
                    <th>Email</th>
                    <th>Status</th>
                    <th>Akcija</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($users as $u): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($u['first_name'] . ' ' . $u['last_name']); ?></td>
                        <td><?php echo htmlspecialchars($u['email']); ?></td>
                        <td>
                            <?php echo $u['is_active'] ? '<span class="badge bg-success">Aktivan</span>' : '<span class="badge bg-secondary">Deaktiviran</span>'; ?>
                        </td>
                        <td>
                            <?php if ($u['is_active']): ?>
                                <form method="post" onsubmit="return confirm('Da li ste sigurni da želite da deaktivirate ovog korisnika?');" style="display:inline;">
                                    <input type="hidden" name="deactivate_id" value="<?php echo $u['id']; ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Deaktiviraj</button>
                                </form>
                            <?php else: ?>
                                <em>Nije aktivan</em>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p class="text-muted mt-4">Nema registrovanih korisnika.</p>
    <?php endif; ?>
</section>

<?php include 'includes/footer.php'; ?>
