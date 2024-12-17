<?php
session_start();
include 'config.php';

$data = json_decode(file_get_contents('php://input'), true);

$npm = $data['npm'];
$id_jadwal = $data['id_jadwal'];
$keterangan = $data['keterangan'];
$tanggal = $data['tanggal'];

// Ambil ID mahasiswa
$stmt = $pdo->prepare("SELECT id_mahasiswa FROM mahasiswa WHERE npm = :npm");
$stmt->execute(['npm' => $npm]);
$id_mahasiswa = $stmt->fetchColumn();

// Masukkan data absensi ke database
$stmt = $pdo->prepare("
    INSERT INTO absensi (id_mahasiswa, id_jadwal, tanggal_absensi, jam_absen, keterangan) 
    VALUES (:id_mahasiswa, :id_jadwal, :tanggal_absensi, NOW(), :keterangan)
");
$result = $stmt->execute([
    'id_mahasiswa' => $id_mahasiswa,
    'id_jadwal' => $id_jadwal,
    'tanggal_absensi' => $tanggal,
    'keterangan' => $keterangan
]);

if ($result) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Gagal mencatat absensi.']);
}
