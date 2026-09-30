<?php

session_start();

include "config/koneksi.php";

$email = $_POST['email'];
$password = $_POST['password'];

$query = mysqli_query(
    $koneksi,
    "SELECT * FROM t_users WHERE email='$email'"
);

$user = mysqli_fetch_assoc($query);

if ($user && password_verify($password, $user['password'])) {

    $_SESSION['login'] = true;
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['nama'] = $user['name'];
    $_SESSION['role'] = $user['role'];

    header("Location: dashboard.php");
    exit;

} else {

    echo "Email atau password salah.";
    echo "<br>";
    echo "<a href='login.php'>Kembali ke Login</a>";

}