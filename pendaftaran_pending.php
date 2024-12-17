<?php
session_start();
include 'config.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit();
}

// Proses Akui dan Tolak
if (isset($_GET['approve'])) {
    $id_mahasiswa = $_GET['approve'];
    $stmt = $pdo->prepare("UPDATE mahasiswa SET status = 'Approved' WHERE id_mahasiswa = ?");
    $stmt->execute([$id_mahasiswa]);
    header('Location: pendaftaran_pending.php');
    exit();
}

if (isset($_GET['reject'])) {
    $id_mahasiswa = $_GET['reject'];
    $stmt = $pdo->prepare("DELETE FROM mahasiswa WHERE id_mahasiswa = ?");
    $stmt->execute([$id_mahasiswa]);
    header('Location: pendaftaran_pending.php');
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
    <title>Pendaftaran Pending - Aplikasi Absensi</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
    <style>
        .card-custom {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .card-custom:hover {
            transform: translateY(-10px);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
        }
        .card-header-custom {
            background: linear-gradient(45deg, #6a11cb, #2575fc);
            color: white;
            font-size: 1.25rem;
            font-weight: bold;
        }
        .card-footer-custom {
            background-color: #f1f1f1;
            text-align: center;
        }
        .btn-custom {
            transition: background-color 0.3s ease;
        }
        .btn-custom:hover {
            background-color: #28a745;
            color: white;
        }
        .btn-danger-custom:hover {
            background-color: #dc3545;
            color: white;
        }
        .card-body-custom {
            text-align: center;
        }
    </style>
</head>
<body class="container mt-5">
    <header>
        <h1 class="text-center mb-4">Pendaftaran Pending Mahasiswa</h1>
    </header>

    <div class="row row-cols-1 row-cols-md-3 g-4">
        <?php foreach ($mahasiswaPendingList as $mahasiswa): ?>
            <div class="col">
                <!-- Card yang lebih menarik -->
                <div class="card card-custom h-100 shadow-sm border-light">
                    <div class="card-header card-header-custom">
                        <?php echo htmlspecialchars($mahasiswa['nama_mahasiswa']); ?>
                    </div>
                    <div class="card-body card-body-custom">
                        <h5 class="card-title text-primary"><?php echo htmlspecialchars($mahasiswa['npm']); ?></h5>
                        <p class="card-text">
                            <strong>Kelas:</strong> <?php echo htmlspecialchars($mahasiswa['kelas']); ?>
                        </p>
                    </div>
                    <div class="card-footer card-footer-custom">
                        <!-- Tombol Akui dan Tolak dengan Animasi -->
                        <button class="btn btn-success btn-custom" data-bs-toggle="modal" data-bs-target="#approveModal-<?php echo $mahasiswa['id_mahasiswa'];?>">
                            <i class="bi bi-check-circle"></i> Akui
                        </button>
                        <button class="btn btn-danger btn-danger-custom" data-bs-toggle="modal" data-bs-target="#rejectModal-<?php echo $mahasiswa['id_mahasiswa'];?>">
                            <i class="bi bi-x-circle"></i> Tolak
                        </button>
                    </div>
                </div>
            </div>

            <!-- Modal Konfirmasi Akui -->
            <div class="modal fade" id="approveModal-<?php echo $mahasiswa['id_mahasiswa']; ?>" tabindex="-1" aria-labelledby="approveModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="approveModalLabel">Konfirmasi Akui Pendaftaran</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            Apakah Anda yakin ingin mengakui pendaftaran mahasiswa <strong><?php echo htmlspecialchars($mahasiswa['nama_mahasiswa']); ?></strong>?
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <a href="?approve=<?php echo $mahasiswa['id_mahasiswa']; ?>" class="btn btn-success">Akui</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Konfirmasi Tolak -->
            <div class="modal fade" id="rejectModal-<?php echo $mahasiswa['id_mahasiswa']; ?>" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="rejectModalLabel">Konfirmasi Tolak Pendaftaran</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            Apakah Anda yakin ingin menolak pendaftaran mahasiswa <strong><?php echo htmlspecialchars($mahasiswa['nama_mahasiswa']); ?></strong>?
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <a href="?reject=<?php echo $mahasiswa['id_mahasiswa']; ?>" class="btn btn-danger">Tolak</a>
                        </div>
                    </div>
                </div>
            </div>

        <?php endforeach; ?>
    </div>

    <a href="admin_index.php" class="btn btn-secondary mt-3">Kembali</a>
</body>
</html>
