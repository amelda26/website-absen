<?php
session_start();
session_destroy();
header('Location: login_mahasiswa.php'); // Ganti dengan halaman login mahasiswa
exit();
