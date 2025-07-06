<?php include "includes/header.php"; ?>

<div class="container mt-5">
    <h2>Registracija</h2>
    <form action="register_process.php" method="POST" id="registerForm">
        <div class="mb-3">
            <label for="first_name" class="form-label">Ime</label>
            <input type="text" class="form-control" id="first_name" name="first_name" required>
        </div>
        <div class="mb-3">
            <label for="last_name" class="form-label">Prezime</label>
            <input type="text" class="form-control" id="last_name" name="last_name" required>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">E-mail adresa</label>
            <input type="email" class="form-control" id="email" name="email" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Lozinka</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <div class="mb-3">
            <label for="id_number" class="form-label">Broj lične karte ili pasoša</label>
            <input type="text" class="form-control" id="id_number" name="id_number" required>
        </div>
        <div class="mb-3">
            <label for="driver_license" class="form-label">Broj vozačke dozvole</label>
            <input type="text" class="form-control" id="driver_license" name="driver_license" required>
        </div>
        <div class="mb-3">
            <label for="license_place" class="form-label">Mesto izdavanja vozačke dozvole</label>
            <input type="text" class="form-control" id="license_place" name="license_place" required>
        </div>
        <button type="submit" class="btn btn-primary">Registruj se</button>
    </form>
</div>

<?php include "includes/footer.php"; ?>
