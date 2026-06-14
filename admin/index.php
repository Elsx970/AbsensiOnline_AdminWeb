<?php
session_start();
if(isset($_SESSION['admin_logged_in'])) {
    header("Location: dashboard.php");
    exit;
}
require_once 'includes/db.php';

$error = '';
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, nama, password, role FROM users WHERE email = ? AND role = 'admin'");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if($result->num_rows > 0) {
        $admin = $result->fetch_assoc();
        if(password_verify($password, $admin['password'])) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_nama'] = $admin['nama'];
            header("Location: dashboard.php");
            exit;
        } else {
            $error = 'Password salah!';
        }
    } else {
        $error = 'Admin tidak ditemukan!';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Presensi Unila</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f4f6f9; display: flex; align-items: center; height: 100vh; }
        .login-card { max-width: 400px; margin: auto; border-radius: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
    </style>
</head>
<body>
    <div class="container">
        <div class="card login-card p-4 border-0">
            <div class="text-center mb-4">
                <img src="../../assets/logo.png" alt="Logo Unila" height="80" class="mb-3" onerror="this.style.display='none'">
                <h4 class="fw-bold" style="color: #003366;">Admin Presensi</h4>
                <p class="text-muted">Silakan masuk ke panel admin</p>
            </div>
            <?php if($error): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>
            <form method="POST">
                <div class="mb-3">
                    <label>Email Admin</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="mb-4">
                    <label>Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <button type="submit" class="btn w-100 text-white" style="background-color: #003366;">Login</button>
            </form>
        </div>
    </div>
</body>
</html>
