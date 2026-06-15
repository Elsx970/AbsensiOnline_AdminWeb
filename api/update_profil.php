<?php
header("Content-Type: application/json");
require 'koneksi.php';

$user_id = $_POST['user_id'] ?? '';
$program_studi = $_POST['program_studi'] ?? '';
$nomor_telpon = $_POST['nomor_telpon'] ?? '';
$email_mhs = $_POST['email_mhs'] ?? '';

if(empty($user_id)) {
    echo json_encode(["success"=>false, "message"=>"User ID required"]);
    exit;
}

$foto_query = "";
$params = [];
$types = "";

if (isset($_FILES['foto_profile']) && $_FILES['foto_profile']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = '../uploads/profiles/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    
    $fileInfo = pathinfo($_FILES['foto_profile']['name']);
    $extension = $fileInfo['extension'];
    $fileName = 'profile_' . $user_id . '_' . time() . '.' . $extension;
    $targetPath = $uploadDir . $fileName;
    
    if (move_uploaded_file($_FILES['foto_profile']['tmp_name'], $targetPath)) {
        $foto_query = ", foto_profile = ?";
        $params[] = $fileName;
        $types .= "s";
    }
}

$query = "UPDATE users SET program_studi = ?, nomor_telpon = ?, email_mhs = ? $foto_query WHERE id = ?";
$types = "sss" . $types . "i";
$params_all = [$program_studi, $nomor_telpon, $email_mhs];
foreach ($params as $p) {
    $params_all[] = $p;
}
$params_all[] = $user_id;

$stmt = $conn->prepare($query);
$stmt->bind_param($types, ...$params_all);

if($stmt->execute()) {
    echo json_encode(["success"=>true, "message"=>"Profil berhasil diupdate"]);
} else {
    echo json_encode(["success"=>false, "message"=>"Gagal update profil"]);
}
?>
