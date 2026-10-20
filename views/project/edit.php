<?php
require_once __DIR__ . '/../../config/database.php';

if (!isset($_SESSION['user'])) {
    header("Location: index.php?page=login");
    exit;
}

$currentUser = $_SESSION['user'];
$myRole = strtolower(trim($currentUser['role'] ?? ''));
$id = $_GET['id'] ?? 0;

$stmt = mysqli_prepare($koneksi, "SELECT * FROM projects WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$project = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

if (!$project) {
    header("Location: index.php?page=projects");
    exit;
}

// Perlindungan hak akses edit karya
$isOwner = ((int)$project['user_id'] === (int)$currentUser['id']);
$canManageAll = in_array($myRole, ['admin', 'super_admin']);

if (!$isOwner && !$canManageAll) {
    header("Location: index.php?page=projects");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_project = trim($_POST['nama_project']);
    $deskripsi    = trim($_POST['deskripsi']);
    $bahasa       = trim($_POST['bahasa'] ?? '');
    $link_project = trim($_POST['link_project']);
    $link_demo    = trim($_POST['link_demo']);
    $gambar_nama  = $project['gambar'];

    if (!empty($nama_project) && !empty($deskripsi) && !empty($bahasa) && !empty($link_project)) {
        if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
            $gambar_name = $_FILES['gambar']['name'];
            $gambar_tmp  = $_FILES['gambar']['tmp_name'];
            $file_ext    = strtolower(pathinfo($gambar_name, PATHINFO_EXTENSION));
            $allowed_ext = ['jpg', 'jpeg', 'png', 'webp'];

            if (in_array($file_ext, $allowed_ext)) {
                $new_gambar_name = time() . '_' . uniqid() . '.' . $file_ext;
                $upload_dir = __DIR__ . '/../../uploads/';

                if (move_uploaded_file($gambar_tmp, $upload_dir . $new_gambar_name)) {
                    if (file_exists($upload_dir . $project['gambar'])) {
                        unlink($upload_dir . $project['gambar']);
                    }
                    $gambar_nama = $new_gambar_name;
                }
            }
        }

        $stmt_up = mysqli_prepare($koneksi, "UPDATE projects SET nama_project = ?, deskripsi = ?, bahasa = ?, gambar = ?, link_project = ?, link_demo = ? WHERE id = ?");
        mysqli_stmt_bind_param($stmt_up, "ssssssi", $nama_project, $deskripsi, $bahasa, $gambar_nama, $link_project, $link_demo, $id);
        mysqli_stmt_execute($stmt_up);
        mysqli_stmt_close($stmt_up);

        header("Location: index.php?page=projects");
        exit;
    } else {
        $error = "Harap isi semua kolom wajib!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Karya</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Link CSS Terpisah -->
    <link href="assets/css/main.css" rel="stylesheet">
    <link href="assets/css/projects.css" rel="stylesheet">
    <link href="assets/css/footer.css" rel="stylesheet">
</head>
<body>
    <main class="page-main py-5">
    <div class="container col-11 col-md-8 col-lg-6">
        <div class="form-project-card">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <span class="badge-pill-light mb-1 d-inline-block">Pembaruan Karya</span>
                    <h3 class="fw-bold mb-0">Edit Karya</h3>
                </div>
                <a href="index.php?page=projects" class="btn btn-outline-dark btn-sm rounded-pill px-3">Batal</a>
            </div>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger rounded-4 small mb-4 py-2"><?= htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form action="" method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label class="form-label small fw-semibold text-muted">Judul Proyek *</label>
                    <input type="text" name="nama_project" class="form-control rounded-pill px-3" value="<?= htmlspecialchars($project['nama_project']); ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold text-muted">Deskripsi Proyek *</label>
                    <textarea name="deskripsi" class="form-control rounded-4 p-3" rows="4" required><?= htmlspecialchars($project['deskripsi']); ?></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold text-muted">Kategori & Bahasa / Teknologi *</label>
                    <input type="text" name="bahasa" class="form-control rounded-pill px-3" value="<?= htmlspecialchars($project['bahasa'] ?? ''); ?>" required list="projectTechList" placeholder="Contoh: Website - HTML, CSS, PHP">
                    <datalist id="projectTechList">
                        <option value="Website - HTML, CSS, JavaScript">
                        <option value="Website - PHP, MySQL, Bootstrap">
                        <option value="Website - React, Node.js">
                        <option value="Aplikasi Android - Java, Kotlin">
                        <option value="Aplikasi Desktop - Python, Tkinter">
                        <option value="Aplikasi Mobile - Flutter, Dart">
                        <option value="Game - C#, Unity">
                        <option value="Game - GDScript, Godot">
                        <option value="Game 2D - JavaScript, HTML5 Canvas">
                        <option value="IoT - C++, Arduino, ESP32">
                        <option value="IoT - Python, Raspberry Pi">
                    </datalist>
                    <div class="form-text">Pisahkan kategori, bahasa, dan teknologi dengan tanda koma.</div>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold text-muted">Gambar Preview Saat Ini</label>
                    <div class="mb-2">
                        <img src="uploads/<?= htmlspecialchars($project['gambar']); ?>" class="rounded-4 image-preview-target" style="width: 100%; height: 180px; object-fit: cover;" onerror="this.src='https://via.placeholder.com/400x180';">
                    </div>
                    <input type="file" name="gambar" class="form-control rounded-pill px-3" accept="image/*">
                    <small class="text-muted opacity-75">Kosongkan jika tidak ingin mengganti gambar.</small>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold text-muted">Link Repository / GitHub *</label>
                    <input type="url" name="link_project" class="form-control rounded-pill px-3" value="<?= htmlspecialchars($project['link_project']); ?>" required>
                </div>
                <div class="mb-4">
                    <label class="form-label small fw-semibold text-muted">Link Live Demo (Opsional)</label>
                    <input type="url" name="link_demo" class="form-control rounded-pill px-3" value="<?= htmlspecialchars($project['link_demo']); ?>">
                </div>
                <button type="submit" class="btn btn-pill-accent w-100 py-3" style="background-color: #dc3545; color: white; border-color: #dc3545;" >Perbarui Karya →</button>
            </form>
        </div>
    </div>
    </main>
    <?php require __DIR__ . '/../partials/footer.php'; ?>
    <script src="assets/js/main.js"></script>
</body>
</html>