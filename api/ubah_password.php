<?php
header("Content-Type: application/json");
require 'koneksi.php';

$user_id = $_POST['user_id'] ?? '';
$password_lama = $_POST['password_lama'] ?? '';
$password_baru = $_POST['password_baru'] ?? '';

if(empty($user_id) || empty($password_lama) || empty($password_baru)) {
    echo json_encode(["success"=>false, "message"=>"Semua field harus diisi"]);
    exit;
}

// Cari user
$query = $conn->query("SELECT password FROM users WHERE id='$user_id'");
if($query->num_rows > 0) {
    $user = $query->fetch_assoc();
    
    // Verifikasi password lama
    if(password_verify($password_lama, $user['password'])) {
        // Hash password baru
        $hashed_baru = password_hash($password_baru, PASSWORD_BCRYPT);
        
        $stmt = $conn->prepare("UPDATE users SET password=? WHERE id=?");
        $stmt->bind_param("si", $hashed_baru, $user_id);
        
        if($stmt->execute()) {
            echo json_encode(["success"=>true, "message"=>"Password berhasil diubah"]);
        } else {
            echo json_encode(["success"=>false, "message"=>"Gagal merubah password"]);
        }
    } else {
        echo json_encode(["success"=>false, "message"=>"Password lama salah"]);
    }
} else {
    echo json_encode(["success"=>false, "message"=>"User tidak ditemukan"]);
}
?>
