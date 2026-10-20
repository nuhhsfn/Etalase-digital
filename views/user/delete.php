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

$target_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($target_id > 0) {
    $stmt = mysqli_prepare($koneksi, "SELECT id, role FROM users WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $target_id);
    mysqli_stmt_execute($stmt);
    $target_user = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);

    if ($target_user) {
        $targetRole = strtolower(trim($target_user['role']));

        // PROTEKSI BACKEND HAPUS SUPER ADMIN
        if ($targetRole === 'super_admin' && $myRole !== 'super_admin') {
            echo "<script>alert('Akses Ditolak! Admin biasa tidak dapat menghapus Super Admin.'); window.location.href='index.php?page=users';</script>";
            exit;
        }

        if ($target_id === (int)$currentUser['id']) {
            echo "<script>alert('Anda tidak bisa menghapus akun sendiri!'); window.location.href='index.php?page=users';</script>";
            exit;
        }

        $stmt_del = mysqli_prepare($koneksi, "DELETE FROM users WHERE id = ?");
        mysqli_stmt_bind_param($stmt_del, "i", $target_id);
        mysqli_stmt_execute($stmt_del);
        mysqli_stmt_close($stmt_del);
    }
}

header("Location: index.php?page=users");
exit;