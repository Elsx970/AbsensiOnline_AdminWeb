<?php
require_once '../includes/db.php';

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=Data_Presensi_Lengkap.xls");
header("Pragma: no-cache");
header("Expires: 0");

?>
<table border="1">
    <tr>
        <th>No</th>
        <th>Nama Mahasiswa</th>
        <th>NPM</th>
        <th>Kelas / Lokasi</th>
        <th>Tanggal</th>
        <th>Jam Masuk</th>
        <th>Jam Pulang</th>
        <th>Status</th>
    </tr>
    <?php
    $lokasi_id = isset($_GET['lokasi_id']) ? $_GET['lokasi_id'] : 'all';
    $where = "";
    if($lokasi_id != 'all') {
        $lokasi_id = $conn->real_escape_string($lokasi_id);
        $where = "WHERE a.lokasi_id = '$lokasi_id'";
    }

    $query = "
        SELECT a.*, u.nama, u.email as npm, l.nama_lokasi 
        FROM absensi a 
        JOIN users u ON a.user_id = u.id 
        LEFT JOIN lokasi l ON a.lokasi_id = l.id 
        $where
        ORDER BY a.tanggal DESC, a.jam_masuk DESC
    ";
    $result = $conn->query($query);
    if($result->num_rows > 0):
        $no = 1;
        while($row = $result->fetch_assoc()):
    ?>
    <tr>
        <td><?= $no++ ?></td>
        <td><?= $row['nama'] ?></td>
        <td>'<?= $row['npm'] ?></td>
        <td><?= $row['nama_lokasi'] ?></td>
        <td><?= $row['tanggal'] ?></td>
        <td><?= $row['jam_masuk'] ?></td>
        <td><?= $row['jam_pulang'] ?></td>
        <td><?= $row['status'] ?></td>
    </tr>
    <?php endwhile; endif; ?>
</table>
