<?php

session_start();

include "config/koneksi.php";

$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

$query = mysqli_query(
    $koneksi,
    "SELECT * FROM t_users WHERE email='$email'"
);

$user = mysqli_fetch_assoc($query);

if ($user && password_verify($password, $user['password'])) {

    $_SESSION['login'] = true;
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['nama'] = $user['name'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['role'] = $user['role'];

    // TES SESSION
    echo "LOGIN BERHASIL<br>";
    echo "Nama: " . $_SESSION['nama'] . "<br>";
    echo "Role: " . $_SESSION['role'] . "<br>";
    echo "Session login: " . $_SESSION['login'];

    echo '<br><br><a href="dashboard.php">MASUK DASHBOARD</a>';
    exit;
}

echo "LOGIN GAGAL";
echo "<br>Email atau password salah";
echo '<br><br><a href="login.php">Kembali ke Login</a>';
exit;

?>