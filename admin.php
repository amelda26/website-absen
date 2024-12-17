<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit();
}

include 'config.php';

// Proses Tambah Admin
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add'])) {
    $username = $_POST['username'];
    $password = md5($_POST['password']); // Hash password dengan MD5

    $stmt = $pdo->prepare("INSERT INTO admin (username, password) VALUES (?, ?)");
    $stmt->execute([$username, $password]);
    header('Location: admin.php'); // Redirect setelah menambah
    exit();
}

// Proses Hapus Admin
if (isset($_GET['delete'])) {
    $id_admin = $_GET['delete'];

    $stmt = $pdo->prepare("DELETE FROM admin WHERE id_admin = ?");
    $stmt->execute([$id_admin]);
    header('Location: admin.php'); // Redirect setelah hapus
    exit();
}

// Proses Edit Admin
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit'])) {
    $id_admin = $_POST['id_admin'];
    $username = $_POST['username'];
    $password = md5($_POST['password']); // Hash password dengan MD5

    $stmt = $pdo->prepare("UPDATE admin SET username = ?, password = ? WHERE id_admin = ?");
    $stmt->execute([$username, $password, $id_admin]);
    header('Location: admin.php'); // Redirect setelah edit
    exit();
}

// Ambil data admin
$stmt = $pdo->query("SELECT * FROM admin");
$adminList = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Kelola Admin</title>
    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            margin-top: 50px;
        }
        .card {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="text-center mb-4">Kelola Admin</h1>

        <div class="card">
            <div class="card-header">
                Tambah Admin
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label for="username" class="form-label">Username:</label>
                        <input type="text" name="username" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password:</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <button type="submit" name="add" class="btn btn-primary">Tambah Admin</button>
                </form>
            </div>
        </div>

        <h2>Daftar Admin</h2>
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>ID Admin</th>
                    <th>Username</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($adminList as $admin): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($admin['id_admin']); ?></td>
                        <td><?php echo htmlspecialchars($admin['username']); ?></td>
                        <td>
                            <a href="admin.php?edit=<?php echo $admin['id_admin']; ?>" class="btn btn-warning btn-sm">Edit</a>
                            <a href="admin.php?delete=<?php echo $admin['id_admin']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus?');">Hapus</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <?php if (isset($_GET['edit'])): ?>
            <?php
            $id_edit = $_GET['edit'];
            $stmt = $pdo->prepare("SELECT * FROM admin WHERE id_admin = ?");
            $stmt->execute([$id_edit]);
            $adminEdit = $stmt->fetch(PDO::FETCH_ASSOC);
            ?>
            <div class="card">
                <div class="card-header">
                    Edit Admin
                </div>
                <div class="card-body">
                    <form method="POST">
                        <input type="hidden" name="id_admin" value="<?php echo htmlspecialchars($adminEdit['id_admin']); ?>">
                        <div class="mb-3">
                            <label for="username" class="form-label">Username:</label>
                            <input type="text" name="username" value="<?php echo htmlspecialchars($adminEdit['username']); ?>" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password:</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <button type="submit" name="edit" class="btn btn-success">Simpan Perubahan</button>
                    </form>
                </div>
            </div>
        <?php endif; ?>

        <a href="admin_index.php" class="btn btn-secondary">Kembali</a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
