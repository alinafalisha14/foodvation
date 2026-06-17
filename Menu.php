<?php
//menu.php
session_start();
require_once 'koneksi.php';

//proteksi Otorisasi: Pastikan user sudah login DAN pertannya adalah 'admin'
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo "<script>alert('Akses Ditolak! Halaman ini khusus Admin Restoran.'); window.location='dashboard.php';</script>";
    exit();
}

$pesan ="";

// =======================
//  FUNGSI CRUD - FULL PHP
// =======================

// 1. CREATE Fungsi Tambah Data Menu Baru
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['tambah_menu'])) {
    $restoran_id    = (int)$_POST['restoran_id'];
    $nama_menu      = mysqli_real_escape_string($koneksi, $_POST['nama_menu']);
    $harga          = (int)$_POST['harga'];
    $gambar_makanan = "default_kuliner.jpg";// Gambar default sementara sesuai struktur database

    $insert = mysqli_query($koneksi, "INSERT INTO daftar_menu (restoran_id, nama_menu, harga, gambar_makanan) VALUES($restoran_id, '$nama_menu', $harga, '$gambar_makanan')");
    if($insert){
        $pesan = "Menu kuliner baru berhasil ditambahkan ke data base!";    
    }else{
        $pesan = "Gagal menambahkan menu: ".mysqli_error($koneksi);
    }
}

// 2. UPDATE Fungsi Edit Data Menu
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_menu'])) {
    $id_menu_edit = (int)$_POST['id_menu'];
    $restoran_id  = (int)$_POST['restoran_id'];
    $nama_menu    = mysqli_real_escape_string($koneksi, $_POST['nama_menu']);
    $harga        = (int)$_POST['harga'];

    $update = mysqli_query($koneksi, "UPDATE daftar_menu SET restoran_id=$restoran_id, nama_menu='$nama_menu', harga=$harga WHERE id_menu=$id_menu_edit");
    if($update) {
        $pesan = "Informasi menu berhasil diperbarui";
    }
}

// 3. DELETE Fungsi Hapus Data Menu
if(isset($_GET['hapus'])) {
    $id_hapus = (int)$_GET['hapus'];
    mysqli_query($koneksi, "DELETE FROM daftar_menu WHERE id_menu=$id_hapus");
    header("Location: Menu.php");
    exit();
}

//4. READ Mengambil data menu + JOIN nama restoran agar tampil informatif di tabel
$list_menu = mysqli_query($koneksi, "SELECT daftar_menu.*, restoran.nama_restoran FROM daftar_menu JOIN restoran ON daftar_menu.restoran_id = restoran.id_restoran");

//Mengambil list restoran untuk dropdown <select> pada from tambah/edit:
$list_resto = mysqli_query($koneksi, "SELECT id_restoran, nama_restoran FROM restoran");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Panel Admin - Kelola Menu</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <nav class="navbar" style="background:#333;">
        <a href="dashboard.php">⬅️ Dashboard Utama</a>
        <a href="admin.php" style="color:#ff9800; margin-left:20px; text-decoration:none;">🏢 Kelola Resto</a>
        <span style="color:#4CAF50; font-weight:bold; margin-left:auto; padding-right:20px;">🛡️ ADMINISTRATOR MODE (MENU)</span>
    </nav>

    <div class="container">
        <h2>Panel Kelola Data Menu Makanan</h2>
        <p style="color:#666; margin-bottom:15px;">Halaman khusus admin untuk mengelola entitas menu kuliner (CRUD Mandiri Kelompok 3).</p>
        
        <?php if($pesan) echo "<div class='alert' style='background:#c8e6c9; color:#2e7d32;'>$pesan</div>"; ?>
        
        <div style="margin-bottom: 15px; text-align: right;">
            <a href="Menu.php?aksi=tambah" style="background:#4CAF50; color:white; padding:10px 15px; text-decoration:none; border-radius:5px; font-weight:bold;">➕ Tambah Menu Baru</a>
        </div>

        <table border="1" width="100%" style="border-collapse:collapse; text-align:left; background:white;">
            <tr style="background:#eee;">
                <th style="padding:12px; width:60px;">ID Menu</th>
                <th style="padding:12px;">Nama Menu</th>
                <th style="padding:12px;">Restoran Pemilik</th>
                <th style="padding:12px;">Harga Kelompok</th>
                <th style="padding:12px; text-align:center; width:160px;">Aksi CRUD</th>
            </tr>
            <?php while($m = mysqli_fetch_assoc($list_menu)): ?>
            <tr>
                <td style="padding:12px;"><?= $m['id_menu']; ?></td>
                <td style="padding:12px;"><b><?= $m['nama_menu']; ?></b></td>
                <td style="padding:12px;"><span style="background:#e3f2fd; color:#0d47a1; padding:4px 8px; border-radius:4px; font-size:13px; font-weight:bold;"><?= $m['nama_restoran']; ?></span></td>
                <td style="padding:12px;"><?= format_rupiah($m['harga']); ?></td>
                <td style="padding:12px; text-align:center;">
                    <a href="Menu.php?edit=<?= $m['id_menu']; ?>" style="background:#ff9800; color:white; padding:6px 12px; text-decoration:none; border-radius:4px; font-size:14px;">Edit</a>
                    <a href="Menu.php?hapus=<?= $m['id_menu']; ?>" onclick="return confirm('Yakin ingin menghapus menu ini?');" style="background:#f44336; color:white; padding:6px 12px; text-decoration:none; border-radius:4px; font-size:14px; margin-left:5px;">Hapus</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>

        <?php if(isset($_GET['aksi']) && $_GET['aksi'] == 'tambah'): ?>
        <div class="form-container" style="max-width:100%; margin-top:30px; border-left:4px solid #4CAF50;">
            <div style="display:flex; justify-content:space-between; align-items:center;">
                <h3 style="color:#2e7d32;">Input Menu Baru</h3>
                <a href="Menu.php" style="color:#f44336; text-decoration:none; font-weight:bold;">✕ Tutup Form</a>
            </div>
            <form action="Menu.php" method="POST" style="margin-top:15px;">
                <div class="form-group">
                    <label>Pilih Restoran Asal</label>
                    <select name="restoran_id" required style="width:100%; padding:8px; border:1px solid #ccc; border-radius:4px;">
                        <option value="">-- Pilih Restoran Pendukung --</option>
                        <?php 
                        mysqli_data_seek($list_resto, 0); // Reset pointer query agar bisa dibaca ulang
                        while($r = mysqli_fetch_assoc($list_resto)): 
                        ?>
                            <option value="<?= $r['id_restoran']; ?>"><?= $r['nama_restoran']; ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Nama Menu</label>
                    <input type="text" name="nama_menu" placeholder="Contoh: Es Jeruk Segar" required>
                </div>
                <div class="form-group">
                    <label>Harga Menu (Rp)</label>
                    <input type="number" name="harga" placeholder="Contoh: 15000" required>
                </div>
                <button type="submit" name="tambah_menu" class="btn-submit" style="background:#4CAF50;">Simpan Data Menu</button>
            </form>
        </div>
        <?php endif; ?>

        <?php 
        if(isset($_GET['edit'])): 
            $id_ed = (int)$_GET['edit'];
            $target = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM daftar_menu WHERE id_menu=$id_ed"));
        ?>
        <div class="form-container" style="max-width:100%; margin-top:30px; border-left:4px solid #ff9800;">
            <div style="display:flex; justify-content:space-between; align-items:center;">
                <h3 style="color:#e65100;">Edit Menu: <?= $target['nama_menu']; ?></h3>
                <a href="Menu.php" style="color:#f44336; text-decoration:none; font-weight:bold;">✕ Tutup Form</a>
            </div>
            <form action="Menu.php" method="POST" style="margin-top:15px;">
                <input type="hidden" name="id_menu" value="<?= $target['id_menu']; ?>">
                <div class="form-group">
                    <label>Pilih Restoran</label>
                    <select name="restoran_id" required style="width:100%; padding:8px; border:1px solid #ccc; border-radius:4px;">
                        <?php 
                        mysqli_data_seek($list_resto, 0);
                        while($r = mysqli_fetch_assoc($list_resto)): 
                        ?>
                            <option value="<?= $r['id_restoran']; ?>" <?= $r['id_restoran'] == $target['restoran_id'] ? 'selected' : ''; ?>>
                                <?= $r['nama_restoran']; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Nama Menu</label>
                    <input type="text" name="nama_menu" value="<?= $target['nama_menu']; ?>" required>
                </div>
                <div class="form-group">
                    <label>Harga (Rp)</label>
                    <input type="number" name="harga" value="<?= $target['harga']; ?>" required>
                </div>
                <button type="submit" name="update_menu" class="btn-submit" style="background:#ff9800;">Simpan Perubahan Menu</button>
            </form>
        </div>
        <?php endif; ?>

    </div>
</body>
</html>
