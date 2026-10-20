<?php
require_once __DIR__ . '/../../config/database.php';

if (isset($_SESSION['user'])) {
    header("Location: index.php?page=projects");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = strtolower(trim($_POST['email'] ?? ''));
    $password = trim($_POST['password'] ?? '');

    if (!empty($email) && !empty($password)) {
        $stmt = mysqli_prepare($koneksi, "SELECT * FROM users WHERE LOWER(TRIM(email)) = ?");
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($user = mysqli_fetch_assoc($result)) {
            if (password_verify($password, $user['password'])) {
                $_SESSION['user'] = [
                    'id'   => (int)$user['id'],
                    'nama' => $user['nama'],
                    'email'=> $user['email'],
                    'role' => strtolower(trim($user['role']))
                ];
                header("Location: index.php?page=projects");
                exit;
            } else {
                $error = "Password yang dimasukkan salah!";
            }
        } else {
            $error = "Email tidak terdaftar!";
        }
        mysqli_stmt_close($stmt);
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
    <title>Masuk</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    
    <!-- Link CSS Terpisah -->
    <link href="assets/css/main.css" rel="stylesheet">
    <link href="assets/css/auth.css" rel="stylesheet">
    <link href="assets/css/footer.css" rel="stylesheet">
</head>
<body>
    <main class="page-main d-flex align-items-center justify-content-center py-5">
    <div class="container col-11 col-sm-8 col-md-5 col-lg-4">
        <div class="auth-card text-center">
            <span class="badge-pill-light mb-2 d-inline-block">Autentikasi Akun</span>
            <h3 class="fw-bold text-dark mb-4">Masuk</h3>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger rounded-4 small mb-4 py-2"><?= htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form action="" method="POST" class="text-start">
                <div class="mb-3">
                    <label class="form-label small fw-semibold text-muted">Email Sekolah</label>
                    <input type="email" name="email" class="form-control" required placeholder="nama@stella.sch.id">
                </div>
                <div class="mb-4">
                    <label class="form-label small fw-semibold text-muted">Password</label>
                    <div class="password-field">
                        <input type="password" id="login-password" name="password" class="form-control" required placeholder="••••••••">
                        <button type="button" class="password-toggle" aria-label="Lihat sandi" aria-controls="login-password" aria-pressed="false">
                            <i class="bi bi-eye" aria-hidden="true"></i>
                        </button>
                    </div>
                </div>
                <button type="submit" class="btn btn-pill-accent w-100 py-3 mb-3" style="background-color: #dc3545; color: white; border-color: #dc3545;">Masuk Sekarang →</button>
                <div class="text-center">
                    <a href="index.php?page=register" class="text-decoration-none text-muted small">Belum punya akun? <strong class="text-dark">Daftar di sini</strong></a>
                </div>
            </form>
        </div>
    </div>
    </main>
    <?php require __DIR__ . '/../partials/footer.php'; ?>
    <script src="assets/js/main.js"></script>
</body>
</html>