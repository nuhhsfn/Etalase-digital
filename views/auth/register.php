<?php
require_once __DIR__ . '/../../config/database.php';

if (isset($_SESSION['user'])) {
    header("Location: index.php?page=projects");
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama     = trim($_POST['nama'] ?? '');
    $email    = strtolower(trim($_POST['email'] ?? ''));
    $password = trim($_POST['password'] ?? '');

    if (!empty($nama) && !empty($email) && !empty($password)) {
        $stmt_check = mysqli_prepare($koneksi, "SELECT id FROM users WHERE LOWER(TRIM(email)) = ?");
        mysqli_stmt_bind_param($stmt_check, "s", $email);
        mysqli_stmt_execute($stmt_check);
        mysqli_stmt_store_result($stmt_check);

        if (mysqli_stmt_num_rows($stmt_check) > 0) {
            $error = "Email sudah terdaftar! Gunakan email lain.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);
            $role = 'siswa';
            $foto = 'default.png';

            $stmt_ins = mysqli_prepare($koneksi, "INSERT INTO users (nama, email, password, role, foto) VALUES (?, ?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt_ins, "sssss", $nama, $email, $hashed_password, $role, $foto);

            if (mysqli_stmt_execute($stmt_ins)) {
                $success = "Registrasi berhasil! Silakan masuk.";
            } else {
                $error = "Gagal membuat akun.";
            }
            mysqli_stmt_close($stmt_ins);
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/css/main.css" rel="stylesheet">
    <link href="assets/css/auth.css" rel="stylesheet">
    <link href="assets/css/footer.css" rel="stylesheet">
</head>
<body>
    <main class="page-main d-flex align-items-center justify-content-center py-5">
    <div class="container col-11 col-sm-8 col-md-5 col-lg-4">
        <div class="auth-card text-center">
            <span class="badge-pill-light mb-2 d-inline-block">Pendaftaran Pengguna</span>
            <h3 class="fw-bold text-dark mb-4">Buat Akun</h3>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger rounded-4 small mb-4 py-2"><?= htmlspecialchars($error); ?></div>
            <?php endif; ?>
            <?php if (!empty($success)): ?>
                <div class="alert alert-success rounded-4 small mb-4 py-2"><?= htmlspecialchars($success); ?> <a href="index.php?page=login" class="fw-bold">Masuk</a></div>
            <?php endif; ?>

            <form action="" method="POST" class="text-start">
                <div class="mb-3">
                    <label class="form-label small fw-semibold text-muted">Nama Lengkap</label>
                    <input type="text" name="nama" class="form-control" required placeholder="Nama Anda">
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold text-muted">Email Sekolah</label>
                    <input type="email" name="email" class="form-control" required placeholder="nama@stella.sch.id">
                </div>
                <div class="mb-4">
                    <label class="form-label small fw-semibold text-muted">Password</label>
                    <div class="password-field">
                        <input type="password" id="register-password" name="password" class="form-control" required placeholder="••••••••">
                        <button type="button" class="password-toggle" aria-label="Lihat sandi" aria-controls="register-password" aria-pressed="false">
                            <i class="bi bi-eye" aria-hidden="true"></i>
                        </button>
                    </div>
                </div>
                <button type="submit" class="btn btn-pill-accent w-100 py-3 mb-3" style="background-color: #dc3545; color: white; border-color: #dc3545;">Daftar Sekarang →</button>
                <div class="text-center">
                    <a href="index.php?page=login" class="text-decoration-none text-muted small">Sudah punya akun? <strong class="text-dark">Masuk</strong></a>
                </div>
            </form>
        </div>
    </div>
    </main>
    <?php require __DIR__ . '/../partials/footer.php'; ?>
    <script src="assets/js/main.js"></script>
</body>
</html>