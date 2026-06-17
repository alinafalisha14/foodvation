<?php
// kelola_users.php
session_start();
require_once 'koneksi.php';

// Proteksi: Pastikan user sudah login DAN memiliki role 'admin'
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo "<script>alert('Akses Ditolak! Halaman ini khusus Admin.'); window.location='dashboard.php';</script>";
    exit();
}

$pesan = "";

// =========================================================================
//  FUNGSI CRUD LENGKAP (CREATE, UPDATE, DELETE) UNTUK USERS
// =========================================================================

// 1. CREATE: Fungsi Tambah Data User Baru
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['tambah_user'])) {
    $email    = mysqli_real_escape_string($koneksi, $_POST['email']);
    $password = mysqli_real_escape_string($koneksi, $_POST['password']);
    $role     = mysqli_real_escape_string($koneksi, $_POST['role']); 

    // Cek apakah email sudah ada
    $cek = mysqli_query($koneksi, "SELECT * FROM users WHERE email='$email'");
    if(mysqli_num_rows($cek) > 0){
        $pesan = "Gagal! Email tersebut sudah terdaftar.";
    } else {
        $insert = mysqli_query($koneksi, "INSERT INTO users (email, password, role) VALUES ('$email', '$password', '$role')");
        if ($insert) {
            $pesan = "Akun baru berhasil ditambahkan ke database!";
        }
    }
}

// 2. UPDATE: Fungsi Edit Data User
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_user'])) {
    $id_user_edit = (int)$_POST['id_user'];
    $email_baru   = mysqli_real_escape_string($koneksi, $_POST['email']);
    $password_baru= mysqli_real_escape_string($koneksi, $_POST['password']);
    $role_baru    = mysqli_real_escape_string($koneksi, $_POST['role']);

    $update = mysqli_query($koneksi, "UPDATE users SET email='$email_baru', password='$password_baru', role='$role_baru' WHERE id_user=$id_user_edit");
    if ($update) {
        $pesan = "Informasi Akun berhasil diperbarui!";
    }
}

// 3. DELETE: Fungsi Hapus Data User
if (isset($_GET['hapus'])) {
    $id_hapus = (int)$_GET['hapus'];
    // Cegah admin menghapus akunnya sendiri
    if($id_hapus == $_SESSION['user_id']){
        echo "<script>alert('Anda tidak bisa menghapus akun Anda sendiri yang sedang aktif!'); window.location='kelola_users.php';</script>";
        exit();
    } else {
        mysqli_query($koneksi, "DELETE FROM users WHERE id_user=$id_hapus");
        header("Location: kelola_users.php");
        exit();
    }
}

// 4. READ: Mengambil seluruh daftar user untuk ditampilkan di tabel
$list_users = mysqli_query($koneksi, "SELECT * FROM users");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Panel Admin - Kelola Akun</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <nav class="navbar" style="background:#333;">
        <a href="dashboard.php">⬅️ Dashboard Utama</a>
        <span style="color:#4CAF50; font-weight:bold;">🛡️ ADMINISTRATOR MODE</span>
    </nav>

    <div class="container">
        <h2>Panel Kelola Akun Pengguna</h2>
        <p style="color:#666; margin-bottom:15px;">Halaman khusus admin untuk mengelola hak akses dan data akun.</p>
        
        <?php if($pesan) echo "<div class='alert' style='background:#c8e6c9; color:#2e7d32;'>$pesan</div>"; ?>
        
        <div style="margin-bottom: 15px; text-align: right;">
            <a href="kelola_users.php?aksi=tambah" style="background:#4CAF50; color:white; padding:10px 15px; text-decoration:none; border-radius:5px; font-weight:bold;">➕ Tambah Akun Baru</a>
        </div>

        <table border="1" width="100%" style="border-collapse:collapse; text-align:left; background:white;">
            <tr style="background:#eee;">
                <th style="padding:12px; width:60px;">ID</th>
                <th style="padding:12px;">Email Pengguna</th>
                <th style="padding:12px;">Role</th>
                <th style="padding:12px; text-align:center; width:160px;">Aksi CRUD</th>
            </tr>
            <?php while($r = mysqli_fetch_assoc($list_users)): ?>
            <tr>
                <td style="padding:12px;"><?= $r['id_user']; ?></td>
                <td style="padding:12px;"><b><?= $r['email']; ?></b></td>
                <td style="padding:12px;">
                    <?php
                    // Bikin warna badge beda antara admin & pelanggan
                    if($r['role'] == 'admin') {
                        echo "<span style='background:#f44336; color:white; padding:3px 8px; border-radius:12px; font-size:12px;'>Admin</span>";
                    } else {
                        echo "<span style='background:#2196F3; color:white; padding:3px 8px; border-radius:12px; font-size:12px;'>Pelanggan</span>";
                    }
                    ?>
                </td>
                <td style="padding:12px; text-align:center;">
                    <a href="kelola_users.php?edit=<?= $r['id_user']; ?>" style="background:#ff9800; color:white; padding:6px 12px; text-decoration:none; border-radius:4px; font-size:14px;">Edit</a>
                    <a href="kelola_users.php?hapus=<?= $r['id_user']; ?>" onclick="return confirm('Yakin ingin menghapus akun ini?');" style="background:#f44336; color:white; padding:6px 12px; text-decoration:none; border-radius:4px; font-size:14px; margin-left:5px;">Hapus</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>

        <?php if(isset($_GET['aksi']) && $_GET['aksi'] == 'tambah'): ?>
        <div class="form-container" style="max-width:100%; margin-top:30px; border-left:4px solid #4CAF50;">
            <div style="display:flex; justify-content:space-between; align-items:center;">
                <h3 style="color:#2e7d32;">Input Akun Baru</h3>
                <a href="kelola_users.php" style="color:#f44336; text-decoration:none; font-weight:bold;">✕ Tutup Form</a>
            </div>
            <form action="kelola_users.php" method="POST" style="margin-top:15px;">
                <div class="form-group">
                    <label>Email Akun</label>
                    <input type="email" name="email" placeholder="Contoh: user@foodvation.com" required>
                </div>
                <div class="form-group">
                    <label>Password Akun</label>
                    <input type="text" name="password" placeholder="Minimal 6 karakter" required>
                </div>
                <div class="form-group">
                    <label>Role / Hak Akses</label>
                    <select name="role" required style="width:100%; padding:10px; border:1px solid #ccc; border-radius:5px;">
                        <option value="pelanggan">Pelanggan</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <button type="submit" name="tambah_user" class="btn-submit" style="background:#4CAF50;">Simpan Data Akun</button>
            </form>
        </div>
        <?php endif; ?>

        <?php 
        if(isset($_GET['edit'])): 
            $id_ed = (int)$_GET['edit'];
            $target = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM users WHERE id_user=$id_ed"));
        ?>
        <div class="form-container" style="max-width:100%; margin-top:30px; border-left:4px solid #ff9800;">
            <div style="display:flex; justify-content:space-between; align-items:center;">
                <h3 style="color:#e65100;">Edit Informasi Akun: <?= $target['email']; ?></h3>
                <a href="kelola_users.php" style="color:#f44336; text-decoration:none; font-weight:bold;">✕ Tutup Form</a>
            </div>
            <form action="kelola_users.php" method="POST" style="margin-top:15px;">
                <input type="hidden" name="id_user" value="<?= $target['id_user']; ?>">
                <div class="form-group">
                    <label>Email Akun</label>
                    <input type="email" name="email" value="<?= $target['email']; ?>" required>
                </div>
                <div class="form-group">
                    <label>Password Akun</label>
                    <input type="text" name="password" value="<?= $target['password']; ?>" required>
                </div>
                <div class="form-group">
                    <label>Role / Hak Akses</label>
                    <select name="role" required style="width:100%; padding:10px; border:1px solid #ccc; border-radius:5px;">
                        <option value="pelanggan" <?= ($target['role'] == 'pelanggan') ? 'selected' : ''; ?>>Pelanggan</option>
                        <option value="admin" <?= ($target['role'] == 'admin') ? 'selected' : ''; ?>>Admin</option>
                    </select>
                </div>
                <button type="submit" name="update_user" class="btn-submit" style="background:#ff9800;">Simpan Perubahan</button>
            </form>
        </div>
        <?php endif; ?>

    </div>
</body>
</html>