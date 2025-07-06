<?php
require_once 'includes/db_config.php';
include 'includes/header.php';
session_start();

// Provera da li je korisnik prijavljen
if (!isset($_SESSION['user_id'])) {
    echo "<div class='container py-5'><p class='text-danger'>Morate biti prijavljeni da biste izvršili rezervaciju.</p></div>";
    include 'includes/footer.php';
    exit;
}

// Provera da li je prosleđen ID vozila
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<div class='container py-5'><p class='text-danger'>Vozilo nije pronađeno.</p></div>";
    include 'includes/footer.php';
    exit;
}

$vehicle_id = (int) $_GET['id'];
$user_id = $_SESSION['user_id'];
$message = "";

// Ako je forma poslata
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $start_date = $_POST['start_date'] ?? '';
    $end_date = $_POST['end_date'] ?? '';

    if (!$start_date || !$end_date || $end_date < $start_date) {
        $message = "<p class='text-danger'>Molimo unesite validne datume.</p>";
    } else {
        // Provera dostupnosti vozila u datom terminu
        $stmt = $pdo->prepare("SELECT * FROM rentals 
            WHERE vehicle_id = ? AND status IN ('pending','approved')
            AND (start_date <= ? AND end_date >= ?)");
        $stmt->execute([$vehicle_id, $end_date, $start_date]);

        if ($stmt->rowCount() > 0) {
            $message = "<p class='text-danger'>Vozilo je već rezervisano za izabrani period.</p>";
        } else {
            // Unos nove rezervacije
            $stmt = $pdo->prepare("INSERT INTO rentals (user_id, vehicle_id, start_date, end_date, status)
                VALUES (?, ?, ?, ?, 'pending')");
            $stmt->execute([$user_id, $vehicle_id, $start_date, $end_date]);

            $message = "<p class='text-success'>Zahtev za rezervaciju je uspešno poslat. Sačekajte odobrenje.</p>";
        }
    }
}
?>

<section class="container py-5">
    <h2>Rezerviši vozilo</h2>
    <?php if ($message) echo "<div class='my-3'>$message</div>"; ?>

    <form method="post" class="form-container mt-4">
        <div class="mb-3">
            <label for="start_date" class="form-label">Datum početka</label>
            <input type="date" name="start_date" id="start_date" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="end_date" class="form-label">Datum završetka</label>
            <input type="date" name="end_date" id="end_date" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Pošalji zahtev</button>
        <a href="vehicle.php?id=<?php echo $vehicle_id; ?>" class="btn btn-secondary ms-2">Nazad</a>
    </form>
</section>

<?php include 'includes/footer.php'; ?>
