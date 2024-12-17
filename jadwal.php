<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit();
}

include 'config.php';

// Proses Tambah Jadwal
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add'])) {
        $id_matakuliah = $_POST['id_matakuliah'];
        $jam_mulai = $_POST['jam_mulai'];
        $jam_selesai = $_POST['jam_selesai'];
        $ruangan = $_POST['ruangan'];
        $tanggal_jadwal = $_POST['tanggal_jadwal'];

        // Validasi jam dan tanggal
        $currentDate = date('Y-m-d');
        if ($tanggal_jadwal < $currentDate) {
            echo "<script>alert('Tanggal tidak boleh sebelum hari ini.');</script>";
        } else {
            // Cek jika ada jadwal lain yang bertabrakan (ruangan dan waktu)
            $stmt = $pdo->prepare("SELECT * FROM jadwal WHERE tanggal_jadwal = ? AND ruangan = ? AND (
                                    (jam_mulai BETWEEN ? AND ?) OR 
                                    (jam_selesai BETWEEN ? AND ?) OR 
                                    (? BETWEEN jam_mulai AND jam_selesai) OR 
                                    (? BETWEEN jam_mulai AND jam_selesai))");
            $stmt->execute([$tanggal_jadwal, $ruangan, $jam_mulai, $jam_selesai, $jam_mulai, $jam_selesai, $jam_mulai, $jam_selesai]);
            $jadwalConflict = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($jadwalConflict) {
                echo "<script>alert('Jadwal bertabrakan dengan jadwal lain pada ruangan dan waktu yang sama.');</script>";
            } else {
                // Jika tidak ada tabrakan, simpan jadwal baru
                $stmt = $pdo->prepare("INSERT INTO jadwal (id_matakuliah, jam_mulai, jam_selesai, ruangan, tanggal_jadwal) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$id_matakuliah, $jam_mulai, $jam_selesai, $ruangan, $tanggal_jadwal]);
                header('Location: jadwal.php');
                exit();
            }
        }
    }

    if (isset($_POST['edit'])) {
        $id_jadwal = $_POST['id_jadwal'];
        $id_matakuliah = $_POST['id_matakuliah'];
        $jam_mulai = $_POST['jam_mulai'];
        $jam_selesai = $_POST['jam_selesai'];
        $ruangan = $_POST['ruangan'];
        $tanggal_jadwal = $_POST['tanggal_jadwal'];

        // Validasi jam dan tanggal
        $currentDate = date('Y-m-d');
        if ($tanggal_jadwal < $currentDate) {
            echo "<script>alert('Tanggal tidak boleh sebelum hari ini.');</script>";
        } else {
            // Cek jika ada jadwal lain yang bertabrakan (ruangan dan waktu)
            $stmt = $pdo->prepare("SELECT * FROM jadwal WHERE id_jadwal != ? AND tanggal_jadwal = ? AND ruangan = ? AND (
                                    (jam_mulai BETWEEN ? AND ?) OR 
                                    (jam_selesai BETWEEN ? AND ?) OR 
                                    (? BETWEEN jam_mulai AND jam_selesai) OR 
                                    (? BETWEEN jam_mulai AND jam_selesai))");
            $stmt->execute([$id_jadwal, $tanggal_jadwal, $ruangan, $jam_mulai, $jam_selesai, $jam_mulai, $jam_selesai, $jam_mulai, $jam_selesai]);
            $jadwalConflict = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($jadwalConflict) {
                echo "<script>alert('Jadwal bertabrakan dengan jadwal lain pada ruangan dan waktu yang sama.');</script>";
            } else {
                // Jika tidak ada tabrakan, simpan perubahan jadwal
                $stmt = $pdo->prepare("UPDATE jadwal SET id_matakuliah = ?, jam_mulai = ?, jam_selesai = ?, ruangan = ?, tanggal_jadwal = ? WHERE id_jadwal = ?");
                $stmt->execute([$id_matakuliah, $jam_mulai, $jam_selesai, $ruangan, $tanggal_jadwal, $id_jadwal]);
                header('Location: jadwal.php');
                exit();
            }
        }
    }
}

// Proses Hapus Jadwal
if (isset($_GET['delete'])) {
    $id_jadwal = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM jadwal WHERE id_jadwal = ?");
    $stmt->execute([$id_jadwal]);
    header('Location: jadwal.php');
    exit();
}

// Ambil data jadwal
$stmt = $pdo->query("SELECT j.*, m.nama_matakuliah FROM jadwal j JOIN matakuliah m ON j.id_matakuliah = m.id_matakuliah");
$jadwalList = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Ambil data matakuliah untuk dropdown
$matakuliahStmt = $pdo->query("SELECT * FROM matakuliah");
$matakuliahList = $matakuliahStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Kelola Jadwal</title>
</head>
<body>
    <div class="container mt-5">
        <h1>Kelola Jadwal</h1>

        <!-- Form Tambah Jadwal -->
        <?php if (!isset($_GET['edit'])): ?>
        <form method="POST" class="mb-4">
            <div class="mb-3">
                <label for="id_matakuliah" class="form-label">Matakuliah:</label>
                <select name="id_matakuliah" class="form-select" required>
                    <?php foreach ($matakuliahList as $matkul): ?>
                        <option value="<?php echo htmlspecialchars($matkul['id_matakuliah']); ?>"><?php echo htmlspecialchars($matkul['nama_matakuliah']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="jam_mulai" class="form-label">Jam Mulai:</label>
                <input type="time" name="jam_mulai" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="jam_selesai" class="form-label">Jam Selesai:</label>
                <input type="time" name="jam_selesai" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="ruangan" class="form-label">Ruangan:</label>
                <input type="text" name="ruangan" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="tanggal_jadwal" class="form-label">Tanggal:</label>
                <input type="date" name="tanggal_jadwal" class="form-control" required>
            </div>

            <button type="submit" name="add" class="btn btn-primary">Tambah Jadwal</button>
        </form>
        <?php endif; ?>

        <!-- Form Edit Jadwal -->
        <?php if (isset($_GET['edit'])): ?>
            <?php
            $id_edit = $_GET['edit'];
            $stmt = $pdo->prepare("SELECT * FROM jadwal WHERE id_jadwal = ?");
            $stmt->execute([$id_edit]);
            $jadwalEdit = $stmt->fetch(PDO::FETCH_ASSOC);
            ?>
            <h2>Edit Jadwal</h2>
            <form method="POST" class="mb-4">
                <input type="hidden" name="id_jadwal" value="<?php echo htmlspecialchars($jadwalEdit['id_jadwal']); ?>">
                <div class="mb-3">
                    <label for="id_matakuliah" class="form-label">Matakuliah:</label>
                    <select name="id_matakuliah" class="form-select" required>
                        <?php foreach ($matakuliahList as $matkul): ?>
                            <option value="<?php echo htmlspecialchars($matkul['id_matakuliah']); ?>" <?php if ($matkul['id_matakuliah'] == $jadwalEdit['id_matakuliah']) echo 'selected'; ?>>
                                <?php echo htmlspecialchars($matkul['nama_matakuliah']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="jam_mulai" class="form-label">Jam Mulai:</label>
                    <input type="time" name="jam_mulai" class="form-control" value="<?php echo htmlspecialchars($jadwalEdit['jam_mulai']); ?>" required>
                </div>

                <div class="mb-3">
                    <label for="jam_selesai" class="form-label">Jam Selesai:</label>
                    <input type="time" name="jam_selesai" class="form-control" value="<?php echo htmlspecialchars($jadwalEdit['jam_selesai']); ?>" required>
                </div>

                <div class="mb-3">
                    <label for="ruangan" class="form-label">Ruangan:</label>
                    <input type="text" name="ruangan" class="form-control" value="<?php echo htmlspecialchars($jadwalEdit['ruangan']); ?>" required>
                </div>

                <div class="mb-3">
                    <label for="tanggal_jadwal" class="form-label">Tanggal:</label>
                    <input type="date" name="tanggal_jadwal" class="form-control" value="<?php echo htmlspecialchars($jadwalEdit['tanggal_jadwal']); ?>" required>
                </div>

                <button type="submit" name="edit" class="btn btn-primary">Simpan Perubahan</button>
            </form>
        <?php endif; ?>

        <h2>Daftar Jadwal</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Matakuliah</th>
                    <th>Jam Mulai</th>
                    <th>Jam Selesai</th>
                    <th>Ruangan</th>
                    <th>Tanggal</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($jadwalList as $jdwl): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($jdwl['id_jadwal']); ?></td>
                        <td><?php echo htmlspecialchars($jdwl['nama_matakuliah']); ?></td>
                        <td><?php echo htmlspecialchars($jdwl['jam_mulai']); ?></td>
                        <td><?php echo htmlspecialchars($jdwl['jam_selesai']); ?></td>
                        <td><?php echo htmlspecialchars($jdwl['ruangan']); ?></td>
                        <td><?php echo htmlspecialchars($jdwl['tanggal_jadwal']); ?></td>
                        <td>
                            <a href="jadwal.php?edit=<?php echo htmlspecialchars($jdwl['id_jadwal']); ?>" class="btn btn-warning btn-sm">Edit</a>
                            <a href="jadwal.php?delete=<?php echo htmlspecialchars($jdwl['id_jadwal']); ?>" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus?');">Hapus</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <a href="admin_index.php" class="btn btn-secondary">Kembali</a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
