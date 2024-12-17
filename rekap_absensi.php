<?php
session_start();
include 'config.php';

// Cek apakah admin sudah login
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit();
}

// Ambil data rekap absensi, urutkan berdasarkan tanggal jadwal (tanggal yang lebih dulu di atas)
$stmt = $pdo->query("
    SELECT 
        a.id_absensi, 
        m.npm, 
        m.nama_mahasiswa, 
        mk.nama_matakuliah, 
        j.jam_mulai, 
        j.jam_selesai, 
        j.tanggal_jadwal, 
        a.jam_absen
    FROM 
        absensi a 
    JOIN 
        mahasiswa m ON a.id_mahasiswa = m.id_mahasiswa 
    JOIN 
        jadwal j ON a.id_jadwal = j.id_jadwal 
    JOIN 
        matakuliah mk ON j.id_matakuliah = mk.id_matakuliah
    ORDER BY 
        j.tanggal_jadwal ASC
");

$rekapAbsensiList = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekap Absensi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container my-5">
        <h1 class="mb-4">Rekap Absensi</h1>

        <a href="admin_index.php" class="btn btn-secondary mb-3">Kembali</a>

        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>ID Absensi</th>
                    <th>NPM</th>
                    <th>Nama Mahasiswa</th>
                    <th>Matakuliah</th>
                    <th>Jam Mulai</th>
                    <th>Jam Selesai</th>
                    <th>Tanggal</th>
                    <th>Status Kehadiran</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rekapAbsensiList as $rekap): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($rekap['id_absensi']); ?></td>
                        <td><?php echo htmlspecialchars($rekap['npm']); ?></td>
                        <td><?php echo htmlspecialchars($rekap['nama_mahasiswa']); ?></td>
                        <td><?php echo htmlspecialchars($rekap['nama_matakuliah']); ?></td>
                        <td><?php echo date('H:i', strtotime($rekap['jam_mulai'])); ?></td>
                        <td><?php echo date('H:i', strtotime($rekap['jam_selesai'])); ?></td>
                        <td><?php echo date('d-m-Y', strtotime($rekap['tanggal_jadwal'])); ?></td>
                        <td>
                            <?php
                            $jamAbsen = strtotime($rekap['jam_absen']);
                            $jamMulai = strtotime($rekap['jam_mulai']);
                            $jamSelesai = strtotime($rekap['jam_selesai']);

                            // Tentukan status kehadiran
                            if (empty($rekap['jam_absen'])) {
                                echo 'Tidak Hadir';
                            } else {
                                // Hitung selisih waktu antara jam absen dan jam mulai
                                $selisihMenit = ($jamAbsen - $jamMulai) / 60;  // dalam menit
                                
                                if ($selisihMenit < 0) {
                                    echo 'Tepat Waktu';  // Absen sebelum jam mulai
                                } elseif ($selisihMenit <= 20) {
                                    echo 'Tepat Waktu';  // Absen tepat waktu atau terlambat maksimal 20 menit
                                } elseif ($selisihMenit <= 60) {
                                    echo 'Terlambat';  // Absen terlambat dalam 60 menit
                                } else {
                                    echo 'Tidak Hadir';  // Absen lebih dari 1 jam setelah jadwal selesai
                                }
                            }
                            ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
