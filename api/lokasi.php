<?php
header("Content-Type: application/json");
require 'koneksi.php';
$res = $conn->query("SELECT * FROM lokasi ORDER BY id DESC");
$data = [];
if($res->num_rows > 0) {
    while($row = $res->fetch_assoc()){
        $data[] = $row;
    }
    echo json_encode(["success"=>true, "data"=>$data]);
} else {
    echo json_encode(["success"=>false, "data"=>[]]);
}
?>