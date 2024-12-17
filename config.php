<?php
$host = 'localhost'; // atau alamat host Anda
$db = 'absensi_db';
$user = 'root'; // ganti dengan username database Anda
$pass = ''; // ganti dengan password database Anda

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>
