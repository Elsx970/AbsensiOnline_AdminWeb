<?php
header("Content-Type: application/json");
require 'koneksi.php';

$user_id = $_POST['user_id'] ?? '';
$lokasi_id = $_POST['lokasi_id'] ?? 1; // Default jika flutter jadul
$lat = $_POST['latitude'] ?? '';
$lng = $_POST['longitude'] ?? '';
$jenis = $_POST['jenis'] ?? 'masuk'; // masuk / pulang
$tanggal = date('Y-m-d');
$jam = date('H:i:s');

// Handle photo upload
$foto_path = "";
if(isset($_FILES['foto'])) {
    $target_dir = "../uploads/absensi/";
    if (!file_exists($target_dir)) { mkdir($target_dir, 0777, true); }
    $file_name = time() . "_" . basename($_FILES["foto"]["name"]);
    $target_file = $target_dir . $file_name;
    if(move_uploaded_file($_FILES["foto"]["tmp_name"], $target_file)) {
        $foto_path = "uploads/absensi/" . $file_name;
    }
}

// Cek jadwal lokasi
$lokasi_query = $conn->query("SELECT tanggal, jam_mulai, jam_selesai FROM lokasi WHERE id='$lokasi_id'");
if($lokasi_query && $lokasi_query->num_rows > 0) {
    $lok = $lokasi_query->fetch_assoc();
    
    // Cek tanggal
    if(!empty($lok['tanggal']) && $lok['tanggal'] != $tanggal) {
        echo json_encode(["success"=>false, "message"=>"Gagal: Hanya bisa absen pada tanggal kelas berlangsung"]);
        exit;
    }
    
    // Cek jam masuk
    if($jenis == 'masuk') {
        if(!empty($lok['jam_selesai']) && $jam > $lok['jam_selesai']) {
            echo json_encode(["success"=>false, "message"=>"Gagal: Waktu kelas sudah berakhir"]);
            exit;
        }
    }
}

// Cek apakah sudah absen masuk hari ini untuk KELAS INI
$cek = $conn->query("SELECT id FROM absensi WHERE user_id='$user_id' AND lokasi_id='$lokasi_id' AND tanggal='$tanggal'");

if($jenis == 'masuk') {
    if($cek->num_rows > 0) {
        echo json_encode(["success"=>false, "message"=>"Sudah presensi masuk di kelas ini"]);
    } else {
        $stmt = $conn->prepare("INSERT INTO absensi (user_id, lokasi_id, tanggal, jam_masuk, latitude_masuk, longitude_masuk, foto_masuk, status) VALUES (?, ?, ?, ?, ?, ?, ?, 'Hadir')");
        $stmt->bind_param("iisssss", $user_id, $lokasi_id, $tanggal, $jam, $lat, $lng, $foto_path);
        if($stmt->execute()) echo json_encode(["success"=>true, "message"=>"Presensi masuk berhasil!"]);
        else echo json_encode(["success"=>false, "message"=>"Gagal presensi"]);
    }
} else {
    if($cek->num_rows == 0) {
        echo json_encode(["success"=>false, "message"=>"Belum presensi masuk di kelas ini"]);
    } else {
        $row = $cek->fetch_assoc();
        $abs_id = $row['id'];
        $stmt = $conn->prepare("UPDATE absensi SET jam_pulang=?, latitude_pulang=?, longitude_pulang=?, foto_pulang=? WHERE id=?");
        $stmt->bind_param("ssssi", $jam, $lat, $lng, $foto_path, $abs_id);
        if($stmt->execute()) echo json_encode(["success"=>true, "message"=>"Presensi pulang berhasil!"]);
        else echo json_encode(["success"=>false, "message"=>"Gagal presensi"]);
    }
}
?>