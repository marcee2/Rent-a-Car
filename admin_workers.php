<?php
require_once 'includes/db_config.php';
include 'includes/header.php';

// Provera pristupa
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo "<div class='container py-5'><p class='text-danger'>Pristup dozvoljen samo administratorima.</p></div>";
    include 'includes/footer.php';
    exit;
}

$message = "";

// Dodavanje radnika
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'])) {
    $first = trim($_POST['first_name']);
    $last = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $pass = $_POST['password'];

    if ($first && $last && $email && $pass) {
        $check = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $check->execute([$email]);

        if ($check->rowCount() === 0) {
            $hash = password_hash($pass, PASSWORD_BCRYPT);
            $stmt = $pdo->prepare("INSERT INTO users (first_name, last_name, email, password, role, is_active)
                VALUES (?, ?, ?, ?, 'worker', 1)");
            $stmt->execute([$first, $last, $email, $hash]);
            $message = "<p class='text-success'>Uspešno dodat novi radnik.</p>";
        } else {
            $message = "<p class='text-danger'>Ova email adresa je već registrovana.</p>";
        }
    } else {
        $message = "<p class='text-danger'>Popunite sva polja.</p>";
    }
}

// Deaktivacija radnika
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['deactivate_id'])) {
    $deactivate_id = (int) $_POST['deactivate_id'];

    if ($deactivate_id !== $_SESSION['user_id']) {
        $stmt = $pdo->prepare("UPDATE users SET is_active = 0 WHERE id = ? AND role = 'worker'");
        $stmt->execute([$deactivate_id]);
        $message = "<p class='text-success'>Radnik je deaktiviran.</p>";
    } else {
        $message = "<p class='text-warning'>Ne možete deaktivirati sami sebe.</p>";
    }
}

// Lista radnika
$workers = $pdo->query("SELECT * FROM users WHERE role = 'worker'")->fetchAll();
?>

<section class="container py-5">
    <h2>Upravljanje radnicima</h2>

    <?php if ($message) echo "<div class='my-3'>$message</div>"; ?>

    <div class="card mb-5">
        <div class="card-header">Dodaj novog radnika</div>
        <div class="card-body">
            <form method="post" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Ime</label>
                    <input type="text" name="first_name" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Prezime</label>
                    <input type="text" name="last_name" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Lozinka</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="col-md-12">
                    <button type="submit" class="btn btn-success">Dodaj radnika</button>
                </div>
            </form>
        </div>
    </div>

    <h4>Postojeći radnici</h4>
    <?php if (count($workers) > 0): ?>
        <div class="table-responsive mt-3">
            <table class="table table-bordered">
                <thead class="table-light">
                <tr>
                    <th>Ime</th>
                    <th>Email</th>
                    <th>Status</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($workers as $w): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($w['first_name'] . ' ' . $w['last_name']); ?></td>
                        <td><?php echo htmlspecialchars($w['email']); ?></td>
                        <td>
                            <?php if ($w['is_active']): ?>
                                <form method="post" onsubmit="return confirm('Da li ste sigurni da želite da deaktivirate ovog radnika?');" style="display:inline;">
                                    <input type="hidden" name="deactivate_id" value="<?php echo $w['id']; ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Deaktiviraj</button>
                                </form>
                            <?php else: ?>
                                <span class="badge bg-secondary">Deaktiviran</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p class="text-muted">Nema registrovanih radnika.</p>
    <?php endif; ?>
</section>

<?php include 'includes/footer.php'; ?>
