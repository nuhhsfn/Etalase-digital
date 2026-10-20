<?php
require_once __DIR__ . '/../../config/database.php';

// Memastikan session telah dimulai
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user'])) {
    header("Location: index.php?page=login");
    exit;
}

$user = $_SESSION['user'];
$userRole = strtolower(trim($user['role'] ?? ''));
if ($userRole === 'admin') {
    header("Location: index.php?page=projects");
    exit;
}

$error = '';
$projectModels = ['Website', 'Aplikasi Mobile', 'Game', 'IoT', 'Desktop'];
$projectModelIcons = [
    'Website' => '',
    'Aplikasi Mobile' => '',
    'Game' => '',
    'IoT' => '',
    'Desktop' => '',
];
$techList = [
    'PHP' => 'devicon-php-plain colored',
    'JavaScript' => 'devicon-javascript-plain colored',
    'Node.js' => 'devicon-nodejs-plain colored',
    'TypeScript' => 'devicon-typescript-plain colored',
    'Python' => 'devicon-python-plain colored',
    'CSS3' => 'devicon-css3-plain colored',
    'Java' => 'devicon-java-plain colored',
    'C' => 'devicon-c-plain colored',
    'C++' => 'devicon-cplusplus-plain colored',
    'C#' => 'devicon-csharp-plain colored',
    'React' => 'devicon-react-original colored',
    'Go' => 'devicon-go-plain colored',
    'Kotlin' => 'devicon-kotlin-plain colored',
    'Dart' => 'devicon-dart-plain colored',
    'Lua' => 'devicon-lua-plain colored',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_project = trim($_POST['nama_project'] ?? '');
    $deskripsi    = trim($_POST['deskripsi'] ?? '');
    $modelInput = $_POST['model_project'] ?? '';
    $model_project = is_string($modelInput) ? trim($modelInput) : '';
    $bahasa_selected = $_POST['bahasa'] ?? [];
    if (!is_array($bahasa_selected)) {
        $bahasa_selected = [$bahasa_selected];
    }
    $bahasa_selected = array_values(array_unique(array_filter(
        $bahasa_selected,
        static fn($technology) => is_string($technology) && isset($techList[$technology])
    )));
    $bahasa = implode(', ', $bahasa_selected);
    $bahasa_final = $model_project !== '' && $bahasa !== ''
        ? $model_project . ' - ' . $bahasa
        : '';
    $link_project = trim($_POST['link_project'] ?? '');
    $link_demo    = trim($_POST['link_demo'] ?? '');
    $user_id      = $user['id'];

    if (!in_array($model_project, $projectModels, true) || empty($bahasa_selected)) {
        $error = "Pilih model proyek dan minimal satu bahasa atau teknologi.";
    } elseif (!empty($nama_project) && !empty($deskripsi) && !empty($link_project) && isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
        $gambar_name = $_FILES['gambar']['name'];
        $gambar_tmp  = $_FILES['gambar']['tmp_name'];
        $file_ext    = strtolower(pathinfo($gambar_name, PATHINFO_EXTENSION));
        $allowed_ext = ['jpg', 'jpeg', 'png', 'webp'];

        if (in_array($file_ext, $allowed_ext)) {
            $new_gambar_name = time() . '_' . uniqid() . '.' . $file_ext;
            $upload_dir = __DIR__ . '/../../uploads/';

            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            if (move_uploaded_file($gambar_tmp, $upload_dir . $new_gambar_name)) {
                $stmt = mysqli_prepare($koneksi, "INSERT INTO projects (user_id, nama_project, deskripsi, bahasa, gambar, link_project, link_demo) VALUES (?, ?, ?, ?, ?, ?, ?)");
                
                if ($stmt) {
                    mysqli_stmt_bind_param($stmt, "issssss", $user_id, $nama_project, $deskripsi, $bahasa_final, $new_gambar_name, $link_project, $link_demo);
                    
                    if (!mysqli_stmt_execute($stmt)) {
                        $error = "Gagal menyimpan proyek: " . mysqli_stmt_error($stmt);
                    }
                    mysqli_stmt_close($stmt);
                } else {
                    $error = "Gagal mempersiapkan kueri database: " . mysqli_error($koneksi);
                }

                if ($error === '') {
                    header("Location: index.php?page=projects");
                    exit;
                }
            } else {
                $error = "Gagal mengunggah gambar preview!";
            }
        } else {
            $error = "Format gambar harus JPG, JPEG, PNG, atau WEBP!";
        }
    } elseif ($error === '') {
        $error = "Harap isi semua kolom yang wajib dan unggah gambar!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unggah Karya Baru - Etalase Stella</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/gh/devicons/devicon@latest/devicon.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link href="assets/css/main.css" rel="stylesheet">
    <link href="assets/css/navbar.css" rel="stylesheet">
    <link href="assets/css/project.css" rel="stylesheet">
    <link href="assets/css/footer.css" rel="stylesheet">
</head>
<body>
    <div class="container navbar-pill-wrapper project-create-navbar">
        <nav class="navbar navbar-pill">
            <div class="container-fluid px-0">
                <a class="navbar-brand d-flex align-items-center gap-2" href="index.php?page=projects">
                    <span class="rounded-circle d-inline-block" style="width: 32px; height: 32px; background-image: url('assets/img/telkom.png'); background-size: cover; background-position: center;"></span>
                    etalase Stella
                </a>
                <div class="project-menu">
                    <button type="button" class="project-menu-toggle" aria-expanded="false" aria-controls="projectMenuPanel" aria-label="Buka menu">
                        <span></span>
                        <span></span>
                        <span></span>
                    </button>
                    <nav class="project-menu-panel" id="projectMenuPanel" aria-label="Navigasi utama" hidden>
                        <a href="index.php?page=projects">Etalase Karya</a>
                        <a href="index.php?page=profile">Profil</a>
                        <?php if (in_array($userRole, ['admin', 'super_admin'], true)): ?>
                            <a href="index.php?page=users">Kelola Pengguna</a>
                        <?php endif; ?>
                        <a href="index.php?page=logout" class="project-menu-logout">Keluar</a>
                    </nav>
                </div>
            </div>
        </nav>
    </div>

    <main class="page-main project-create-main py-5">
    <div class="container col-11 col-md-8 col-lg-6">
        <div class="form-project-card p-4 p-md-5 rounded-4 shadow-sm bg-white">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <span class="badge-pill-light mb-1 d-inline-block text-muted small fw-semibold">Publikasi Portofolio</span>
                    <h3 class="fw-bold mb-0">Unggah Karya Baru</h3>
                </div>
                <a href="index.php?page=projects" class="btn btn-outline-dark btn-sm rounded-pill px-3">Kembali ke Etalase</a>
            </div>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger rounded-4 small mb-4 py-2" role="alert">
                    <?= htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <form action="" method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label class="form-label small fw-semibold text-muted">Judul Proyek *</label>
                    <input type="text" name="nama_project" class="form-control rounded-pill px-3" required placeholder="Contoh: Aplikasi Kasir Kantin" value="<?= htmlspecialchars($_POST['nama_project'] ?? ''); ?>">
                </div>
                
                <div class="mb-3">
                    <label class="form-label small fw-semibold text-muted">Deskripsi Proyek *</label>
                    <textarea name="deskripsi" class="form-control rounded-4 p-3" rows="4" required placeholder="Jelaskan fitur dan teknologi yang dipakai..."><?= htmlspecialchars($_POST['deskripsi'] ?? ''); ?></textarea>
                </div>
                
                <div class="mb-4">
                    <label class="form-label small fw-semibold text-muted d-block mb-2">1. Pilih Model Proyek *</label>
                    <div class="row g-2">
                        <?php foreach ($projectModels as $index => $model): ?>
                            <?php $modelId = 'model_project_' . $index; ?>
                            <div class="col-6 col-md-4">
                                <input type="radio" class="btn-check" name="model_project" id="<?= $modelId; ?>" value="<?= htmlspecialchars($model); ?>" required <?= (($_POST['model_project'] ?? '') === $model) ? 'checked' : ''; ?>>
                                <label class="btn btn-outline-dark w-100 py-2 rounded-3 small fw-semibold" for="<?= $modelId; ?>"><?= $projectModelIcons[$model]; ?> <?= htmlspecialchars($model); ?></label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label small fw-semibold text-muted d-block mb-2">2. Pilih Bahasa Pemrograman / Teknologi *</label>
                    <div class="tech-icon-grid d-flex flex-wrap gap-3 p-3 bg-light rounded-4 border justify-content-center">
                        <?php foreach ($techList as $name => $icon): ?>
                            <label class="tech-logo-item mb-0" title="<?= htmlspecialchars($name); ?>">
                                <input type="checkbox" name="bahasa[]" value="<?= htmlspecialchars($name); ?>" class="tech-checkbox" aria-label="<?= htmlspecialchars($name); ?>" <?= in_array($name, $bahasa_selected ?? [], true) ? 'checked' : ''; ?>>
                                <span class="tech-logo-box bg-white shadow-sm rounded-4 d-flex align-items-center justify-content-center">
                                    <i class="<?= htmlspecialchars($icon); ?>" aria-hidden="true"></i>
                                </span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                    <div class="form-text small text-muted text-center mt-2">Klik logo teknologi yang digunakan (bisa pilih lebih dari satu).</div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label small fw-semibold text-muted">Gambar Preview *</label>
                    <input type="file" name="gambar" class="form-control rounded-pill px-3" accept="image/*" required>
                </div>
                
                <div class="mb-3">
                    <label class="form-label small fw-semibold text-muted">Link Repository / GitHub *</label>
                    <input type="url" name="link_project" class="form-control rounded-pill px-3" required placeholder="https://github.com/username/project" value="<?= htmlspecialchars($_POST['link_project'] ?? ''); ?>">
                </div>
                
                <div class="mb-4">
                    <label class="form-label small fw-semibold text-muted">Link Live Demo (Opsional)</label>
                    <input type="url" name="link_demo" class="form-control rounded-pill px-3" placeholder="https://demo.project.com" value="<?= htmlspecialchars($_POST['link_demo'] ?? ''); ?>">
                </div>
                
                <button type="submit" class="btn w-100 py-3 rounded-pill fw-bold text-white shadow-sm" style="background-color: #dc3545; border-color: #dc3545;">
                    Simpan & Publikasikan Karya →
                </button>
            </form>
        </div>
    </div>
    </main>

    <?php require __DIR__ . '/../partials/footer.php'; ?>

    <script src="assets/js/main.js"></script>
    <script>
        const technologyCheckboxes = document.querySelectorAll('.tech-checkbox');
        const updateTechnologyValidity = () => {
            const hasSelection = Array.from(technologyCheckboxes).some((checkbox) => checkbox.checked);
            technologyCheckboxes.forEach((checkbox) => {
                checkbox.setCustomValidity(hasSelection ? '' : 'Pilih minimal satu bahasa atau teknologi.');
            });
        };

        technologyCheckboxes.forEach((checkbox) => checkbox.addEventListener('change', updateTechnologyValidity));
        updateTechnologyValidity();
    </script>
</body>
</html>