<?php
session_start();

if (isset($_SESSION['login']) && $_SESSION['login'] == true) {

    if ($_SESSION['role'] == 'admin') {
        header("Location: admin/dashboard.php");
    } else {
        header("Location: guru/dashboard.php");
    }

    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
</head>
<body>

<h2>Login Sistem Informasi Pelanggaran Siswa</h2>

<form action="proses_login.php" method="POST">

    <label>Email</label><br>
    <input type="email" name="email" required>

    <br><br>

    <label>Password</label><br>
    <input type="password" name="password" required>

    <br><br>

    <button type="submit">Login</button>

</form>

</body>
</html>