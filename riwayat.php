<?php
// riwayat.php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Riwayat Booking - Foodvation</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .table-api { width: 100%; border-collapse: collapse; margin-top: 15px; background: white; }
        .table-api th, .table-api td { border: 1px solid #dddddd; padding: 12px; text-align: left; }
        .table-api th { background: #f5f5f5; color: #333; }
        .btn-cancel { background: #e53935; color: white; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer; font-size: 13px; font-weight: bold; }
        .btn-cancel:hover { background: #d32f2f; }
    </style>
</head>
<body>
    <nav class="navbar" style="display:flex; align-items:center; padding:10px 20px;">
        <a href="dashboard.php" style="color:white; text-decoration:none; font-weight:bold;">⬅️ Kembali ke Dashboard</a>
        <span style="color:#ff9800; font-weight:bold; margin-left:auto;"> JALUR API MODE (RIWAYAT)</span>
    </nav>

    <div class="container" style="margin-top: 30px;">
        <h2>📋 Riwayat Transaksi Booking Anda</h2>
        <p style="color:#666;">Data di bawah ditarik real-time menggunakan mekanisme Fetch API GET & POST.</p>
        
        <table class="table-api">
            <thead>
                <tr>
                    <th>No Invoice</th>
                    <th>Nama Restoran</th>
                    <th>Tanggal Booking</th>
                    <th>Status Data</th>
                    <th style="text-align: center;">Aksi Jalur API</th>
                </tr>
            </thead>
            <tbody id="tabelRiwayatAPI">
                </tbody>
        </table>
    </div>

    <script>
        const KUNCI_API_RAHASIA = "FoodvationSecretKey123";

        window.addEventListener('DOMContentLoaded', ambilDaftarRiwayatPengguna);

        async function ambilDaftarRiwayatPengguna() {
            try {
                let response = await fetch('api_riwayat.php', {
                    method: 'GET',
                    headers: { 'X-API-KEY': KUNCI_API_RAHASIA }
                });
                let hasil = await response.json();
                let barisKoneksi = '';

                if (hasil.status === 'success' && hasil.data.length > 0) {
                    hasil.data.forEach(item => {
                        let statusWarna = item.status === 'Dibatalkan' ? 'red' : 'green';
                        let tombolAksiAPI = item.status === 'Dibatalkan' 
                            ? `<span style="color:gray; font-style:italic;">No Action</span>`
                            : `<button class="btn-cancel" onclick="batalkanBookingLewatAPI(${item.id_reservasi})">Cancel Booking</button>`;
                        
                        barisKoneksi += `
                            <tr>
                                <td><b>${item.no_reservasi}</b></td>
                                <td>${item.nama_restoran}</td>
                                <td>${item.tanggal_reservasi}</td>
                                <td><span style="color:${statusWarna}; font-weight:bold;">${item.status}</span></td>
                                <td style="text-align:center;">${tombolAksiAPI}</td>
                            </tr>`;
                    });
                } else {
                    barisKoneksi = `<tr><td colspan="5" style="text-align:center; color:#999; padding:20px;">Belum ada data riwayat transaksi.</td></tr>`;
                }
                document.getElementById('tabelRiwayatAPI').innerHTML = barisKoneksi;
            } catch (err) { console.error("Gagal memuat riwayat:", err); }
        }

        async function batalkanBookingLewatAPI(idReservasi) {
            if(!confirm("Apakah Anda yakin ingin membatalkan transaksi booking ini via API?")) return;

            let dataFormCancel = new FormData();
            dataFormCancel.append('id_reservasi', idReservasi);

            try {
                let response = await fetch('api_riwayat.php', {
                    method: 'POST',
                    headers: { 'X-API-KEY': KUNCI_API_RAHASIA },
                    body: dataFormCancel
                });
                let hasil = await response.json();
                alert(hasil.message);
                if(hasil.status === 'success') ambilDaftarRiwayatPengguna();
            } catch (err) { alert("Gagal memproses pembatalan."); }
        }
    </script>
</body>
</html>