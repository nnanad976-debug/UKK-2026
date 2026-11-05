
<?php
include __DIR__ . "/config/koneksi.php";

$email = "user10@sekolah.sch.id";
$password = "Fitri12345";

$stmt = mysqli_prepare(
    $koneksi,
    "SELECT password FROM t_users WHERE email = ? LIMIT 1"
);

mysqli_stmt_bind_param($stmt, "s", $email);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $hash);

if (mysqli_stmt_fetch($stmt)) {
    echo password_verify($password, $hash)
        ? "PASSWORD COCOK"
        : "PASSWORD TIDAK COCOK";
} else {
    echo "AKUN TIDAK DITEMUKAN";
}

mysqli_stmt_close($stmt);
?>
