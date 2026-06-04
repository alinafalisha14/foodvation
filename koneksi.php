<?php
// koneksi.php
$host     = "127.0.0.1";
$user     = "root";
$password = ""; 
$database = "foodvation";
$port	= 3307;

$koneksi = mysqli_connect($host, $user, $password, $database, $port);

if (!$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// Fungsi bantuan untuk format rupiah
function format_rupiah($angka){
    return "Rp " . number_format($angka,0,',','.');
}
?>