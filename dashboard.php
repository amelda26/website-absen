<?php
session_start();
if (!isset($_SESSION['logged_in'])) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Mahasiswa</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Selamat Datang, <?php echo htmlspecialchars($_SESSION['npm']); ?></h1>
    </header>

    <main>
        <p>Ini adalah halaman dashboard mahasiswa.</p>
        <a href="logout.php">Logout</a>
    </main>
</body>
</html>
