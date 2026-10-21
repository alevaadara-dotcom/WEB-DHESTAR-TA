<?php
include 'koneksi.php';
if (!isset($_SESSION['login'])) {
    header("Location: index.php");
    exit;
}

$id = $_GET['id'];
$query = mysqli_query($koneksi, "SELECT * FROM produk WHERE id='$id'");
$data = mysqli_fetch_assoc($query);

if (isset($_POST['update'])) {
    $nama_produk = $_POST['nama_produk'];
    $harga       = $_POST['harga'];
    $stok        = $_POST['stok'];
    
    $gambar = $_FILES['gambar']['name'];
    $tmp_name = $_FILES['gambar']['tmp_name'];

    if ($gambar != "") {
        $nama_gambar = time() . '_' . $gambar;
        move_uploaded_file($tmp_name, 'uploads/' . $nama_gambar);
        $update = mysqli_query($koneksi, "UPDATE produk SET nama_produk='$nama_produk', harga='$harga', stok='$stok', gambar='$nama_gambar' WHERE id='$id'");
    } else {
        $update = mysqli_query($koneksi, "UPDATE produk SET nama_produk='$nama_produk', harga='$harga', stok='$stok' WHERE id='$id'");
    }

    if ($update) {
        echo "<script>alert('Data produk berhasil diperbarui!'); window.location='produk.php';</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Produk - Dhestar Store</title>
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
            <h2>Edit Produk HP</h2>
            <form action="" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Nama HP</label>
                    <input type="text" name="nama_produk" value="<?= htmlspecialchars($data['nama_produk']); ?>" required>
                </div>

                <div class="form-group">
                    <label>Harga (Rp)</label>
                    <input type="number" name="harga" value="<?= $data['harga']; ?>" required>
                </div>

                <div class="form-group">
                    <label>Stok</label>
                    <input type="number" name="stok" value="<?= $data['stok']; ?>" required>
                </div>

                <div class="form-group">
                    <label>Ganti Foto Produk (Kosongkan jika tidak diubah)</label>
                    <input type="file" name="gambar" accept="image/*">
                </div>

                <button type="submit" name="update" class="btn">Update Produk</button>
                <a href="produk.php" style="display: block; text-align: center; margin-top: 15px; color: #64748b; text-decoration: none;">Batal</a>
            </form>
        </div>
    </div>
</body>
</html>