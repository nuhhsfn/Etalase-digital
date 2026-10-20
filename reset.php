<?php
require_once __DIR__ . '/config/database.php';

global $koneksi;

// Akun dan password yang diinginkan
$data = [
    'superadmin@stella.sch.id' => 'superadmin123',
    'admin@stella.sch.id'      => 'admin123',
    'siswa@stella.sch.id'      => 'siswa123'
];

foreach ($data as $email => $pass_plain) {
    // Generate hash Bcrypt langsung dari PHP lokal Anda
    $hash = password_hash($pass_plain, PASSWORD_BCRYPT);
    
    $stmt = mysqli_prepare($koneksi, "UPDATE users SET password = ? WHERE LOWER(TRIM(email)) = ?");
    mysqli_stmt_bind_param($stmt, "ss", $hash, $email);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}

echo "<h2>✅ Password Berhasil Di-reset Total!</h2>";
echo "<ul>
        <li><b>Super Admin:</b> superadmin@stella.sch.id | Password: <b>superadmin123</b></li>
        <li><b>Admin:</b> admin@stella.sch.id | Password: <b>admin123</b></li>
        <li><b>Siswa:</b> siswa@stella.sch.id | Password: <b>siswa123</b></li>
      </ul>";
echo "<a href='index.php?page=login'>Klik di sini untuk Login</a>";
?>