<?php

session_start();

include __DIR__ . "/config/koneksi.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: login.php");
    exit;
}

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if ($email === '' || $password === '') {
    $_SESSION['pesan_error'] = 'Email dan password wajib diisi.';
    header("Location: login.php");
    exit;
}

$email = mysqli_real_escape_string($koneksi, $email);

$query = mysqli_query(
    $koneksi,
    "SELECT id, name, email, password, role
     FROM t_users
     WHERE email = '$email'
     LIMIT 1"
);

if (!$query) {
    die("Query login gagal: " . mysqli_error($koneksi));
}

$user = mysqli_fetch_assoc($query);

if (!$user || !password_verify($password, $user['password'])) {
    $_SESSION['pesan_error'] = 'Email atau password salah.';
    header("Location: login.php");
    exit;
}

if ($user['role'] !== 'admin' && $user['role'] !== 'guru') {
    $_SESSION['pesan_error'] = 'Role akun tidak valid.';
    header("Location: login.php");
    exit;
}

session_regenerate_id(true);

$_SESSION['login']   = true;
$_SESSION['user_id'] = $user['id'];
$_SESSION['nama']    = $user['name'];
$_SESSION['email']   = $user['email'];
$_SESSION['role']    = $user['role'];

header("Location: dashboard.php");
exit;
?>