<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}
include 'config.php';

/* =========================================
   AMBIL DATA PEMESANAN & MEMBER
========================================= */
$pemesanan = mysqli_query($conn, "SELECT * FROM pemesanan ORDER BY id_pemesanan DESC");
$members   = mysqli_query($conn, "SELECT * FROM members ORDER BY id DESC");

/* =========================================
   FUNGSI HARGA & KAPASITAS KAMAR
========================================= */
function hargaKamar($tipe) {
    switch ($tipe) {
        case 'Suite':   return 1200000;
        case 'Deluxe':  return 750000;
        case 'Standar': return 500000;
        default:        return 0;
    }
}

function kapasitasKamar($tipe) {
    switch ($tipe) {
        case 'Suite':   return 6;
        case 'Deluxe':  return 4;
        case 'Standar': return 2;
        default:        return 1;
    }
}

function jumlahMalam($checkin, $checkout) {
    $tgl1 = new DateTime($checkin);
    $tgl2 = new DateTime($checkout);
    $diff = $tgl2->diff($tgl1)->days;
    return max($diff, 1);
}

/* =========================================
   AKSI UBAH STATUS MEMBER
   URL: dashboard.php?aksi=ubah_status&id=ID&status=pending|gagal+verifikasi|berhasil+verifikasi
========================================= */
if (isset($_GET['aksi']) && $_GET['aksi'] === 'ubah_status' && isset($_GET['id']) && isset($_GET['status'])) {
    $id     = (int)$_GET['id'];
    $status = mysqli_real_escape_string($conn, $_GET['status']);

    $allowed_status = ['pending', 'gagal verifikasi', 'berhasil verifikasi'];

    if (in_array($status, $allowed_status)) {
        mysqli_query($conn, "UPDATE members SET status='$status' WHERE id=$id");
        echo "<script>alert('Status member berhasil diubah menjadi: $status'); window.location='dashboard.php#verifikasi-member';</script>";
        exit;
    } else {
        echo "<script>alert('Status tidak dikenal!'); window.location='dashboard.php#verifikasi-member';</script>";
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Dashboard Admin - Hotel Luviana</title>
  <style>
    body {
        font-family: "Poppins", sans-serif;
        background-color: #f5f6fa;
        margin:0;
        padding:0;
    }
    header {
        background: #007bff;
        color: white;
        padding: 15px 20px;
        display:flex;
        justify-content: space-between;
        align-items:center;
    }
    header h1 { margin:0; font-size:22px; }
    .logout {
        background:#dc3545;
        color:white;
        padding:8px 14px;
        border-radius:5px;
        text-decoration:none;
    }
    .logout:hover { background:#c82333; }

    .wrapper {
        display:flex;
        min-height: calc(100vh - 60px);
    }

    /* SIDEBAR */
    .sidebar {
        width:230px;
        background:#ffffff;
        border-right:1px solid #ddd;
        padding:20px 15px;
        box-shadow:0 0 10px rgba(0,0,0,0.05);
    }
    .sidebar h2 {
        font-size:18px;
        margin-bottom:15px;
        color:#333;
    }
    .sidebar a.menu-item {
        display:block;
        padding:10px 12px;
        margin-bottom:8px;
        border-radius:8px;
        text-decoration:none;
        color:#333;
        font-size:14px;
        transition:0.2s;
    }
    .sidebar a.menu-item:hover {
        background:#e9f2ff;
        color:#007bff;
    }
    .sidebar a.menu-item.active {
        background:#007bff;
        color:#fff;
    }

    /* KONTEN UTAMA */
    .content {
        flex:1;
        padding:25px;
    }
    .card {
        background:white;
        padding:20px;
        border-radius:10px;
        box-shadow:0 0 10px rgba(0,0,0,0.08);
        margin-bottom:20px;
    }
    h2 {
        margin-top:0;
        color:#333;
    }
    table {
        width:100%;
        border-collapse: collapse;
        margin-top: 15px;
        font-size:14px;
    }
    th, td {
        border: 1px solid #ddd;
        padding: 8px 10px;
        text-align: center;
    }
    th {
        background-color: #007bff;
        color: white;
    }
    tr:nth-child(even) { background-color: #f9f9f9; }

    a.btn {
        display:inline-block;
        background:#007bff;
        color:white;
        padding:5px 10px;
        border-radius:5px;
        text-decoration:none;
        font-size:12px;
        margin:2px 0;
    }
    a.btn:hover { background:#0056b3; }

    .edit-btn {
        background:#17a2b8;
    }
    .edit-btn:hover { background:#117a8b; }

    .delete-btn {
        background:#dc3545;
    }
    .delete-btn:hover { background:#a71d2a; }

    .status {
        padding:5px 10px;
        border-radius:6px;
        font-weight:bold;
        font-size:12px;
    }
    .Menunggu { background:#ffc107; color:#000; }
    .Diterima { background:#28a745; color:white; }
    .Ditolak  { background:#dc3545; color:white; }

    .status-member {
        padding:4px 8px;
        border-radius:6px;
        font-size:12px;
        font-weight:bold;
        display:inline-block;
    }
    .st-pending { background:#ffc107; color:#000; }
    .st-gagal { background:#dc3545; color:#fff; }
    .st-berhasil, .st-verified { background:#28a745; color:#fff; }

    .status-btn-group a {
        display:inline-block;
        margin:2px 1px;
        font-size:11px;
        padding:4px 8px;
    }
    .btn-pending { background:#ffc107; color:#000; }
    .btn-pending:hover { background:#e0a800; }
    .btn-gagal { background:#dc3545; }
    .btn-gagal:hover { background:#c82333; }
    .btn-berhasil { background:#28a745; }
    .btn-berhasil:hover { background:#218838; }

    .top-actions { 
        margin-bottom:10px; 
        display:flex;
        gap:10px;
        flex-wrap:wrap;
    }

    /* SECTION */
    .section {
        display:none;
    }
    .section.active {
        display:block;
    }

    @media (max-width:900px) {
        .wrapper { flex-direction:column; }
        .sidebar {
            width:100%;
            display:flex;
            flex-direction:row;
            align-items:center;
            justify-content:space-between;
        }
        .sidebar h2 { margin-bottom:0; }
        .sidebar a.menu-item { flex:1; text-align:center; }
    }
  </style>

  <script>
    function confirmDelete(nama, url) {
        if (confirm("Apakah Anda yakin ingin menghapus pemesanan untuk " + nama + "?")) {
            window.location.href = url;
        }
    }

    // Fungsi print area tertentu (data pemesanan)
    function printSection(divId) {
        var content = document.getElementById(divId).innerHTML;
        var w = window.open('', '', 'height=700,width=900');
        w.document.write('<html><head><title>Cetak Data Pemesanan</title>');
        w.document.write('<style>');
        w.document.write('body{font-family:Arial, sans-serif;font-size:12px;}');
        w.document.write('table{width:100%;border-collapse:collapse;}');
        w.document.write('th,td{border:1px solid #000;padding:6px;text-align:center;}');
        w.document.write('th{background:#f0f0f0;}');
        w.document.write('</style>');
        w.document.write('</head><body>');
        w.document.write('<h3 style="text-align:center;margin-bottom:10px;">Data Pemesanan - Hotel Luviana</h3>');
        w.document.write(content);
        w.document.write('</body></html>');
        w.document.close();
        w.focus();
        w.print();
        w.close();
    }

    // Script untuk handle sidebar tab
    document.addEventListener("DOMContentLoaded", function() {
        const menuItems = document.querySelectorAll('.menu-item');
        const sections  = document.querySelectorAll('.section');

        function showSection(id) {
            sections.forEach(sec => {
                if (sec.id === id) {
                    sec.classList.add('active');
                } else {
                    sec.classList.remove('active');
                }
            });
        }

        menuItems.forEach(item => {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                const target = this.getAttribute('data-section');

                menuItems.forEach(m => m.classList.remove('active'));
                this.classList.add('active');

                showSection(target);
            });
        });

        // Jika ada hash di URL (misal #verifikasi-member)
        if (window.location.hash) {
            const hash = window.location.hash.replace('#','');
            const link = document.querySelector('.menu-item[data-section="'+hash+'"]');
            if (link) {
                link.click();
                return;
            }
        }

        // default ke data pemesanan
        const first = document.querySelector('.menu-item[data-section="pemesanan"]');
        if (first) first.click();
    });
  </script>
</head>
<body>
  <header>
    <h1>Dashboard Admin - Hotel Luviana</h1>
    <a href="logout.php" class="logout">Logout</a>
  </header>

  <div class="wrapper">
    <!-- SIDEBAR -->
    <div class="sidebar">
        <h2>Menu Admin</h2>
        <a href="#" class="menu-item" data-section="pemesanan">Data Pemesanan</a>
        <a href="#" class="menu-item" data-section="verifikasi-member">Verifikasi Member</a>
        <a href="#" class="menu-item" data-section="data-member">Data Member</a>
    </div>

    <!-- KONTEN -->
    <div class="content">

      <!-- SECTION: DATA PEMESANAN -->
      <div class="section" id="pemesanan">
        <div class="card">
          <div class="top-actions">
            <a href="index.php" class="btn">← Kembali ke Beranda</a>
            <a href="javascript:void(0)" class="btn" onclick="printSection('print-area-pemesanan')">
              🖨 Print Data Pemesanan
            </a>
          </div>
          <h2>Data Pemesanan Kamar</h2>

          <!-- AREA YANG DI-PRINT -->
          <div id="print-area-pemesanan">
            <table>
              <tr>
                <th>No</th>
                <th>Nama Tamu</th>
                <th>Email</th>
                <th>Check-in</th>
                <th>Check-out</th>
                <th>Dewasa</th>
                <th>Anak-anak</th>
                <th>Tipe Kamar</th>
                <th>Harga Kamar / Malam</th>
                <th>Total Biaya</th>
                <th>Status</th>
                <th>Tanggal Pesan</th>
                <th>Aksi</th>
              </tr>

              <?php
              $no = 1;
              while ($row = mysqli_fetch_assoc($pemesanan)) {
                  $status_class = '';
                  if ($row['status_pemesanan'] == 'Menunggu Konfirmasi') $status_class = 'Menunggu';
                  elseif ($row['status_pemesanan'] == 'Diterima')       $status_class = 'Diterima';
                  else                                                   $status_class = 'Ditolak';

                  $harga = hargaKamar($row['tipe_kamar']);
                  $kapasitas = kapasitasKamar($row['tipe_kamar']);
                  $malam = jumlahMalam($row['checkin'], $row['checkout']);
                  $jumlah_dewasa = $row['jumlah_dewasa'];
                  $jumlah_kamar = ceil($jumlah_dewasa / $kapasitas);
                  if ($jumlah_kamar < 1) $jumlah_kamar = 1;

                  $total_biaya = $jumlah_kamar * $harga * $malam;
                  $total_biaya_text = "Rp ".number_format($total_biaya,0,',','.');

                  $hapus_url   = "hapus_pemesanan.php?id_pemesanan=".$row['id_pemesanan'];
                  $rincian_url = "rincian_pemesanan.php?id_pemesanan=".$row['id_pemesanan'];

                  echo "<tr>
                    <td>{$no}</td>
                    <td>{$row['nama_tamu']}</td>
                    <td>{$row['email']}</td>
                    <td>{$row['checkin']}</td>
                    <td>{$row['checkout']}</td>
                    <td>{$row['jumlah_dewasa']}</td>
                    <td>{$row['jumlah_anak']}</td>
                    <td>{$row['tipe_kamar']}</td>
                    <td>Rp ".number_format($harga,0,',','.')."</td>
                    <td>
                        {$total_biaya_text} <br>
                        <a href='{$rincian_url}' class='btn'>Rincian</a>
                    </td>
                    <td><span class='status {$status_class}'>{$row['status_pemesanan']}</span></td>
                    <td>{$row['tanggal_pesan']}</td>
                    <td>
                      <a href='edit_pemesanan.php?id_pemesanan={$row['id_pemesanan']}' class='btn edit-btn'>Edit</a>
                      <a href='javascript:void(0)' onclick='confirmDelete(\"{$row['nama_tamu']}\",\"{$hapus_url}\")' class='btn delete-btn'>Hapus</a>
                    </td>
                  </tr>";
                  $no++;
              }

              if ($no == 1) {
                  echo "<tr><td colspan='13'>Belum ada data pemesanan.</td></tr>";
              }
              ?>
            </table>
          </div><!-- /print-area-pemesanan -->
        </div>
      </div>

      <!-- SECTION: VERIFIKASI MEMBER -->
      <div class="section" id="verifikasi-member">
        <div class="card">
          <h2>Verifikasi Member</h2>
          <p>Pilih status <strong>pending</strong>, <strong>gagal verifikasi</strong>, atau <strong>berhasil verifikasi</strong> untuk setiap member.</p>

          <table>
            <tr>
              <th>No</th>
              <th>Nama</th>
              <th>Username</th>
              <th>Email</th>
              <th>Status Saat Ini</th>
              <th>Ubah Status</th>
            </tr>

            <?php 
            $n=1; 
            mysqli_data_seek($members, 0);
            while($m = mysqli_fetch_assoc($members)){ 
                $status_label = strtolower($m['status']);

                if ($status_label == 'pending') {
                    $status_class = 'st-pending';
                } elseif ($status_label == 'gagal verifikasi') {
                    $status_class = 'st-gagal';
                } elseif ($status_label == 'berhasil verifikasi' || $status_label == 'verified') {
                    $status_class = 'st-berhasil';
                } else {
                    $status_class = 'st-pending';
                }
            ?>
            <tr>
              <td><?= $n ?></td>
              <td><?= htmlspecialchars($m['nama']) ?></td>
              <td><?= htmlspecialchars($m['username']) ?></td>
              <td><?= htmlspecialchars($m['email']) ?></td>
              <td>
                <span class="status-member <?= $status_class ?>">
                    <?= htmlspecialchars($m['status']) ?>
                </span>
              </td>
              <td class="status-btn-group">
                <a href="dashboard.php?aksi=ubah_status&id=<?= $m['id'] ?>&status=pending#verifikasi-member" class="btn btn-pending">Pending</a>
                <a href="dashboard.php?aksi=ubah_status&id=<?= $m['id'] ?>&status=gagal verifikasi#verifikasi-member" class="btn btn-gagal">Gagal</a>
                <a href="dashboard.php?aksi=ubah_status&id=<?= $m['id'] ?>&status=berhasil verifikasi#verifikasi-member" class="btn btn-berhasil">Berhasil</a>
              </td>
            </tr>
            <?php $n++; } ?>

            <?php if($n==1){ ?>
              <tr><td colspan="6">Belum ada data member.</td></tr>
            <?php } ?>
          </table>
        </div>
      </div>

      <!-- SECTION: DATA MEMBER -->
      <div class="section" id="data-member">
        <div class="card">
          <h2>Data Member</h2>
          <p>Hanya menampilkan data member, tanpa aksi verifikasi.</p>

          <table>
            <tr>
              <th>No</th>
              <th>Nama</th>
              <th>Username</th>
              <th>Email</th>
              <th>Status</th>
            </tr>

            <?php 
            $noM=1; 
            mysqli_data_seek($members, 0);
            while($rowM = mysqli_fetch_assoc($members)){ 
                $status_label = strtolower($rowM['status']);
                if ($status_label == 'pending') {
                    $status_class = 'st-pending';
                } elseif ($status_label == 'gagal verifikasi') {
                    $status_class = 'st-gagal';
                } elseif ($status_label == 'berhasil verifikasi' || $status_label == 'verified') {
                    $status_class = 'st-berhasil';
                } else {
                    $status_class = 'st-pending';
                }
            ?>
            <tr>
              <td><?= $noM ?></td>
              <td><?= htmlspecialchars($rowM['nama']) ?></td>
              <td><?= htmlspecialchars($rowM['username']) ?></td>
              <td><?= htmlspecialchars($rowM['email']) ?></td>
              <td>
                <span class="status-member <?= $status_class ?>">
                    <?= htmlspecialchars($rowM['status']) ?>
                </span>
              </td>
            </tr>
            <?php $noM++; } ?>

            <?php if($noM==1){ ?>
              <tr><td colspan="5">Belum ada data member.</td></tr>
            <?php } ?>
          </table>
        </div>
      </div>

    </div>
  </div>
</body>
</html>
