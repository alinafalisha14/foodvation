<?php
// forgot_password.php
require_once 'koneksi.php';
$hasil_password = "";
$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = mysqli_real_escape_string($koneksi, $_POST['email']);
    $query = mysqli_query($koneksi, "SELECT password FROM users WHERE email = '$email'");
    
    if (mysqli_num_rows($query) > 0) {
        $data = mysqli_fetch_assoc($query);
        $hasil_password = "Password Anda adalah: <b>" . $data['password'] . "</b>";
    } else {
        $error = "Email tidak ditemukan di sistem kami.";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head><title>Lupa Password - Foodvation</title><link rel="stylesheet" href="assets/css/style.css"></head>
<body style="display:flex; justify-content:center; align-items:center; height:100vh;">
    <div class="form-container" style="width:100%;">
        <h2 style="text-align:center; margin-bottom:20px;">Lupa Password</h2>
        <?php if($error) echo "<div class='alert' style='background:#ffcdd2;'>$error</div>"; ?>
        <?php if($hasil_password) echo "<div class='alert' style='background:#c8e6c9;'>$hasil_password</div>"; ?>
        
        <form action="" method="POST">
            <div class="form-group">
                <label>Masukkan Email Akun Anda</label>
                <input type="email" name="email" required>
            </div>
            <button type="submit" class="btn-submit">Tampilkan Password</button>
        </form>
        <p style="text-align:center; margin-top:15px;"><a href="login.php">Kembali ke Login</a></p>
    </div>
</body>
</html>