<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}
include 'config.php';

if (!isset($_GET['id_pemesanan'])) {
    echo "Data tidak ditemukan.";
    exit;
}

$id = intval($_GET['id_pemesanan']);
$result = mysqli_query($conn, "SELECT * FROM pemesanan WHERE id_pemesanan = $id");
$row = mysqli_fetch_assoc($result);

if (!$row) {
    echo "Data tidak ditemukan.";
    exit;
}

// Fungsi harga kamar
function hargaKamar($tipe) {
    switch ($tipe) {
        case 'Suite': return 1200000;
        case 'Deluxe': return 750000;
        case 'Standar': return 500000;
        default: return 0;
    }
}

// Fungsi kapasitas kamar
function kapasitasKamar($tipe) {
    switch ($tipe) {
        case 'Suite': return 6;
        case 'Deluxe': return 4;
        case 'Standar': return 2;
        default: return 1;
    }
}

// Fungsi hitung jumlah malam
function jumlahMalam($checkin, $checkout) {
    $tgl1 = new DateTime($checkin);
    $tgl2 = new DateTime($checkout);
    $diff = $tgl2->diff($tgl1)->days;
    return max($diff, 1);
}

// Hitung rincian biaya
$harga = hargaKamar($row['tipe_kamar']);
$kapasitas = kapasitasKamar($row['tipe_kamar']);
$malam = jumlahMalam($row['checkin'], $row['checkout']);
$jumlah_dewasa = $row['jumlah_dewasa'];
$jumlah_kamar = ceil($jumlah_dewasa / $kapasitas);
if ($jumlah_kamar < 1) $jumlah_kamar = 1;

$total_biaya = $jumlah_kamar * $harga * $malam;
$rincian_formula = "$jumlah_kamar kamar × Rp ".number_format($harga,0,',','.')." × $malam malam = Rp ".number_format($total_biaya,0,',','.');
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Rincian Biaya Pemesanan</title>
  <style>
    body { font-family: "Poppins", sans-serif; background:#f5f6fa; padding:30px; }
    .container { background:white; padding:20px; border-radius:10px; width:500px; margin:auto; box-shadow:0 0 10px rgba(0,0,0,0.1);}
    h2 { text-align:center; }
    table { width:100%; border-collapse: collapse; margin-top:20px; }
    th, td { border:1px solid #ddd; padding:10px; text-align:left; }
    th { background:#007bff; color:white; }
    .formula { margin-top:15px; background:#e9ecef; padding:10px; border-radius:5px; }
    a.btn { display:inline-block; margin-top:15px; padding:8px 15px; background:#007bff; color:white; text-decoration:none; border-radius:5px; }
    a.btn:hover { background:#0056b3; }
  </style>
</head>
<body>
  <div class="container">
    <h2>Rincian Biaya Pemesanan</h2>
    <table>
      <tr><th>Nama Tamu</th><td><?php echo $row['nama_tamu']; ?></td></tr>
      <tr><th>Tipe Kamar</th><td><?php echo $row['tipe_kamar']; ?></td></tr>
      <tr><th>Harga Kamar / Malam</th><td>Rp <?php echo number_format($harga,0,',','.'); ?></td></tr>
      <tr><th>Jumlah Malam</th><td><?php echo $malam; ?></td></tr>
      <tr><th>Kapasitas Kamar</th><td><?php echo $kapasitas; ?> orang</td></tr>
      <tr><th>Jumlah Dewasa</th><td><?php echo $jumlah_dewasa; ?></td></tr>
      <tr><th>Jumlah Kamar</th><td><?php echo $jumlah_kamar; ?></td></tr>
      <tr><th>Total Biaya</th><td>Rp <?php echo number_format($total_biaya,0,',','.'); ?></td></tr>
    </table>

    <!-- Rincian formula -->
    <div class="formula">
      <strong>Rincian Perhitungan:</strong><br>
      <?php echo $rincian_formula; ?>
    </div>

    <a href="dashboard.php" class="btn">← Kembali</a>
  </div>
</body>
</html>
