<?php
session_start();
include 'config.php';

// Cek apakah mahasiswa sudah login 
if (!isset($_SESSION['mahasiswa_logged_in'])) {
    header('Location: login_mahasiswa.php');
    exit();
}

// Ambil daftar jadwal dari database
$stmt = $pdo->query("SELECT j.*, m.nama_matakuliah 
                      FROM jadwal j 
                      JOIN matakuliah m ON j.id_matakuliah = m.id_matakuliah");
$jadwalList = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Daftar nama hari dalam bahasa Indonesia
$hariIndo = [
    "Sunday" => "Minggu",
    "Monday" => "Senin",
    "Tuesday" => "Selasa",
    "Wednesday" => "Rabu",
    "Thursday" => "Kamis",
    "Friday" => "Jumat",
    "Saturday" => "Sabtu"
];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id_jadwal'])) {
    // Simpan ID jadwal yang dipilih ke session
    $_SESSION['id_jadwal'] = $_POST['id_jadwal'];
    header('Location: dashboard_mahasiswa.php'); // Redirect ke dashboard
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Pilih Jadwal</title>
    <style>
        body {
            background-color: #f4f6f9;
        }
        .jadwal-container {
            max-width: 900px;
            margin: auto;
            margin-top: 50px;
            padding: 20px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        .card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border-radius: 15px;
            border: none;
            background: #ffffff;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .card:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }
        .card-header {
            background-color: #4db8ff;
            color: white;
            font-size: 1.25rem;
            font-weight: bold;
            border-radius: 15px 15px 0 0;
        }
        .card-body {
            padding: 20px;
        }
        .card-footer {
            background-color: #f8f9fa;
            text-align: center;
            padding: 15px;
        }
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }
        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }
        .card-title {
            font-size: 1.5rem;
            font-weight: bold;
        }
        .card-subtitle {
            font-size: 1rem;
            color: #555;
        }
        .badge-pending {
            background-color: #ffc107;
        }
    </style>
</head>
<body>
    <div class="jadwal-container">
        <h2 class="text-center mb-4">Pilih Jadwal Kuliah</h2>
        
        <!-- Jadwal Cards -->
        <div class="row">
            <?php foreach ($jadwalList as $jadwal): ?>
                <?php
                    // Menghitung hari dari tanggal_jadwal
                    $tanggal_jadwal = $jadwal['tanggal_jadwal'];
                    $hari = date('l', strtotime($tanggal_jadwal));  // Mengambil hari dalam bahasa Inggris
                    $hariIndoText = $hariIndo[$hari];  // Mencocokkan dengan nama hari dalam bahasa Indonesia
                ?>
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <?php echo htmlspecialchars($jadwal['nama_matakuliah']); ?>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($jadwal['nama_matakuliah']); ?></h5>
                            <p><strong>Hari: </strong> <?php echo ucfirst($hariIndoText); ?></p>
                            <p><strong>Tanggal: </strong> <?php echo date('d-m-Y', strtotime($jadwal['tanggal_jadwal'])); ?></p>
                            <p><strong>Jam: </strong> <?php echo htmlspecialchars($jadwal['jam_mulai']) . ' - ' . htmlspecialchars($jadwal['jam_selesai']); ?></p>
                        </div>
                        <div class="card-footer">
                            <form method="POST">
                                <input type="hidden" name="id_jadwal" value="<?php echo $jadwal['id_jadwal']; ?>">
                                <button type="submit" class="btn btn-primary w-100">Pilih Jadwal</button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
