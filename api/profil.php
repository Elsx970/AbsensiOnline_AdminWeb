<?php
header("Content-Type: application/json");
require 'koneksi.php';

$user_id = $_GET['user_id'] ?? '';

if(empty($user_id)) {
    echo json_encode(["success"=>false, "message"=>"User ID required"]);
    exit;
}

$stmt = $conn->prepare("SELECT id, nama, email as npm, foto_profile, program_studi, nomor_telpon, email_mhs FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$res = $stmt->get_result();

if($res->num_rows > 0) {
    $user = $res->fetch_assoc();
    echo json_encode(["success"=>true, "data"=>$user]);
} else {
    echo json_encode(["success"=>false, "message"=>"User not found"]);
}
?>
