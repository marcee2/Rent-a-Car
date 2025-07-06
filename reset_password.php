<?php
require_once 'includes/db_config.php';
session_start();

if (!isset($_GET['token'])) {
    echo "<div class='container py-5'><p class='text-danger'>Token nije validan.</p></div>";
    exit;
}

$token = $_GET['token'];

// Sanitize token to avoid issues with URL encoding
$token = filter_var($token, FILTER_SANITIZE_STRING);

$stmt = $pdo->prepare("SELECT email FROM password_resets WHERE token = ? AND expires_at >= NOW()");
$stmt->execute([$token]);
$result = $stmt->fetch();

if (!$result) {
    echo "<div class='container py-5'><p class='text-danger'>Link za resetovanje je neispravan ili je istekao.</p></div>";
    exit;
}

$email = $result['email'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if ($new_password !== $confirm_password) {
        $error = "Lozinke se ne poklapaju.";
    } elseif (strlen($new_password) < 6) {
        $error = "Lozinka mora imati najmanje 6 karaktera.";
    } else {
        $hashed = password_hash($new_password, PASSWORD_BCRYPT);

        $update = $pdo->prepare("UPDATE users SET password = ? WHERE email = ?");
        $update->execute([$hashed, $email]);

        $delete = $pdo->prepare("DELETE FROM password_resets WHERE email = ?");
        $delete->execute([$email]);

        $_SESSION['success'] = "Lozinka je uspešno promenjena. Možete se sada prijaviti.";
        header("Location: login.php");
        exit;
    }
}

include 'includes/header.php';
?>

<div class="container py-5">
    <h2>Resetuj lozinku</h2>
    <?php if (!empty($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
    <form method="post">
        <div class="mb-3">
            <label class="form-label">Nova lozinka</label>
            <input type="password" name="new_password" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Potvrdi lozinku</label>
            <input type="password" name="confirm_password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Sačuvaj novu lozinku</button>
    </form>
</div>

<?php include 'includes/footer.php'; ?>
