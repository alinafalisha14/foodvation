<?php
// Wajib: Mengatur agar output file ini dibaca sebagai JSON
header('Content-Type: application/json');
require_once 'koneksi.php';

// ==========================================
// 1. OTORISASI API KEY
// ==========================================
$headers = getallheaders();
$api_key = isset($headers['X-API-KEY']) ? $headers['X-API-KEY'] : '';

if ($api_key !== 'foodvation-rahasia-2026') {
    http_response_code(401);
    echo json_encode([
        "status" => "error", 
        "message" => "Akses Ditolak! API Key tidak valid atau tidak ditemukan."
    ]);
    exit();
}

$method = $_SERVER['REQUEST_METHOD'];

// ==========================================
// 2. METHOD 1: GET (Tampilkan Detail Nota)
// ==========================================
// Dipakai saat pelanggan ingin melihat struk bukti booking mereka
if ($method === 'GET') {
    // Mengambil nomor reservasi dari parameter URL, contoh: nota_booking.php?no_reservasi=RSV-123
    $no_rsv = isset($_GET['no_reservasi']) ? mysqli_real_escape_string($koneksi, $_GET['no_reservasi']) : '';
    
    if ($no_rsv != '') {
        // Query untuk mengambil data reservasi lengkap dengan nama restoran
        $sql = "SELECT reservasi.*, restoran.nama_restoran, restoran.alamat 
                FROM reservasi 
                LEFT JOIN restoran ON reservasi.restoran_id = restoran.id_restoran 
                WHERE reservasi.no_reservasi = '$no_rsv'";
                
        $query = mysqli_query($koneksi, $sql);
        $data_nota = mysqli_fetch_assoc($query);
        
        if ($data_nota) {
            echo json_encode([
                "status" => "success",
                "message" => "Detail nota berhasil ditemukan.",
                "data" => $data_nota
            ]);
        } else {
            http_response_code(404);
            echo json_encode([
                "status" => "error",
                "message" => "Nota dengan nomor '$no_rsv' tidak ditemukan."
            ]);
        }
    } else {
        http_response_code(400);
        echo json_encode([
            "status" => "error",
            "message" => "Parameter 'no_reservasi' wajib diisi."
        ]);
    }
}

// ==========================================
// 3. METHOD 2: DELETE (Batalkan Reservasi)
// ==========================================
// Dipakai jika pelanggan ingin membatalkan pesanan mereka via API
elseif ($method === 'DELETE') {
    // Membaca data raw input karena PHP native tidak otomatis menangkap $_POST pada method DELETE
    parse_str(file_get_contents("php://input"), $delete_vars);
    $id_rsv = isset($delete_vars['id_reservasi']) ? (int)$delete_vars['id_reservasi'] : 0;

    if ($id_rsv > 0) {
        // Cek dulu apakah datanya memang ada
        $cek = mysqli_query($koneksi, "SELECT * FROM reservasi WHERE id_reservasi = $id_rsv");
        
        if (mysqli_num_rows($cek) > 0) {
            // Hapus data di tabel induk (reservasi)
            mysqli_query($koneksi, "DELETE FROM reservasi WHERE id_reservasi = $id_rsv");
            
            echo json_encode([
                "status" => "success",
                "message" => "Reservasi dengan ID $id_rsv berhasil dibatalkan/dihapus."
            ]);
        } else {
            http_response_code(404);
            echo json_encode([
                "status" => "error",
                "message" => "Data reservasi tidak ditemukan di database."
            ]);
        }
    } else {
        http_response_code(400);
        echo json_encode([
            "status" => "error",
            "message" => "Parameter 'id_reservasi' tidak valid."
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