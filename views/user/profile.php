<?php
require_once __DIR__ . '/../../config/database.php';

if (!isset($_SESSION['user'])) {
    header("Location: index.php?page=login");
    exit;
}

$currentUserId = $_SESSION['user']['id'];
$error = '';
$success = '';

$stmt = mysqli_prepare($koneksi, "SELECT * FROM users WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $currentUserId);
mysqli_stmt_execute($stmt);
$userData = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama     = trim($_POST['nama'] ?? '');
    $old_pass = $_POST['old_password'] ?? '';
    $new_pass = $_POST['new_password'] ?? '';
    $foto_nama = $userData['foto'];

    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $file_name = $_FILES['foto']['name'];
        $file_tmp  = $_FILES['foto']['tmp_name'];
        $file_ext  = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $allowed   = ['jpg', 'jpeg', 'png', 'webp'];

        if (in_array($file_ext, $allowed)) {
            $new_foto_name = 'profile_' . time() . '_' . uniqid() . '.' . $file_ext;
            $upload_dir = __DIR__ . '/../../uploads/';

            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            if (move_uploaded_file($file_tmp, $upload_dir . $new_foto_name)) {
                if ($userData['foto'] !== 'default.png' && file_exists($upload_dir . $userData['foto'])) {
                    unlink($upload_dir . $userData['foto']);
                }
                $foto_nama = $new_foto_name;
            }
        }
    }

    $password_sql_part = "";
    $bind_types = "ssi";
    $bind_values = [$nama, $foto_nama, $currentUserId];

    if (!empty($new_pass)) {
        if (password_verify($old_pass, $userData['password'])) {
            $hashed_new_pass = password_hash($new_pass, PASSWORD_BCRYPT);
            $password_sql_part = ", password = ?";
            $bind_types = "sssi";
            $bind_values = [$nama, $foto_nama, $hashed_new_pass, $currentUserId];
        } else {
            $error = "Password lama salah!";
        }
    }

    if (empty($error)) {
        $query = "UPDATE users SET nama = ?, foto = ?" . $password_sql_part . " WHERE id = ?";
        $stmt_up = mysqli_prepare($koneksi, $query);

        if (strlen($bind_types) === 4) {
            mysqli_stmt_bind_param($stmt_up, $bind_types, $bind_values[0], $bind_values[1], $bind_values[2], $bind_values[3]);
        } else {
            mysqli_stmt_bind_param($stmt_up, $bind_types, $bind_values[0], $bind_values[1], $bind_values[2]);
        }

        if (mysqli_stmt_execute($stmt_up)) {
            $_SESSION['user']['nama'] = $nama;
            $success = "Profil berhasil diperbarui!";

            $stmt_ref = mysqli_prepare($koneksi, "SELECT * FROM users WHERE id = ?");
            mysqli_stmt_bind_param($stmt_ref, "i", $currentUserId);
            mysqli_stmt_execute($stmt_ref);
            $userData = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_ref));
        } else {
            $error = "Gagal memperbarui profil di database.";
        }
        mysqli_stmt_close($stmt_up);
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Saya</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    
    <!-- Link CSS Terpisah -->
    <link href="assets/css/main.css" rel="stylesheet">
    <link href="assets/css/profile.css" rel="stylesheet">
    <link href="assets/css/footer.css" rel="stylesheet">
</head>
<body>
    <main class="page-main py-5">
    <div class="container col-11 col-md-8 col-lg-5">
        <div class="profile-card">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <span class="badge-pill-light mb-1 d-inline-block">Pengaturan Akun</span>
                    <h3 class="fw-bold mb-0">Profil Saya</h3>
                </div>
                <a href="index.php?page=projects" class="btn btn-outline-dark btn-sm rounded-pill px-3">Kembali</a>
            </div>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger rounded-4 small mb-4 py-2"><?= htmlspecialchars($error); ?></div>
            <?php endif; ?>
            <?php if (!empty($success)): ?>
                <div class="alert alert-success rounded-4 small mb-4 py-2"><?= htmlspecialchars($success); ?></div>
            <?php endif; ?>

            <form action="" method="POST" enctype="multipart/form-data">
                <div class="text-center mb-4">
                    <div class="profile-avatar-wrapper mb-3">
                        <img src="uploads/<?= htmlspecialchars($userData['foto'] ?? 'default.png'); ?>" class="profile-avatar-img image-preview-target" onerror="this.src='https://via.placeholder.com/110';">
                    </div>
                    <div>
                        <label class="btn btn-pill-dark btn-sm px-3">
                            <i class="bi bi-camera"></i> Ganti Foto Profil
                            <input type="file" name="foto" class="d-none" accept="image/*">
                        </label>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-semibold text-muted">Email (Tidak bisa diubah)</label>
                    <input type="email" class="form-control rounded-pill px-3 bg-light" value="<?= htmlspecialchars($userData['email']); ?>" disabled>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-semibold text-muted">Nama Lengkap / Username</label>
                    <input type="text" name="nama" class="form-control rounded-pill px-3" value="<?= htmlspecialchars($userData['nama']); ?>" required>
                </div>

                <hr class="my-4 text-muted opacity-25">
                <p class="fw-bold small text-muted mb-3">UBAH PASSWORD </p>

                <div class="mb-3">
                    <label class="form-label small fw-semibold text-muted">Password Lama</label>
                    <div class="password-field">
                        <input type="password" id="old-password" name="old_password" class="form-control rounded-pill px-3" placeholder="••••••••">
                        <button type="button" class="password-toggle" aria-label="Lihat sandi" aria-controls="old-password" aria-pressed="false">
                            <i class="bi bi-eye" aria-hidden="true"></i>
                        </button>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label small fw-semibold text-muted">Password Baru</label>
                    <div class="password-field">
                        <input type="password" id="new-password" name="new_password" class="form-control rounded-pill px-3" placeholder="••••••••">
                        <button type="button" class="password-toggle" aria-label="Lihat sandi" aria-controls="new-password" aria-pressed="false">
                            <i class="bi bi-eye" aria-hidden="true"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn btn-pill-accent w-100 py-3" style="background-color: #dc3545; color: white; border-color: #dc3545;">Simpan Perubahan →</button>
            </form>
        </div>
    </div>
    </main>
    <?php require __DIR__ . '/../partials/footer.php'; ?>
    <script src="assets/js/main.js"></script>
</body>
</html>