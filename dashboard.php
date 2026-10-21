<?php
include 'koneksi.php';

if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

// Inisialisasi Keranjang Belanja di Session
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Fitur Kasir: Tambah ke Keranjang
if (isset($_POST['tambah_keranjang'])) {
    $id_produk = (int)$_POST['id_produk'];
    $jumlah    = (int)$_POST['jumlah'];

    $q_prod = mysqli_query($koneksi, "SELECT * FROM produk WHERE id='$id_produk'");
    $prod   = mysqli_fetch_assoc($q_prod);

    if ($prod && $prod['stok'] >= $jumlah) {
        if (isset($_SESSION['cart'][$id_produk])) {
            $_SESSION['cart'][$id_produk]['jumlah'] += $jumlah;
        } else {
            $_SESSION['cart'][$id_produk] = [
                'nama_produk' => $prod['nama_produk'],
                'harga'       => $prod['harga'],
                'jumlah'      => $jumlah
            ];
        }
    }
    header("Location: dashboard.php?page=kasir");
    exit();
}

// Fitur Kasir: Hapus Item Keranjang
if (isset($_GET['hapus_cart'])) {
    $id = (int)$_GET['hapus_cart'];
    unset($_SESSION['cart'][$id]);
    header("Location: dashboard.php?page=kasir");
    exit();
}

// Fitur Kasir: Reset Keranjang
if (isset($_GET['reset_cart'])) {
    $_SESSION['cart'] = [];
    header("Location: dashboard.php?page=kasir");
    exit();
}

// Fitur Kasir: Proses Transaksi Bayar
$nota_tercetak = "";
if (isset($_POST['bayar_transaksi'])) {
    $pembeli     = mysqli_real_escape_string($koneksi, $_POST['nama_pembeli']);
    $metode      = $_POST['metode_pembayaran'];
    $jumlah_uang = (int)$_POST['jumlah_uang'];

    $grand_total = 0;
    foreach ($_SESSION['cart'] as $item) {
        $grand_total += ($item['harga'] * $item['jumlah']);
    }

    if (!empty($_SESSION['cart']) && $jumlah_uang >= $grand_total) {
        $kembalian = $jumlah_uang - $grand_total;
        $no_nota   = "NT-" . date("YmdHis");

        foreach ($_SESSION['cart'] as $id_produk => $item) {
            $total_item  = $item['harga'] * $item['jumlah'];
            $jumlah_item = $item['jumlah'];

            // Simpan Transaksi
            mysqli_query($koneksi, "INSERT INTO transaksi (no_nota, nama_pembeli, id_produk, jumlah, total_harga, metode_pembayaran, jumlah_uang, kembalian) 
                VALUES ('$no_nota', '$pembeli', '$id_produk', '$jumlah_item', '$total_item', '$metode', '$jumlah_uang', '$kembalian')");

            // Potong Stok
            mysqli_query($koneksi, "UPDATE produk SET stok = stok - $jumlah_item WHERE id='$id_produk'");
        }

        $_SESSION['cart'] = []; // Bersihkan keranjang
        $nota_tercetak = $no_nota;
    }
}

// Fitur Tambah Produk Baru
if (isset($_POST['tambah_produk'])) {
    $nama  = mysqli_real_escape_string($koneksi, $_POST['nama_produk']);
    $harga = (int)$_POST['harga'];
    $stok  = (int)$_POST['stok'];
    mysqli_query($koneksi, "INSERT INTO produk (nama_produk, harga, stok) VALUES ('$nama', '$harga', '$stok')");
    header("Location: dashboard.php?page=produk");
    exit();
}

// Fitur Hapus Produk
if (isset($_GET['hapus_produk'])) {
    $id = (int)$_GET['hapus_produk'];
    mysqli_query($koneksi, "DELETE FROM produk WHERE id='$id'");
    header("Location: dashboard.php?page=produk");
    exit();
}

$page = $_GET['page'] ?? 'home';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard POS - Dhestar Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { background-color: #f8f9fa; }
        .sidebar { min-height: 100vh; background: #212529; color: white; }
        .sidebar a { color: #adb5bd; text-decoration: none; display: block; padding: 12px 20px; border-radius: 6px; margin-bottom: 4px; }
        .sidebar a:hover, .sidebar a.active { background: #0d6efd; color: white; }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar Menu -->
        <div class="col-md-2 sidebar p-3">
            <h4 class="text-white fw-bold text-center my-3">Dhestar Store</h4>
            <hr class="text-secondary">
            <a href="dashboard.php?page=home" class="<?= $page == 'home' ? 'active' : ''; ?>">Dashboard</a>
            <a href="dashboard.php?page=kasir" class="<?= $page == 'kasir' ? 'active' : ''; ?>">Kasir / Transaksi</a>
            <a href="dashboard.php?page=produk" class="<?= $page == 'produk' ? 'active' : ''; ?>">Kelola Produk</a>
            <a href="dashboard.php?page=riwayat" class="<?= $page == 'riwayat' ? 'active' : ''; ?>">Riwayat Transaksi</a>
            <hr class="text-secondary">
            <a href="logout.php" class="text-danger fw-bold">Logout</a>
        </div>

        <!-- Main Content -->
        <div class="col-md-10 p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="fw-bold">
                    <?php 
                        if ($page == 'kasir') echo "Menu Kasir (Multi Produk)";
                        elseif ($page == 'produk') echo "Kelola Stok Produk";
                        elseif ($page == 'riwayat') echo "Riwayat Transaksi";
                        else echo "Dashboard Utama";
                    ?>
                </h3>
                <span class="badge bg-primary fs-6">Operator: <?= htmlspecialchars($_SESSION['nama_lengkap'] ?? 'Admin'); ?></span>
            </div>

            <?php if ($page == 'home'): ?>
                <!-- Halaman Utama -->
                <?php 
                    $tot_prod = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM produk"))['total'] ?? 0;
                    $tot_trx  = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(DISTINCT no_nota) as total FROM transaksi"))['total'] ?? 0;
                    $omzet    = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT SUM(total_harga) as total FROM transaksi"))['total'] ?? 0;
                ?>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <div class="card bg-primary text-white p-3 shadow-sm">
                            <h5>Total Produk</h5>
                            <h2><?= $tot_prod; ?> Items</h2>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="card bg-success text-white p-3 shadow-sm">
                            <h5>Total Transaksi</h5>
                            <h2><?= $tot_trx; ?> Penjualan</h2>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="card bg-warning text-dark p-3 shadow-sm">
                            <h5>Total Omzet</h5>
                            <h2>Rp <?= number_format($omzet, 0, ',', '.'); ?></h2>
                        </div>
                    </div>
                </div>

            <?php elseif ($page == 'kasir'): ?>
                <!-- Halaman Kasir Multi Produk -->
                <div class="row">
                    <!-- Form Pilih Produk -->
                    <div class="col-md-5 mb-3">
                        <div class="card p-3 shadow-sm">
                            <h5 class="fw-bold mb-3">Pilih Produk</h5>
                            <form method="POST">
                                <div class="mb-3">
                                    <label class="form-label">Pilih Barang</label>
                                    <select name="id_produk" class="form-select" required>
                                        <option value="">-- Pilih Produk --</option>
                                        <?php 
                                            $p_query = mysqli_query($koneksi, "SELECT * FROM produk WHERE stok > 0");
                                            while ($p = mysqli_fetch_assoc($p_query)): 
                                        ?>
                                            <option value="<?= $p['id']; ?>"><?= htmlspecialchars($p['nama_produk']); ?> - Rp <?= number_format($p['harga'], 0, ',', '.'); ?> (Stok: <?= $p['stok']; ?>)</option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Jumlah Beli</label>
                                    <input type="number" name="jumlah" value="1" min="1" class="form-control" required>
                                </div>
                                <button type="submit" name="tambah_keranjang" class="btn btn-primary w-100">+ Tambah ke Keranjang</button>
                            </form>
                        </div>
                    </div>

                    <!-- Tabel Keranjang Belanja & Form Bayar -->
                    <div class="col-md-7">
                        <div class="card p-3 shadow-sm">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h5 class="fw-bold m-0">Keranjang Belanja</h5>
                                <a href="dashboard.php?page=kasir&reset_cart=1" class="btn btn-outline-danger btn-sm">Reset</a>
                            </div>
                            <table class="table table-sm align-middle">
                                <thead>
                                    <tr>
                                        <th>Produk</th>
                                        <th>Harga</th>
                                        <th>Qty</th>
                                        <th>Subtotal</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                        $grand_total = 0;
                                        if (!empty($_SESSION['cart'])):
                                            foreach ($_SESSION['cart'] as $id => $item):
                                                $subtotal = $item['harga'] * $item['jumlah'];
                                                $grand_total += $subtotal;
                                    ?>
                                    <tr>
                                        <td><?= htmlspecialchars($item['nama_produk']); ?></td>
                                        <td>Rp <?= number_format($item['harga'], 0, ',', '.'); ?></td>
                                        <td><?= $item['jumlah']; ?></td>
                                        <td>Rp <?= number_format($subtotal, 0, ',', '.'); ?></td>
                                        <td><a href="dashboard.php?page=kasir&hapus_cart=<?= $id; ?>" class="text-danger fw-bold text-decoration-none">x</a></td>
                                    </tr>
                                    <?php 
                                            endforeach;
                                        else:
                                    ?>
                                    <tr><td colspan="5" class="text-center text-muted">Keranjang masih kosong</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>

                            <hr>

                            <!-- Form Pembayaran Otomatis -->
                            <form method="POST">
                                <div class="mb-2">
                                    <label class="form-label">Nama Pembeli</label>
                                    <input type="text" name="nama_pembeli" class="form-control" placeholder="Nama Pelanggan" required>
                                </div>
                                <div class="mb-2">
                                    <label class="form-label">Metode Pembayaran</label>
                                    <select name="metode_pembayaran" id="metode_pembayaran" class="form-select" onchange="cekMetodeBayar()">
                                        <option value="Tunai">Tunai</option>
                                        <option value="QRIS">QRIS</option>
                                        <option value="Transfer">Transfer</option>
                                    </select>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Total Belanja (Rp)</label>
                                        <input type="number" id="grand_total" class="form-control bg-light fw-bold text-primary" value="<?= $grand_total; ?>" readonly>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Jumlah Uang (Rp)</label>
                                        <input type="number" name="jumlah_uang" id="jumlah_uang" class="form-control fw-bold" placeholder="0" required oninput="hitungKembalian()">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Kembalian (Rp)</label>
                                    <input type="text" id="kembalian" class="form-control bg-light fw-bold text-success" value="0" readonly>
                                </div>
                                <button type="submit" name="bayar_transaksi" class="btn btn-success w-100 py-2 fw-bold" <?= empty($_SESSION['cart']) ? 'disabled' : ''; ?>>Proses Bayar & Cetak Nota</button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- JavaScript QRIS Pop-up & Perhitungan Otomatis -->
                <script>
                function cekMetodeBayar() {
                    let metode = document.getElementById('metode_pembayaran').value;
                    let total = parseInt(document.getElementById('grand_total').value) || 0;
                    let inputUang = document.getElementById('jumlah_uang');

                    if (metode === 'QRIS') {
                        inputUang.value = total;
                        hitungKembalian();

                        let qrisUrl = "https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=DHESTARSTORE-QRIS-PAYMENT-" + total;
                        
                        Swal.fire({
                            title: 'Scan QRIS Dhestar Store',
                            text: 'Total Pembayaran: Rp ' + total.toLocaleString('id-ID'),
                            imageUrl: qrisUrl,
                            imageWidth: 220,
                            imageHeight: 220,
                            imageAlt: 'Barcode QRIS',
                            confirmButtonText: 'Sudah Transfer / Bayar',
                            confirmButtonColor: '#198754'
                        });
                    }
                }

                function hitungKembalian() {
                    let total = parseInt(document.getElementById('grand_total').value) || 0;
                    let bayar = parseInt(document.getElementById('jumlah_uang').value) || 0;
                    let kembali = bayar - total;
                    
                    if (kembali >= 0) {
                        document.getElementById('kembalian').value = "Rp " + kembali.toLocaleString('id-ID');
                    } else {
                        document.getElementById('kembalian').value = "Uang Kurang!";
                    }
                }
                </script>

            <?php elseif ($page == 'produk'): ?>
                <!-- Kelola Produk -->
                <div class="card p-4 shadow-sm mb-4">
                    <h5 class="fw-bold mb-3">Tambah Produk Baru</h5>
                    <form method="POST" class="row g-3">
                        <div class="col-md-4">
                            <input type="text" name="nama_produk" class="form-control" placeholder="Nama Produk" required>
                        </div>
                        <div class="col-md-3">
                            <input type="number" name="harga" class="form-control" placeholder="Harga (Rp)" required>
                        </div>
                        <div class="col-md-3">
                            <input type="number" name="stok" class="form-control" placeholder="Jumlah Stok" required>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" name="tambah_produk" class="btn btn-primary w-100">Simpan</button>
                        </div>
                    </form>
                </div>

                <div class="card p-4 shadow-sm">
                    <h5 class="fw-bold mb-3">Daftar Produk</h5>
                    <table class="table table-bordered align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>No</th>
                                <th>Nama Produk</th>
                                <th>Harga</th>
                                <th>Stok</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                                $no = 1;
                                $q = mysqli_query($koneksi, "SELECT * FROM produk ORDER BY id DESC");
                                while($row = mysqli_fetch_assoc($q)):
                            ?>
                            <tr>
                                <td><?= $no++; ?></td>
                                <td><?= htmlspecialchars($row['nama_produk']); ?></td>
                                <td>Rp <?= number_format($row['harga'], 0, ',', '.'); ?></td>
                                <td><span class="badge bg-<?= $row['stok'] > 0 ? 'success' : 'danger'; ?>"><?= $row['stok']; ?></span></td>
                                <td>
                                    <a href="dashboard.php?page=produk&hapus_produk=<?= $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Hapus produk ini?')">Hapus</a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>

            <?php elseif ($page == 'riwayat'): ?>
                <!-- Riwayat Transaksi -->
                <div class="card p-4 shadow-sm">
                    <table class="table table-striped align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>No Nota</th>
                                <th>Tanggal</th>
                                <th>Pembeli</th>
                                <th>Produk</th>
                                <th>Jumlah</th>
                                <th>Total</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                                $q = mysqli_query($koneksi, "SELECT t.*, p.nama_produk FROM transaksi t JOIN produk p ON t.id_produk = p.id ORDER BY t.id DESC");
                                while($row = mysqli_fetch_assoc($q)):
                            ?>
                            <tr>
                                <td><span class="fw-bold text-primary"><?= $row['no_nota']; ?></span></td>
                                <td><?= $row['tanggal']; ?></td>
                                <td><?= htmlspecialchars($row['nama_pembeli']); ?></td>
                                <td><?= htmlspecialchars($row['nama_produk']); ?></td>
                                <td><?= $row['jumlah']; ?></td>
                                <td>Rp <?= number_format($row['total_harga'], 0, ',', '.'); ?></td>
                                <td>
                                    <a href="cetak_nota.php?no_nota=<?= $row['no_nota']; ?>" target="_blank" class="btn btn-sm btn-outline-secondary">Cetak Struk</a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>

        </div>
    </div>
</div>

<!-- SweetAlert Jika Transaksi Berhasil -> Buka Halaman Cetak Nota -->
<?php if (!empty($nota_tercetak)): ?>
<script>
    Swal.fire({
        title: 'Transaksi Berhasil!',
        text: 'Klik OK untuk mencetak nota pembayarannya.',
        icon: 'success',
        confirmButtonText: 'Cetak Nota'
    }).then((result) => {
        if (result.isConfirmed) {
            window.open('cetak_nota.php?no_nota=<?= $nota_tercetak; ?>', '_blank');
        }
    });
</script>
<?php endif; ?>

</body>
</html>