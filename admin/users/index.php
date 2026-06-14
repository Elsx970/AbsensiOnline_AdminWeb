<?php
require_once '../includes/db.php';
require_once '../includes/header.php';

// Handle Delete
if(isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ? AND role = 'user'");
    $stmt->bind_param("i", $id);
    if($stmt->execute()) {
        echo "<script>alert('Mahasiswa berhasil dihapus!'); window.location='index.php';</script>";
    }
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold">Data Mahasiswa</h3>
    <a href="create.php" class="btn text-white" style="background-color: #003366;">
        <i class="bi bi-plus-circle me-1"></i> Tambah Mahasiswa
    </a>
</div>

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>Nama Lengkap</th>
                        <th>NPM</th>
                        <th>Tanggal Daftar</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $result = $conn->query("SELECT * FROM users WHERE role = 'user' ORDER BY id DESC");
                    if($result->num_rows > 0):
                        $no = 1;
                        while($row = $result->fetch_assoc()):
                    ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td class="fw-bold"><?= $row['nama'] ?></td>
                        <td><?= $row['email'] ?></td> <!-- field email dipakai sbg NPM -->
                        <td><?= date('d M Y', strtotime($row['created_at'])) ?></td>
                        <td>
                            <a href="index.php?delete=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus?')"><i class="bi bi-trash"></i> Hapus</a>
                        </td>
                    </tr>
                    <?php endwhile; else: ?>
                    <tr><td colspan="5" class="text-center py-4">Belum ada data mahasiswa.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
