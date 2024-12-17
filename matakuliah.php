<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit();
}

include 'config.php';

// Proses Tambah Matakuliah
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add'])) {
    $nama_matakuliah = trim($_POST['nama_matakuliah']);
    $sks = intval($_POST['sks']); // Ambil SKS sebagai integer

    $stmt = $pdo->prepare("INSERT INTO matakuliah (nama_matakuliah, sks) VALUES (:nama_matakuliah, :sks)");
    $stmt->execute(['nama_matakuliah' => $nama_matakuliah, 'sks' => $sks]);
    header('Location: matakuliah.php'); // Redirect setelah menambah
    exit();
}

// Proses Hapus Matakuliah
if (isset($_GET['delete'])) {
    $id_matakuliah = $_GET['delete'];

    $stmt = $pdo->prepare("DELETE FROM matakuliah WHERE id_matakuliah = :id_matakuliah");
    $stmt->execute(['id_matakuliah' => $id_matakuliah]);
    header('Location: matakuliah.php'); // Redirect setelah hapus
    exit();
}

// Proses Edit Matakuliah
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit'])) {
    $id_matakuliah = $_POST['id_matakuliah'];
    $nama_matakuliah = trim($_POST['nama_matakuliah']);
    $sks = intval($_POST['sks']); // Ambil SKS sebagai integer

    $stmt = $pdo->prepare("UPDATE matakuliah SET nama_matakuliah = :nama_matakuliah, sks = :sks WHERE id_matakuliah = :id_matakuliah");
    $stmt->execute(['nama_matakuliah' => $nama_matakuliah, 'sks' => $sks, 'id_matakuliah' => $id_matakuliah]);
    header('Location: matakuliah.php'); // Redirect setelah edit
    exit();
}

// Ambil data matakuliah
$stmt = $pdo->query("SELECT * FROM matakuliah");
$matakuliahList = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Matakuliah</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">Kelola Matakuliah</h1>

        <form method="POST" class="mb-4">
            <div class="mb-3">
                <label for="nama_matakuliah" class="form-label">Nama Matakuliah:</label>
                <input type="text" name="nama_matakuliah" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="sks" class="form-label">SKS:</label>
                <input type="number" name="sks" class="form-control" min="1" required>
            </div>
            <button type="submit" name="add" class="btn btn-primary">Tambah Matakuliah</button>
        </form>

        <h2 class="mb-4">Daftar Matakuliah</h2>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nama Matakuliah</th>
                    <th>SKS</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($matakuliahList as $matkul): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($matkul['id_matakuliah']); ?></td>
                        <td><?php echo htmlspecialchars($matkul['nama_matakuliah']); ?></td>
                        <td><?php echo htmlspecialchars($matkul['sks']); ?></td>
                        <td>
                            <a href="matakuliah.php?edit=<?php echo $matkul['id_matakuliah']; ?>" class="btn btn-warning btn-sm">Edit</a>
                            <a href="matakuliah.php?delete=<?php echo $matkul['id_matakuliah']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus?');">Hapus</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <?php if (isset($_GET['edit'])): ?>
            <?php
            $id_edit = $_GET['edit'];
            $stmt = $pdo->prepare("SELECT * FROM matakuliah WHERE id_matakuliah = :id_matakuliah");
            $stmt->execute(['id_matakuliah' => $id_edit]);
            $matkulEdit = $stmt->fetch(PDO::FETCH_ASSOC);
            ?>
            <h2 class="mt-4">Edit Matakuliah</h2>
            <form method="POST" class="mb-4">
                <input type="hidden" name="id_matakuliah" value="<?php echo htmlspecialchars($matkulEdit['id_matakuliah']); ?>">
                <div class="mb-3">
                    <label for="nama_matakuliah" class="form-label">Nama Matakuliah:</label>
                    <input type="text" name="nama_matakuliah" class="form-control" value="<?php echo htmlspecialchars($matkulEdit['nama_matakuliah']); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="sks" class="form-label">SKS:</label>
                    <input type="number" name="sks" class="form-control" value="<?php echo htmlspecialchars($matkulEdit['sks']); ?>" min="1" required>
                </div>
                <button type="submit" name="edit" class="btn btn-success">Simpan Perubahan</button>
            </form>
        <?php endif; ?>

        <a href="admin_index.php" class="btn btn-secondary">Kembali</a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
