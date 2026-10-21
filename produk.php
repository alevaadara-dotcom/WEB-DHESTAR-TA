<?php
include 'koneksi.php';
if (!isset($_SESSION['login'])) {
    header("Location: index.php");
    exit;
}

if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $hapus = mysqli_query($koneksi, "DELETE FROM produk WHERE id='$id'");
    if ($hapus) {
        echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire('Terhapus!', 'Produk berhasil dihapus.', 'success').then(() => {
                    window.location='produk.php';
                });
            });
        </script>";
    }
}

$query_produk = mysqli_query($koneksi, "SELECT * FROM produk ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Master Produk - Dhestar Store</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <div class="navbar">
        <h1>Dhestar Phone Store</h1>
        <div>
            <a href="dashboard.php">Dashboard</a>
            <a href="produk.php">Master Produk</a>
            <a href="input.php">Transaksi Baru</a>
            <a href="laporan.php">Laporan</a>
            <a href="logout.php">Logout</a>
        </div>
    </div>

    <div class="container">
        <div class="card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h2>Kelola Data Produk HP</h2>
                <a href="produk_tambah.php" class="btn" style="width: auto; text-decoration: none; padding: 10px 15px;">+ Tambah HP Baru</a>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Foto</th>
                        <th>Nama HP</th>
                        <th>Harga</th>
                        <th>Stok</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no=1; while($p = mysqli_fetch_assoc($query_produk)): ?>
                    <tr>
                        <td><?= $no++; ?></td>
                        <td>
                            <img src="uploads/<?= !empty($p['gambar']) ? $p['gambar'] : 'default.jpg'; ?>" alt="HP" width="50" height="50" style="object-fit: cover; border-radius: 6px;" onerror="this.src='https://via.placeholder.com/50?text=HP'">
                        </td>
                        <td><?= htmlspecialchars($p['nama_produk']); ?></td>
                        <td>Rp <?= number_format($p['harga'],0,',','.'); ?></td>
                        <td>
                            <?php if($p['stok'] <= 3): ?>
                                <span style="background: #fee2e2; color: #dc2626; padding: 3px 8px; border-radius: 4px; font-weight: bold; font-size: 12px;">
                                    <?= $p['stok']; ?> (Stok Tipis)
                                </span>
                            <?php else: ?>
                                <?= $p['stok']; ?> Unit
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="produk_edit.php?id=<?= $p['id']; ?>" style="color: #0284c7; text-decoration: none; font-weight: bold; margin-right: 10px;">Edit</a>
                            <a href="#" onclick="konfirmasiHapus(<?= $p['id']; ?>)" style="color: #dc2626; text-decoration: none; font-weight: bold;">Hapus</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
    function konfirmasiHapus(id) {
        Swal.fire({
            title: 'Apakah kamu yakin?',
            text: "Data HP ini akan dihapus permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'produk.php?hapus=' + id;
            }
        });
    }
    </script>
</body>
</html>