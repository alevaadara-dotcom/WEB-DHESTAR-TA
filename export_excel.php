<?php
include 'koneksi.php';
if (!isset($_SESSION['login'])) {
    header("Location: index.php");
    exit;
}

$tgl_mulai = $_GET['tgl_mulai'] ?? date('Y-m-01');
$tgl_selesai = $_GET['tgl_selesai'] ?? date('Y-m-d');

header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=Laporan_Penjualan_Dhestar_$tgl_mulai_s_d_$tgl_selesai.xls");

$query = mysqli_query($koneksi, "SELECT transaksi.*, produk.nama_produk 
    FROM transaksi 
    JOIN produk ON transaksi.id_produk = produk.id 
    WHERE DATE(transaksi.tanggal) BETWEEN '$tgl_mulai' AND '$tgl_selesai'
    ORDER BY transaksi.id DESC");
?>

<h2>LAPORAN PENJUALAN DHESTAR STORE</h2>
<p>Periode: <?= $tgl_mulai; ?> s/d <?= $tgl_selesai; ?></p>

<table border="1">
    <thead>
        <tr>
            <th>No</th>
            <th>No Nota</th>
            <th>Tanggal</th>
            <th>Pembeli</th>
            <th>Produk</th>
            <th>Jumlah</th>
            <th>Total Harga</th>
            <th>Metode</th>
        </tr>
    </thead>
    <tbody>
        <?php $no=1; $total=0; while($r = mysqli_fetch_assoc($query)): $total += $r['total_harga']; ?>
        <tr>
            <td><?= $no++; ?></td>
            <td><?= $r['no_nota']; ?></td>
            <td><?= $r['tanggal']; ?></td>
            <td><?= $r['nama_pembeli']; ?></td>
            <td><?= $r['nama_produk']; ?></td>
            <td><?= $r['jumlah']; ?></td>
            <td><?= $r['total_harga']; ?></td>
            <td><?= $r['metode_pembayaran']; ?></td>
        </tr>
        <?php endwhile; ?>
        <tr>
            <td colspan="6" align="right"><b>Total Pendapatan:</b></td>
            <td colspan="2"><b><?= $total; ?></b></td>
        </tr>
    </tbody>
</table>