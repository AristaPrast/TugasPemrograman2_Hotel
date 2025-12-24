<?php
session_start();
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $member_id = $_SESSION['member'] ?? null;

    $nama  = "";
    $email = "";

    /* ==========================================
       Jika user adalah MEMBER, cek status dulu
    =========================================== */
    if ($member_id) {
        $id_member = (int)$member_id;
        $res = mysqli_query($conn, "SELECT * FROM members WHERE id = $id_member");
        if ($res && mysqli_num_rows($res) == 1) {
            $m = mysqli_fetch_assoc($res);
            $status = strtolower($m['status']);

            // STATUS PENDING
            if ($status === 'pending') {
                ?>
                <!DOCTYPE html>
                <html lang="id">
                <head>
                    <meta charset="UTF-8">
                    <title>Pemesanan Ditolak</title>
                </head>
                <body style="font-family:Arial; background:#f5f6fa;">
                    <div style="max-width:500px;margin:60px auto;background:#fff;padding:20px;border-radius:10px;box-shadow:0 0 10px rgba(0,0,0,0.1);">
                        <h3>Pemesanan Tidak Bisa Dilanjutkan</h3>
                        <p>Akun Anda masih <b>pending verifikasi</b>. Tunggu hingga admin menyetujui akun Anda sebelum memesan kamar sebagai member.</p>
                        <a href="dashboard_member.php" style="display:inline-block;margin-top:10px;padding:8px 14px;background:#007bff;color:#fff;text-decoration:none;border-radius:6px;">Kembali ke Dashboard Member</a>
                    </div>
                </body>
                </html>
                <?php
                exit;
            }

            // STATUS GAGAL VERIFIKASI
            if ($status === 'gagal verifikasi') {
                ?>
                <!DOCTYPE html>
                <html lang="id">
                <head>
                    <meta charset="UTF-8">
                    <title>Verifikasi Ulang Diperlukan</title>
                </head>
                <body style="font-family:Arial; background:#f5f6fa;">
                    <div style="max-width:520px;margin:60px auto;background:#fff;padding:20px;border-radius:10px;box-shadow:0 0 10px rgba(0,0,0,0.1);">
                        <h3>Pemesanan Tidak Bisa Dilanjutkan</h3>
                        <p><b>Perbaiki data anda supaya bisa diverifikasi.</b></p>
                        <p>Silakan perbarui data profil Anda (nama & email) di dashboard, lalu ajukan verifikasi ulang.</p>
                        <a href="dashboard_member.php" style="display:inline-block;margin-top:10px;padding:8px 14px;background:#6c757d;color:#fff;text-decoration:none;border-radius:6px;">Perbaiki Data</a>
                        <a href="dashboard_member.php?verifikasi_ulang=1" style="display:inline-block;margin-top:10px;margin-left:5px;padding:8px 14px;background:#ffc107;color:#000;text-decoration:none;border-radius:6px;">Verifikasi Ulang</a>
                    </div>
                </body>
                </html>
                <?php
                exit;
            }

            // STATUS BERHASIL VERIFIKASI / VERIFIED → boleh pesan
            if ($status === 'berhasil verifikasi' || $status === 'verified') {
                $nama  = mysqli_real_escape_string($conn, $m['nama']);
                $email = mysqli_real_escape_string($conn, $m['email']);
            }
        }
    }

    /* ==========================================
       Jika BUKAN MEMBER atau data member tidak terbaca
       pakai data dari form (tamu umum)
    =========================================== */
    if ($nama === "") {
        $nama = mysqli_real_escape_string($conn, $_POST['nama'] ?? '');
    }
    if ($email === "") {
        $email = mysqli_real_escape_string($conn, $_POST['email'] ?? '');
    }

    // Data lain dari form booking
    $checkin  = mysqli_real_escape_string($conn, $_POST['checkin']);
    $checkout = mysqli_real_escape_string($conn, $_POST['checkout']);
    $dewasa   = (int)($_POST['adults'] ?? 1);
    $anak     = (int)($_POST['children'] ?? 0);
    $kamar    = mysqli_real_escape_string($conn, $_POST['room_type']);

    $query = "INSERT INTO pemesanan (nama_tamu, email, checkin, checkout, jumlah_dewasa, jumlah_anak, tipe_kamar)
          VALUES ('$nama', '$email', '$checkin', '$checkout', '$dewasa', '$anak', '$kamar')";

$source = $_POST['source'] ?? '';

if (mysqli_query($conn, $query)) {
    if ($source === 'dashboard_member') {
        echo "<script>
                alert('Silahkan menunggu status konfirmasi pemesanan.');
                window.location.href='dashboard_member.php?tab=riwayat';
              </script>";
    } else {
        echo "<script>
                alert('Pemesanan Anda telah berhasil dikirim!');
                window.location.href='index.php#booking';
              </script>";
    }
} else {
    if ($source === 'dashboard_member') {
        echo "<script>
                alert('Terjadi kesalahan saat menyimpan data pemesanan!');
                window.location.href='dashboard_member.php?tab=riwayat';
              </script>";
    } else {
        echo "<script>
                alert('Terjadi kesalahan saat menyimpan data!');
                window.location.href='index.php#booking';
              </script>";
    }
}

} else {
    header("Location: index.php");
    exit();
}
?>
