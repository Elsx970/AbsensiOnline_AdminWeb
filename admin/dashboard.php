<?php
require_once 'includes/db.php';
require_once 'includes/header.php';

// Statistik
$hari_ini = date('Y-m-d');

$total_mhs = $conn->query("SELECT COUNT(*) as total FROM users WHERE role='user'")->fetch_assoc()['total'];
$hadir_hari_ini = $conn->query("SELECT COUNT(*) as total FROM absensi WHERE tanggal='$hari_ini' AND status='Hadir'")->fetch_assoc()['total'];
$terlambat_hari_ini = $conn->query("SELECT COUNT(*) as total FROM absensi WHERE tanggal='$hari_ini' AND status='Terlambat'")->fetch_assoc()['total'];
$total_absen = $hadir_hari_ini + $terlambat_hari_ini;
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold">Dashboard Statistik</h3>
    <span class="text-muted"><?= date('d F Y') ?></span>
</div>

<div class="row">
    <div class="col-md-3 mb-4">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-2">Total Mahasiswa</h6>
                        <h2 class="fw-bold mb-0"><?= $total_mhs ?></h2>
                    </div>
                    <div class="bg-primary bg-opacity-10 p-3 rounded-3 text-primary">
                        <i class="bi bi-people-fill fs-3"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-4">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-2">Presensi Hari Ini</h6>
                        <h2 class="fw-bold mb-0"><?= $total_absen ?></h2>
                    </div>
                    <div class="bg-success bg-opacity-10 p-3 rounded-3 text-success">
                        <i class="bi bi-check-circle-fill fs-3"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4 mt-2">
    <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
        <h5 class="fw-bold">Presensi Terbaru Hari Ini</h5>
    </div>
    <div class="card-body p-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>NPM/Nama</th>
                        <th>Jam Masuk</th>
                        <th>Lokasi</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $q = "SELECT a.*, u.nama, u.email as npm FROM absensi a JOIN users u ON a.user_id = u.id WHERE a.tanggal = '$hari_ini' ORDER BY a.jam_masuk DESC LIMIT 5";
                    $res = $conn->query($q);
                    if($res->num_rows > 0):
                        while($row = $res->fetch_assoc()):
                    ?>
                    <tr>
                        <td>
                            <div class="fw-bold"><?= $row['nama'] ?></div>
                            <small class="text-muted"><?= $row['npm'] ?></small>
                        </td>
                        <td><?= $row['jam_masuk'] ?></td>
                        <td><?= $row['latitude_masuk'] ?>, <?= $row['longitude_masuk'] ?></td>
                        <td>
                            <?php if($row['status'] == 'Hadir'): ?>
                                <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill">Hadir</span>
                            <?php else: ?>
                                <span class="badge bg-warning bg-opacity-10 text-warning px-3 py-2 rounded-pill">Terlambat</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; else: ?>
                    <tr><td colspan="4" class="text-center text-muted py-4">Belum ada data presensi hari ini.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
