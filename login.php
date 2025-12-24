<?php
session_start();
include 'config.php';

// Jika sudah login
if (isset($_SESSION['admin'])) {
    header("Location: dashboard.php");
    exit;
}
if (isset($_SESSION['member'])) {
    header("Location: dashboard_member.php");
    exit;
}

if (isset($_POST['login'])) {

    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];

    /* ================================
       1. CEK LOGIN ADMIN
    ================================== */
    $queryAdmin = mysqli_query($conn, "SELECT * FROM users WHERE username='$username' LIMIT 1");
    $admin = mysqli_fetch_assoc($queryAdmin);

    if ($admin) {
        if ($password == $admin['password']) {
            $_SESSION['admin'] = $admin['username'];
            header("Location: dashboard.php");
            exit;
        } else {
            echo "<script>alert('Password admin salah!');</script>";
        }
    }

    /* ================================
       2. CEK LOGIN MEMBER
    ================================== */
    $queryMember = mysqli_query($conn, "SELECT * FROM members WHERE username='$username' LIMIT 1");
    $member = mysqli_fetch_assoc($queryMember);

    if ($member) {

        // Cek password member (hashed)
        if (!password_verify($password, $member['password'])) {
            echo "<script>alert('Password member salah!');</script>";
            exit;
        }

        // Cek status member
        if ($member['status'] == 'pending') {
          echo "<script>alert('Akun Anda belum diverifikasi admin!');</script>";
          exit;
      }
      
      if ($member['status'] == 'gagal verifikasi') {
          echo "<script>alert('Verifikasi akun Anda gagal. Silakan hubungi admin hotel.');</script>";
          exit;
      }

        // Login berhasil
        $_SESSION['member'] = $member['id'];
        header("Location: dashboard_member.php");
        exit;
    }

    // Username tidak ada di kedua tabel
    echo "<script>alert('Username tidak ditemukan!');</script>";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Login - Hotel</title>
  <style>
    body { font-family: Arial, sans-serif; background: #f2f2f2; }
    .login-box {
      width: 350px; margin: 100px auto; background: white; padding: 30px;
      border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    input[type=text], input[type=password] {
      width: 100%; padding: 10px; margin: 8px 0; border: 1px solid #ccc; border-radius: 6px;
    }
    button {
      background: #007bff; color: white; border: none; padding: 10px;
      width: 100%; border-radius: 6px; cursor: pointer;
    }
    button:hover { background: #0056b3; }

    .btn-back, .btn-register {
      display: block; text-align:center; margin-top: 10px; text-decoration:none;
      padding:8px 12px; border-radius:5px;
    }
    .btn-back { background:#6c757d; color:white; }
    .btn-back:hover { background:#5a6268; }

    .btn-register { background:#28a745; color:white; }
    .btn-register:hover { background:#1e7e34; }

  </style>
</head>
<body>
  <div class="login-box">
    <h2 align="center">Login</h2>

    <form method="POST">
      <label>Username</label>
      <input type="text" name="username" required>

      <label>Password</label>
      <input type="password" name="password" required>

      <button type="submit" name="login">Login</button>

      <a href="register.php" class="btn-register">Buat Akun Member</a>
      <a href="index.php" class="btn-back">← Kembali ke Beranda</a>
    </form>
  </div>
</body>
</html>
