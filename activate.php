<?php
include 'includes/header.php';
require_once 'db_config.php';

?>

<section class="container py-5">
    <div class="activation-message">
        <?php
        if (isset($_GET['code'])) {
            $activation_code = $_GET['code'];

            $stmt = $pdo->prepare("SELECT id FROM users WHERE activation_code = ? AND is_active = 0");
            $stmt->execute([$activation_code]);

            if ($stmt->rowCount() === 1) {
                $stmt = $pdo->prepare("UPDATE users SET is_active = 1, activation_code = NULL WHERE activation_code = ?");
                $stmt->execute([$activation_code]);
                echo '<div class="activation-message success"><h3>✅ Uspešno!</h3><p>Vaš nalog je uspešno aktiviran. Možete se sada <a href="login.php">prijaviti</a>.</p></div>';
            } else {
                echo '<div class="activation-message error"><h3>⚠️ Greška!</h3><p>Aktivacioni kod je nevažeći ili je već iskorišćen.</p></div>';
            }
        } else {
            echo '<div class="activation-message error"><h3>⚠️ Nedostaje kod</h3><p>Aktivacioni kod nije prosleđen u URL-u.</p></div>';
        }
        ?>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
