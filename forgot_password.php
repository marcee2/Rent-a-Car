<<?php
require_once 'includes/db_config.php';
require_once 'PHPMailer/src/PHPMailer.php';
require_once 'PHPMailer/src/SMTP.php';
require_once 'PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user) {
        $token = bin2hex(random_bytes(32));
        $expires_at = date('Y-m-d H:i:s', time() + 3600);

        $insert = $pdo->prepare("INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, ?) 
            ON DUPLICATE KEY UPDATE token = VALUES(token), expires_at = VALUES(expires_at)");
        $insert->execute([$email, $token, $expires_at]);

        $link = "http://localhost/car_rental_project/reset_password.php?token=$token";

        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'TVOJ_EMAIL@gmail.com';
        $mail->Password = 'TVOJA_APP_LOZINKA';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('noreply@rentcar.com', 'Rent a Car SU');
        $mail->addAddress($email);
        $mail->isHTML(true);
        $mail->Subject = 'Resetovanje lozinke';
        $mail->Body = "Kliknite na sledeći link kako biste resetovali lozinku:<br><a href='$link'>$link</a>";

        $mail->send();
    }

    echo "<div class='container py-5'><p class='text-success'>Ako postoji nalog sa tom e-mail adresom, link za resetovanje je poslat.</p></div>";
}
?>


<?php include "includes/header.php"; ?>
<div class="container py-5">
    <h2>Zaboravljena lozinka</h2>
    <?php if (!empty($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
    <?php if (!empty($success)) echo "<div class='alert alert-success'>$success</div>"; ?>
    <form method="post" class="mt-4">
        <div class="mb-3">
            <label class="form-label">Unesite e-mail adresu</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Pošalji link za reset</button>
    </form>
</div>
<?php include "includes/footer.php"; ?>
