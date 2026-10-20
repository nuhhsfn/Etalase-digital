<?php
require_once __DIR__ . '/../../config/database.php';

if (!isset($_SESSION['user'])) {
    header("Location: index.php?page=login");
    exit;
}

$currentUser = $_SESSION['user'];
$myRole = strtolower(trim($currentUser['role'] ?? ''));
$project_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($project_id > 0) {
    $stmt = mysqli_prepare($koneksi, "SELECT user_id, gambar FROM projects WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $project_id);
    mysqli_stmt_execute($stmt);
    $project = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);

    if ($project) {
        $isOwner = ((int)$project['user_id'] === (int)$currentUser['id']);
        $canManageAll = in_array($myRole, ['admin', 'super_admin']);

        // VALIDASI HAK AKSES HAPUS
        if ($isOwner || $canManageAll) {
            if (!empty($project['gambar'])) {
                $file_path = __DIR__ . '/../../uploads/' . $project['gambar'];
                if (file_exists($file_path)) {
                    unlink($file_path);
                }
            }

            $stmt_del = mysqli_prepare($koneksi, "DELETE FROM projects WHERE id = ?");
            mysqli_stmt_bind_param($stmt_del, "i", $project_id);
            mysqli_stmt_execute($stmt_del);
            mysqli_stmt_close($stmt_del);
        } else {
            echo "<script>alert('Anda tidak berhak menghapus karya ini!');</script>";
        }
    }
}

header("Location: index.php?page=projects");
exit;