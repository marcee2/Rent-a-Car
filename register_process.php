<?php
require_once 'includes/db_config.php';
require_once 'PHPMailer/src/PHPMailer.php';
require_once 'PHPMailer/src/SMTP.php';
require_once 'PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $id_number = trim($_POST['id_number']);
    $driver_license = trim($_POST['driver_license']);
    $license_place = trim($_POST['license_place']);

    // Proveri da li email već postoji
    $check = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $check->execute([$email]);
    if ($check->rowCount() > 0) {
        $error = "Nalog sa tom e-mail adresom već postoji.";
    } else {
        // Hash lozinke
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        $activation_code = bin2hex(random_bytes(16));

        // Unos u bazu
        $stmt = $pdo->prepare("INSERT INTO users (first_name, last_name, email, password, id_number, driver_license, license_place, role, is_active, activation_code) VALUES (?, ?, ?, ?, ?, ?, ?, 'user', 0, ?)");
        $stmt->execute([$first_name, $last_name, $email, $hashed_password, $id_number, $driver_license, $license_place, $activation_code]);

        // Slanje mejla
        $activation_link = "https://builders.stud.vts.su.ac.rs/car_rental_project/activate.php?code=$activation_code";

        try {
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'marcetic.nikola05@gmail.com';
            $mail->Password = 'vbpc pita ovnt ymeu';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('marcetic.nikola05@gmail.com', 'Rent a Car SU');
            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject = 'Aktivacija naloga';
            $mail->Body = "<p>Poštovani $first_name,</p>
                            <p>Hvala što ste se registrovali. Da biste aktivirali svoj nalog, kliknite na sledeći link:</p>
                            <p><a href='$activation_link'>$activation_link</a></p>
                            <p>Ako niste vi kreirali ovaj nalog, zanemarite ovu poruku.</p>";

            $mail->send();
            $success = "Registracija uspešna. Proverite email za aktivacioni link.";
        } catch (Exception $e) {
            $error = "Registracija nije uspela. Došlo je do greške pri slanju emaila.";
        }
    }
}

include 'includes/header.php';
?>

<div class="container py-5">
    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php elseif (!empty($success)): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
