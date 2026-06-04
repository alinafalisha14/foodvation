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
        <a href="dashboard.php">🍽️ FOODVATION</a>
        <div>
            <span>Halo, <?= $_SESSION['email']; ?> (<b><?= strtoupper($_SESSION['role']); ?></b>)</span>
            <?php if($_SESSION['role'] === 'admin'): ?>
                <a href="admin.php" style="margin-left:15px; text-decoration:underline;">Kelola Resto</a>
            <?php endif; ?>
            <a href="logout.php" class="btn-logout" style="margin-left:10px;">Logout</a>
        </div>
    </nav>

    <div class="container">
        <h2>Daftar Restoran Pilihan</h2>
        <p>Pilih restoran untuk melihat menu dan melakukan booking meja.</p>
        
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