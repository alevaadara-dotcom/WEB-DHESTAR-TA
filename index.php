<?php
include 'koneksi.php';

if (isset($_SESSION['username'])) {
    header("Location: dashboard.php");
    exit();
}

$error_msg = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = $_POST['password'];

    $query = mysqli_query($koneksi, "SELECT * FROM users WHERE username='$username'");
    
    if (mysqli_num_rows($query) > 0) {
        $user = mysqli_fetch_assoc($query);
        if (password_verify($password, $user['password']) || $password === $user['password']) {
            $_SESSION['username']     = $user['username'];
            $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
            header("Location: dashboard.php");
            exit();
        } else {
            $error_msg = "Password salah!";
        }
    } else {
        $error_msg = "Username tidak ditemukan!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login - Dhestar Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { background-color: #eef2f7; height: 100vh; display: flex; align-items: center; justify-content: center; }
        .card-login { width: 100%; max-width: 400px; border-radius: 12px; border: none; box-shadow: 0 4px 20px rgba(0,0,0,0.08); }
    </style>
</head>
<body>
<div class="card card-login p-4 bg-white">
    <h3 class="text-center fw-bold text-primary">Dhestar Store</h3>
    <p class="text-center text-muted mb-4">Aplikasi POS Kasir Sekolah</p>
    <form method="POST">
        <div class="mb-3">
            <label class="form-label">Username</label>
            <input type="text" name="username" class="form-control" required placeholder="Masukkan username">
        </div>
        <div class="mb-4">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" required placeholder="Masukkan password">
        </div>
        <button type="submit" class="btn btn-primary w-100 py-2 fw-bold">Login</button>
    </form>
</div>
<?php if (!empty($error_msg)): ?>
<script>
    Swal.fire({ icon: 'error', title: 'Gagal Login', text: '<?= $error_msg; ?>', confirmButtonColor: '#0d6efd' });
</script>
<?php endif; ?>
</body>
</html>