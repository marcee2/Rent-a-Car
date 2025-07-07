<?php
require_once 'includes/db_config.php';
include 'includes/header.php';

$message = '';

if (isset($_GET['code']) && !empty($_GET['code'])) {
    $activation_code = $_GET['code'];

    $stmt = $pdo->prepare("SELECT id FROM users WHERE activation_code = ? AND is_active = 0");
    $stmt->execute([$activation_code]);

    if ($stmt->rowCount() === 1) {
        $stmt = $pdo->prepare("UPDATE users SET is_active = 1, activation_code = NULL WHERE activation_code = ?");
        $stmt->execute([$activation_code]);
        $message = '<div class="alert alert-success">✅ Vaš nalog je uspešno aktiviran. Možete se sada <a href="login.php">prijaviti</a>.</div>';
    } else {
        $message = '<div class="alert alert-danger">⚠️ Aktivacioni kod je nevažeći, ne postoji ili je već iskorišćen.</div>';
    }
} else {
    $message = '<div class="alert alert-warning">⚠️ Aktivacioni kod nije prosleđen u URL-u.</div>';
}
?>

<section class="container py-5">
    <h2 class="mb-4">Verifikacija naloga</h2>
    <?php echo $message; ?>
</section>

<?php include 'includes/footer.php'; ?>
