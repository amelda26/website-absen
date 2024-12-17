<?php
session_start();
include 'config.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit();
}

// Ambil data mahasiswa yang disetujui
$stmt = $pdo->query("SELECT * FROM mahasiswa WHERE status = 'Approved'");
$mahasiswaList = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Mahasiswa</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
</head>
<body class="container mt-5">
    <h1 class="text-center mb-4">Kelola Mahasiswa</h1>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID Mahasiswa</th>
                <th>NPM</th>
                <th>Nama</th>
                <th>Kelas</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($mahasiswaList as $mahasiswa): ?>
                <tr>
                    <td><?php echo $mahasiswa['id_mahasiswa']; ?></td>
                    <td><?php echo $mahasiswa['npm']; ?></td>
                    <td><?php echo $mahasiswa['nama_mahasiswa']; ?></td>
                    <td><?php echo $mahasiswa['kelas']; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <a href="admin_index.php" class="btn btn-secondary">Kembali</a>
</body>
</html>
