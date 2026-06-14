<?php
require_once '../includes/db.php';
require_once '../includes/header.php';

// Handle Delete
if(isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM lokasi WHERE id = ?");
    $stmt->bind_param("i", $id);
    if($stmt->execute()) {
        echo "<script>alert('Kelas/Lokasi berhasil dihapus!'); window.location='index.php';</script>";
    }
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold">Data Mata Kuliah & Jadwal</h3>
    <a href="create.php" class="btn text-white" style="background-color: #003366;">
        <i class="bi bi-plus-circle me-1"></i> Tambah Mata Kuliah
    </a>
</div>

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>Mata Kuliah</th>
                        <th>Waktu (Tanggal & Jam)</th>
                        <th>Koordinat (Lat, Lng)</th>
                        <th>Radius</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $result = $conn->query("SELECT * FROM lokasi ORDER BY id DESC");
                    if($result->num_rows > 0):
                        $no = 1;
                        while($row = $result->fetch_assoc()):
                    ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td class="fw-bold">
                            <?= $row['nama_lokasi'] ?><br>
                            <small class="text-primary">Pertemuan Ke-<?= $row['pertemuan'] ?? 1 ?></small>
                        </td>
                        <td>
                            <?php if($row['tanggal']): ?>
                                <?= date('d M Y', strtotime($row['tanggal'])) ?><br>
                                <small class="text-muted"><?= date('H:i', strtotime($row['jam_mulai'])) ?> - <?= date('H:i', strtotime($row['jam_selesai'])) ?> WIB</small>
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                        </td>
                        <td><?= $row['latitude'] ?>, <?= $row['longitude'] ?></td>
                        <td><?= $row['radius'] ?> Meter</td>
                        <td>
                            <a href="index.php?delete=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus kelas ini?')"><i class="bi bi-trash"></i> Hapus</a>
                        </td>
                    </tr>
                    <?php endwhile; else: ?>
                    <tr><td colspan="5" class="text-center py-4">Belum ada data kelas.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
