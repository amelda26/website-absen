<?php
session_start();
include 'config.php';

// Cek apakah admin sudah login
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit();
}

// Ambil data mahasiswa yang pending
$stmt = $pdo->query("SELECT * FROM mahasiswa WHERE status = 'Pending'");
$mahasiswaPendingList = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran Pending Mahasiswa - Aplikasi Absensi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet"> <!-- FontAwesome for icons -->
    <style>
        .container {
            max-width: 1200px;
        }
        .card-body {
            padding: 20px;
        }
        .search-bar {
            margin-bottom: 20px;
            max-width: 400px;
            margin-left: auto;
            margin-right: auto;
        }
        .pending-status {
            font-weight: bold;
        }
        .badge-pending {
            background-color: #f0ad4e;
            color: white;
        }
        .card-footer {
            background-color: #f9f9f9;
        }
        /* Styling untuk card */
        .card-custom {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .card-custom:hover {
            transform: translateY(-10px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }
        .card-header-custom {
            background: linear-gradient(45deg, #1e90ff, #00bfff);
            color: white;
            font-size: 1.25rem;
            font-weight: bold;
        }
        .search-bar input {
            padding: 10px;
            font-size: 1rem;
        }
    </style>
</head>
<body>
    <header class="bg-dark text-white text-center py-3">
        <h1>Selamat Datang di Aplikasi Absensi</h1>
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container-fluid">
                <a class="navbar-brand" href="#">Aplikasi Absensi</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link" href="register.php">Registrasi Mahasiswa</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="pendaftaran_pending.php">Pendaftaran Pending</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="mahasiswa.php">Kelola Mahasiswa</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="absensi.php">Kelola Absensi</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="matakuliah.php">Kelola Matakuliah</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="jadwal.php">Kelola Jadwal</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="rekap_absensi.php">Rekap Absensi</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="admin.php">Kelola Admin</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="logout.php">Logout</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <main class="container my-5">
        <section>
            <h2>Pendaftaran Pending Mahasiswa</h2>

            <!-- Search Bar -->
            <div class="search-bar">
                <input type="text" class="form-control" id="searchInput" placeholder="Cari Mahasiswa..." onkeyup="searchTable()">
            </div>

            <!-- Tampilkan Mahasiswa Pending dalam Card -->
            <div class="row">
                <?php if (count($mahasiswaPendingList) > 0): ?>
                    <?php foreach ($mahasiswaPendingList as $mahasiswa): ?>
                        <div class="col-md-4 mb-3">
                            <div class="card card-custom shadow-sm border-light">
                                <div class="card-header card-header-custom">
                                    <?php echo htmlspecialchars($mahasiswa['nama_mahasiswa']); ?>
                                </div>
                                <div class="card-body">
                                    <p><strong>NPM: </strong><?php echo htmlspecialchars($mahasiswa['npm']); ?></p>
                                    <p><strong>Kelas: </strong><?php echo htmlspecialchars($mahasiswa['kelas']); ?></p>
                                    <p><strong>Status: </strong>
                                        <span class="badge badge-pending"><?php echo htmlspecialchars($mahasiswa['status']); ?></span>
                                    </p>
                                </div>
                                <!-- Hapus bagian footer yang sebelumnya berisi tombol "Tolak" -->
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="alert alert-warning" role="alert">
                        Tidak ada pendaftaran pending.
                    </div>
                <?php endif; ?>
            </div>

        </section>
    </main>

    <footer class="bg-dark text-white text-center py-3">
        <p>&copy; 2024 Aplikasi Absensi. All rights reserved.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Fungsi untuk mencari tabel berdasarkan input pengguna
        function searchTable() {
            const filter = document.getElementById('searchInput').value.toUpperCase();
            const cards = document.querySelectorAll('.card');

            cards.forEach(function(card) {
                const title = card.querySelector('.card-title').textContent;
                const npm = card.querySelector('.card-subtitle').textContent;
                
                if (title.toUpperCase().indexOf(filter) > -1 || npm.toUpperCase().indexOf(filter) > -1) {
                    card.style.display = '';
                } else {
                    card.style.display = 'none';
                }
            });
        }
    </script>
</body>
</html>
