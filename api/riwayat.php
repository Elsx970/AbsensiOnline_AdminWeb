<?php
header('Content-Type: application/json');
require_once 'koneksi.php';

$user_id = $_GET['user_id'] ?? '';

if(empty($user_id)) {
    echo json_encode(['success' => false, 'message' => 'user_id wajib diisi']);
    exit;
}

$stmt = $conn->prepare("SELECT a.*, l.nama_lokasi FROM absensi a LEFT JOIN lokasi l ON a.lokasi_id = l.id WHERE a.user_id = ? ORDER BY a.tanggal DESC, a.jam_masuk DESC LIMIT 50");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$data = [];
while($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode(['success' => true, 'data' => $data]);
