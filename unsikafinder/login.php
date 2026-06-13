<?php
session_start();
include 'koneksi.php';

if (isset($_SESSION['id_user'])) {
    header("Location: index.php");
    exit;
}

if (isset($_POST['register'])) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $user = mysqli_real_escape_string($conn, $_POST['username']);
    $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $wa = mysqli_real_escape_string($conn, $_POST['no_wa']);

    $cek = mysqli_query($conn, "SELECT * FROM users WHERE username='$user'");
    if (mysqli_num_rows($cek) > 0) {
        echo "<script>alert('Username sudah dipakai! Coba yang lain.');</script>";
    } else {
        mysqli_query($conn, "INSERT INTO users (username, password, nama, no_wa) VALUES ('$user', '$pass', '$nama', '$wa')");
        echo "<script>alert('Daftar berhasil! Silakan Login.');</script>";
    }
}

if (isset($_POST['login'])) {
    $user = mysqli_real_escape_string($conn, $_POST['username']);
    $pass = $_POST['password'];

    $cek = mysqli_query($conn, "SELECT * FROM users WHERE username='$user'");
    if (mysqli_num_rows($cek) > 0) {
        $data = mysqli_fetch_assoc($cek);
        if (password_verify($pass, $data['password'])) {
            $_SESSION['id_user'] = $data['id_user'];
            $_SESSION['nama'] = $data['nama'];
            header("Location: index.php");
            exit;
        } else {
            echo "<script>alert('Password salah!');</script>";
        }
    } else {
        echo "<script>alert('Username tidak ditemukan!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - UnsikaFinder</title>
    <link rel="stylesheet" href="login.css">
</head>
<body>

    <div class="login-card">
        <img src="unsika.png" alt="Logo UNSIKA" class="logo" onerror="this.style.display='none'">
        <h2>UnsikaFinder</h2>

        <div id="form-login">
            <form method="POST">
                <input type="text" name="username" placeholder="Username / NPM" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit" name="login">Masuk</button>
            </form>
            <div class="toggle-text">
                Belum punya akun? <span onclick="toggleForm()">Daftar di sini</span>
            </div>
        </div>

        <div id="form-register" style="display: none;">
            <form method="POST">
                <input type="text" name="nama" placeholder="Nama Lengkap" required>
                <input type="text" name="username" placeholder="Buat Username / NPM" required>
                <input type="password" name="password" placeholder="Buat Password" required>
                <input type="number" name="no_wa" placeholder="No. WhatsApp (08...)" required>
                <button type="submit" name="register" style="background: #10b981;">Daftar Sekarang</button>
            </form>
            <div class="toggle-text">
                Sudah punya akun? <span onclick="toggleForm()">Login di sini</span>
            </div>
        </div>
    </div>

    <script>
        function toggleForm() {
            let loginForm = document.getElementById('form-login');
            let regForm = document.getElementById('form-register');
            
            if (loginForm.style.display === 'none') {
                loginForm.style.display = 'block';
                regForm.style.display = 'none';
            } else {
                loginForm.style.display = 'none';
                regForm.style.display = 'block';
            }
        }
    </script>

</body>
</html>