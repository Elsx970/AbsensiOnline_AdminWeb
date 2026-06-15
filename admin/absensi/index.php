<?php
require_once '../includes/db.php';
require_once '../includes/header.php';
?>

<?php
$lokasi_id = isset($_GET['lokasi_id']) ? $_GET['lokasi_id'] : '';
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$title = "Data Presensi Harian";

if (!empty($lokasi_id) && $lokasi_id != 'all') {
    $res_title = $conn->query("SELECT nama_lokasi, pertemuan FROM lokasi WHERE id = '".$conn->real_escape_string($lokasi_id)."'");
    if($res_title && $res_title->num_rows > 0) {
        $row_title = $res_title->fetch_assoc();
        $title = "Data Presensi - " . $row_title['nama_lokasi'] . " (Pert. " . $row_title['pertemuan'] . ")";
    }
} elseif ($lokasi_id == 'all') {
    $title = "Semua Data Presensi";
}

if(isset($_GET['delete'])) {
    $del_id = intval($_GET['delete']);
    $cek_foto = $conn->query("SELECT foto_masuk FROM absensi WHERE id = $del_id");
    if($cek_foto && $cek_foto->num_rows > 0) {
        $rf = $cek_foto->fetch_assoc();
        if(!empty($rf['foto_masuk']) && file_exists('../../' . $rf['foto_masuk'])) unlink('../../' . $rf['foto_masuk']);
    }
    $conn->query("DELETE FROM absensi WHERE id = $del_id");
    $param_lokasi = !empty($lokasi_id) ? "lokasi_id=$lokasi_id" : "";
    $param_search = !empty($search) ? "&search=$search" : "";
    echo "<script>window.location.href='index.php?$param_lokasi$param_search';</script>";
    exit;
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold"><?= htmlspecialchars($title) ?></h3>
</div>

<?php if(empty($lokasi_id)): ?>
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4 text-center py-5">
            <i class="bi bi-journal-check text-primary" style="font-size: 4rem;"></i>
            <h4 class="mt-3">Pilih Mata Kuliah</h4>
            <p class="text-muted">Pilih mata kuliah di bawah ini untuk melihat data kehadiran mahasiswa.</p>
            
            <div class="row justify-content-center mt-4">
                <div class="col-md-6">
                    <div class="input-group shadow-sm mb-4">
                        <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" id="searchKelas" class="form-control border-start-0" placeholder="Cari nama kelas atau pertemuan..." onkeyup="filterKelas()">
                    </div>
                </div>
            </div>
            
            <div class="row text-start" id="kelasList">
                <div class="col-md-6 mb-3">
                    <a href="?lokasi_id=all" class="text-decoration-none">
                        <div class="card bg-light border-0 shadow-sm h-100 table-hover-card">
                            <div class="card-body d-flex align-items-center">
                                <i class="bi bi-collection-fill fs-3 text-primary me-3"></i>
                                <div>
                                    <h6 class="mb-0 fw-bold text-dark">Semua Data Kelas</h6>
                                    <small class="text-muted">Tampilkan seluruh riwayat presensi</small>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <?php
                $lokasi_res = $conn->query("SELECT id, nama_lokasi, pertemuan, tanggal, jam_mulai, jam_selesai FROM lokasi ORDER BY id DESC");
                while($l = $lokasi_res->fetch_assoc()) {
                    $tgl = $l['tanggal'] ? date('d M Y', strtotime($l['tanggal'])) : '-';
                    $waktu = ($l['jam_mulai'] ? substr($l['jam_mulai'], 0, 5) : '08:00') . " - " . ($l['jam_selesai'] ? substr($l['jam_selesai'], 0, 5) : '09:40');
                    echo '
                    <div class="col-md-6 mb-3 kelas-item">
                        <a href="?lokasi_id='.$l['id'].'" class="text-decoration-none">
                            <div class="card bg-light border-0 shadow-sm h-100 table-hover-card">
                                <div class="card-body d-flex align-items-center">
                                    <i class="bi bi-journal-text fs-3 text-success me-3"></i>
                                    <div>
                                        <h6 class="mb-0 fw-bold text-dark kelas-title">'.$l['nama_lokasi'].' <span class="badge bg-primary ms-1">Pert. '.$l['pertemuan'].'</span></h6>
                                        <small class="text-muted"><i class="bi bi-calendar-event"></i> '.$tgl.' &nbsp; <i class="bi bi-clock"></i> '.$waktu.'</small>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>';
                }
                ?>
            </div>
            <style>
                .table-hover-card:hover { background-color: #e9ecef !important; transition: 0.2s; }
            </style>
            <script>
                function filterKelas() {
                    let input = document.getElementById("searchKelas").value.toLowerCase();
                    let items = document.getElementsByClassName("kelas-item");
                    
                    for (let i = 0; i < items.length; i++) {
                        let title = items[i].querySelector(".kelas-title").innerText.toLowerCase();
                        if (title.indexOf(input) > -1) {
                            items[i].style.display = "";
                        } else {
                            items[i].style.display = "none";
                        }
                    }
                }
            </script>
        </div>
    </div>
<?php else: ?>
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <a href="index.php" class="btn btn-outline-secondary me-2"><i class="bi bi-arrow-left"></i> Kembali</a>
            <a href="../laporan/export.php?lokasi_id=<?= htmlspecialchars($lokasi_id) ?>" class="btn btn-success"><i class="bi bi-file-earmark-excel"></i> Export Excel</a>
        </div>
        <form method="GET" action="" class="d-flex">
            <input type="hidden" name="lokasi_id" value="<?= htmlspecialchars($lokasi_id) ?>">
            <input type="text" name="search" class="form-control me-2" placeholder="Cari nama/NPM..." value="<?= htmlspecialchars($search) ?>">
            <button type="submit" class="btn btn-outline-primary"><i class="bi bi-search"></i></button>
        </form>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Mahasiswa</th>
                            <th>Kelas / Lokasi</th>
                            <th>Jam Masuk</th>
                            <th>Jam Selesai</th>
                            <th>Status</th>
                            <th>Foto</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $where = "WHERE 1=1";
                        if($lokasi_id != 'all') {
                            $where .= " AND a.lokasi_id = '".$conn->real_escape_string($lokasi_id)."'";
                        }
                        if(!empty($search)) {
                            $where .= " AND (u.nama LIKE '%$search%' OR u.email LIKE '%$search%')";
                        }
                        
                        $sql = "SELECT a.*, u.nama, u.email as npm, l.nama_lokasi, l.pertemuan, l.jam_selesai 
                                FROM absensi a 
                                JOIN users u ON a.user_id = u.id 
                                LEFT JOIN lokasi l ON a.lokasi_id = l.id 
                                $where
                                ORDER BY a.tanggal DESC, a.jam_masuk DESC";
                        $result = $conn->query($sql);
                        if($result->num_rows > 0):
                            $no = 1;
                            while($row = $result->fetch_assoc()):
                        ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= date('d-M-Y', strtotime($row['tanggal'])) ?></td>
                            <td>
                                <div class="fw-bold"><?= $row['nama'] ?></div>
                                <small class="text-muted"><?= $row['npm'] ?></small>
                            </td>
                            <td>
                                <?= $row['nama_lokasi'] ?? '-' ?><br>
                                <small class="text-primary">Pertemuan Ke-<?= $row['pertemuan'] ?? 1 ?></small>
                            </td>
                            <td><?= $row['jam_masuk'] ?></td>
                            <td><?= !empty($row['jam_selesai']) ? date('H:i', strtotime($row['jam_selesai'])) . ' WIB' : '-' ?></td>
                            <td>
                                <span class="badge bg-success"><?= $row['status'] ?></span>
                            </td>
                            <td>
                                <?php if(!empty($row['foto_masuk'])): ?>
                                    <a href="../../<?= $row['foto_masuk'] ?>" target="_blank">
                                        <img src="../../<?= $row['foto_masuk'] ?>" alt="Foto" width="50" height="50" class="rounded object-fit-cover border">
                                    </a>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="?delete=<?= $row['id'] ?>&lokasi_id=<?= htmlspecialchars($lokasi_id) ?>&search=<?= htmlspecialchars($search) ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Yakin ingin menghapus data presensi ini? Data yang dihapus tidak bisa dikembalikan.')"><i class="bi bi-trash"></i></a>
                            </td>
                        </tr>
                        <?php endwhile; else: ?>
                        <tr><td colspan="8" class="text-center py-4">Tidak ada data presensi.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php require_once '../includes/footer.php'; ?>
