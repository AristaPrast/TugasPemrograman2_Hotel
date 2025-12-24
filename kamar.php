<?php
session_start();
include 'config.php';
if (!isset($_SESSION['admin'])) {
  header("Location: login.php");
  exit;
}

$result = mysqli_query($conn, "SELECT * FROM kamar ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Kelola Kamar - Admin</title>
<style>
body { font-family:Poppins, sans-serif; background:#f2f2f2; }
.container { max-width:900px; margin:50px auto; background:white; padding:20px; border-radius:12px; box-shadow:0 0 10px rgba(0,0,0,0.1); }
table { width:100%; border-collapse:collapse; margin-top:20px; }
th, td { padding:10px; border:1px solid #ddd; text-align:left; }
.btn { padding:8px 12px; background:#007bff; color:white; border-radius:6px; text-decoration:none; }
.btn:hover { background:#0056b3; }
</style>
</head>
<body>
<div class="container">
  <h2>Data Kamar</h2>
  <a href="dashboard.php" class="btn">⬅ Kembali</a>
  <table>
    <tr>
      <th>ID</th><th>Nama Kamar</th><th>Harga</th><th>Foto</th>
    </tr>
    <?php while($row = mysqli_fetch_assoc($result)) { ?>
    <tr>
      <td><?= $row['id'] ?></td>
      <td><?= $row['nama_kamar'] ?></td>
      <td>Rp <?= number_format($row['harga'],0,',','.') ?></td>
      <td><img src="uploads/<?= $row['foto'] ?>" width="100"></td>
    </tr>
    <?php } ?>
  </table>
</div>
</body>
</html>
