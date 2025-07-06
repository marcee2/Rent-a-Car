<?php
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
include "includes/header.php";
?>

<div class="container mt-5">
    <h3>Dobrodošli, <?= htmlspecialchars($_SESSION['first_name']) ?>!</h3>
    <p>Uspešno ste prijavljeni kao <strong><?= $_SESSION['role'] ?></strong>.</p>
    <a href="logout.php" class="btn btn-danger">Odjavi se</a>
</div>

<?php include "includes/footer.php"; ?>
