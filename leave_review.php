<?php
require_once 'includes/db_config.php';
include 'includes/header.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    echo "<div class='container py-5'><p class='text-danger'>Morate biti prijavljeni da biste ostavili komentar.</p></div>";
    include 'includes/footer.php';
    exit;
}

$user_id = $_SESSION['user_id'];
$message = "";

// Ako korisnik šalje novi komentar
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['rental_id'], $_POST['content'], $_POST['rating'])) {
    $rental_id = (int) $_POST['rental_id'];
    $content = trim($_POST['content']);
    $rating = (int) $_POST['rating'];

    // Proveri da li je rental korisnikov i status completed
    $stmt = $pdo->prepare("SELECT * FROM rentals WHERE id = ? AND user_id = ? AND status = 'completed'");
    $stmt->execute([$rental_id, $user_id]);
    $rental = $stmt->fetch();

    if ($rental && $rating >= 1 && $rating <= 5 && $content !== "") {
        // Proveri da li komentar već postoji
        $check = $pdo->prepare("SELECT id FROM comments WHERE user_id = ? AND vehicle_id = ?");
        $check->execute([$user_id, $rental['vehicle_id']]);
        if ($check->rowCount() == 0) {
            // Upis komentara
            $insert = $pdo->prepare("INSERT INTO comments (user_id, vehicle_id, content, rating) VALUES (?, ?, ?, ?)");
            $insert->execute([$user_id, $rental['vehicle_id'], $content, $rating]);
            $message = "<p class='text-success'>Uspešno ste ostavili komentar.</p>";
        } else {
            $message = "<p class='text-warning'>Već ste ostavili komentar za ovo vozilo.</p>";
        }
    } else {
        $message = "<p class='text-danger'>Greška: neispravni podaci ili nevažeća rezervacija.</p>";
    }
}

// Prikaz svih completed rezervacija korisnika
$stmt = $pdo->prepare("
    SELECT r.id AS rental_id, v.id AS vehicle_id, v.name, v.model
    FROM rentals r
    JOIN vehicles v ON r.vehicle_id = v.id
    WHERE r.user_id = ? AND r.status = 'completed'
");
$stmt->execute([$user_id]);
$rentals = $stmt->fetchAll();
?>

<section class="container py-5">
    <h2>Ostavi komentar</h2>
    <?php if ($message) echo "<div class='my-3'>$message</div>"; ?>

    <?php if (count($rentals) > 0): ?>
        <?php foreach ($rentals as $r): ?>
            <?php
            // Proveri da li komentar postoji
            $check = $pdo->prepare("SELECT * FROM comments WHERE user_id = ? AND vehicle_id = ?");
            $check->execute([$user_id, $r['vehicle_id']]);
            $comment = $check->fetch();
            ?>
            <div class="card my-4">
                <div class="card-body">
                    <h5 class="card-title"><?php echo htmlspecialchars($r['name'] . ' ' . $r['model']); ?></h5>

                    <?php if ($comment): ?>
                        <p><strong>Vaša ocena:</strong> ⭐ <?php echo $comment['rating']; ?>/5</p>
                        <p><?php echo nl2br(htmlspecialchars($comment['content'])); ?></p>
                        <p class="text-muted">Komentar unet: <?php echo date('d.m.Y H:i', strtotime($comment['created_at'])); ?></p>
                    <?php else: ?>
                        <form method="post">
                            <input type="hidden" name="rental_id" value="<?php echo $r['rental_id']; ?>">
                            <div class="mb-2">
                                <label class="form-label">Ocena (1–5)</label>
                                <select name="rating" class="form-select" required>
                                    <option value="">-- Izaberite --</option>
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <option value="<?php echo $i; ?>"><?php echo $i; ?> ⭐</option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            <div class="mb-2">
                                <label class="form-label">Vaš komentar</label>
                                <textarea name="content" rows="3" class="form-control" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Pošalji komentar</button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p class="text-muted mt-4">Nemate nijednu vraćenu rezervaciju dostupnu za komentarisanje.</p>
    <?php endif; ?>
</section>

<?php include 'includes/footer.php'; ?>
