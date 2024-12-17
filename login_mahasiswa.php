<?php
session_start();
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $npm = $_POST['npm'];

    // Cek apakah NPM valid
    $stmt = $pdo->prepare("SELECT * FROM mahasiswa WHERE npm = :npm");
    $stmt->execute(['npm' => $npm]);
    $mahasiswa = $stmt->fetch();

    if ($mahasiswa) {
        // Jika berhasil login
        $_SESSION['mahasiswa_logged_in'] = true;
        $_SESSION['npm'] = $npm; // Simpan NPM di session
        header('Location: pilih_jadwal.php'); // Arahkan ke halaman pilih jadwal
        exit();
    } else {
        $error = "NPM tidak ditemukan!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Login Mahasiswa</title>
    <style>
        body {
            background-color: #f8f9fa;
        }
        .login-container {
            max-width: 400px;
            margin: auto;
            margin-top: 100px;
            padding: 20px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2 class="text-center">Login Mahasiswa</h2>
        <?php if (isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
        <form method="POST">
            <div class="mb-3">
                <label for="npm" class="form-label">NPM:</label>
                <input type="text" class="form-control" name="npm" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Login</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
