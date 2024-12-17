<?php
session_start();
include 'config.php';

// Cek apakah mahasiswa sudah login 
if (!isset($_SESSION['mahasiswa_logged_in'])) {
    header('Location: login_mahasiswa.php');
    exit();
}

// Cek ID jadwal dalam session
if (!isset($_SESSION['id_jadwal'])) {
    die("ID jadwal tidak ditemukan dalam session.");
}

// Ambil ID jadwal dari session
$id_jadwal = $_SESSION['id_jadwal'];
$npm = $_SESSION['npm'];

// Ambil informasi jadwal dan nama mata kuliah
$stmt = $pdo->prepare("
    SELECT j.*, m.nama_matakuliah 
    FROM jadwal j 
    JOIN matakuliah m ON j.id_matakuliah = m.id_matakuliah 
    WHERE j.id_jadwal = :id_jadwal
");
$stmt->execute(['id_jadwal' => $id_jadwal]);
$jadwal = $stmt->fetch();

// Cek apakah jadwal ditemukan
if (!$jadwal) {
    die("Jadwal tidak ditemukan.");
}

// Ambil ID mahasiswa berdasarkan NPM
$stmt = $pdo->prepare("SELECT id_mahasiswa FROM mahasiswa WHERE npm = :npm");
$stmt->execute(['npm' => $npm]);
$id_mahasiswa = $stmt->fetchColumn();

// Cek apakah mahasiswa sudah absen hari ini
$stmt = $pdo->prepare("
    SELECT * FROM absensi 
    WHERE id_mahasiswa = :id_mahasiswa 
    AND id_jadwal = :id_jadwal 
    AND tanggal_absensi = CURDATE()
");
$stmt->execute(['id_mahasiswa' => $id_mahasiswa, 'id_jadwal' => $id_jadwal]);
$absenHariIni = $stmt->fetch();

// Ambil status absen
$absenStatus = $absenHariIni ? true : false;

// Periksa tanggal
$tanggalJadwal = date('Y-m-d', strtotime($jadwal['tanggal_jadwal']));
$tanggalSekarang = date('Y-m-d');
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Dashboard Mahasiswa</title>
    <style>
        body { background-color: #f8f9fa; }
        .card { margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="text-center my-4">Dashboard Mahasiswa</h2>
        
        <div class="card">
            <div class="card-header">
                <h5>Jadwal Kuliah</h5>
            </div>
            <div class="card-body">
                <p><strong>Tanggal:</strong> <?php echo date('d-m-Y', strtotime($jadwal['tanggal_jadwal'])); ?></p>
                <p><strong>Mata Kuliah:</strong> <?php echo htmlspecialchars($jadwal['nama_matakuliah']); ?></p>
                <p><strong>Jam Mulai:</strong> <?php echo htmlspecialchars($jadwal['jam_mulai']); ?></p>
                <p><strong>Jam Selesai:</strong> <?php echo htmlspecialchars($jadwal['jam_selesai']); ?></p>
                <p><strong>Ruangan:</strong> <?php echo htmlspecialchars($jadwal['ruangan']); ?></p>

                <?php if ($tanggalSekarang !== $tanggalJadwal): ?>
                    <div class="alert alert-danger">Tidak bisa melakukan absensi. Tanggal tidak sesuai dengan jadwal.</div>
                <?php else: ?>
                    <?php if (!$absenStatus): ?>
                        <button id="tombolAbsensi" class="btn btn-primary">Absen</button>
                    <?php else: ?>
                        <div class="alert alert-warning">Anda sudah absen hari ini untuk jadwal ini.</div>
                    <?php endif; ?>
                <?php endif; ?>

                <div id="keterangan" class="mt-3" style="display: none;"></div>
            </div>
        </div>

        <a href="logout_mahasiswa.php" class="btn btn-danger mt-4">Logout</a>
    </div>

    <script>
        const jamMulai = new Date("<?php echo date('Y-m-d') . ' ' . htmlspecialchars($jadwal['jam_mulai']); ?>");
        const sekarang = new Date();
        const satuJamSebelum = new Date(jamMulai.getTime() - 60 * 60 * 1000); // 1 jam sebelum kuliah
        const setengahJamSetelah = new Date(jamMulai.getTime() + 30 * 60 * 1000); // 30 menit setelah kuliah

        // Tampilkan tombol absensi jika waktu saat ini sesuai dan tanggal sesuai
        if (sekarang >= satuJamSebelum && sekarang <= setengahJamSetelah && "<?php echo $tanggalSekarang; ?>" === "<?php echo $tanggalJadwal; ?>") {
            document.getElementById('tombolAbsensi').style.display = 'block';
        } else {
            document.getElementById('tombolAbsensi').style.display = 'none'; // Menyembunyikan tombol jika tidak dalam rentang waktu
        }

        document.getElementById('tombolAbsensi').addEventListener('click', function() {
            const now = new Date();
            const jamAbsen = now.getHours() + ':' + (now.getMinutes() < 10 ? '0' : '') + now.getMinutes();
            let keterangan;

            // Hitung selisih waktu
            const selisihMenit = (now - jamMulai) / (1000 * 60); // dalam menit

            // Tentukan keterangan berdasarkan selisih waktu
            if (selisihMenit < 0) {
                keterangan = 'Tepat waktu. Anda hadir.';
            } else if (selisihMenit <= 20) {
                keterangan = 'Tepat waktu. Anda hadir.';
            } else if (selisihMenit <= 60) {
                keterangan = 'Terlambat. Anda hadir.';
            } else {
                keterangan = 'Tidak hadir.';
            }

            // Tampilkan keterangan
            const keteranganDiv = document.getElementById('keterangan');
            keteranganDiv.innerText = 'Keterangan: ' + keterangan + ' (Jam Absen: ' + jamAbsen + ')';
            keteranganDiv.style.display = 'block';

            // Kirim absensi ke server
            fetch('absen.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    npm: "<?php echo $npm; ?>",
                    id_jadwal: "<?php echo $id_jadwal; ?>",
                    keterangan: keterangan,
                    tanggal: "<?php echo date('Y-m-d', strtotime($jadwal['tanggal_jadwal'])); ?>"
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    keteranganDiv.innerText += ' - Absen berhasil!';
                } else {
                    keteranganDiv.innerText += ' - Absen gagal. ' + (data.message || 'Coba lagi.');
                }
            })
            .catch(error => {
                keteranganDiv.innerText = 'Terjadi kesalahan saat mengirim data absensi: ' + error.message;
                console.error('Error:', error);
            });
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
