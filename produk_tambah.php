<?php
include 'koneksi.php';
if (!isset($_SESSION['login'])) {
    header("Location: index.php");
    exit;
}

if (isset($_POST['simpan'])) {
    $nama_produk = $_POST['nama_produk'];
    $harga       = $_POST['harga'];
    $stok        = $_POST['stok'];
    
    // Upload Gambar
    $gambar = $_FILES['gambar']['name'];
    $tmp_name = $_FILES['gambar']['tmp_mode'] ?? $_FILES['gambar']['tmp_name'];
    
    if ($gambar != "") {
        $nama_gambar = time() . '_' . $gambar;
        move_uploaded_file($tmp_name, 'uploads/' . $nama_gambar);
    } else {
        $nama_gambar = 'default.jpg';
    }

    $simpan = mysqli_query($koneksi, "INSERT INTO produk (nama_produk, harga, stok, gambar) VALUES ('$nama_produk', '$harga', '$stok', '$nama_gambar')");

    if ($simpan) {
        echo "<script>alert('Produk berhasil ditambahkan!'); window.location='produk.php';</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Produk - Dhestar Store</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="navbar">
        <h1>Dhestar Phone Store</h1>
        <div>
            <a href="dashboard.php">Dashboard</a>
            <a href="produk.php">Master Produk</a>
            <a href="input.php">Transaksi Baru</a>
            <a href="logout.php">Logout</a>
        </div>
    </div>

    <div class="container">
        <div class="card" style="max-width: 600px; margin: auto;">
            <h2>Tambah Produk HP Baru</h2>
            <form action="" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Nama HP</label>
                    <input type="text" name="nama_produk" required placeholder="Contoh: iPhone 15 Pro Max">
                </div>

                <div class="form-group">
                    <label>Harga (Rp)</label>
                    <input type="number" name="harga" required placeholder="15000000">
                </div>

                <div class="form-group">
                    <label>Stok Awal</label>
                    <input type="number" name="stok" min="1" required placeholder="10">
                </div>

                <div class="form-group">
                    <label>Foto Produk (Opsional)</label>
                    <input type="file" name="gambar" accept="image/*">
                </div>

                <button type="submit" name="simpan" class="btn">Simpan Produk</button>
                <a href="produk.php" style="display: block; text-align: center; margin-top: 15px; color: #64748b; text-decoration: none;">Batal</a>
            </form>
        </div>
    </div>
</body>
</html>