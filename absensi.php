<?php
session_start();
include 'config.php';

// Cek apakah admin sudah login
$isAdmin = isset($_SESSION['admin_logged_in']);

// Jika mahasiswa login
if (!$isAdmin && !isset($_SESSION['mahasiswa_logged_in'])) {
    header('Location: login.php');
    exit();
}

// Ambil daftar jadwal dari database
$jadwalStmt = $pdo->query("SELECT j.*, m.nama_matakuliah 
                            FROM jadwal j 
                            JOIN matakuliah m ON j.id_matakuliah = m.id_matakuliah");
$jadwalList = $jadwalStmt->fetchAll(PDO::FETCH_ASSOC);

// Proses absensi jika admin mengabsen mahasiswa
if ($isAdmin && $_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id_mahasiswa'], $_POST['id_jadwal'])) {
    $id_mahasiswa = $_POST['id_mahasiswa'];
    $id_jadwal = $_POST['id_jadwal'];
    $today = date('Y-m-d');

    // Cek apakah mahasiswa sudah absen untuk jadwal ini
    $stmt = $pdo->prepare("SELECT * FROM absensi WHERE id_mahasiswa = :id_mahasiswa AND id_jadwal = :id_jadwal AND DATE(tanggal_absensi) = :today");
    $stmt->execute(['id_mahasiswa' => $id_mahasiswa, 'id_jadwal' => $id_jadwal, 'today' => $today]);
    $absenExists = $stmt->fetch();

    // Ambil tanggal dari jadwal
    $stmt = $pdo->prepare("SELECT tanggal_jadwal FROM jadwal WHERE id_jadwal = :id_jadwal");
    $stmt->execute(['id_jadwal' => $id_jadwal]);
    $jadwal = $stmt->fetch();

    if ($absenExists) {
        echo "<div class='alert alert-warning'>Mahasiswa ini sudah absen untuk jadwal ini.</div>";
    } elseif ($jadwal['tanggal_jadwal'] !== $today) {
        echo "<div class='alert alert-danger'>Absensi sudah tidak berlaku untuk tanggal ini.</div>";
    } else {
        // Tambahkan logika untuk menyimpan absensi
        try {
            $stmt = $pdo->prepare("INSERT INTO absensi (id_mahasiswa, id_jadwal, jam_absen, tanggal_absensi) VALUES (:id_mahasiswa, :id_jadwal, NOW(), :today)");
            $stmt->execute(['id_mahasiswa' => $id_mahasiswa, 'id_jadwal' => $id_jadwal, 'today' => $today]);
            echo "<div class='alert alert-success'>Absensi berhasil dicatat.</div>";
        } catch (PDOException $e) {
            echo "<div class='alert alert-danger'>Gagal mencatat absensi: " . htmlspecialchars($e->getMessage()) . "</div>";
        }
    }
}

// Ambil daftar mahasiswa yang sudah disetujui
$mahasiswaList = [];
if ($isAdmin) {
    $mahasiswaStmt = $pdo->query("SELECT * FROM mahasiswa WHERE status = 'approved'"); // Hanya mahasiswa yang disetujui
    $mahasiswaList = $mahasiswaStmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Absensi Mahasiswa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container my-5">
        <h2>Absensi Mahasiswa</h2>
        <a href="admin_index.php" class="btn btn-secondary mb-3">Kembali ke Dashboard Admin</a>

        <?php if ($isAdmin): ?>
            <form method="POST">
                <div class="mb-3">
                    <label for="id_mahasiswa" class="form-label">Pilih Mahasiswa</label>
                    <select name="id_mahasiswa" id="id_mahasiswa" class="form-select" required>
                        <option value="">-- Pilih Mahasiswa --</option>
                        <?php foreach ($mahasiswaList as $mahasiswa): ?>
                            <option value="<?php echo htmlspecialchars($mahasiswa['id_mahasiswa']); ?>">
                                <?php echo htmlspecialchars($mahasiswa['nama_mahasiswa']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="id_jadwal" class="form-label">Pilih Jadwal</label>
                    <select name="id_jadwal" id="id_jadwal" class="form-select" required>
                        <option value="">-- Pilih Jadwal --</option>
                        <?php foreach ($jadwalList as $jadwal): ?>
                            <option value="<?php echo htmlspecialchars($jadwal['id_jadwal']); ?>">
                                <?php echo htmlspecialchars($jadwal['nama_matakuliah']); ?> - <?php echo htmlspecialchars($jadwal['jam_mulai']) . ' - ' . htmlspecialchars($jadwal['jam_selesai']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Catat Absensi</button>
            </form>
        <?php else: ?>
            <h3>Anda belum terdaftar untuk melakukan absensi.</h3>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
