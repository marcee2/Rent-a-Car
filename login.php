<?php include "includes/header.php"; ?>
<div class="container mt-5">
    <h2>Prijava</h2>
    <form action="login_process.php" method="POST" id="loginForm">
        <div class="mb-3">
            <label for="email" class="form-label">E-mail adresa</label>
            <input type="email" class="form-control" name="email" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Lozinka</label>
            <input type="password" class="form-control" name="password" required>
        </div>
        <button type="submit" class="btn btn-primary">Prijavi se</button>
    </form>
    <p class="mt-3">
        <a href="forgot_password.php">Zaboravili ste lozinku?</a>
    </p>

</div>
<?php include "includes/footer.php"; ?>
