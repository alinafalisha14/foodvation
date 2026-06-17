<?php
// dashboard.php
session_start();
require_once 'koneksi.php';

//Memiliki Otorisasi (Tidak bisa di-hit tanpa login)
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

//  List Data (Mengambil seluruh daftar restoran)
$query_resto = mysqli_query($koneksi, "SELECT * FROM restoran");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Dashboard - Foodvation</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <nav class="navbar">
        <a href="dashboard.php" style="text-decoration:none; color:inherit; font-weight:bold;">🍽️ FOODVATION</a>
        <div>
            <span>Halo, <?= $_SESSION['email']; ?> (<b><?= strtoupper($_SESSION['role']); ?></b>)</span>
           
            <!-- Link ke Halaman Baru -->
            <a href="profil.php" style="margin-left:15px; text-decoration:none; font-weight:bold; color:#03a9f4;">👤 Profil Saya</a>
            <a href="riwayat.php" style="margin-left:15px; text-decoration:none; font-weight:bold; color:#ff9800;">📋 Riwayat Booking</a>

            <?php if($_SESSION['role'] === 'admin'): ?>
                <a href="admin.php" style="margin-left:15px; text-decoration:underline;">Kelola Resto</a>
            <?php endif; ?>
            <a href="logout.php" class="btn-logout" style="margin-left:10px; color:#4CAF50; font-weight:bold;">Logout</a>
        </div>
    </nav>

    <div class="container" style="margin-top: 20px;">
        <h2>Daftar Restoran Pilihan</h2>
        <p style="color:#666; margin-bottom:20px;">Pilih restoran untuk melihat menu dan melakukan booking meja.</p>
        <div style="margin-bottom: 20px;">
    <form action="cari_resto.php" method="POST" style="display: flex; gap: 10px;">
        <input type="text" name="keyword" placeholder="Cari nama restoran..." style="padding: 8px; width: 250px; border-radius: 4px; border: 1px solid #ccc;">
        <button type="submit" style="background: #2196f3; color: white; padding: 8px 15px; border: none; border-radius: 4px; cursor: pointer; font-weight: bold;">Cari via API</button>
    </form>
</div>
        
        <div class="grid-cards">
            <?php while($resto = mysqli_fetch_assoc($query_resto)): ?>
                <a href="restoran.php?id=<?= $resto['id_restoran']; ?>" class="card">
                    <img src="assets/images/restoran/<?= $resto['gambar_restoran']; ?>" alt="<?= $resto['nama_restoran']; ?>">
                    <div class="card-content">
                        <div class="card-title"><?= $resto['nama_restoran']; ?></div>
                        <p style="font-size:14px; color:#666;"><?= $resto['alamat']; ?></p>
                    </div>
                </a>
            <?php endwhile; ?>
        </div>
    </div>
</body>
</html>