<?php
include 'config.php';
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

// Ambil data berdasarkan id_pemesanan
if (isset($_GET['id_pemesanan'])) {
    $id = $_GET['id_pemesanan'];
    $query = mysqli_query($conn, "SELECT * FROM pemesanan WHERE id_pemesanan='$id'");
    $data = mysqli_fetch_assoc($query);
    if (!$data) {
        echo "<script>alert('Data tidak ditemukan!'); window.location='dashboard_admin.php';</script>";
        exit;
    }
} else {
    echo "<script>window.location='dashboard_admin.php';</script>";
    exit;
}

// Update data ketika tombol ditekan
if (isset($_POST['update'])) {
    $status = $_POST['status_pemesanan'];
    $tipe_kamar = $_POST['tipe_kamar'];

    $update = mysqli_query($conn, "
        UPDATE pemesanan 
        SET status_pemesanan='$status', tipe_kamar='$tipe_kamar'
        WHERE id_pemesanan='$id'
    ");

    if ($update) {
        echo "<script>
            alert('✅ Data berhasil diperbarui!');
            window.location='dashboard.php';
        </script>";
        exit;
    } else {
        echo "<script>alert('❌ Gagal memperbarui data.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Edit Pemesanan</title>
<style>
body {
    font-family: "Poppins", sans-serif;
    background-color: #f5f6fa;
    padding: 40px;
}
.container {
    width: 400px;
    margin: auto;
    background: white;
    padding: 25px;
    border-radius: 10px;
    box-shadow: 0 0 10px rgba(0,0,0,0.1);
}
h2 {
    text-align: center;
    color: #333;
}
label {
    font-weight: bold;
    display: block;
    margin-top: 10px;
}
select, input {
    width: 100%;
    padding: 8px;
    margin-top: 5px;
    border: 1px solid #ccc;
    border-radius: 5px;
}
button {
    width: 100%;
    padding: 10px;
    margin-top: 15px;
    background-color: #007bff;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-weight: bold;
}
button:hover {
    background-color: #0056b3;
}
a {
    text-decoration: none;
    display: block;
    text-align: center;
    margin-top: 10px;
    color: #007bff;
}
</style>
</head>
<body>
<div class="container">
    <h2>Edit Pemesanan</h2>
    <form method="POST">
        <label>Status Pemesanan:</label>
        <select name="status_pemesanan" required>
            <option value="Menunggu Konfirmasi" <?= ($data['status_pemesanan']=='Menunggu Konfirmasi')?'selected':''; ?>>Menunggu Konfirmasi</option>
            <option value="Diterima" <?= ($data['status_pemesanan']=='Diterima')?'selected':''; ?>>Diterima</option>
            <option value="Ditolak" <?= ($data['status_pemesanan']=='Ditolak')?'selected':''; ?>>Ditolak</option>
        </select>

        <label>Tipe Kamar:</label>
        <select name="tipe_kamar" required>
            <option value="Standar" <?= ($data['tipe_kamar']=='Standar')?'selected':''; ?>>Standar</option>
            <option value="Deluxe" <?= ($data['tipe_kamar']=='Deluxe')?'selected':''; ?>>Deluxe</option>
            <option value="Suite" <?= ($data['tipe_kamar']=='Suite')?'selected':''; ?>>Suite</option>
        </select>

        <button type="submit" name="update">💾 Simpan Perubahan</button>
    </form>
    <a href="dashboard.php">⬅ Kembali ke Dashboard</a>
</div>
</body>
</html>
