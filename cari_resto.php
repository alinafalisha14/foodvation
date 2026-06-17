<?php
// Wajib: Mengatur agar output file ini dibaca sebagai JSON, bukan HTML biasa
header('Content-Type: application/json');
require_once 'koneksi.php';

// ==========================================
// 1. OTORISASI API KEY
// ==========================================
// Menangkap semua header yang dikirim oleh client (Frontend/Postman)
$headers = getallheaders();
$api_key = isset($headers['X-API-KEY']) ? $headers['X-API-KEY'] : '';

// Jika API Key tidak ada atau salah, tolak aksesnya
if ($api_key !== 'foodvation-rahasia-2026') {
    http_response_code(401);
    echo json_encode([
        "status" => "error", 
        "message" => "Akses Ditolak! API Key tidak valid atau tidak ditemukan."
    ]);
    exit(); // Hentikan script di sini
}

// Menangkap jenis method yang digunakan (GET, POST, dll)
$method = $_SERVER['REQUEST_METHOD'];

// ==========================================
// 2. METHOD 1: GET (Tampilkan Semua Restoran)
// ==========================================
// Biasanya dipakai saat halaman awal pencarian baru dibuka
if ($method === 'GET') {
    $query = mysqli_query($koneksi, "SELECT id_restoran, nama_restoran, alamat, gambar_restoran FROM restoran");
    $data = [];
    
    while($r = mysqli_fetch_assoc($query)) { 
        $data[] = $r; 
    }
    
    echo json_encode([
        "status" => "success", 
        "total_data" => count($data),
        "data" => $data
    ]);
}

// ==========================================
// 3. METHOD 2: POST (Pencarian Spesifik / Live Search)
// ==========================================
// Dipakai saat user mulai mengetik nama restoran di kolom pencarian
elseif ($method === 'POST') {
    // Tangkap keyword ketikan user
    $keyword = isset($_POST['keyword']) ? mysqli_real_escape_string($koneksi, $_POST['keyword']) : '';
    
    // Cari restoran yang namanya mengandung keyword tersebut
    $query = mysqli_query($koneksi, "SELECT id_restoran, nama_restoran, alamat, gambar_restoran FROM restoran WHERE nama_restoran LIKE '%$keyword%'");
    $data = [];
    
    while($r = mysqli_fetch_assoc($query)) { 
        $data[] = $r; 
    }
    
    if (count($data) > 0) {
        echo json_encode([
            "status" => "success", 
            "total_ditemukan" => count($data),
            "data" => $data
        ]);
    } else {
        echo json_encode([
            "status" => "not_found", 
            "message" => "Restoran dengan kata kunci '$keyword' tidak ditemukan."
        ]);
    }
} 

// ==========================================
// 4. JIKA METHOD TIDAK SESUAI
// ==========================================
else {
    http_response_code(405);
    echo json_encode([
        "status" => "error", 
        "message" => "Method $method tidak diizinkan untuk endpoint ini."
    ]);
}
?>