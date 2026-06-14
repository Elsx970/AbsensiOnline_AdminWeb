<?php
require_once '../includes/db.php';
require_once '../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold">Laporan Presensi</h3>
</div>

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-4 text-center py-5">
        <i class="bi bi-file-earmark-spreadsheet text-success" style="font-size: 4rem;"></i>
        <h4 class="mt-3">Export Data Presensi per Kelas</h4>
        <p class="text-muted">Pilih mata kuliah di bawah ini untuk mencetak laporan kehadiran mahasiswa.</p>
        
        <form action="export.php" method="GET" class="d-inline-block text-start mt-3" style="min-width: 300px;">
            <div class="mb-3">
                <label class="form-label fw-bold">Pilih Mata Kuliah / Kelas:</label>
                <select name="lokasi_id" class="form-select" required>
                    <option value="">-- Pilih Kelas --</option>
                    <option value="all">Semua Kelas</option>
                    <?php
                    $lokasi_res = $conn->query("SELECT id, nama_lokasi, tanggal FROM lokasi ORDER BY id DESC");
                    while($l = $lokasi_res->fetch_assoc()) {
                        $tgl = $l['tanggal'] ? date('d M Y', strtotime($l['tanggal'])) : '-';
                        echo "<option value='{$l['id']}'>{$l['nama_lokasi']} ($tgl)</option>";
                    }
                    ?>
                </select>
            </div>
            <button type="submit" class="btn btn-success w-100"><i class="bi bi-file-earmark-excel me-2"></i> Download Excel</button>
        </form>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
