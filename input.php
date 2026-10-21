<?php
include 'koneksi.php';
if (!isset($_SESSION['login'])) {
    header("Location: index.php");
    exit;
}

$produk = mysqli_query($koneksi, "SELECT * FROM produk WHERE stok > 0");

if (isset($_POST['simpan'])) {
    $no_nota = "DHS-" . date("YmdHis");
    $nama_pembeli = $_POST['nama_pembeli'];
    $id_produk = $_POST['id_produk'];
    $jumlah = $_POST['jumlah'];
    $total_harga = $_POST['total_harga'];
    $metode = $_POST['metode_pembayaran'];
    $jumlah_uang = $_POST['jumlah_uang'];
    $kembalian = $_POST['kembalian'];

    $simpan = mysqli_query($koneksi, "INSERT INTO transaksi (no_nota, nama_pembeli, id_produk, jumlah, total_harga, metode_pembayaran, jumlah_uang, kembalian) 
                                      VALUES ('$no_nota', '$nama_pembeli', '$id_produk', '$jumlah', '$total_harga', '$metode', '$jumlah_uang', '$kembalian')");
    
    if ($simpan) {
        mysqli_query($koneksi, "UPDATE produk SET stok = stok - $jumlah WHERE id = '$id_produk'");
        echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'success',
                    title: 'Transaksi Berhasil!',
                    text: 'Data telah disimpan ke sistem.',
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    window.location='dashboard.php';
                });
            });
        </script>";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Input Transaksi - Dhestar Store</title>
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
            <h2>Form Penjualan Kasir</h2>
            <form action="" method="POST" id="formTransaksi">
                <div class="form-group">
                    <label>Nama Pembeli</label>
                    <input type="text" name="nama_pembeli" required>
                </div>

                <div class="form-group">
                    <label>Pilih Produk HP</label>
                    <select name="id_produk" id="id_produk" required onchange="hitungTotal()">
                        <option value="" data-harga="0">-- Pilih Produk --</option>
                        <?php while($row = mysqli_fetch_assoc($produk)): ?>
                            <option value="<?= $row['id']; ?>" data-harga="<?= $row['harga']; ?>">
                                <?= $row['nama_produk']; ?> - Rp <?= number_format($row['harga'],0,',','.'); ?> (Stok: <?= $row['stok']; ?>)
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Jumlah Beli</label>
                    <input type="number" name="jumlah" id="jumlah" min="1" value="1" required oninput="hitungTotal()">
                </div>

                <div class="form-group">
                    <label>Total Harga (Rp)</label>
                    <input type="number" name="total_harga" id="total_harga" readonly>
                </div>

                <div class="form-group">
                    <label>Metode Pembayaran</label>
                    <select name="metode_pembayaran" id="metode" required onchange="toggleMetode()">
                        <option value="Cash">Cash (Tunai)</option>
                        <option value="QRIS">QRIS</option>
                    </select>
                </div>

                <div class="qris-box" id="qrisBox">
                    <p style="margin-bottom: 10px; font-weight: bold;">Scan QRIS Dhestar Store:</p>
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=DhestarStorePayment" alt="QRIS Code">
                    <p style="font-size: 12px; color: #666; margin-top: 5px;">Pastikan nominal transfer sesuai dengan total harga.</p>
                </div>

                <div class="form-group">
                    <label>Jumlah Uang Dibayar (Rp)</label>
                    <input type="number" name="jumlah_uang" id="jumlah_uang" required oninput="hitungKembalian()">
                </div>

                <div class="form-group">
                    <label>Kembalian (Rp)</label>
                    <input type="number" name="kembalian" id="kembalian" readonly>
                </div>

                <button type="submit" name="simpan" class="btn">Proses Transaksi</button>
            </form>
        </div>
    </div>

    <script>
    function hitungTotal() {
        var select = document.getElementById('id_produk');
        var harga = select.options[select.selectedIndex].getAttribute('data-harga');
        var jumlah = document.getElementById('jumlah').value;
        var total = harga * jumlah;
        document.getElementById('total_harga').value = total;
        hitungKembalian();
    }

    function toggleMetode() {
        var metode = document.getElementById('metode').value;
        var qrisBox = document.getElementById('qrisBox');
        if (metode === 'QRIS') {
            qrisBox.style.display = 'block';
            var total = document.getElementById('total_harga').value;
            document.getElementById('jumlah_uang').value = total;
            hitungKembalian();
        } else {
            qrisBox.style.display = 'none';
        }
    }

    function hitungKembalian() {
        var total = parseFloat(document.getElementById('total_harga').value) || 0;
        var bayar = parseFloat(document.getElementById('jumlah_uang').value) || 0;
        var kembalian = bayar - total;
        document.getElementById('kembalian').value = kembalian < 0 ? 0 : kembalian;
    }
    </script>
</body>
</html>