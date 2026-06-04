<?php
// admin.php
session_start();
require_once 'koneksi.php';

// Proteksi: Pastikan user sudah login DAN memiliki role 'admin'
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo "<script>alert('Akses Ditolak! Halaman ini khusus Admin Restoran.'); window.location='dashboard.php';</script>";
    exit();
}

$pesan = "";

// =========================================================================
//  FUNGSI CRUD LENGKAP (CREATE, UPDATE, DELETE)
// =========================================================================

// 1. CREATE: Fungsi Tambah Data Restoran Baru
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['tambah_resto'])) {
    $nama   = mysqli_real_escape_string($koneksi, $_POST['nama_restoran']);
    $alamat = mysqli_real_escape_string($koneksi, $_POST['alamat']);
    $gambar = "default_resto.jpg"; // Menggunakan file gambar default sementara

    $insert = mysqli_query($koneksi, "INSERT INTO restoran (nama_restoran, alamat, gambar_restoran) VALUES ('$nama', '$alamat', '$gambar')");
    if ($insert) {
        $pesan = "Restoran baru berhasil ditambahkan ke database!";
    }
}

// 2. UPDATE: Fungsi Edit Data Restoran
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_resto'])) {
    $id_resto_edit = (int)$_POST['id_restoran'];
    $nama_baru     = mysqli_real_escape_string($koneksi, $_POST['nama_restoran']);
    $alamat_baru   = mysqli_real_escape_string($koneksi, $_POST['alamat']);

    $update = mysqli_query($koneksi, "UPDATE restoran SET nama_restoran='$nama_baru', alamat='$alamat_baru' WHERE id_restoran=$id_resto_edit");
    if ($update) {
        $pesan = "Informasi Restoran berhasil diperbarui!";
    }
}

// 3. DELETE: Fungsi Hapus Data Restoran
if (isset($_GET['hapus'])) {
    $id_hapus = (int)$_GET['hapus'];
    mysqli_query($koneksi, "DELETE FROM restoran WHERE id_restoran=$id_hapus");
    header("Location: admin.php");
    exit();
}

// 4. READ: Mengambil seluruh daftar restoran untuk ditampilkan di tabel
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
        <h2>Panel Kelola Data Restoran</h2>
        <p style="color:#666; margin-bottom:15px;">Halaman khusus admin untuk mengelola entitas restoran (Tanpa modul transaksi/order).</p>
        
        <?php if($pesan) echo "<div class='alert' style='background:#c8e6c9; color:#2e7d32;'>$pesan</div>"; ?>
        
        <div style="margin-bottom: 15px; text-align: right;">
            <a href="admin.php?aksi=tambah" style="background:#4CAF50; color:white; padding:10px 15px; text-decoration:none; border-radius:5px; font-weight:bold;">➕ Tambah Restoran Baru</a>
        </div>

        <table border="1" width="100%" style="border-collapse:collapse; text-align:left; background:white;">
            <tr style="background:#eee;">
                <th style="padding:12px; width:60px;">ID</th>
                <th style="padding:12px;">Nama Restoran</th>
                <th style="padding:12px;">Alamat</th>
                <th style="padding:12px; text-align:center; width:160px;">Aksi CRUD</th>
            </tr>
            <?php while($r = mysqli_fetch_assoc($list_resto)): ?>
            <tr>
                <td style="padding:12px;"><?= $r['id_restoran']; ?></td>
                <td style="padding:12px;"><b><?= $r['nama_restoran']; ?></b></td>
                <td style="padding:12px;"><?= $r['alamat']; ?></td>
                <td style="padding:12px; text-align:center;">
                    <a href="admin.php?edit=<?= $r['id_restoran']; ?>" style="background:#ff9800; color:white; padding:6px 12px; text-decoration:none; border-radius:4px; font-size:14px;">Edit</a>
                    <a href="admin.php?hapus=<?= $r['id_restoran']; ?>" onclick="return confirm('Yakin ingin menghapus restoran ini?');" style="background:#f44336; color:white; padding:6px 12px; text-decoration:none; border-radius:4px; font-size:14px; margin-left:5px;">Hapus</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>

        <?php if(isset($_GET['aksi']) && $_GET['aksi'] == 'tambah'): ?>
        <div class="form-container" style="max-width:100%; margin-top:30px; border-left:4px solid #4CAF50;">
            <div style="display:flex; justify-content:space-between; align-items:center;">
                <h3 style="color:#2e7d32;">Input Restoran Baru</h3>
                <a href="admin.php" style="color:#f44336; text-decoration:none; font-weight:bold;">✕ Tutup Form</a>
            </div>
            <form action="admin.php" method="POST" style="margin-top:15px;">
                <div class="form-group">
                    <label>Nama Restoran</label>
                    <input type="text" name="nama_restoran" placeholder="Contoh: Restoran Sedap Malam" required>
                </div>
                <div class="form-group">
                    <label>Alamat Lengkap</label>
                    <input type="text" name="alamat" placeholder="Contoh: Jl. Raya Universitas No. 5" required>
                </div>
                <button type="submit" name="tambah_resto" class="btn-submit" style="background:#4CAF50;">Simpan Data Restoran</button>
            </form>
        </div>
        <?php endif; ?>

        <?php 
        if(isset($_GET['edit'])): 
            $id_ed = (int)$_GET['edit'];
            $target = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM restoran WHERE id_restoran=$id_ed"));
        ?>
        <div class="form-container" style="max-width:100%; margin-top:30px; border-left:4px solid #ff9800;">
            <div style="display:flex; justify-content:space-between; align-items:center;">
                <h3 style="color:#e65100;">Edit Informasi: <?= $target['nama_restoran']; ?></h3>
                <a href="admin.php" style="color:#f44336; text-decoration:none; font-weight:bold;">✕ Tutup Form</a>
            </div>
            <form action="admin.php" method="POST" style="margin-top:15px;">
                <input type="hidden" name="id_restoran" value="<?= $target['id_restoran']; ?>">
                <div class="form-group">
                    <label>Nama Restoran</label>
                    <input type="text" name="nama_restoran" value="<?= $target['nama_restoran']; ?>" required>
                </div>
                <div class="form-group">
                    <label>Alamat Lengkap</label>
                    <input type="text" name="alamat" value="<?= $target['alamat']; ?>" required>
                </div>
                <button type="submit" name="update_resto" class="btn-submit" style="background:#ff9800;">Simpan Perubahan</button>
            </form>
        </div>
        <?php endif; ?>

    </div>
</body>
</html>