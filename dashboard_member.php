<?php
session_start();
if (!isset($_SESSION['member'])) {
    header("Location: login.php");
    exit;
}
include 'config.php';

$member_id = (int)$_SESSION['member'];

// Ambil data member
$result = mysqli_query($conn, "SELECT * FROM members WHERE id = $member_id");
$member = mysqli_fetch_assoc($result);

if (!$member) {
    echo "Akun member tidak ditemukan. Silakan login ulang.";
    session_destroy();
    exit;
}

$notif = "";

/* ================================
   VERIFIKASI ULANG (dari tombol)
   URL: dashboard_member.php?verifikasi_ulang=1
================================ */
if (isset($_GET['verifikasi_ulang'])) {
    if (strtolower($member['status']) === 'gagal verifikasi') {
        mysqli_query($conn, "UPDATE members SET status='pending' WHERE id=$member_id");
        $member['status'] = 'pending';
        $notif = "Verifikasi ulang telah diajukan. Tunggu persetujuan admin.";
    } else {
        $notif = "Status akun Anda saat ini tidak memerlukan verifikasi ulang.";
    }
}

/* ================================
   UPDATE PROFIL
================================ */
if (isset($_POST['update_profile'])) {
    $nama  = mysqli_real_escape_string($conn, $_POST['nama']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);

    $update = mysqli_query($conn, "
        UPDATE members 
        SET nama='$nama', email='$email'
        WHERE id=$member_id
    ");

    if ($update) {
        $notif = "Profil berhasil diperbarui.";

        // Ambil ulang data terbaru
        $result = mysqli_query($conn, "SELECT * FROM members WHERE id = $member_id");
        $member = mysqli_fetch_assoc($result);
    } else {
        $notif = "Gagal memperbarui profil.";
    }
}

/* ================================
   DATA PEMESANAN SAYA (BERDASARKAN EMAIL)
================================ */
$email_member   = mysqli_real_escape_string($conn, $member['email']);
$pemesanan_saya = mysqli_query($conn, "
    SELECT * FROM pemesanan 
    WHERE email = '$email_member'
    ORDER BY tanggal_pesan DESC, id_pemesanan DESC
");

/* ================================
   FUNGSI HARGA / KAPASITAS / MALAM
================================ */
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

// Fungsi styling status akun member
function status_class($status) {
    $status = strtolower($status);
    if ($status == 'pending') return 'st-pending';
    if ($status == 'gagal verifikasi') return 'st-gagal';
    if ($status == 'berhasil verifikasi' || $status == 'verified') return 'st-berhasil';
    return 'st-pending';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Dashboard Member - Hotel Luviana</title>
<link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css"/>
<style>
    * {
        box-sizing:border-box;
        margin:0;
        padding:0;
    }
    body {
        font-family: "Poppins", sans-serif;
        background:#f5f6fa;
        color:#333;
    }

    /* HEADER */
    header {
        background:#007bff;
        color:#fff;
        padding:12px 25px;
        display:flex;
        justify-content:space-between;
        align-items:center;
        box-shadow:0 2px 6px rgba(0,0,0,0.1);
        position:sticky;
        top:0;
        z-index:10;
    }
    header .title {
        display:flex;
        align-items:center;
        gap:10px;
    }
    header .title i {
        font-size:20px;
    }
    header h1 {
        font-size:18px;
        margin:0;
    }
    .logout {
        background:#dc3545;
        color:#fff;
        padding:8px 14px;
        border-radius:20px;
        text-decoration:none;
        font-size:14px;
        display:flex;
        align-items:center;
        gap:6px;
    }
    .logout i { font-size:14px; }
    .logout:hover { background:#c82333; }

    /* LAYOUT */
    .wrapper {
        display:flex;
        min-height:calc(100vh - 56px);
    }

    /* SIDEBAR */
    .sidebar {
        width:250px;
        background:#ffffff;
        border-right:1px solid #e0e0e0;
        padding:20px 15px;
        box-shadow:2px 0 6px rgba(0,0,0,0.03);
    }
    .sidebar .brand {
        text-align:center;
        margin-bottom:20px;
    }
    .sidebar .avatar {
        width:70px;
        height:70px;
        border-radius:50%;
        background:linear-gradient(135deg,#007bff,#00c8ff);
        display:flex;
        align-items:center;
        justify-content:center;
        color:#fff;
        font-size:28px;
        font-weight:bold;
        margin:0 auto 10px;
    }
    .sidebar .member-name {
        font-weight:600;
        font-size:14px;
    }
    .sidebar .member-username {
        font-size:12px;
        color:#777;
    }

    .sidebar .menu-title {
        font-size:12px;
        text-transform:uppercase;
        color:#999;
        margin:20px 10px 8px;
        letter-spacing:0.5px;
    }

    .sidebar ul {
        list-style:none;
    }
    .sidebar a.nav-link {
        display:flex;
        align-items:center;
        gap:9px;
        padding:9px 12px;
        margin-bottom:5px;
        border-radius:8px;
        text-decoration:none;
        color:#333;
        font-size:14px;
        transition:0.2s;
    }
    .sidebar a.nav-link i {
        width:18px;
        text-align:center;
    }
    .sidebar a.nav-link:hover {
        background:#e9f2ff;
        color:#007bff;
    }
    .sidebar a.nav-link.active {
        background:#007bff;
        color:#fff;
    }

    /* MAIN CONTENT */
    .main {
        flex:1;
        padding:25px 30px;
    }

    .card {
        background:#fff;
        border-radius:12px;
        padding:20px 22px;
        box-shadow:0 2px 8px rgba(0,0,0,0.05);
        margin-bottom:20px;
    }

    h2.section-title {
        font-size:18px;
        margin-bottom:10px;
        display:flex;
        align-items:center;
        gap:8px;
    }
    h2.section-title i {
        color:#007bff;
    }

    .subtitle {
        font-size:13px;
        color:#666;
        margin-bottom:10px;
    }

    .notif {
        padding:10px 12px;
        border-radius:8px;
        margin-bottom:15px;
        background:#e9f7ef;
        color:#155724;
        border:1px solid #c3e6cb;
        font-size:14px;
        display:flex;
        align-items:flex-start;
        gap:8px;
    }
    .notif i { margin-top:2px; }

    .status-badge {
        display:inline-block;
        padding:4px 10px;
        border-radius:20px;
        font-size:12px;
        font-weight:600;
        margin-top:4px;
    }
    .st-pending { background:#fff3cd; color:#856404; }
    .st-gagal { background:#f8d7da; color:#721c24; }
    .st-berhasil { background:#d4edda; color:#155724; }

    .form-group { margin-bottom:12px; }
    label {
        display:block;
        margin-bottom:4px;
        font-weight:600;
        font-size:13px;
    }
    input[type=text],
    input[type=email],
    input[type=date],
    input[type=number],
    select {
        width:100%;
        padding:9px 10px;
        border-radius:8px;
        border:1px solid #ccc;
        font-size:14px;
        transition:0.2s;
    }
    input[type=text]:focus,
    input[type=email]:focus,
    input[type=date]:focus,
    input[type=number]:focus,
    select:focus {
        outline:none;
        border-color:#007bff;
        box-shadow:0 0 0 2px rgba(0,123,255,0.2);
    }
    input[readonly] {
        background:#f1f3f5;
    }

    button, .btn-link {
        display:inline-flex;
        align-items:center;
        justify-content:center;
        gap:6px;
        padding:9px 14px;
        border-radius:8px;
        border:none;
        cursor:pointer;
        font-size:14px;
        text-decoration:none;
    }
    .btn-primary {
        background:#007bff;
        color:#fff;
    }
    .btn-primary:hover { background:#0056b3; }

    .btn-secondary {
        background:#6c757d;
        color:#fff;
    }
    .btn-secondary:hover { background:#5a6268; }

    .btn-warning {
        background:#ffc107;
        color:#000;
    }
    .btn-warning:hover { background:#e0a800; }

    .actions {
        margin-top:12px;
        display:flex;
        flex-wrap:wrap;
        gap:8px;
    }

    .info-box {
        margin-top:15px;
        padding:10px 12px;
        background:#f8f9fa;
        border-radius:8px;
        font-size:13px;
        color:#555;
    }
    .info-box ul {
        margin-left:17px;
        margin-top:5px;
    }

    hr.separator {
        margin:20px 0;
        border:none;
        border-top:1px dashed #ddd;
    }

    .booking-grid {
        display:grid;
        grid-template-columns:repeat(auto-fit,minmax(200px,1fr));
        gap:15px;
        margin-top:10px;
    }

    .booking-note {
        font-size:13px;
        color:#666;
        margin-top:8px;
    }

    .alert-inline {
        margin-top:10px;
        font-size:13px;
        padding:8px 10px;
        border-radius:8px;
        border:1px solid transparent;
    }
    .alert-pending {
        background:#fff3cd;
        border-color:#ffeeba;
        color:#856404;
    }
    .alert-gagal {
        background:#f8d7da;
        border-color:#f5c6cb;
        color:#721c24;
    }

    /* TABLE RIWAYAT & PRICE TABLE */
    table {
        width:100%;
        border-collapse:collapse;
        margin-top:10px;
        font-size:13px;
    }
    th, td {
        border:1px solid #ddd;
        padding:6px 8px;
        text-align:center;
    }
    th {
        background:#007bff;
        color:#fff;
        font-weight:500;
    }
    tr:nth-child(even) { background:#f9f9f9; }

    .badge-status-booking {
        display:inline-block;
        padding:3px 8px;
        border-radius:12px;
        font-size:11px;
        font-weight:600;
    }
    .st-booking-menunggu { background:#fff3cd; color:#856404; }
    .st-booking-diterima  { background:#d4edda; color:#155724; }
    .st-booking-ditolak   { background:#f8d7da; color:#721c24; }

    .price-table {
        width:100%;
        border-collapse:collapse;
        margin-top:8px;
        font-size:12px;
    }
    .price-table th,
    .price-table td {
        border:1px solid #ddd;
        padding:5px 8px;
        text-align:center;
    }
    .price-table th {
        background:#e9f2ff;
        color:#007bff;
    }

    /* SECTIONS (untuk sidebar switching) */
    .content-section { display:none; }
    .content-section.active { display:block; }

    /* RESPONSIVE */
    @media (max-width:900px) {
        .wrapper {
            flex-direction:column;
        }
        .sidebar {
            width:100%;
            display:flex;
            flex-direction:row;
            align-items:center;
            justify-content:space-between;
            padding:10px 15px;
        }
        .sidebar .brand {
            display:flex;
            align-items:center;
            gap:10px;
            margin-bottom:0;
        }
        .sidebar .menu-title {
            display:none;
        }
        .sidebar ul {
            display:flex;
            gap:5px;
        }
        .sidebar a.nav-link {
            font-size:13px;
            padding:7px 10px;
        }
        .main {
            padding:15px;
        }
        table { font-size:12px; }
    }
</style>
<script>
document.addEventListener("DOMContentLoaded", function() {
    const links = document.querySelectorAll('.sidebar a.nav-link');
    const sections = document.querySelectorAll('.content-section');

    function showSection(id) {
        sections.forEach(sec => {
            if (sec.id === id) {
                sec.classList.add('active');
            } else {
                sec.classList.remove('active');
            }
        });
    }

    links.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const target = this.getAttribute('data-section');

            links.forEach(l => l.classList.remove('active'));
            this.classList.add('active');

            showSection(target);
        });
    });

    // Default: tampilkan profil
    const defaultLink = document.querySelector('.sidebar a.nav-link[data-section="section-profil"]');
    if (defaultLink) defaultLink.click();

    // Listener untuk estimasi biaya
    const fields = ['bk-checkin','bk-checkout','bk-adults','bk-children','bk-room-type'];
    fields.forEach(id => {
        const el = document.getElementById(id);
        if (el) {
            el.addEventListener('change', updateEstimation);
            el.addEventListener('input', updateEstimation);
        }
    });
});

// Fungsi print hanya pada area tertentu
function printSection(divId) {
    const content = document.getElementById(divId).innerHTML;
    const w = window.open('', '', 'height=700,width=900');
    w.document.write('<html><head><title>Cetak Data Pemesanan</title>');
    w.document.write('<style>body{font-family:Arial, sans-serif;font-size:12px;} table{width:100%;border-collapse:collapse;} th,td{border:1px solid #000;padding:6px;text-align:center;} th{background:#f0f0f0;}</style>');
    w.document.write('</head><body>');
    w.document.write('<h3 style="text-align:center;margin-bottom:10px;">Data Pemesanan Saya - Hotel Luviana</h3>');
    w.document.write(content);
    w.document.write('</body></html>');
    w.document.close();
    w.focus();
    w.print();
    w.close();
}

// Estimasi biaya di form booking dashboard
function updateEstimation() {
    const checkin  = document.getElementById('bk-checkin')?.value;
    const checkout = document.getElementById('bk-checkout')?.value;
    const adults   = parseInt(document.getElementById('bk-adults')?.value || '0', 10);
    const roomType = document.getElementById('bk-room-type')?.value;
    const box      = document.getElementById('estimasi-biaya');

    if (!box) return;

    if (!checkin || !checkout || !roomType || !adults) {
        box.innerHTML = "Isi tanggal, jumlah dewasa, dan tipe kamar untuk melihat perkiraan biaya.";
        return;
    }

    const t1 = new Date(checkin);
    const t2 = new Date(checkout);
    let nights = Math.round((t2 - t1) / 86400000);
    if (!isFinite(nights) || nights <= 0) nights = 1;

    const hargaMap = {
        "Standar": 500000,
        "Deluxe": 750000,
        "Suite": 1200000
    };
    const kapasitasMap = {
        "Standar": 2,
        "Deluxe": 4,
        "Suite": 6
    };

    const harga = hargaMap[roomType] || 0;
    const kapasitas = kapasitasMap[roomType] || 1;

    let rooms = Math.ceil(adults / kapasitas);
    if (rooms < 1) rooms = 1;

    const total = harga * nights * rooms;

    function rupiah(x) {
        return "Rp " + (x || 0).toLocaleString('id-ID');
    }

    box.innerHTML =
        "<strong>Perkiraan Biaya:</strong><br>" +
        rooms + " kamar × " + rupiah(harga) + " × " + nights + " malam<br>" +
        "<strong>Total: " + rupiah(total) + "</strong>";
}
</script>
</head>
<body>

<header>
    <div class="title">
        <i class="fas fa-hotel"></i>
        <h1>Hotel Luviana &mdash; Member Area</h1>
    </div>
    <a href="logout.php" class="logout">
        <i class="fas fa-sign-out-alt"></i> Logout
    </a>
</header>

<div class="wrapper">
    <!-- SIDEBAR -->
    <aside class="sidebar">
        <div class="brand">
            <div class="avatar">
                <?php
                $initial = strtoupper(substr($member['nama'] ?: $member['username'], 0, 1));
                echo htmlspecialchars($initial);
                ?>
            </div>
            <div>
                <div class="member-name"><?= htmlspecialchars($member['nama']) ?></div>
                <div class="member-username">@<?= htmlspecialchars($member['username']) ?></div>
            </div>
        </div>

        <div class="menu-title">Menu</div>
        <ul>
            <li>
                <a href="#" class="nav-link" data-section="section-profil">
                    <i class="fas fa-user-circle"></i> <span>Profil Saya</span>
                </a>
            </li>
            <li>
                <a href="#" class="nav-link" data-section="section-booking">
                    <i class="fas fa-bed"></i> <span>Pesan Kamar</span>
                </a>
            </li>
            <li>
                <a href="#" class="nav-link" data-section="section-riwayat">
                    <i class="fas fa-list-alt"></i> <span>Pemesanan Saya</span>
                </a>
            </li>
        </ul>
    </aside>

    <!-- MAIN CONTENT -->
    <main class="main">

        <!-- PROFIL SECTION -->
        <section id="section-profil" class="content-section">
            <div class="card">
                <h2 class="section-title"><i class="fas fa-id-card"></i> Profil Akun</h2>
                <p class="subtitle">Kelola data akun Anda. Pastikan nama dan email sudah benar agar proses verifikasi berjalan lancar.</p>

                <?php if (!empty($notif)) { ?>
                    <div class="notif">
                        <i class="fas fa-info-circle"></i>
                        <span><?= htmlspecialchars($notif) ?></span>
                    </div>
                <?php } ?>

                <p>
                    Status Verifikasi:
                    <span class="status-badge <?= status_class($member['status']) ?>">
                        <?= htmlspecialchars($member['status']) ?>
                    </span>
                </p>

                <hr class="separator">

                <form method="POST">
                    <div class="form-group">
                        <label>Username (tidak bisa diubah)</label>
                        <input type="text" value="<?= htmlspecialchars($member['username']) ?>" readonly>
                    </div>

                    <div class="form-group">
                        <label>Nama Lengkap</label>
                        <input type="text" name="nama" required value="<?= htmlspecialchars($member['nama']) ?>">
                    </div>

                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" required value="<?= htmlspecialchars($member['email']) ?>">
                    </div>

                    <button type="submit" name="update_profile" class="btn-primary">
                        <i class="fas fa-save"></i> Simpan Perubahan
                    </button>
                </form>

                <div class="actions">
                    <?php if (strtolower($member['status']) === 'gagal verifikasi') { ?>
                        <a href="dashboard_member.php?verifikasi_ulang=1" class="btn-link btn-warning">
                            <i class="fas fa-sync-alt"></i> Verifikasi Ulang
                        </a>
                    <?php } ?>
                </div>

                <div class="info-box">
                    <strong>Keterangan Status:</strong>
                    <ul>
                        <li><b>Pending</b>: Data Anda sedang menunggu pengecekan admin.</li>
                        <li><b>Gagal verifikasi</b>: Perbaiki data (nama/email) lalu ajukan <i>Verifikasi Ulang</i>.</li>
                        <li><b>Berhasil verifikasi</b>: Anda bisa memesan kamar hotel sebagai member.</li>
                    </ul>
                </div>
            </div>
        </section>

        <!-- BOOKING SECTION -->
        <section id="section-booking" class="content-section">
            <div class="card">
                <h2 class="section-title"><i class="fas fa-bed"></i> Pesan Kamar dari Dashboard</h2>
                <p class="subtitle">
                    Form ini digunakan untuk memesan kamar langsung dari dashboard member.  
                    Untuk member yang sudah <b>berhasil verifikasi</b>, sistem akan memproses pemesanan Anda.
                </p>

                <form action="booking.php" method="POST">
                <input type="hidden" name="source" value="dashboard_member">
                    <div class="booking-grid">
                        <div class="form-group">
                            <label>Nama Lengkap (mengikuti profil)</label>
                            <input type="text" value="<?= htmlspecialchars($member['nama']) ?>" readonly>
                            <input type="hidden" name="nama" value="<?= htmlspecialchars($member['nama']) ?>">
                        </div>

                        <div class="form-group">
                            <label>Email (mengikuti profil)</label>
                            <input type="email" value="<?= htmlspecialchars($member['email']) ?>" readonly>
                            <input type="hidden" name="email" value="<?= htmlspecialchars($member['email']) ?>">
                        </div>

                        <div class="form-group">
                            <label>Check-in</label>
                            <input type="date" name="checkin" id="bk-checkin" required>
                        </div>

                        <div class="form-group">
                            <label>Check-out</label>
                            <input type="date" name="checkout" id="bk-checkout" required>
                        </div>

                        <div class="form-group">
                            <label>Dewasa</label>
                            <input type="number" name="adults" id="bk-adults" min="1" value="1" required>
                        </div>

                        <div class="form-group">
                            <label>Anak-anak</label>
                            <input type="number" name="children" id="bk-children" min="0" value="0">
                        </div>

                        <div class="form-group" style="grid-column: span 2;">
                            <label>Tipe Kamar</label>
                            <select name="room_type" id="bk-room-type" required>
                                <option value="">-- Pilih Kamar --</option>
                                <option value="Standar">Standar</option>
                                <option value="Deluxe">Deluxe</option>
                                <option value="Suite">Suite</option>
                            </select>
                        </div>
                    </div>

                    <div style="margin-top:15px;">
                        <button type="submit" class="btn-primary">
                            <i class="fas fa-paper-plane"></i> Pesan Sekarang
                        </button>
                    </div>
                </form>

                <?php
                $st = strtolower($member['status']);
                if ($st === 'pending') {
                    echo '<div class="alert-inline alert-pending">
                            <i class="fas fa-exclamation-circle"></i>
                            Akun Anda masih <b>pending verifikasi</b>. Admin perlu menyetujui akun Anda sebelum pemesanan diproses.
                          </div>';
                } elseif ($st === 'gagal verifikasi') {
                    echo '<div class="alert-inline alert-gagal">
                            <i class="fas fa-times-circle"></i>
                            Status akun Anda <b>gagal verifikasi</b>. Perbaiki data di menu <b>Profil Saya</b>, lalu klik tombol <b>Verifikasi Ulang</b>.
                          </div>';
                }
                ?>

                <div class="info-box">
                    <strong>Tabel Harga Kamar:</strong>
                    <table class="price-table">
                        <tr>
                            <th>Tipe Kamar</th>
                            <th>Harga / Malam</th>
                            <th>Kapasitas (Dewasa)</th>
                        </tr>
                        <tr>
                            <td>Standar</td>
                            <td>Rp 500.000</td>
                            <td>2 orang</td>
                        </tr>
                        <tr>
                            <td>Deluxe</td>
                            <td>Rp 750.000</td>
                            <td>4 orang</td>
                        </tr>
                        <tr>
                            <td>Suite</td>
                            <td>Rp 1.200.000</td>
                            <td>6 orang</td>
                        </tr>
                    </table>
                    <p class="booking-note">
                        Perhitungan biaya: <b>Jumlah kamar × Harga per malam × Jumlah malam</b>.
                    </p>
                    <p id="estimasi-biaya" class="booking-note" style="margin-top:8px;">
                        Isi tanggal, jumlah dewasa, dan tipe kamar untuk melihat perkiraan biaya.
                    </p>
                </div>
            </div>
        </section>

        <!-- RIWAYAT PEMESANAN SAYA -->
        <section id="section-riwayat" class="content-section">
            <div class="card">
                <h2 class="section-title"><i class="fas fa-list-alt"></i> Pemesanan Saya</h2>
                <p class="subtitle">Riwayat pemesanan kamar yang menggunakan email akun ini, lengkap dengan rincian biaya.</p>

                <div style="display:flex;justify-content:flex-end;margin-bottom:10px;">
                    <button type="button" class="btn-secondary" onclick="printSection('print-area-riwayat')">
                        <i class="fas fa-print"></i> Print Data Pemesanan
                    </button>
                </div>

                <div id="print-area-riwayat">
                    <table>
                        <tr>
                            <th>No</th>
                            <th>ID</th>
                            <th>Tanggal Pesan</th>
                            <th>Check-in</th>
                            <th>Check-out</th>
                            <th>Tipe Kamar</th>
                            <th>Harga/Malam</th>
                            <th>Malam</th>
                            <th>Jumlah Kamar</th>
                            <th>Total Biaya</th>
                            <th>Dewasa</th>
                            <th>Anak</th>
                            <th>Status</th>
                        </tr>
                        <?php
                        $no = 1;
                        if ($pemesanan_saya && mysqli_num_rows($pemesanan_saya) > 0) {
                            while ($row = mysqli_fetch_assoc($pemesanan_saya)) {

                                $harga    = hargaKamar($row['tipe_kamar']);
                                $malam    = jumlahMalam($row['checkin'], $row['checkout']);
                                $kapasitas = kapasitasKamar($row['tipe_kamar']);
                                $jml_dewasa = (int)$row['jumlah_dewasa'];
                                $jml_kamar  = max(1, ceil($jml_dewasa / max($kapasitas,1)));
                                $total_biaya = $jml_kamar * $harga * $malam;

                                $harga_text = "Rp ".number_format($harga,0,',','.');
                                $total_text = "Rp ".number_format($total_biaya,0,',','.');

                                $status_text = $row['status_pemesanan'];
                                $status_class_booking = 'st-booking-menunggu';
                                if (strtolower($row['status_pemesanan']) == 'diterima') {
                                    $status_class_booking = 'st-booking-diterima';
                                } elseif (strtolower($row['status_pemesanan']) == 'ditolak') {
                                    $status_class_booking = 'st-booking-ditolak';
                                }

                                echo "<tr>
                                    <td>{$no}</td>
                                    <td>{$row['id_pemesanan']}</td>
                                    <td>{$row['tanggal_pesan']}</td>
                                    <td>{$row['checkin']}</td>
                                    <td>{$row['checkout']}</td>
                                    <td>{$row['tipe_kamar']}</td>
                                    <td>{$harga_text}</td>
                                    <td>{$malam}</td>
                                    <td>{$jml_kamar}</td>
                                    <td>{$total_text}</td>
                                    <td>{$row['jumlah_dewasa']}</td>
                                    <td>{$row['jumlah_anak']}</td>
                                    <td><span class='badge-status-booking {$status_class_booking}'>".$status_text."</span></td>
                                </tr>";
                                $no++;
                            }
                        } else {
                            echo "<tr><td colspan='13'>Belum ada pemesanan yang tercatat dengan email ini.</td></tr>";
                        }
                        ?>
                    </table>
                </div>

                <div class="info-box" style="margin-top:15px;">
                    <strong>Catatan:</strong>
                    <ul>
                        <li>Perhitungan total biaya: <b>Jumlah kamar × Harga per malam × Jumlah malam</b>, mengikuti kapasitas tiap tipe kamar.</li>
                        <li>Data di atas difilter berdasarkan <b>email</b> yang terdaftar di akun Anda.</li>
                        <li>Jika Anda pernah memesan sebagai tamu (bukan login member) dengan email yang sama, data tersebut tetap muncul di sini.</li>
                    </ul>
                </div>
            </div>
        </section>

    </main>
</div>

</body>
</html>
