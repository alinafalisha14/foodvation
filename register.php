<?php
// register.php
session_start();
require_once 'koneksi.php';

$pemberitahuan = "";
$email_default = isset($_GET['email']) ? $_GET['email'] : "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email    = mysqli_real_escape_string($koneksi, $_POST['email']);
    $password = mysqli_real_escape_string($koneksi, $_POST['password']);
    $role     = $_POST['role']; // Pilihan role: pelanggan / admin

    //ini ngecek apakah email udah dipakai
    $cek = mysqli_query($koneksi, "SELECT email FROM users WHERE email='$email'");
    if (mysqli_num_rows($cek) > 0) {
        $pemberitahuan = "Email sudah terdaftar! Silakan gunakan email lain.";
    } else {
        //Fungsi Input Data (CRUD Akun)
        $insert = mysqli_query($koneksi, "INSERT INTO users (email, password, role) VALUES ('$email', '$password', '$role')");
        if ($insert) {
            echo "<script>alert('Registrasi Berhasil! Silakan Login.'); window.location='login.php';</script>";
            exit();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head><title>Register - Foodvation</title><link rel="stylesheet" href="assets/css/style.css"></head>
<body style="display:flex; justify-content:center; align-items:center; height:100vh;">
    <div class="form-container" style="width:100%;">
        <h2 style="text-align:center; margin-bottom:20px;">Daftar Akun Baru</h2>
        <?php if($pemberitahuan) echo "<div class='alert'>$pemberitahuan</div>"; ?>
        <form action="" method="POST">
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" value="<?= htmlspecialchars($email_default); ?>" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>
            <div class="form-group">
                <label>Daftar Sebagai (Role)</label>
                <select name="role">
                    <option value="pelanggan">Pelanggan (Bisa Reservasi)</option>
                    <option value="admin">Admin Restoran (Bisa Kelola Data)</option>
                </select>
            </div>
            <button type="submit" class="btn-submit" style="background:#2a6f47;">Daftar</button>
        </form>
        <p style="text-align:center; margin-top:15px;"><a href="login.php">Sudah punya akun? Login</a></p>
    </div>
</body>
</html>