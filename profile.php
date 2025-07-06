<?php
require_once 'includes/db_config.php';
include 'includes/header.php';


if (!isset($_SESSION['user']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'user'){
    $_SESSION['profile_error'] = 'Morate biti ulogovani kao korisnik da biste pristupili profilu.';
    header("Location: login.php");
    exit;
}

$user = $_SESSION['user'];
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $id_number = trim($_POST['id_number'] ?? '');
    $driver_license = trim($_POST['driver_license'] ?? '');
    $phone = trim($_POST['phone'] ?? '');

    if ($first_name && $last_name && $id_number && $driver_license && $phone) {
        $stmt = $pdo->prepare("UPDATE users SET first_name = ?, last_name = ?, id_number = ?, driver_license = ?, phone = ? WHERE id = ?");
        $stmt->execute([$first_name, $last_name, $id_number, $driver_license, $phone, $user['id']]);

        // Update session user info
        $_SESSION['user']['first_name'] = $first_name;
        $_SESSION['user']['last_name'] = $last_name;
        $_SESSION['user']['id_number'] = $id_number;
        $_SESSION['user']['driver_license'] = $driver_license;
        $_SESSION['user']['phone'] = $phone;

        $message = '<div class="alert alert-success">Podaci uspešno ažurirani.</div>';
    } else {
        $message = '<div class="alert alert-danger">Sva polja su obavezna.</div>';
    }
}

?>

<section class="container py-5">
    <h2>Moj profil</h2>
    <?php if ($message) echo $message; ?>
    <form method="POST" class="row g-3 mt-3">
        <div class="col-md-6">
            <label class="form-label">Ime</label>
            <input type="text" name="first_name" class="form-control" value="<?php echo htmlspecialchars($user['first_name']); ?>" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">Prezime</label>
            <input type="text" name="last_name" class="form-control" value="<?php echo htmlspecialchars($user['last_name']); ?>">
        </div>
        <div class="col-md-6">
            <label class="form-label">Broj lične karte</label>
            <input type="text" name="id_number" class="form-control" value="<?php echo htmlspecialchars($user['id_number'] ?? ''); ?>" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">Broj vozačke dozvole</label>
            <input type="text" name="driver_license" class="form-control" value="<?php echo htmlspecialchars($user['driver_license'] ?? ''); ?>" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">Telefon</label>
            <input type="text" name="phone" class="form-control" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>" required>
        </div>
        <div class="col-12">
            <button type="submit" class="btn btn-primary">Sačuvaj izmene</button>
        </div>
    </form>
</section>

<?php include 'includes/footer.php'; ?>
