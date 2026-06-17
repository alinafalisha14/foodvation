<?php
// restoran.php
session_start();
require_once 'koneksi.php';

$id_restoran = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// =========================================================================
//  MEMILIKI ENDPOINT 2 METHOD (SISI BACKEND)
// =========================================================================

// -------------------------------------------------------------------------
// METHOD 1: GET (READ DATA RESTORAN & MENU & MEJA)
// -------------------------------------------------------------------------
if ($id_restoran > 0) {
    // Ambil Profil Restoran
    $q_resto = mysqli_query($koneksi, "SELECT * FROM restoran WHERE id_restoran = $id_restoran");
    $data_resto = mysqli_fetch_assoc($q_resto);
    
    // Ambil Daftar Menu
    $q_menu = mysqli_query($koneksi, "SELECT * FROM daftar_menu WHERE restoran_id = $id_restoran");
    
    // Ambil Daftar Meja
    $q_meja = mysqli_query($koneksi, "SELECT * FROM meja WHERE restoran_id = $id_restoran");
}

// -------------------------------------------------------------------------
// METHOD 2: POST (CREATE RESERVASI & INSERT DETAIL PESANAN MANY-TO-MANY)
// -------------------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['proses_reservasi'])) {
    
    // Proteksi Lanjutan: Pastikan benar-benar sudah login sebelum memproses
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }

    $no_rsv     = mysqli_real_escape_string($koneksi, $_POST['no_reservasi']);
    $user_id    = (int)$_SESSION['user_id'];
    $nama_pemesan = mysqli_real_escape_string($koneksi, $_POST['nama_pemesan']);
    $resto_id   = (int)$_POST['id_restoran'];
    $meja_id    = (int)$_POST['id_meja'];
    $tanggal    = mysqli_real_escape_string($koneksi, $_POST['tanggal_reservasi']);
    $waktu      = mysqli_real_escape_string($koneksi, $_POST['waktu_reservasi']);
    
    // Array dari form detail pesanan
    $menu_ids   = $_POST['menu_id'];       // Array ID Menu
    $quantities = $_POST['jumlah_pesan'];  // Array Jumlah Pesanan per Menu

    // Mulai Transaksi SQL
    mysqli_begin_transaction($koneksi);

    try {
        // A. INSERT KE TABEL INDUK: reservasi
        $sql_rsv = "INSERT INTO reservasi (no_reservasi, user_id, restoran_id, meja_id, tanggal_reservasi, waktu_reservasi) 
                    VALUES ('$no_rsv', $user_id, $resto_id, $meja_id, '$tanggal', '$waktu')";
        mysqli_query($koneksi, $sql_rsv);
        
        // Tangkap ID Reservasi yang baru saja di-generate secara otomatis
        $id_reservasi_baru = mysqli_insert_id($koneksi);

        // B. LOOPING INSERT KE TABEL PIVOT MANY-TO-MANY: detail_pesanan_reservasi
        foreach ($menu_ids as $index => $m_id) {
            $qty = (int)$quantities[$index];
            
            // Hanya simpan menu yang jumlah pesanannya diisi di atas 0
            if ($qty > 0) {
                $m_id_clean = (int)$m_id;
                $sql_detail = "INSERT INTO detail_pesanan_reservasi (reservasi_id, menu_id, jumlah_pesan) 
                               VALUES ($id_reservasi_baru, $m_id_clean, $qty)";
                mysqli_query($koneksi, $sql_detail);
            }
        }

        // Commit (Simpan Permanen) jika semua query berhasil
        mysqli_commit($koneksi);
        echo "<script>alert('Reservasi Berhasil Dibuat! Kode Booking: $no_rsv'); window.location='dashboard.php';</script>";
        exit();

    } catch (Exception $e) {
        // Rollback (Batalkan semua) jika terjadi error di tengah jalan
        mysqli_rollback($koneksi);
        $error_transaksi = "Gagal memproses reservasi: " . $e->getMessage();
    }
}

// Generate Default Nomor Reservasi untuk Form
$default_no_rsv = "RSV-" . date("Ymd") . "-" . rand(100,999);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title><?= isset($data_resto['nama_restoran']) ? $data_resto['nama_restoran'] : 'Restoran'; ?> - Detail</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <nav class="navbar">
        <a href="dashboard.php">⬅️ Kembali ke Dashboard</a>
        <?php if(isset($_SESSION['email'])): ?>
            <span>User: <b><?= $_SESSION['email']; ?></b></span>
        <?php else: ?>
            <a href="login.php" style="text-decoration:underline;">Login Akun</a>
        <?php endif; ?>
    </nav>

    <div class="container">
        <?php if(!isset($data_resto)): ?>
            <div class="alert">Data restoran tidak ditemukan.</div>
        <?php else: ?>
            
            <div class="resto-header">
                <img src="assets/images/restoran/<?= $data_resto['gambar_restoran']; ?>" alt="Foto Resto">
                <div class="resto-overlay">
                    <h1><?= $data_resto['nama_restoran']; ?></h1>
                    <p>📍 <?= $data_resto['alamat']; ?></p>
                </div>
            </div>

            <div class="tabs">
                <button id="btnMenu" class="btn-tab active" onclick="gantiTab('menu')">Daftar Menu</button>
                <button id="btnMeja" class="btn-tab" onclick="gantiTab('meja')">Ketersediaan Meja</button>
            </div>

            <div id="tabMenu" class="grid-cards">
                <?php while($menu = mysqli_fetch_assoc($q_menu)): ?>
                    <div class="card" style="cursor:default;">
                        <img src="assets/images/menu/<?= $menu['gambar_makanan']; ?>" alt="Menu">
                        <div class="card-content">
                            <div class="card-title"><?= $menu['nama_menu']; ?></div>
                            <p style="color:#ff5722; font-weight:bold;"><?= format_rupiah($menu['harga']); ?></p>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>

            <div id="tabMeja" class="grid-cards" style="display:none;">
                <?php while($meja = mysqli_fetch_assoc($q_meja)): ?>
                    <?php $is_login = isset($_SESSION['user_id']) ? 'true' : 'false'; ?>
                    
                    <div class="card" onclick="pilihMeja(<?= $meja['id_meja']; ?>, <?= $meja['no_meja']; ?>, <?= $is_login; ?>)">
                        <img src="assets/images/meja/meja_default.jpg" alt="Meja">
                        <div class="card-content" style="text-align:center;">
                            <div class="card-title">Meja Nomor <?= $meja['no_meja']; ?></div>
                            <p style="background:#e8f5e9; color:#2e7d32; padding:4px; border-radius:4px; display:inline-block; font-size:14px; font-weight:bold;">
                                Kapasitas: <?= $meja['kapasitas']; ?> Orang
                            </p>
                            <p style="margin-top:10px; font-size:12px; color:#2196F3;">Klik untuk Booking 🖱️</p>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>

            <div id="formReservasiContainer" style="display:none; margin-top:40px;">
                <hr style="border:1px solid #ccc; margin-bottom:30px;">
                <div class="form-container" style="max-width:700px;">
                    <h2 style="color:#ff5722; margin-bottom:5px;">Form Detail Reservasi</h2>
                    <p style="margin-bottom:20px; color:#666;">ID Restoran: <b><?= $data_resto['nama_restoran']; ?> (ID: <?= $id_restoran; ?>)</b></p>
                    
                    <form action="restoran.php?id=<?= $id_restoran; ?>" method="POST">
                        <input type="hidden" name="id_restoran" value="<?= $id_restoran; ?>">
                        <input type="hidden" name="id_meja" id="inputMejaId" value="">

                        <div style="display:grid; grid-template-columns:1fr 1fr; gap:15px;">
                            <div class="form-group">
                                <label>Nomor Reservasi (Auto)</label>
                                <input type="text" name="no_reservasi" value="<?= $default_no_rsv; ?>" readonly style="background:#eee;">
                            </div>
                            <div class="form-group">
                                <label>ID Akun Pemesan (Auto)</label>
                                <input type="text" value="User ID: <?= isset($_SESSION['user_id']) ? $_SESSION['user_id'] : ''; ?> (<?= isset($_SESSION['email']) ? $_SESSION['email'] : ''; ?>)" readonly style="background:#eee;">
                            </div>
                            <div class="form-group">
                                <label>Nama pemesan</label>
                                <input type="text" name="nama_pemesan" value="<?= isset($_SESSION['email']) ? $_SESSION['email'] : 'Tanpa Nama'; ?>" readonly style="background:#eee;">
                            </div>
                        </div>

                        <div style="display:grid; grid-template-columns:1fr 1fr; gap:15px;">
                            <div class="form-group">
                                <label>Meja Terpilih</label>
                                <input type="text" id="tampilanNoMeja" value="" readonly style="background:#e3f2fd; font-weight:bold; color:#1565c0;">
                            </div>
                            <div class="form-group">
                                <label>Tanggal Reservasi</label>
                                <input type="date" name="tanggal_reservasi" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Waktu Kedatangan</label>
                            <input type="time" name="waktu_reservasi" required>
                        </div>

                        <h3 style="margin:25px 0 10px 0; border-bottom:2px solid #ff5722; padding-bottom:5px;">Daftar Pesanan Menu (Relasi Transaksi)</h3>
                        <p style="font-size:13px; color:#666; margin-bottom:15px;">Isi jumlah porsi pada menu yang ingin dipesan (biarkan 0 jika tidak dipesan):</p>
                        
                        <?php 
                        // Reset pointer query menu agar bisa dilooping lagi
                        $q_menu_form = mysqli_query($koneksi, "SELECT * FROM daftar_menu WHERE restoran_id = $id_restoran");
                        while($item = mysqli_fetch_assoc($q_menu_form)): 
                        ?>
                            <div style="display:flex; justify-content:space-between; align-items:center; padding:8px 0; border-bottom:1px solid #eee;">
                                <div style="width:60%;">
                                    <b><?= $item['nama_menu']; ?></b><br>
                                    <span style="font-size:13px; color:#ff5722;"><?= format_rupiah($item['harga']); ?></span>
                                </div>
                                <div style="width:30%; display:flex; align-items:center; gap:10px;">
                                    <label style="font-size:12px; margin:0;">Qty:</label>
                                    <input type="hidden" name="menu_id[]" value="<?= $item['id_menu']; ?>">
                                    <input type="number" name="jumlah_pesan[]" value="0" min="0" max="50" style="padding:5px; text-align:center;">
                                </div>
                            </div>
                        <?php endwhile; ?>

                        <button type="submit" name="proses_reservasi" class="btn-submit" style="margin-top:25px;">Konfirmasi & Simpan Transaksi</button>
                    </form>
                </div>
            </div>

        <?php endif; ?>
    </div>

    <script>
        function gantiTab(tab) {
            let tMenu  = document.getElementById('tabMenu');
            let tMeja  = document.getElementById('tabMeja');
            let bMenu  = document.getElementById('btnMenu');
            let bMeja  = document.getElementById('btnMeja');
            let fRsv   = document.getElementById('formReservasiContainer');

            if (tab === 'menu') {
                tMenu.style.display = 'grid';
                tMeja.style.display = 'none';
                bMenu.classList.add('active');
                bMeja.classList.remove('active');
                fRsv.style.display = 'none'; // Sembunyikan form jika balik ke menu
            } else {
                tMenu.style.display = 'none';
                tMeja.style.display = 'grid';
                bMenu.classList.remove('active');
                bMeja.classList.add('active');
            }
        }

        function pilihMeja(idMeja, noMeja, isLogin) {
            // Logika Proteksi: Jika user belum login saat klik kartu meja, paksa pindah
            if (!isLogin) {
                alert("Anda harus login terlebih dahulu untuk melakukan reservasi meja!");
                window.location.href = "login.php";
                return;
            }

            // Jika sudah login, isi data tersembunyi dan munculkan formulir
            document.getElementById('inputMejaId').value = idMeja;
            document.getElementById('tampilanNoMeja').value = "Meja Nomor " + noMeja;
            
            let wadahForm = document.getElementById('formReservasiContainer');
            wadahForm.style.display = 'block';
            
            // Efek gulir otomatis ke arah form
            wadahForm.scrollIntoView({ behavior: 'smooth' });
        }
    </script>
</body>
</html>