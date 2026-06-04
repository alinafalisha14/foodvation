<?php
// login.php
session_start();
require_once 'koneksi.php';

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

$error = "";
// Memiliki Login
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email    = mysqli_real_escape_string($koneksi, $_POST['email']);
    $password = $_POST['password'];

    $query = mysqli_query($koneksi, "SELECT * FROM users WHERE email = '$email'");
    
    // Email belum terdaftar dipaksa pindah ke register
    if (mysqli_num_rows($query) == 0) {
        header("Location: register.php?email=" . urlencode($email));
        exit();
    } else {
        $user = mysqli_fetch_assoc($query);
        if ($password === $user['password']) {
            $_SESSION['user_id'] = $user['id_user'];
            $_SESSION['email']   = $user['email'];
            $_SESSION['role']    = $user['role']; // Hak Akses Pelanggan / Admin
            
            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Password yang Anda masukkan salah!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head><title>Login - Foodvation</title><link rel="stylesheet" href="assets/css/style.css"></head>
<body style="display:flex; justify-content:center; align-items:center; height:100vh;">
    <div class="form-container" style="width:100%;">
        <h2 style="text-align:center; margin-bottom:20px;">Login Foodvation</h2>
        <?php if($error) echo "<div class='alert'>$error</div>"; ?>
        <form action="" method="POST">
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" required placeholder="name@example.com">
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>
            <button type="submit" class="btn-submit">Masuk</button>
        </form>
        <p style="text-align:center; margin-top:15px;">
            <a href="forgot_password.php">Lupa Password?</a> | <a href="register.php">Daftar Akun</a>
        </p>
    </div>
</body>
</html>