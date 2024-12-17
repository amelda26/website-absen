<?php
session_start();
include 'config.php';

// Proses Registrasi Mahasiswa
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $npm = $_POST['npm'];
    $nama = $_POST['nama'];
    $kelas = $_POST['kelas'];

    // Cek apakah NPM sudah ada
    $stmt = $pdo->prepare("SELECT * FROM mahasiswa WHERE npm = ?");
    $stmt->execute([$npm]);
    
    if ($stmt->rowCount() > 0) {
        // NPM sudah ada
        $error = "NPM sudah terdaftar. Silakan gunakan NPM lain.";
    } else {
        // Masukkan data baru dengan status 'Pending'
        $stmt = $pdo->prepare("INSERT INTO mahasiswa (npm, nama_mahasiswa, kelas, status) VALUES (?, ?, ?, 'Pending')");
        $stmt->execute([$npm, $nama, $kelas]);
        $success = "Pendaftaran berhasil. Tunggu persetujuan admin.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Registrasi Mahasiswa</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
</head>
<body class="container mt-5">
    <h1 class="text-center mb-4">Registrasi Mahasiswa</h1>
    <?php if (isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
    <?php if (isset($success)) echo "<div class='alert alert-success'>$success</div>"; ?>
    <form action="" method="post">
        <div class="mb-3">
            <label for="npm" class="form-label">NPM:</label>
            <input type="text" name="npm" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="nama" class="form-label">Nama:</label>
            <input type="text" name="nama" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="kelas" class="form-label">Kelas:</label>
            <input type="text" name="kelas" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Daftar</button>
    </form>
    <a href="admin_index.php" class="btn btn-secondary mt-3">Kembali</a>
</body>
</html>
