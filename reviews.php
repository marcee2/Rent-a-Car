<?php
require_once 'includes/db_config.php';
include 'includes/header.php';

if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'user') {
    echo "<div class='container py-5'><p class='text-danger'>Morate biti prijavljeni kao korisnik da biste videli ovu stranicu.</p></div>";
    include 'includes/footer.php';
    exit;
}

$user_id = $_SESSION['user']['id'];
$message = "";

// Obrada forme za komentar putem koda
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['code'], $_POST['vehicle_id'])) {
    $code = trim($_POST['code']);
    $vehicle_id = (int) $_POST['vehicle_id'];
    $rating = (int) ($_POST['rating'] ?? 0);
    $content = trim($_POST['content'] ?? '');

    $stmt = $pdo->prepare("SELECT * FROM reservations WHERE user_id = ? AND vehicle_id = ? AND code = ? AND is_cancelled = 0 AND end_datetime <= NOW()");
    $stmt->execute([$user_id, $vehicle_id, $code]);
    $reservation = $stmt->fetch();

    if ($reservation) {
        // Proveri da li je već ostavljen komentar
        $check = $pdo->prepare("SELECT COUNT(*) FROM comments WHERE user_id = ? AND reservation_id = ?");
        $check->execute([$user_id, $reservation['id']]);

        if ($check->fetchColumn() == 0 && $rating >= 1 && $rating <= 5 && $content !== '') {
            $insert = $pdo->prepare("INSERT INTO comments (user_id, vehicle_id, reservation_id, content, rating, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
            $insert->execute([$user_id, $vehicle_id, $reservation['id'], $content, $rating]);
            $message = "<div class='alert alert-success'>Komentar uspešno dodat.</div>";
        } else {
            $message = "<div class='alert alert-warning'>Već ste ostavili komentar ili su podaci neispravni.</div>";
        }
    } else {
        $message = "<div class='alert alert-danger'>Nevalidan kod ili vreme komentarisanja nije dozvoljeno.</div>";
    }
}

// Prikaz već ostavljenih komentara
$stmt = $pdo->prepare("SELECT c.*, v.name, v.model FROM comments c JOIN vehicles v ON c.vehicle_id = v.id WHERE c.user_id = ? ORDER BY c.created_at DESC");
$stmt->execute([$user_id]);
$comments = $stmt->fetchAll();
?>

<section class="container py-5">
    <h2>Vaše recenzije</h2>
    <?php if ($message) echo $message; ?>

    <?php if ($comments): ?>
        <?php foreach ($comments as $c): ?>
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title"><?php echo htmlspecialchars($c['name'] . ' ' . $c['model']); ?></h5>
                    <p><strong>Ocena:</strong> <?php echo $c['rating']; ?>/5</p>
                    <p><?php echo nl2br(htmlspecialchars($c['content'])); ?></p>
                    <small class="text-muted"><?php echo date('d.m.Y H:i', strtotime($c['created_at'])); ?></small>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p class="text-muted">Još uvek niste ostavili nijednu recenziju.</p>
    <?php endif; ?>

    <hr class="my-5">
    <h4>Ostavi novu recenziju putem koda</h4>
    <form method="post" class="row g-3">
        <div class="col-md-4">
            <label class="form-label">Rezervacioni kod</label>
            <input type="text" name="code" class="form-control" required>
        </div>
        <div class="col-md-4">
            <label class="form-label">ID vozila</label>
            <input type="number" name="vehicle_id" class="form-control" required>
        </div>
        <div class="col-md-2">
            <label class="form-label">Ocena</label>
            <select name="rating" class="form-select" required>
                <option value="">--</option>
                <?php for ($i = 1; $i <= 5; $i++): ?>
                    <option value="<?php echo $i; ?>"><?php echo $i; ?> ★</option>
                <?php endfor; ?>
            </select>
        </div>
        <div class="col-md-12">
            <label class="form-label">Komentar</label>
            <textarea name="content" class="form-control" rows="3" required></textarea>
        </div>
        <div class="col-12">
            <button type="submit" class="btn btn-primary">Pošalji komentar</button>
        </div>
    </form>
</section>

<?php include 'includes/footer.php'; ?>
