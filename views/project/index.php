<?php
require_once __DIR__ . '/../../config/database.php';

if (!isset($_SESSION['user'])) {
    header("Location: index.php?page=login");
    exit;
}

$currentUser = $_SESSION['user'];
$myRole = strtolower(trim($currentUser['role'] ?? ''));

$stmt_me = mysqli_prepare($koneksi, "SELECT * FROM users WHERE id = ?");
mysqli_stmt_bind_param($stmt_me, "i", $currentUser['id']);
mysqli_stmt_execute($stmt_me);
$meData = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_me));
mysqli_stmt_close($stmt_me);

$res_total_projects = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM projects");
$total_projects = mysqli_fetch_assoc($res_total_projects)['total'] ?? 0;

$res_total_users = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM users");
$total_users = mysqli_fetch_assoc($res_total_users)['total'] ?? 0;

$query = "SELECT projects.*, users.nama AS pembuat_nama, users.foto AS pembuat_foto 
          FROM projects 
          LEFT JOIN users ON projects.user_id = users.id 
          ORDER BY projects.id DESC";
$result = mysqli_query($koneksi, $query);

$languageIcons = [
    'c' => 'devicon-c-plain colored',
    'c#' => 'devicon-csharp-plain colored',
    'c++' => 'devicon-cplusplus-plain colored',
    'css' => 'devicon-css3-plain colored',
    'css3' => 'devicon-css3-plain colored',
    'dart' => 'devicon-dart-plain colored',
    'go' => 'devicon-go-plain colored',
    'html' => 'devicon-html5-plain colored',
    'html5' => 'devicon-html5-plain colored',
    'java' => 'devicon-java-plain colored',
    'javascript' => 'devicon-javascript-plain colored',
    'kotlin' => 'devicon-kotlin-plain colored',
    'lua' => 'devicon-lua-plain colored',
    'node.js' => 'devicon-nodejs-plain colored',
    'php' => 'devicon-php-plain colored',
    'python' => 'devicon-python-plain colored',
    'react' => 'devicon-react-original colored',
    'ruby' => 'devicon-ruby-plain colored',
    'rust' => 'devicon-rust-plain colored',
    'swift' => 'devicon-swift-plain colored',
    'typescript' => 'devicon-typescript-plain colored',
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Etalase Stella</title>
    
    <!-- CSS External & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/gh/devicons/devicon@latest/devicon.min.css" rel="stylesheet">
    
    <!-- CSS Lokal -->
    <link href="assets/css/main.css" rel="stylesheet">
    <link href="assets/css/navbar.css" rel="stylesheet">
    <link href="assets/css/project.css" rel="stylesheet">
    <link href="assets/css/footer.css" rel="stylesheet">

    <style>
        /* Mengatur deskripsi di card agar hanya 1 baris */
        .project-card-description {
            display: -webkit-box;
            -webkit-line-clamp: 1;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
            font-size: 0.85rem;
            color: #6c757d;
            margin-bottom: 10px;
            min-height: 1.25rem;
        }

        /* Menyamakan tinggi gambar preview card */
        .card-showcase-img {
            height: 150px;
            object-fit: cover;
            width: 100%;
            border-radius: 12px;
        }

        /* Memastikan tombol modal berada di lapisan paling atas */
        .project-detail-trigger {
            position: relative;
            z-index: 2;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <!-- FLOATING NAVBAR PILL -->
    <div class="container navbar-pill-wrapper">
        <nav class="navbar navbar-expand-lg navbar-pill">
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
                        <?php if (in_array($myRole, ['admin', 'super_admin'], true)): ?>
                            <a href="index.php?page=users">Kelola Pengguna</a>
                        <?php endif; ?>
                        <?php if ($myRole !== 'admin'): ?>
                            <a href="index.php?page=project-create">Tambah Karya</a>
                        <?php endif; ?>
                        <a href="index.php?page=logout" class="project-menu-logout">Keluar</a>
                    </nav>
                </div>
            </div>
        </nav>
    </div>

    <!-- HERO BANNER -->
    <div class="container my-4">
        <div class="hero-banner">
            <div>
                <h1>Sustainable Innovation<br>for a Smarter Future.</h1>
                <p>Jelajahi berbagai ide, aplikasi, dan proyek teknologi dalam satu etalase digital.</p>
                <div class="hero-banner-actions d-flex justify-content-center gap-3">
                    <?php if ($myRole !== 'admin'): ?>
                        <a href="index.php?page=project-create" class="btn btn-pill-accent" style="background-color: #dc3545; color: white; border-color: #dc3545;">Unggah Karya Sekarang</a>
                    <?php endif; ?>
                    <a href="#katalog" class="btn btn-pill-dark">Lihat Karya</a>
                </div>
            </div>
        </div>
    </div>

    <!-- STATS CARDS -->
    <div class="container my-5">
        <div class="row g-3">
            <div class="col-6 col-md-6">
                <div class="stat-card text-center">
                    <div class="stat-number"><?= $total_projects; ?>+</div>
                    <div class="stat-label">Total Karya Terdaftar</div>
                </div>
            </div>
            <div class="col-6 col-md-6">
                <div class="stat-card text-center">
                    <div class="stat-number"><?= $total_users; ?>+</div>
                    <div class="stat-label">Pengguna / Kreator</div>
                </div>
            </div>
        </div>
    </div>

    <!-- SHOWCASE KATALOG -->
    <div class="container my-5 pt-3" id="katalog">
        <div class="catalog-heading d-flex justify-content-between align-items-end mb-4">
            <div>
                <span class="text-muted small fw-bold text-uppercase">Koleksi Terbaru</span>
                <h3 class="fw-bold mb-0">Portofolio Digital</h3>
            </div>
            <div class="col-md-4">
                <input type="text" id="searchInput" class="form-control rounded-pill px-4" placeholder="Cari judul karya atau pembuat...">
            </div>
        </div>

        <div class="row g-4" id="projectGrid">
            <?php if (mysqli_num_rows($result) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <?php
                    $language = trim($row['bahasa'] ?? '');
                    $languageParts = preg_split('/\s+-\s+/', $language, 2);
                    $projectModel = count($languageParts) === 2 ? trim($languageParts[0]) : '';
                    $technologyList = count($languageParts) === 2 ? $languageParts[1] : $language;
                    $technologies = $technologyList !== '' ? preg_split('/\s*,\s*/', $technologyList, -1, PREG_SPLIT_NO_EMPTY) : [];
                    ?>
                    <div class="col-12 col-md-6 col-lg-4 project-item">
                        <div class="card card-showcase h-100 p-3 shadow-sm border-0 rounded-4 d-flex flex-column">
                            <img src="uploads/<?= htmlspecialchars($row['gambar']); ?>" class="card-showcase-img mb-3" alt="Preview <?= htmlspecialchars($row['nama_project']); ?>" onerror="this.src='https://via.placeholder.com/400x230';">
                            
                            <h5 class="project-card-title fw-bold text-dark mb-1 fs-6 text-truncate"><?= htmlspecialchars($row['nama_project']); ?></h5>
                            
                            <!-- Deskripsi Dibatasi 1 Baris -->
                            <p class="project-card-description"><?= htmlspecialchars($row['deskripsi']); ?></p>
                            
                            <!-- Badges Bahasa / Teknologi -->
                            <div class="project-tech-badges mb-3" aria-label="Kategori dan bahasa pemrograman">
                                <?php if ($projectModel !== ''): ?>
                                    <span class="badge bg-light text-dark border rounded-pill px-2 py-1 mb-1" style="font-size: 0.75rem;">
                                        <i class="bi bi-grid-3x3-gap" aria-hidden="true"></i>
                                        <?= htmlspecialchars($projectModel); ?>
                                    </span>
                                <?php endif; ?>
                                <?php if ($technologies): ?>
                                    <?php foreach ($technologies as $technology): ?>
                                        <?php $technologyIcon = $languageIcons[strtolower(trim($technology))] ?? ''; ?>
                                        <span class="badge bg-light text-dark border rounded-pill px-2 py-1 mb-1" style="font-size: 0.75rem;">
                                            <?php if ($technologyIcon !== ''): ?>
                                                <i class="<?= htmlspecialchars($technologyIcon); ?>" aria-hidden="true"></i>
                                            <?php else: ?>
                                                <i class="bi bi-code-slash" aria-hidden="true"></i>
                                            <?php endif; ?>
                                            <?= htmlspecialchars(trim($technology)); ?>
                                        </span>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <span class="badge bg-light text-dark border rounded-pill" style="font-size: 0.75rem;"><i class="bi bi-code-slash" aria-hidden="true"></i> Belum diisi</span>
                                <?php endif; ?>
                            </div>

                            <?php 
                            $isOwner = ((int)$row['user_id'] === (int)$currentUser['id']);
                            $canManageAll = in_array($myRole, ['admin', 'super_admin'], true);
                            ?>
                            
                            <!-- Tombol Pemicu Modal -->
                            <div class="mt-auto">
                                <button type="button"
                                        class="btn btn-outline-dark btn-sm rounded-pill w-100 py-1 project-detail-trigger"
                                        style="font-size: 0.8rem;"
                                        data-bs-toggle="modal"
                                        data-bs-target="#projectDetailModal"
                                        data-title="<?= htmlspecialchars($row['nama_project']); ?>"
                                        data-language="<?= htmlspecialchars($language); ?>"
                                        data-description="<?= htmlspecialchars($row['deskripsi']); ?>"
                                        data-image="uploads/<?= htmlspecialchars($row['gambar']); ?>"
                                        data-author="<?= htmlspecialchars($row['pembuat_nama'] ?? 'Pengguna Dihapus'); ?>"
                                        data-author-image="uploads/<?= htmlspecialchars($row['pembuat_foto'] ?? 'default.png'); ?>"
                                        data-repository="<?= htmlspecialchars($row['link_project']); ?>"
                                        data-demo="<?= htmlspecialchars($row['link_demo'] ?? ''); ?>">
                                    Lihat Selengkapnya <i class="bi bi-arrow-right"></i>
                                </button>

                                <?php if ($isOwner || $canManageAll): ?>
                                    <div class="border-top pt-2 mt-2 d-flex justify-content-end gap-2">
                                        <a href="index.php?page=project-edit&id=<?= $row['id']; ?>" class="btn btn-light btn-sm rounded-pill"><i class="bi bi-pencil me-1"></i>Edit</a>
                                        <a href="index.php?page=project-delete&id=<?= $row['id']; ?>" class="btn btn-light text-danger btn-sm rounded-pill" onclick="return confirm('Hapus karya ini?');"><i class="bi bi-trash me-1"></i>Hapus</a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12 py-5 text-center">
                    <p class="text-muted">Belum ada karya yang diunggah.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- MODAL DETAIL PROYEK (POP-UP) -->
    <div class="modal fade" id="projectDetailModal" tabindex="-1" aria-labelledby="projectDetailTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
            <div class="modal-content rounded-4 overflow-hidden border-0 shadow">
                <div class="modal-header border-0 pb-0">
                    <div class="d-flex align-items-center gap-2">
                        <img id="projectDetailAuthorImage" src="" alt="" class="rounded-circle border" style="width: 36px; height: 36px; object-fit: cover;" onerror="this.src='https://via.placeholder.com/100';">
                        <span id="projectDetailAuthor" class="text-muted small fw-semibold"></span>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body pt-3">
                    <div class="row align-items-start">
                        <div class="col-12 col-md-6 mb-3 mb-md-0">
                            <img id="projectDetailImage" src="" alt="" class="w-100 rounded-4 shadow-sm" style="max-height: 280px; object-fit: cover;" onerror="this.src='https://via.placeholder.com/400x230';">
                        </div>
                        <div class="col-12 col-md-6">
                            <h3 id="projectDetailTitle" class="fw-bold h4 mb-2"></h3>
                            <div id="projectDetailLanguage" class="project-tech-badges mb-3"></div>
                            <p id="projectDetailDescription" class="text-muted small mb-4" style="white-space: pre-line;"></p>
                            <div class="d-grid gap-2">
                                <a id="projectDetailRepository" href="#" target="_blank" rel="noopener noreferrer" class="btn btn-outline-dark btn-sm rounded-pill">Lihat Repository / GitHub</a>
                                <a id="projectDetailDemo" href="#" target="_blank" rel="noopener noreferrer" class="btn btn-danger btn-sm rounded-pill" style="display: none;">Lihat Live Demo</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php require __DIR__ . '/../partials/footer.php'; ?>

    <!-- BOOTSTRAP JS BUNDLE (Wajib untuk modal) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/main.js"></script>

    <!-- JAVASCRIPT POP-UP MODAL -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const projectDetailModal = document.getElementById('projectDetailModal');
            if (projectDetailModal) {
                projectDetailModal.addEventListener('show.bs.modal', function (event) {
                    const button = event.relatedTarget;
                    if (!button) return;

                    // Mengambil nilai data-* dari tombol
                    const title = button.getAttribute('data-title') || '';
                    const language = button.getAttribute('data-language') || '';
                    const description = button.getAttribute('data-description') || '';
                    const image = button.getAttribute('data-image') || '';
                    const author = button.getAttribute('data-author') || '';
                    const authorImage = button.getAttribute('data-author-image') || '';
                    const repository = button.getAttribute('data-repository') || '#';
                    const demo = button.getAttribute('data-demo') || '';

                    // Memasukkan data ke dalam modal
                    projectDetailModal.querySelector('#projectDetailTitle').textContent = title;
                    projectDetailModal.querySelector('#projectDetailDescription').textContent = description;
                    projectDetailModal.querySelector('#projectDetailImage').src = image;
                    projectDetailModal.querySelector('#projectDetailAuthor').textContent = author;
                    projectDetailModal.querySelector('#projectDetailAuthorImage').src = authorImage;
                    projectDetailModal.querySelector('#projectDetailRepository').href = repository;

                    // Bahasa / Badge di modal
                    const langContainer = projectDetailModal.querySelector('#projectDetailLanguage');
                    if (language.trim() !== '') {
                        langContainer.innerHTML = '<span class="badge bg-light text-dark border rounded-pill px-3 py-2"><i class="bi bi-code-slash me-1"></i> ' + language + '</span>';
                    } else {
                        langContainer.innerHTML = '';
                    }

                    // Pengaturan Tombol Demo
                    const demoBtn = projectDetailModal.querySelector('#projectDetailDemo');
                    if (demo && demo.trim() !== '') {
                        demoBtn.href = demo;
                        demoBtn.style.display = 'block';
                    } else {
                        demoBtn.style.display = 'none';
                    }
                });
            }
        });
    </script>
</body>
</html>