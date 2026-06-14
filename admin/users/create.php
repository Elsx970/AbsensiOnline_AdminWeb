<?php
require_once '../includes/db.php';
require_once '../includes/header.php';

$success = '';
$error = '';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = $_POST['nama'];
    $npm = $_POST['npm'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    
    // Cek NPM duplikat
    $cek = $conn->query("SELECT id FROM users WHERE email = '$npm'");
    if($cek->num_rows > 0) {
        $error = "NPM sudah terdaftar!";
    } else {
        $stmt = $conn->prepare("INSERT INTO users (nama, email, password, role) VALUES (?, ?, ?, 'user')");
        $stmt->bind_param("sss", $nama, $npm, $password);
        if($stmt->execute()) {
            $success = "Mahasiswa berhasil ditambahkan!";
        } else {
            $error = "Gagal menyimpan data!";
        }
    }
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold">Tambah Mahasiswa</h3>
    <a href="index.php" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Kembali
    </a>
</div>

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-4">
        <?php if($error): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>
        <?php if($success): ?><div class="alert alert-success"><?= $success ?></div><?php endif; ?>
        
        <form method="POST" action="">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Nama Lengkap</label>
                    <input type="text" name="nama" class="form-control" required placeholder="Contoh: Andi Pratama">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">NPM</label>
                    <input type="number" name="npm" class="form-control" required placeholder="Contoh: 2115061001">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required placeholder="Minimal 6 karakter">
                </div>
            </div>
            <hr>
            <button type="submit" class="btn text-white px-4" style="background-color: #003366;">
                <i class="bi bi-save me-1"></i> Simpan Data
            </button>
        </form>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
