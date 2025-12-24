<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}
include 'config.php';

$members = mysqli_query($conn, "SELECT * FROM members WHERE status='pending' ORDER BY id DESC");

if (isset($_GET['verify'])) {
    $id = $_GET['verify'];
    mysqli_query($conn, "UPDATE members SET status='verified' WHERE id=$id");
    echo "<script>alert('Member berhasil diverifikasi!'); window.location='verifikasi_member.php';</script>";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Verifikasi Member</title>
    <style>
        body { font-family: Poppins; background:#f5f6fa; padding:20px; }
        table { width:100%; border-collapse:collapse; margin-top:20px; }
        th, td { padding:10px; border:1px solid #ddd; text-align:center; }
        th { background:#007bff; color:#fff; }
        .btn { background:#007bff; color:white; padding:6px 10px; border-radius:5px; text-decoration:none; }
        .btn:hover { background:#0056b3; }
    </style>
</head>
<body>

<h2>🔐 Verifikasi Member Pending</h2>

<a href="dashboard.php" class="btn">← Kembali ke Dashboard</a>

<table>
    <tr>
        <th>No</th>
        <th>Nama</th>
        <th>Username</th>
        <th>Email</th>
        <th>Status</th>
        <th>Aksi</th>
    </tr>

    <?php 
    $no=1; 
    while($row=mysqli_fetch_assoc($members)){ ?>
        <tr>
            <td><?= $no ?></td>
            <td><?= $row['nama'] ?></td>
            <td><?= $row['username'] ?></td>
            <td><?= $row['email'] ?></td>
            <td><?= $row['status'] ?></td>
            <td>
                <a class="btn" href="verifikasi_member.php?verify=<?= $row['id'] ?>">Verifikasi</a>
            </td>
        </tr>
    <?php $no++; } ?>

    <?php if($no==1){ ?>
        <tr><td colspan="6">Tidak ada member pending.</td></tr>
    <?php } ?>
</table>

</body>
</html>
