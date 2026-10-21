<?php
include 'koneksi.php';

if (!isset($_GET['no_nota'])) {
    die("Nota tidak ditemukan!");
}

$no_nota = mysqli_real_escape_string($koneksi, $_GET['no_nota']);
$q_trx  = mysqli_query($koneksi, "SELECT t.*, p.nama_produk, p.harga FROM transaksi t JOIN produk p ON t.id_produk = p.id WHERE t.no_nota='$no_nota'");

$items = [];
$total_bayar = 0;
$pembeli = "";
$tanggal = "";
$metode = "";
$jumlah_uang = 0;
$kembalian = 0;

while ($row = mysqli_fetch_assoc($q_trx)) {
    $items[] = $row;
    $total_bayar += $row['total_harga'];
    $pembeli = $row['nama_pembeli'];
    $tanggal = $row['tanggal'];
    $metode  = $row['metode_pembayaran'];
    $jumlah_uang = $row['jumlah_uang'];
    $kembalian = $row['kembalian'];
}

if (empty($items)) {
    die("Data transaksi kosong.");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Nota Kasir - <?= $no_nota; ?></title>
    <style>
        body { font-family: 'Courier New', Courier, monospace; width: 280px; margin: 0 auto; padding: 10px; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .line { border-bottom: 1px dashed #000; margin: 8px 0; }
        table { width: 100%; font-size: 12px; }
        @media print {
            .btn-print { display: none; }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="text-center">
        <h3 style="margin: 0;">DHESTAR STORE</h3>
        <p style="margin: 2px; font-size: 11px;">Jl. Raya Sekolah POS No. 1</p>
        <p style="margin: 2px; font-size: 11px;"><?= $tanggal; ?></p>
    </div>

    <div class="line"></div>
    <div style="font-size: 11px;">
        <div>Nota: <?= $no_nota; ?></div>
        <div>Pembeli: <?= htmlspecialchars($pembeli); ?></div>
        <div>Metode: <?= $metode; ?></div>
    </div>
    <div class="line"></div>

    <table>
        <?php foreach ($items as $item): ?>
        <tr>
            <td colspan="2"><b><?= htmlspecialchars($item['nama_produk']); ?></b></td>
        </tr>
        <tr>
            <td><?= $item['jumlah']; ?> x Rp <?= number_format($item['harga'], 0, ',', '.'); ?></td>
            <td class="text-right">Rp <?= number_format($item['total_harga'], 0, ',', '.'); ?></td>
        </tr>
        <?php endforeach; ?>
    </table>

    <div class="line"></div>
    <table>
        <tr>
            <td><b>TOTAL</b></td>
            <td class="text-right"><b>Rp <?= number_format($total_bayar, 0, ',', '.'); ?></b></td>
        </tr>
        <tr>
            <td>Uang Bayar</td>
            <td class="text-right">Rp <?= number_format($jumlah_uang, 0, ',', '.'); ?></td>
        </tr>
        <tr>
            <td>Kembalian</td>
            <td class="text-right">Rp <?= number_format($kembalian, 0, ',', '.'); ?></td>
        </tr>
    </table>
    <div class="line"></div>

    <div class="text-center" style="font-size: 11px; margin-top: 10px;">
        <p>-- Terima Kasih --<br>Selamat Belanja Kembali</p>
        <button class="btn-print" onclick="window.print()">Cetak Ulang</button>
        <a href="dashboard.php?page=kasir" class="btn-print">Kembali ke Kasir</a>
    </div>

</body>
</html>