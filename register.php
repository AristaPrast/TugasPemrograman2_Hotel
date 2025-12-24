<?php
include 'config.php';

if (isset($_POST['register'])) {
    $nama = $_POST['nama'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $query = mysqli_query($conn, 
        "INSERT INTO members (nama, username, email, password, status)
         VALUES ('$nama', '$username', '$email', '$password', 'pending')"
    );

    if ($query) {
        echo "<script>alert('Pendaftaran berhasil! Tunggu verifikasi admin.'); window.location='login.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Registrasi Member</title>

<style>
    body {
        font-family: "Poppins", sans-serif;
        background: #f0f2f5;
        margin: 0;
    }

    .container {
        width: 400px;
        margin: 70px auto;
        background: #fff;
        padding: 30px;
        border-radius: 12px;
        box-shadow: 0 0 15px rgba(0,0,0,0.1);
        animation: fadeIn 0.5s ease;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    h2 {
        text-align: center;
        margin-bottom: 20px;
        color: #333;
    }

    .form-group {
        margin-bottom: 15px;
    }

    label {
        font-weight: 600;
        display: block;
        margin-bottom: 6px;
        color: #444;
    }

    input {
        width: 100%;
        padding: 12px;
        border-radius: 6px;
        border: 1px solid #ccc;
        font-size: 14px;
        outline: none;
        transition: 0.2s;
    }

    input:focus {
        border-color: #007bff;
        box-shadow: 0 0 3px rgba(0,123,255,0.5);
    }

    button {
        width: 100%;
        padding: 12px;
        background: #28a745;
        border: none;
        color: white;
        border-radius: 6px;
        font-size: 16px;
        cursor: pointer;
        margin-top: 10px;
        transition: 0.3s;
    }

    button:hover {
        background: #218838;
    }

    .back-btn {
        display: block;
        text-align: center;
        padding: 10px;
        margin-top: 15px;
        background: #6c757d;
        color: white;
        text-decoration: none;
        border-radius: 6px;
    }

    .back-btn:hover {
        background: #5a6268;
    }

</style>

</head>
<body>

<div class="container">
    <h2>Registrasi Member</h2>

    <form method="POST">

        <div class="form-group">
            <label>Nama Lengkap</label>
            <input type="text" name="nama" required>
        </div>

        <div class="form-group">
            <label>Username</label>
            <input type="text" name="username" required>
        </div>

        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" required>
        </div>

        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" required>
        </div>

        <button type="submit" name="register">Daftar</button>

        <a href="login.php" class="back-btn">← Kembali ke Login</a>
    </form>
</div>

</body>
</html>
