<?php
include 'koneksi.php';
if (!isset($_SESSION['login'])) {
    header("Location: index.php");
    exit;
}

$tgl_mulai = $_GET['tgl_mulai'] ?? date('Y-m-01');
$tgl_selesai = $_GET['tgl_selesai'] ?? date('Y-m-d');

$query_laporan = mysqli_query($koneksi, "SELECT transaksi.*, produk.nama_produk 
    FROM transaksi 
    JOIN produk ON transaksi.id_produk = produk.id 
    WHERE DATE(transaksi.tanggal) BETWEEN '$tgl_mulai' AND '$tgl_selesai'
    ORDER BY transaksi.id DESC");

$total_pendapatan = 0;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Penjualan - Dhestar Store</title>
    <link rel="stylesheet" href="style.css">
    <style>
        @media print {
            .navbar, .filter-box, .btn-action { display: none !important; }
            .card { border: none !important; box-shadow: none !important; }
        }
    </style>
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
        <div class="card filter-box">
            <h2>Filter Laporan Penjualan</h2>
            <form action="" method="GET" style="display: flex; gap: 15px; align-items: flex-end; margin-top: 15px;">
                <div class="form-group" style="margin-bottom: 0; flex: 1;">
                    <label>Dari Tanggal</label>
                    <input type="date" name="tgl_mulai" value="<?= $tgl_mulai; ?>" required>
                </div>
                <div class="form-group" style="margin-bottom: 0; flex: 1;">
                    <label>Sampai Tanggal</label>
                    <input type="date" name="tgl_selesai" value="<?= $tgl_selesai; ?>" required>
                </div>
                <button type="submit" class="btn btn-action" style="width: auto; padding: 10px 20px;">Tampilkan</button>
                <button type="button" class="btn btn-action" onclick="window.print()" style="width: auto; padding: 10px 20px; background: #0d9488;">Cetak / PDF</button>
                <a href="export_excel.php?tgl_mulai=<?= $tgl_mulai; ?>&tgl_selesai=<?= $tgl_selesai; ?>" class="btn btn-action" style="width: auto; padding: 10px 20px; background: #16a34a; text-decoration: none; text-align: center;">Export Excel</a>
            </form>
        </div>

        <div class="card">
            <div style="text-align: center; margin-bottom: 20px;">
                <h2>LAPORAN PENJUALAN DHESTAR STORE</h2>
                <p>Periode: <?= date('d/m/Y', strtotime($tgl_mulai)); ?> s/d <?= date('d/m/Y', strtotime($tgl_selesai)); ?></p>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>No Nota</th>
                        <th>Tanggal</th>
                        <th>Pembeli</th>
                        <th>Produk</th>
                        <th>Jumlah</th>
                        <th>Total Harga</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(mysqli_num_rows($query_laporan) > 0): ?>
                        <?php $no=1; while($row = mysqli_fetch_assoc($query_laporan)): 
                            $total_pendapatan += $row['total_harga'];
                        ?>
                        <tr>
                            <td><?= $no++; ?></td>
                            <td><strong><?= htmlspecialchars($row['no_nota']); ?></strong></td>
                            <td><?= date('d/m/Y H:i', strtotime($row['tanggal'])); ?></td>
                            <td><?= htmlspecialchars($row['nama_pembeli']); ?></td>
                            <td><?= htmlspecialchars($row['nama_produk']); ?></td>
                            <td><?= $row['jumlah']; ?> Unit</td>
                            <td>Rp <?= number_format($row['total_harga'],0,',','.'); ?></td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" style="text-align: center;">Tidak ada transaksi pada periode tanggal ini.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
                <tfoot>
                    <tr style="background: #f1f5f9; font-weight: bold;">
                        <td colspan="6" style="text-align: right;">Total Pendapatan:</td>
                        <td style="color: #16a34a; font-size: 16px;">Rp <?= number_format($total_pendapatan,0,',','.'); ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</body>
</html>