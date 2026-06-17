<?php
session_start();
require_once 'koneksi.php';

// Proteksi: Pastikan user sudah login DAN memiliki role 'admin'
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo "<script>alert('Akses Ditolak! Halaman ini khusus Admin.'); window.location='dashboard.php';</script>";
    exit();
}

// 1. CREATE: Tambah Restoran
if (isset($_POST['tambah_resto'])) {
    $nama   = mysqli_real_escape_string($koneksi, $_POST['nama_restoran']);
    $alamat = mysqli_real_escape_string($koneksi, $_POST['alamat']);
    $gambar = "default_resto.jpg"; // Sementara di-set default agar tidak ribet upload file

    mysqli_query($koneksi, "INSERT INTO restoran (nama_restoran, alamat, gambar_restoran) VALUES ('$nama', '$alamat', '$gambar')");
    header("Location: kelola_restoran.php");
    exit();
}

// 2. UPDATE: Edit Restoran
if (isset($_POST['update_resto'])) {
    $id     = (int)$_POST['id_restoran'];
    $nama   = mysqli_real_escape_string($koneksi, $_POST['nama_restoran']);
    $alamat = mysqli_real_escape_string($koneksi, $_POST['alamat']);
    
    mysqli_query($koneksi, "UPDATE restoran SET nama_restoran='$nama', alamat='$alamat' WHERE id_restoran=$id");
    header("Location: kelola_restoran.php");
    exit();
}

// 3. DELETE: Hapus Restoran
if (isset($_GET['hapus'])) {
    $id_hapus = (int)$_GET['hapus'];
    mysqli_query($koneksi, "DELETE FROM restoran WHERE id_restoran=$id_hapus");
    header("Location: kelola_restoran.php");
    exit();
}

// 4. READ: Ambil semua data restoran
$list_resto = mysqli_query($koneksi, "SELECT * FROM restoran");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Panel Admin - Kelola Restoran</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <nav class="navbar" style="background:#333;">
        <a href="dashboard.php">⬅️ Dashboard Utama</a>
        <span style="color:#4CAF50; font-weight:bold;">🛡️ ADMINISTRATOR MODE</span>
    </nav>

    <div class="container">
        <h2>Panel Kelola Restoran</h2>
        <p style="color:#666; margin-bottom:15px;">Manajemen data induk restoran untuk aplikasi FOODVATION.</p>
        
        <div style="margin-bottom: 15px; text-align: right;">
            <a href="kelola_restoran.php?aksi=tambah" style="background:#4CAF50; color:white; padding:10px 15px; text-decoration:none; border-radius:5px; font-weight:bold;">➕ Tambah Restoran</a>
        </div>

        <table border="1" width="100%" style="border-collapse:collapse; text-align:left; background:white;">
            <tr style="background:#eee;">
                <th style="padding:12px; width:60px;">ID</th>
                <th style="padding:12px;">Nama Restoran</th>
                <th style="padding:12px;">Alamat</th>
                <th style="padding:12px; text-align:center;">Aksi CRUD</th>
            </tr>
            <?php while($r = mysqli_fetch_assoc($list_resto)): ?>
            <tr>
                <td style="padding:12px;"><?= $r['id_restoran']; ?></td>
                <td style="padding:12px;"><b><?= $r['nama_restoran']; ?></b></td>
                <td style="padding:12px;"><?= $r['alamat']; ?></td>
                <td style="padding:12px; text-align:center;">
                    <a href="kelola_restoran.php?edit=<?= $r['id_restoran']; ?>" style="background:#2196F3; color:white; padding:6px 12px; text-decoration:none; border-radius:4px; font-size:14px; margin-right:5px;">Edit</a>
                    <a href="kelola_restoran.php?hapus=<?= $r['id_restoran']; ?>" onclick="return confirm('Yakin hapus restoran ini?');" style="background:#f44336; color:white; padding:6px 12px; text-decoration:none; border-radius:4px; font-size:14px;">Hapus</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>

        <?php if(isset($_GET['aksi']) && $_GET['aksi'] == 'tambah'): ?>
        <div class="form-container" style="margin-top:30px; border-left:4px solid #4CAF50;">
            <div style="display:flex; justify-content:space-between; align-items:center;">
                <h3 style="color:#2e7d32;">Input Restoran Baru</h3>
                <a href="kelola_restoran.php" style="color:#f44336; text-decoration:none; font-weight:bold;">✕ Tutup Form</a>
            </div>
            <form method="POST" style="margin-top:15px;">
                <div class="form-group">
                    <label>Nama Restoran</label>
                    <input type="text" name="nama_restoran" required>
                </div>
                <div class="form-group">
                    <label>Alamat Lengkap</label>
                    <input type="text" name="alamat" required>
                </div>
                <button type="submit" name="tambah_resto" class="btn-submit" style="background:#4CAF50;">Simpan Data</button>
            </form>
        </div>
        <?php endif; ?>

        <?php if(isset($_GET['edit'])): 
            $id_ed = (int)$_GET['edit'];
            $target = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM restoran WHERE id_restoran=$id_ed"));
        ?>
        <div class="form-container" style="margin-top:30px; border-left:4px solid #2196F3;">
            <div style="display:flex; justify-content:space-between; align-items:center;">
                <h3 style="color:#1565c0;">Edit Restoran: <?= $target['nama_restoran']; ?></h3>
                <a href="kelola_restoran.php" style="color:#f44336; text-decoration:none; font-weight:bold;">✕ Tutup Form</a>
            </div>
            <form method="POST" style="margin-top:15px;">
                <input type="hidden" name="id_restoran" value="<?= $target['id_restoran']; ?>">
                <div class="form-group">
                    <label>Nama Restoran</label>
                    <input type="text" name="nama_restoran" value="<?= $target['nama_restoran']; ?>" required>
                </div>
                <div class="form-group">
                    <label>Alamat Lengkap</label>
                    <input type="text" name="alamat" value="<?= $target['alamat']; ?>" required>
                </div>
                <button type="submit" name="update_resto" class="btn-submit" style="background:#2196F3;">Simpan Perubahan</button>
            </form>
        </div>
        <?php endif; ?>

    </div>
</body>
</html>