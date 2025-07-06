<?php
require_once 'includes/db_config.php';
include 'includes/header.php';

if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'user') {
    echo "<div class='container py-5'><p class='text-danger'>Morate biti prijavljeni kao korisnik da biste videli svoje rezervacije.</p></div>";
    include 'includes/footer.php';
    exit;
}

$user_id = $_SESSION['user']['id'];
$message = "";

// Otkazivanje rezervacije (ako postoji forma za to)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_id'])) {
    $cancel_id = (int) $_POST['cancel_id'];

    $stmt = $pdo->prepare("SELECT * FROM reservations WHERE id = ? AND user_id = ? AND is_cancelled = 0");
    $stmt->execute([$cancel_id, $user_id]);
    $res = $stmt->fetch();

    if ($res) {
        $now = new DateTime();
        $start = new DateTime($res['start_datetime']);
        $hours_left = ($start->getTimestamp() - $now->getTimestamp()) / 3600;

        if ($hours_left >= 4) {
            $update = $pdo->prepare("UPDATE reservations SET is_cancelled = 1 WHERE id = ?");
            $update->execute([$cancel_id]);
            $message = "<p class='text-success'>Rezervacija je uspešno otkazana.</p>";
        } else {
            $message = "<p class='text-danger'>Rezervaciju možete otkazati najkasnije 4 sata pre početka.</p>";
        }
    }
}

$stmt = $pdo->prepare("SELECT r.*, v.name, v.model FROM reservations r JOIN vehicles v ON r.vehicle_id = v.id WHERE r.user_id = ? ORDER BY r.start_datetime DESC");
$stmt->execute([$user_id]);
$reservations = $stmt->fetchAll();
?>

<section class="container py-5">
    <h2>Moje rezervacije</h2>
    <?php if ($message) echo "<div class='my-3'>$message</div>"; ?>

    <?php if ($reservations): ?>
        <div class="table-responsive mt-4">
            <table class="table table-bordered">
                <thead class="table-light">
                <tr>
                    <th>Vozilo</th>
                    <th>Početak</th>
                    <th>Kraj</th>
                    <th>Status</th>
                    <th>Akcija</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($reservations as $r): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($r['name'] . ' ' . $r['model']); ?></td>
                        <td><?php echo date('d.m.Y H:i', strtotime($r['start_datetime'])); ?></td>
                        <td><?php echo date('d.m.Y H:i', strtotime($r['end_datetime'])); ?></td>
                        <td>
                            <?php echo $r['is_cancelled'] ? '<span class="badge bg-secondary">Otkazano</span>' : '<span class="badge bg-success">Aktivno</span>'; ?>
                        </td>
                        <td>
                            <?php
                            if (!$r['is_cancelled']) {
                                $now = new DateTime();
                                $start = new DateTime($r['start_datetime']);
                                $hours_left = ($start->getTimestamp() - $now->getTimestamp()) / 3600;

                                if ($hours_left >= 4) {
                                    echo '<form method="post" onsubmit="return confirm(\'Otkazati rezervaciju?\');">';
                                    echo '<input type="hidden" name="cancel_id" value="' . $r['id'] . '">';
                                    echo '<button type="submit" class="btn btn-sm btn-outline-danger">Otkaži</button>';
                                    echo '</form>';
                                } else {
                                    echo '<em>Nije moguće otkazati</em>';
                                }
                            } else {
                                echo '-';
                            }
                            ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p class="text-muted mt-4">Nemate nijednu rezervaciju.</p>
    <?php endif; ?>
</section>

<?php include 'includes/footer.php'; ?>
