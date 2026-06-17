<?php
// profil.php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Profil Akun - Foodvation</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <nav class="navbar" style="display:flex; align-items:center; padding:10px 20px;">
        <a href="dashboard.php" style="color:white; text-decoration:none; font-weight:bold;">⬅️ Kembali ke Dashboard</a>
        <span style="color:#03a9f4; font-weight:bold; margin-left:auto;"> JALUR API MODE (PROFIL)</span>
    </nav>

    <div class="container" style="margin-top: 30px; max-width: 600px;">
        <div style="background: white; padding: 25px; border-radius: 8px; border: 1px solid #e0e0e0; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
            <h2 style="color: #03a9f4; margin-top:0; border-bottom: 2px solid #03a9f4; padding-bottom: 10px;">👤 Kelola Informasi Akun</h2>
            <p style="color:#777; font-size:13px;">Data di bawah ini dimanipulasi secara asinkronus menggunakan JavaScript Fetch API.</p>
            
            <form id="formProfilAPI" onsubmit="simpanProfilLewatAPI(event)" style="margin-top: 20px;">
                <div style="margin-bottom: 15px;">
                    <label style="font-weight:bold; color:#555; display:block; margin-bottom:5px;">Nama Lengkap:</label>
                    <input type="text" id="api_nama_lengkap" required style="width:100%; padding:10px; border:1px solid #ccc; border-radius:4px; box-sizing: border-box;">
                </div>
                <div style="margin-bottom:20px;">
                    <label style="font-weight:bold; color:#555; display:block; margin-bottom:5px;">Nomor Telepon:</label>
                    <input type="text" id="api_no_telepon" required style="width:100%; padding:10px; border:1px solid #ccc; border-radius:4px; box-sizing: border-box;">
                </div>
                <button type="submit" style="background: #03a9f4; color: white; border: none; padding: 10px 20px; border-radius: 4px; cursor: pointer; font-weight: bold; width: 100%;">Simpan Perubahan (POST via API)</button>
            </form>
        </div>
    </div>

    <script>
        const KUNCI_API_RAHASIA = "FoodvationSecretKey123";

        window.addEventListener('DOMContentLoaded', ambilProfilPengguna);

        async function ambilProfilPengguna() {
            try {
                let response = await fetch('api_profil.php', {
                    method: 'GET',
                    headers: { 'X-API-KEY': KUNCI_API_RAHASIA }
                });
                let hasil = await response.json();
                if (hasil.status === 'success') {
                    document.getElementById('api_nama_lengkap').value = hasil.data.nama_lengkap || '';
                    document.getElementById('api_no_telepon').value = hasil.data.no_telepon || '';
                }
            } catch (err) { console.error("Gagal sinkronisasi data via API:", err); }
        }

        async function simpanProfilLewatAPI(e) {
            e.preventDefault();
            let dataForm = new FormData();
            dataForm.append('nama_lengkap', document.getElementById('api_nama_lengkap').value);
            dataForm.append('no_telepon', document.getElementById('api_no_telepon').value);

            try {
                let response = await fetch('api_profil.php', {
                    method: 'POST',
                    headers: { 'X-API-KEY': KUNCI_API_RAHASIA },
                    body: dataForm
                });
                let hasil = await response.json();
                alert(hasil.message);
                if(hasil.status === 'success') ambilProfilPengguna();
            } catch (err) { alert("Gagal memperbarui profil."); }
        }
    </script>
</body>
</html>