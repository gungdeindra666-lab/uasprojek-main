<?php
// Header agar output berupa JSON
header("Content-Type: application/json; charset=UTF-8");

// 1. Koneksi Database SQL via Laragon (Default: root / tanpa password)
$host = "localhost";
$user = "root";
$pass = "";
$db   = "rest_api";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    echo json_encode(["status" => "error", "message" => "Koneksi database gagal"]);
    exit;
}

$action = $_GET['action'] ?? '';
$method = $_SERVER['REQUEST_METHOD'];

// 2. Routing Fitur
switch ($action) {
    
    // --- FITUR 1: DATA PENGGUNA ---
    case 'pengguna':
        if ($method === 'GET') {
            $result = $conn->query("SELECT * FROM pengguna ORDER BY id DESC");
            echo json_encode(["status" => "success", "data" => $result->fetch_all(MYSQLI_ASSOC)]);
        } elseif ($method === 'POST') {
            $input = json_decode(file_get_contents("php://input"), true);
            $stmt = $conn->prepare("INSERT INTO pengguna (nama, email) VALUES (?, ?)");
            $stmt->bind_param("ss", $input['nama'], $input['email']);
            $stmt->execute();
            echo json_encode(["status" => "success", "message" => "Pengguna berhasil ditambahkan"]);
        }
        break;

    // --- FITUR 2: KELUAR & MASUK BUKU ---
    case 'transaksi':
        if ($method === 'GET') {
            // Cek riwayat keluar & masuk buku beserta data penggunanya
            $sql = "SELECT t.id, p.nama AS peminjam, t.judul_buku, t.jenis_transaksi, t.tanggal 
                    FROM transaksi_buku t 
                    JOIN pengguna p ON t.id_pengguna = p.id 
                    ORDER BY t.tanggal DESC";
            $result = $conn->query($sql);
            echo json_encode(["status" => "success", "data" => $result->fetch_all(MYSQLI_ASSOC)]);
        } elseif ($method === 'POST') {
            // Record transaksi buku (jenis_transaksi: 'keluar' atau 'masuk')
            $input = json_decode(file_get_contents("php://input"), true);
            $stmt = $conn->prepare("INSERT INTO transaksi_buku (id_pengguna, judul_buku, jenis_transaksi) VALUES (?, ?, ?)");
            $stmt->bind_param("iss", $input['id_pengguna'], $input['judul_buku'], $input['jenis_transaksi']);
            $stmt->execute();
            echo json_encode(["status" => "success", "message" => "Transaksi " . $input['jenis_transaksi'] . " buku dicatat"]);
        }
        break;

    default:
        echo json_encode(["status" => "error", "message" => "Action tidak valid. Gunakan ?action=pengguna atau ?action=transaksi"]);
        break;
}

$conn->close();