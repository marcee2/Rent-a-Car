<?php
require_once 'includes/db_config.php';
include 'includes/header.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo "<div class='container py-5'><p class='text-danger'>Pristup dozvoljen samo administratorima.</p></div>";
    include 'includes/footer.php';
    exit;
}

$message = "";

$uploadDir = 'images/';
if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $pdo->prepare("DELETE FROM vehicles WHERE id = ?")->execute([$id]);
    $message = "<p class='text-success'>Vozilo je obrisano.</p>";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['vehicle_id'] ?? null;
    $name = trim($_POST['name']);
    $model = trim($_POST['model']);
    $year = (int)$_POST['year'];
    $fuel = $_POST['fuel_type'];
    $seats = (int)$_POST['seats'];
    $gearbox = $_POST['transmission'];
    $popularity = (int)$_POST['popularity'];
    $price_per_day = (float)$_POST['price_per_day'];

    $image_path = $_POST['existing_image'] ?? '';
    if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
        $tmpName = $_FILES['image_file']['tmp_name'];
        $ext = strtolower(pathinfo($_FILES['image_file']['name'], PATHINFO_EXTENSION));

        if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
            $newName = uniqid('car_', true) . '.' . $ext;
            $relativePath = $uploadDir . $newName;
            $absolutePath = __DIR__ . '/' . $relativePath;
            if (move_uploaded_file($tmpName, $absolutePath)) {
                $image_path = $relativePath;
            }
        }
    }

    if ($name && $model && $year && $fuel && $seats && $gearbox && $image_path && $price_per_day) {
        if ($id) {
            $stmt = $pdo->prepare("UPDATE vehicles SET name=?, model=?, year=?, fuel_type=?, seats=?, gearbox=?, popularity=?, image=?, price_per_day=? WHERE id=?");
            $stmt->execute([$name, $model, $year, $fuel, $seats, $gearbox, $popularity, $image_path, $price_per_day, $id]);
            $message = "<p class='text-success'>Vozilo je izmenjeno.</p>";
        } else {
            $stmt = $pdo->prepare("INSERT INTO vehicles (name, model, year, fuel_type, seats, gearbox, popularity, image, price_per_day) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$name, $model, $year, $fuel, $seats, $gearbox, $popularity, $image_path, $price_per_day]);
            $message = "<p class='text-success'>Dodato novo vozilo.</p>";
        }
    } else {
        $message = "<p class='text-danger'>Popunite sva polja.</p>";
    }
}

$edit_vehicle = null;
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM vehicles WHERE id = ?");
    $stmt->execute([$_GET['edit']]);
    $edit_vehicle = $stmt->fetch();
}

$search = $_GET['search'] ?? '';
$sort = $_GET['sort'] ?? 'id';
$sort_order = in_array($sort, ['name','model','year','popularity']) ? $sort : 'id';

$stmt = $pdo->prepare("SELECT * FROM vehicles WHERE name LIKE ? OR model LIKE ? ORDER BY $sort_order ASC");
$stmt->execute(['%' . $search . '%', '%' . $search . '%']);
$vehicles = $stmt->fetchAll();
?>

<section class="container py-5">
    <h2>Upravljanje vozilima</h2>
    <?php if ($message) echo "<div class='my-3'>$message</div>"; ?>

    <form class="row mb-4" method="get">
        <div class="col-md-4">
            <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" class="form-control" placeholder="Pretraga po nazivu ili modelu">
        </div>
        <div class="col-md-3">
            <select name="sort" class="form-select">
                <option value="id">Sortiraj po...</option>
                <option value="name" <?php if ($sort == 'name') echo 'selected'; ?>>Naziv</option>
                <option value="year" <?php if ($sort == 'year') echo 'selected'; ?>>Godina</option>
                <option value="popularity" <?php if ($sort == 'popularity') echo 'selected'; ?>>Popularnost</option>
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary">Primeni</button>
        </div>
    </form>

    <div class="card mb-5">
        <div class="card-header"><?php echo $edit_vehicle ? "Izmena vozila" : "Dodaj novo vozilo"; ?></div>
        <div class="card-body">
            <form method="post" enctype="multipart/form-data" class="row g-3">
                <?php if ($edit_vehicle): ?>
                    <input type="hidden" name="vehicle_id" value="<?php echo $edit_vehicle['id']; ?>">
                    <input type="hidden" name="existing_image" value="<?php echo $edit_vehicle['image']; ?>">
                <?php endif; ?>
                <div class="col-md-4">
                    <label class="form-label">Naziv</label>
                    <input type="text" name="name" value="<?php echo $edit_vehicle['name'] ?? ''; ?>" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Model</label>
                    <input type="text" name="model" value="<?php echo $edit_vehicle['model'] ?? ''; ?>" class="form-control" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Godina</label>
                    <input type="number" name="year" value="<?php echo $edit_vehicle['year'] ?? ''; ?>" class="form-control" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Sedista</label>
                    <input type="number" name="seats" value="<?php echo $edit_vehicle['seats'] ?? ''; ?>" class="form-control" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Gorivo</label>
                    <select name="fuel_type" class="form-select" required>
                        <option value="">--</option>
                        <?php foreach (['petrol','diesel','electric'] as $ft): ?>
                            <option value="<?php echo $ft; ?>" <?php if (($edit_vehicle['fuel_type'] ?? '') === $ft) echo 'selected'; ?>><?php echo ucfirst($ft); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Menjač</label>
                    <select name="transmission" class="form-select" required>
                        <option value="">--</option>
                        <?php foreach (['manual','automatic'] as $gb): ?>
                            <option value="<?php echo $gb; ?>" <?php if (($edit_vehicle['gearbox'] ?? '') === $gb) echo 'selected'; ?>><?php echo ucfirst($gb); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Popularnost</label>
                    <input type="number" name="popularity" value="<?php echo $edit_vehicle['popularity'] ?? 0; ?>" class="form-control" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Cena (€/dan)</label>
                    <input type="number" step="0.01" name="price_per_day" value="<?php echo $edit_vehicle['price_per_day'] ?? ''; ?>" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Slika vozila</label>
                    <input type="file" name="image_file" class="form-control" <?php echo $edit_vehicle ? '' : 'required'; ?>>
                    <?php if ($edit_vehicle && !empty($edit_vehicle['image'])): ?>
                        <p class="mt-2">Postojeća slika:<br><img src="<?php echo $edit_vehicle['image']; ?>" width="100"></p>
                    <?php endif; ?>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-success"><?php echo $edit_vehicle ? "Sačuvaj" : "Dodaj vozilo"; ?></button>
                    <?php if ($edit_vehicle): ?>
                        <a href="admin_vehicles.php" class="btn btn-secondary ms-2">Otkaži</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>

    <table class="table table-bordered">
        <thead class="table-light">
        <tr>
            <th>ID</th>
            <th>Slika</th>
            <th>Vozilo</th>
            <th>Godina</th>
            <th>Gorivo</th>
            <th>Menjač</th>
            <th>Popularnost</th>
            <th>Akcija</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($vehicles as $v): ?>
            <tr>
                <td><?php echo $v['id']; ?></td>
                <td><img src="<?php echo htmlspecialchars($v['image']); ?>" width="100"></td>
                <td><?php echo $v['name'] . ' ' . $v['model']; ?></td>
                <td><?php echo $v['year']; ?></td>
                <td><?php echo ucfirst($v['fuel_type']); ?></td>
                <td><?php echo ucfirst($v['gearbox']); ?></td>
                <td><?php echo $v['popularity']; ?></td>
                <td>
                    <a href="admin_vehicles.php?edit=<?php echo $v['id']; ?>" class="btn btn-sm btn-outline-primary">Izmeni</a>
                    <a href="admin_vehicles.php?delete=<?php echo $v['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Obrisati vozilo?');">Obriši</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</section>

<?php include 'includes/footer.php'; ?>
