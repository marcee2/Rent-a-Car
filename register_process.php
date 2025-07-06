<?php
require_once 'db_config.php';
require_once 'PHPMailer/src/PHPMailer.php';
require_once 'PHPMailer/src/SMTP.php';
require_once 'PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Provera da li je forma poslata
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Prikupljanje podataka iz forme
    $first_name      = trim($_POST['first_name']);
    $last_name       = trim($_POST['last_name']);
    $email           = trim($_POST['email']);
    $password        = $_POST['password'];
    $id_number       = trim($_POST['id_number']);
    $driver_license  = trim($_POST['driver_license']);
    $license_place   = trim($_POST['license_place']);

    // Provera da li korisnik već postoji
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);

    if ($stmt->rowCount() > 0) {
        echo "E-mail adresa je već registrovana.";
        exit;
    }

    // Heširanje lozinke
    $password_hash = password_hash($password, PASSWORD_BCRYPT);

    // Generisanje aktivacionog koda
    $activation_code = bin2hex(random_bytes(16));

    // Unos korisnika u bazu
    $stmt = $pdo->prepare("
        INSERT INTO users 
            (first_name, last_name, email, password, id_number, driver_license, license_place, activation_code, is_active, role)
        VALUES 
            (?, ?, ?, ?, ?, ?, ?, ?, 0, 'user')
    ");

    $stmt->execute([
        $first_name,
        $last_name,
        $email,
        $password_hash,
        $id_number,
        $driver_license,
        $license_place,
        $activation_code
    ]);

    // Slanje aktivacionog e-maila
    $mail = new PHPMailer(true);

    try {
        // SMTP podešavanja
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'marcetic.nikola05@gmail.com';
        $mail->Password   = 'oxnq wlcl tmgp acwp';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Podaci o pošiljaocu i primaocu
        $mail->setFrom('marcetic.nikola05@gmail.com', 'RentCar');
        $mail->addAddress($email, "$first_name $last_name");

        // Sadržaj poruke
        $mail->isHTML(true);
        $mail->Subject = 'Aktivacija korisničkog naloga';

        $activation_link = "http://localhost/car_rental_project/activate.php?code=$activation_code";

        $mail->Body = "
            <h3>Poštovani $first_name,</h3>
            <p>Hvala što ste se registrovali.</p>
            <p>Molimo kliknite na sledeći link da biste aktivirali svoj nalog:</p>
            <p><a href='$activation_link'>$activation_link</a></p>
            <br>
            <p>S poštovanjem,<br><b>Rent a Car SU tim</b></p>
        ";

        $mail->send();
        echo "Registracija uspešna! Proverite svoj e-mail i kliknite na aktivacioni link.";
    } catch (Exception $e) {
        echo "Slanje e-maila nije uspelo. Greška: {$mail->ErrorInfo}";
    }
}
?>
