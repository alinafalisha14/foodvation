<?php
//Meja.php
session_start();
require_once 'koneksi.php';

//Proteksi Otorisasi: Hanya Admin yang bisa mengelola meja retoran
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo"<script>alert('Akses Ditolak! Halaman ini Khusus Admin Restoran.'); window.location ='dashboard.php';</script>";
    exit();
}

$pesan = "";

// =======================
//  FUNGSI CRUD DATA MEJA
// =======================

// 1. CREATE: Tambah Meja Baru
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['tambah_meja'])) {
    $restoran_id = (int)$_POST['restoran_id'];
    $no_meja     = (int)$_POST['no_meja'];
    $kapasitas   = (int)$_POST['kapasitas'];
    $gambar_meja = "";

    $insert = mysqli_query($koneksi, "INSERT INTO meja (restoran_id, no_meja, kapasitas, gambar_meja) VALUES($restoran_id, $no_meja, $kapasitas,'$gambar_meja')");
    if($insert){
        $pesan = "Konfigurasi meja baru berhasil ditambahkan!";
    }else{
        $pesan = "Gagal menambahkan meja: ". mysqli_error($koneksi);
    }
}

// 2. UPDATE Edit Nomor Meja/Kapasitas
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_meja'])) {
    $id_meja_edit = (int)$_POST['id_meja'];
    $restoran_id  = (int)$_POST['restoran_id'];
    $no_meja      = (int)$_POST['no_meja'];
    $kapasitas    = (int)$_POST['kapasitas'];

    $update = mysqli_query($koneksi, "UPDATE meja SET restoran_id=$restoran_id, no_meja=$no_meja, kapasitas=$kapasitas WHERE id_meja=$id_meja_edit");
    if ($update) {
        $pesan = "Data meja berhasil diperbarui!";
    }else{
        $pesan = "Gagal memperbarui data meja: ". mysqli_error($koneksi);
    }
}

// 3. DELETE Hapus Konfigurasi Meja
if (isset($_GET['hapus'])){
    $id_hapus = (int)$_GET['hapus'];
    mysqli_query($koneksi, "DELETE FROM meja WHERE id_meja=$id_hapus");
    header("Location: Meja.php");
    exit();
}

// READ Ambil data meja + JOIN nama restoran agar informatif di layar
$list_meja = mysqli_query($koneksi, "SELECT meja.*, restoran.nama_restoran FROM meja JOIN restoran ON meja.restoran_id = restoran.id_restoran ORDER BY restoran.nama_restoran ASC, meja.no_meja ASC");

// Ambil list restoran untuk opsi pilihan dropdown
$list_resto = mysqli_query($koneksi, "SELECT id_restoran, nama_restoran FROM restoran");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Panel Admin - Kelola Meja</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <nav class="navbar" style="background:#333;">
        <a href="dashboard.php">⬅️ Dashboard Utama</a>
        <a href="Menu.php" style="color:#4CAF50; margin-left:20px; text-decoration:none;">🍕 Kelola Menu</a>
        <span style="color:#03a9f4; font-weight:bold; margin-left:auto; padding-right:20px;">🛡️ ADMINISTRATOR MODE (MEJA)</span>
    </nav>

    <div class="container">
        <h2>Panel Kelola Tata Letak Meja Restoran</h2>
        <p style="color:#666; margin-bottom:15px;">Halaman mandiri admin untuk mengelola nomor meja dan kapasitas kursi per cabang.</p>
        
        <?php if($pesan) echo "<div class='alert' style='background:#c8e6c9; color:#2e7d32;'>$pesan</div>"; ?>
        
        <div style="margin-bottom: 15px; text-align: right;">
            <a href="Meja.php?aksi=tambah" style="background:#03a9f4; color:white; padding:10px 15px; text-decoration:none; border-radius:5px; font-weight:bold;">➕ Tambah Meja Baru</a>
        </div>

        <table border="1" width="100%" style="border-collapse:collapse; text-align:left; background:white;">
            <tr style="background:#eee;">
                <th style="padding:12px; width:80px;">ID Meja</th>
                <th style="padding:12px;">Nama Restoran</th>
                <th style="padding:12px;">Nomor Meja</th>
                <th style="padding:12px;">Kapasitas Kursi</th>
                <th style="padding:12px; text-align:center; width:160px;">Aksi CRUD</th>
            </tr>
            <?php while($row = mysqli_fetch_assoc($list_meja)): ?>
            <tr>
                <td style="padding:12px;"><?= $row['id_meja']; ?></td>
                <td style="padding:12px;"><b><?= $row['nama_restoran']; ?></b></td>
                <td style="padding:12px;"><span style="background:#e1f5fe; color:#0288d1; padding:3px 8px; border-radius:4px; font-weight:bold;">Meja <?= $row['no_meja']; ?></span></td>
                <td style="padding:12px;"><?= $row['kapasitas']; ?> Orang</td>
                <td style="padding:12px; text-align:center;">
                    <a href="Meja.php?edit=<?= $row['id_meja']; ?>" style="background:#ff9800; color:white; padding:6px 12px; text-decoration:none; border-radius:4px; font-size:14px;">Edit</a>
                    <a href="Meja.php?hapus=<?= $row['id_meja']; ?>" onclick="return confirm('Yakin menghapus konfigurasi meja ini?');" style="background:#f44336; color:white; padding:6px 12px; text-decoration:none; border-radius:4px; font-size:14px; margin-left:5px;">Hapus</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>

        <?php if(isset($_GET['aksi']) && $_GET['aksi'] == 'tambah'): ?>
        <div class="form-container" style="max-width:100%; margin-top:30px; border-left:4px solid #03a9f4;">
            <div style="display:flex; justify-content:space-between; align-items:center;">
                <h3 style="color:#0288d1;">Input Meja Baru</h3>
                <a href="Meja.php" style="color:#f44336; text-decoration:none; font-weight:bold;">✕ Tutup Form</a>
            </div>
            <form action="Meja.php" method="POST" style="margin-top:15px;">
                <div class="form-group">
                    <label>Pilih Restoran</label>
                    <select name="restoran_id" required style="width:100%; padding:8px; border:1px solid #ccc; border-radius:4px;">
                        <option value="">-- Pilih Lokasi Cabang Resto --</option>
                        <?php 
                        mysqli_data_seek($list_resto, 0);
                        while($r = mysqli_fetch_assoc($list_resto)): 
                        ?>
                            <option value="<?= $r['id_restoran']; ?>"><?= $r['nama_restoran']; ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Nomor Meja</label>
                    <input type="number" name="no_meja" placeholder="Contoh: 4" min="1" required>
                </div>
                <div class="form-group">
                    <label>Kapasitas Maksimal (Orang)</label>
                    <input type="number" name="kapasitas" placeholder="Contoh: 6" min="1" required>
                </div>
                <button type="submit" name="tambah_meja" class="btn-submit" style="background:#03a9f4;">Simpan Data Meja</button>
            </form>
        </div>
        <?php endif; ?>

        <?php 
        if(isset($_GET['edit'])): 
            $id_ed = (int)$_GET['edit'];
            $target = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM meja WHERE id_meja=$id_ed"));
        ?>
        <div class="form-container" style="max-width:100%; margin-top:30px; border-left:4px solid #ff9800;">
            <div style="display:flex; justify-content:space-between; align-items:center;">
                <h3 style="color:#e65100;">Edit Meja ID: <?= $target['id_meja']; ?></h3>
                <a href="Meja.php" style="color:#f44336; text-decoration:none; font-weight:bold;">✕ Tutup Form</a>
            </div>
            <form action="Meja.php" method="POST" style="margin-top:15px;">
                <input type="hidden" name="id_meja" value="<?= $target['id_meja']; ?>">
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
                    <label>Nomor Meja</label>
                    <input type="number" name="no_meja" value="<?= $target['no_meja']; ?>" min="1" required>
                </div>
                <div class="form-group">
                    <label>Kapasitas Kursi</label>
                    <input type="number" name="kapasitas" value="<?= $target['kapasitas']; ?>" min="1" required>
                </div>
                <button type="submit" name="update_meja" class="btn-submit" style="background:#ff9800;">Simpan Perubahan Meja</button>
            </form>
        </div>
        <?php endif; ?>

    </div>
</body>
</html>