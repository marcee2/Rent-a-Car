<?php
require_once 'db_config.php';
include 'includes/header.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<div class='container py-5'><p class='text-danger'>Greška: ID vozila nije validan.</p></div>";
    include 'includes/footer.php';
    exit;
}

$vehicle_id = (int) $_GET['id'];

$pdo->prepare("UPDATE vehicles SET popularity = popularity + 1 WHERE id = ?")->execute([$vehicle_id]);

$stmt = $pdo->prepare("SELECT * FROM vehicles WHERE id = ?");
$stmt->execute([$vehicle_id]);
$vehicle = $stmt->fetch();

if (!$vehicle) {
    echo "<div class='container py-5'><p class='text-danger'>Vozilo nije pronađeno.</p></div>";
    include 'includes/footer.php';
    exit;
}

$user_id = $_SESSION['user']['id'] ?? null;
$can_review = false;

if ($user_id) {
    $check = $pdo->prepare("SELECT COUNT(*) FROM reservations WHERE user_id = ? AND vehicle_id = ? AND is_cancelled = 0 AND end_datetime <= NOW()");
    $check->execute([$user_id, $vehicle_id]);
    $can_review = $check->fetchColumn() > 0;
}
?>

<section class="container py-5">
    <?php if (!empty($_SESSION['reservation_success'])): ?>
        <div class="alert alert-success"> <?php echo $_SESSION['reservation_success']; unset($_SESSION['reservation_success']); ?> </div>
    <?php elseif (!empty($_SESSION['reservation_error'])): ?>
        <div class="alert alert-danger"> <?php echo $_SESSION['reservation_error']; unset($_SESSION['reservation_error']); ?> </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-md-6">
            <img src="<?php echo htmlspecialchars($vehicle['image']); ?>" alt="<?php echo htmlspecialchars($vehicle['model']); ?>" class="img-fluid rounded shadow-sm">
        </div>
        <div class="col-md-6">
            <h2><?php echo htmlspecialchars($vehicle['name'] . ' ' . $vehicle['model']); ?></h2>
            <p><strong>Godište:</strong> <?php echo $vehicle['year']; ?></p>
            <p><strong>Gorivo:</strong> <?php echo $vehicle['fuel_type']; ?></p>
            <p><strong>Menjač:</strong> <?php echo $vehicle['gearbox']; ?></p>
            <p><strong>Sedišta:</strong> <?php echo $vehicle['seats']; ?></p>
            <p><strong>Cena:</strong> <?php echo $vehicle['price_per_day']; ?> € / dan</p>
            <p><strong>Popularnost:</strong> <?php echo $vehicle['popularity']; ?></p>

            <?php if (isset($_SESSION['user']) && $_SESSION['role'] === 'user'): ?>
                <div class="mt-4">
                    <h4>Rezerviši ovo vozilo</h4>
                    <form method="POST" action="reserve_process.php">
                        <input type="hidden" name="vehicle_id" value="<?php echo $vehicle['id']; ?>">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Datum i vreme početka</label>
                                <input type="datetime-local" name="start_date" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Datum i vreme završetka</label>
                                <input type="datetime-local" name="end_date" class="form-control" required>
                            </div>
                        </div>
                        <div class="mt-3">
                            <button type="submit" class="btn btn-success">Rezerviši</button>
                        </div>
                    </form>
                </div>
            <?php else: ?>
                <div class="alert alert-info mt-4">Morate biti ulogovani kao korisnik da biste mogli da rezervišete vozilo.</div>
            <?php endif; ?>
        </div>
    </div>

    <hr class="my-5">
    <h4>Komentari i ocene</h4>
    <?php
    $comments_stmt = $pdo->prepare("SELECT c.content, c.rating, c.created_at, u.first_name, u.last_name FROM comments c JOIN users u ON c.user_id = u.id WHERE c.vehicle_id = ? ORDER BY c.created_at DESC");
    $comments_stmt->execute([$vehicle_id]);
    $comments = $comments_stmt->fetchAll();

    if ($comments):
        foreach ($comments as $comment): ?>
            <div class="mb-3 p-3 border rounded bg-light">
                <p><strong><?php echo htmlspecialchars($comment['first_name'] . ' ' . $comment['last_name']); ?></strong>
                    – ⭐ <?php echo $comment['rating']; ?>/5</p>
                <p><?php echo nl2br(htmlspecialchars($comment['content'])); ?></p>
                <small class="text-muted"><?php echo date('d.m.Y H:i', strtotime($comment['created_at'])); ?></small>
            </div>
        <?php endforeach;
    else:
        echo "<p class='text-muted'>Nema komentara za ovo vozilo još uvek.</p>";
    endif;

    if (isset($_SESSION['user']) && $_SESSION['role'] === 'user' && $can_review): ?>
        <div class="mt-5">
            <h5>Ostavi komentar i ocenu</h5>
            <?php if (!empty($_SESSION['review_error'])): ?>
                <div class="alert alert-danger"><?php echo $_SESSION['review_error']; unset($_SESSION['review_error']); ?></div>
            <?php endif; ?>
            <?php if (!empty($_SESSION['review_success'])): ?>
                <div class="alert alert-success"><?php echo $_SESSION['review_success']; unset($_SESSION['review_success']); ?></div>
            <?php endif; ?>
            <form method="post" action="submit_review.php">
                <input type="hidden" name="vehicle_id" value="<?php echo $vehicle_id; ?>">
                <div class="mb-3">
                    <label class="form-label">Ocena (1-5)</label>
                    <select name="rating" class="form-select" required>
                        <option value="">Izaberi ocenu</option>
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <option value="<?php echo $i; ?>"><?php echo $i; ?> ★</option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Komentar</label>
                    <textarea name="content" class="form-control" rows="4" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Pošalji recenziju</button>
            </form>
        </div>
    <?php endif; ?>
</section>

<div class="text-center my-4">
    <a href="vehicles.php" class="btn btn-secondary">&larr; Nazad na izbor vozila</a>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.querySelector('form[action="reserve_process.php"]');
        if (!form) return;

        form.addEventListener('submit', function (e) {
            const startInput = form.querySelector('input[name="start_date"]');
            const endInput = form.querySelector('input[name="end_date"]');

            const start = new Date(startInput.value);
            const end = new Date(endInput.value);

            if (isNaN(start.getTime()) || isNaN(end.getTime())) {
                alert('Molimo unesite validne datume i vremena.');
                e.preventDefault();
                return;
            }

            const hours = (end - start) / (1000 * 60 * 60);

            if (hours < 12 || hours % 12 !== 0) {
                alert('Rezervacija mora trajati najmanje 12 sati i biti u koracima od 12h (npr. 12h, 24h, 36h...).');
                e.preventDefault();
            }
        });
    });
</script>

<?php include 'includes/footer.php'; ?>
