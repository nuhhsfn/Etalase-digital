<?php
require_once __DIR__ . '/../../config/database.php';

if (!isset($_SESSION['user'])) {
    header("Location: index.php?page=login");
    exit;
}

$currentUser = $_SESSION['user'];
$myRole = strtolower(trim($currentUser['role'] ?? ''));

// Hanya Admin dan Super Admin yang boleh masuk
if (!in_array($myRole, ['admin', 'super_admin'])) {
    header("Location: index.php?page=projects");
    exit;
}

$isMeSuperAdmin = ($myRole === 'super_admin');
$result_users = mysqli_query($koneksi, "SELECT * FROM users ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Pengguna</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/css/main.css" rel="stylesheet">
    <link href="assets/css/navbar.css" rel="stylesheet">
    <link href="assets/css/user.css" rel="stylesheet">
    <link href="assets/css/footer.css" rel="stylesheet">
</head>
<body>
    <main class="page-main">
        <div class="container navbar-pill-wrapper pt-3">
            <nav class="navbar navbar-expand navbar-pill">
                <div class="container-fluid px-0">
                    <a class="navbar-brand d-flex align-items-center gap-2" href="index.php?page=projects">
                        <span class="rounded-circle d-inline-block" style="width: 32px; height: 32px; background-image: url('assets/img/unnamed.jpg'); background-size: cover; background-position: center;"></span>
                        etalase Stella
                    </a>
                    <div class="d-flex align-items-center gap-2 gap-md-3">
                        <a href="index.php?page=projects" class="btn btn-sm btn-outline-light rounded-pill px-3">Karya</a>
                        <a href="index.php?page=profile" class="btn btn-sm btn-outline-light rounded-pill px-3 d-none d-sm-inline-block">Profil</a>
                        <a href="index.php?page=logout" class="btn btn-sm btn-outline-danger rounded-circle p-2" aria-label="Keluar">
                            <i class="bi bi-box-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </nav>
        </div>

        <section class="container py-5">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end gap-3 mb-4">
                <div>
                    <span class="badge-pill-light mb-2 d-inline-block">Administrasi</span>
                    <h1 class="fw-bold mb-2">Kelola Pengguna</h1>
                    <p class="text-muted mb-0">Lihat akun dan kelola akses pengguna Etalase Stella.</p>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <a href="index.php?page=projects" class="btn btn-outline-dark rounded-pill px-3">
                        <i class="bi bi-arrow-left me-1"></i>Kembali ke Karya
                    </a>
                    <a href="index.php?page=user-create" class="btn btn-pill-accent" style="background-color: #dc3545; color: white; border-color: #dc3545;">
                        <i class="bi bi-person-plus me-1"></i>Tambah Pengguna
                    </a>
                </div>
            </div>

            <div class="d-flex align-items-center gap-2 mb-3 text-muted small">
                <i class="bi bi-people-fill"></i>
                <span><?= mysqli_num_rows($result_users); ?> pengguna terdaftar</span>
            </div>

            <div class="users-table-card">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (mysqli_num_rows($result_users) > 0): ?>
                                <?php $no = 1; while ($row = mysqli_fetch_assoc($result_users)):
                                    $targetRole = strtolower(trim($row['role'] ?? ''));
                                    $isTargetSuperAdmin = ($targetRole === 'super_admin');

                                    // Admin biasa tidak dapat menghapus Super Admin.
                                    $canDelete = true;
                                    if ($isTargetSuperAdmin && !$isMeSuperAdmin) { $canDelete = false; }
                                    if ((int)$row['id'] === (int)$currentUser['id']) { $canDelete = false; }
                                ?>
                                    <tr>
                                        <td class="text-muted"><?= $no++; ?></td>
                                        <td class="fw-semibold"><?= htmlspecialchars($row['nama']); ?></td>
                                        <td><?= htmlspecialchars($row['email']); ?></td>
                                        <td>
                                            <span class="badge bg-<?= $isTargetSuperAdmin ? 'danger' : ($targetRole === 'admin' ? 'warning text-dark' : 'secondary'); ?>">
                                                <?= strtoupper(htmlspecialchars($row['role'])); ?>
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <?php if ($canDelete): ?>
                                                <a href="index.php?page=user-delete&id=<?= (int)$row['id']; ?>" class="btn btn-outline-danger btn-sm rounded-pill px-3" onclick="return confirm('Yakin ingin menghapus pengguna ini?');">
                                                    <i class="bi bi-trash me-1"></i>Hapus
                                                </a>
                                            <?php else: ?>
                                                <button class="btn btn-light text-muted btn-sm rounded-pill px-3 border" disabled>
                                                    <i class="bi bi-lock me-1"></i>Terkunci
                                                </button>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="py-5 text-center text-muted">Belum ada pengguna terdaftar.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </main>
    <?php require __DIR__ . '/../partials/footer.php'; ?>
</body>
</html>