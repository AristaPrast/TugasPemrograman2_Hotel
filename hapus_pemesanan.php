<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}
include 'config.php';

if (isset($_GET['id_pemesanan'])) {
    $id = intval($_GET['id_pemesanan']);
    mysqli_query($conn, "DELETE FROM pemesanan WHERE id_pemesanan = $id");
}

header("Location: dashboard.php");
exit;
?>
