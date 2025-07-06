<?php
require_once 'includes/db_config.php';
include 'includes/header.php';

// Search and sort parameters
$search = $_GET['search'] ?? '';
$sort = $_GET['sort'] ?? 'popularity';
$order = in_array($sort, ['name', 'model', 'year', 'popularity']) ? $sort : 'popularity';

// Fetch vehicles from database
$stmt = $pdo->prepare("SELECT * FROM vehicles WHERE name LIKE ? OR model LIKE ? ORDER BY $order DESC");
$stmt->execute(['%' . $search . '%', '%' . $search . '%']);
$vehicles = $stmt->fetchAll();
?>

<section class="container py-5">
    <h2 class="mb-4">Svi automobili</h2>

    <form method="get" class="row g-3 mb-4">
        <div class="col-md-6">
            <input type="text" name="search" class="form-control" placeholder="Pretraga po imenu ili modelu" value="<?php echo htmlspecialchars($search); ?>">
        </div>
        <div class="col-md-4">
            <select name="sort" class="form-select">
                <option value="popularity" <?php if ($sort == 'popularity') echo 'selected'; ?>>Popularnost</option>
                <option value="name" <?php if ($sort == 'name') echo 'selected'; ?>>Naziv</option>
                <option value="model" <?php if ($sort == 'model') echo 'selected'; ?>>Model</option>
                <option value="year" <?php if ($sort == 'year') echo 'selected'; ?>>Godina</option>
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary w-100">Pretraži</button>
        </div>
    </form>

    <?php if (count($vehicles) > 0): ?>
        <div class="row g-4">
            <?php foreach ($vehicles as $vehicle): ?>
                <div class="col-md-6">
                    <div class="card h-100">
                        <a href="vehicle.php?id=<?php echo $vehicle['id']; ?>">
                            <img src="<?php echo htmlspecialchars($vehicle['image']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($vehicle['model']); ?>">
                        </a>
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($vehicle['name'] . ' ' . $vehicle['model']); ?></h5>
                            <p class="card-text">
                                <strong>Godina:</strong> <?php echo $vehicle['year']; ?><br>
                                <strong>Gorivo:</strong> <?php echo $vehicle['fuel_type']; ?><br>
                                <strong>Menjač:</strong> <?php echo $vehicle['gearbox']; ?><br>
                                <strong>Popularnost:</strong> <?php echo $vehicle['popularity']; ?>
                            </p>
                            <a href="vehicle.php?id=<?php echo $vehicle['id']; ?>" class="btn btn-outline-primary">Detalji</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p class="text-muted">Nema dostupnih vozila.</p>
    <?php endif; ?>
</section>

<?php include 'includes/footer.php'; ?>
