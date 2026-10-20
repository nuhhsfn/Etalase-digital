<?php
require_once __DIR__ . '/../../config/database.php';

if (!isset($_SESSION['user'])) {
    header("Location: index.php?page=login");
    exit;
}

$currentUser = $_SESSION['user'];
$myRole = strtolower(trim($currentUser['role'] ?? ''));

if (!in_array($myRole, ['admin', 'super_admin'])) {
    header("Location: index.php?page=projects");
    exit;
}

$isMeSuperAdmin = ($myRole === 'super_admin');
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama     = trim($_POST['nama']);
    $email    = strtolower(trim($_POST['email']));
    $password = trim($_POST['password']);
    $role_req = strtolower(trim($_POST['role'] ?? 'siswa'));

    // PROTEKSI BACKEND: Jika BUKAN Super Admin, cegah penambahan Super Admin
    if (!$isMeSuperAdmin && $role_req === 'super_admin') {
        $role_req = 'siswa';
    }

    if (!empty($nama) && !empty($email) && !empty($password)) {
        $stmt_check = mysqli_prepare($koneksi, "SELECT id FROM users WHERE LOWER(TRIM(email)) = ?");
        mysqli_stmt_bind_param($stmt_check, "s", $email);
        mysqli_stmt_execute($stmt_check);
        mysqli_stmt_store_result($stmt_check);

        if (mysqli_stmt_num_rows($stmt_check) > 0) {
            $error = "Email sudah terdaftar!";
        } else {
            $hashed = password_hash($password, PASSWORD_BCRYPT);
            $stmt_ins = mysqli_prepare($koneksi, "INSERT INTO users (nama, email, password, role) VALUES (?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt_ins, "ssss", $nama, $email, $hashed, $role_req);
            mysqli_stmt_execute($stmt_ins);
            mysqli_stmt_close($stmt_ins);

            header("Location: index.php?page=users");
            exit;
        }
        mysqli_stmt_close($stmt_check);
    } else {
        $error = "Semua bidang wajib diisi!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/footer.css" rel="stylesheet">
</head>
<body class="bg-light py-5">
    <div class="container col-md-5">
        <div class="card border-0 shadow-sm rounded-4 p-4">
            <h4 class="fw-bold mb-3">Tambah Pengguna Baru</h4>
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger py-2 small"><?= htmlspecialchars($error); ?></div>
            <?php endif; ?>
            <form action="" method="POST">
                <div class="mb-3"><label class="form-label small">Nama Lengkap</label><input type="text" name="nama" class="form-control" required></div>
                <div class="mb-3"><label class="form-label small">Email</label><input type="email" name="email" class="form-control" required></div>
                <div class="mb-3"><label class="form-label small">Password</label><input type="password" name="password" class="form-control" required></div>
                <div class="mb-4">
                    <label class="form-label small">Role</label>
                    <select name="role" class="form-select" required>
                        <option value="siswa">Siswa</option>
                        <option value="admin">Admin</option>
                        <?php if ($isMeSuperAdmin): ?>
                            <option value="super_admin">Super Admin</option>
                        <?php endif; ?>
                    </select>
                </div>
                <div class="d-flex gap-2">
                    <a href="index.php?page=users" class="btn btn-outline-secondary w-50">Batal</a>
                    <button type="submit" class="btn btn-primary w-50 fw-semibold">Simpan</button>
                </div>
            </form>
        </div>
    </div>
    <?php require __DIR__ . '/../partials/footer.php'; ?>
</body>
</html>