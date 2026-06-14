<?php
header("Content-Type: application/json");
require 'koneksi.php';
$npm = $_POST['npm'] ?? '';
$password = $_POST['password'] ?? '';

$stmt = $conn->prepare("SELECT id, nama, email, password FROM users WHERE email = ? AND role = 'user'");
$stmt->bind_param("s", $npm);
$stmt->execute();
$res = $stmt->get_result();

if($res->num_rows > 0) {
    $user = $res->fetch_assoc();
    if(password_verify($password, $user['password'])) {
        echo json_encode(["success"=>true, "user"=>["id"=>$user['id'], "nama"=>$user['nama'], "npm"=>$user['email']]]);
    } else {
        echo json_encode(["success"=>false, "message"=>"Password salah"]);
    }
} else {
    echo json_encode(["success"=>false, "message"=>"Mahasiswa tidak ditemukan"]);
}
?>