<?php

include "config/koneksi.php";

$password_admin = password_hash("admin123", PASSWORD_DEFAULT);
$password_guru = password_hash("guru123", PASSWORD_DEFAULT);

mysqli_query($koneksi, "
    UPDATE t_users 
    SET password='$password_admin', role='admin'
    WHERE email='user1@sekolah.sch.id'
");

mysqli_query($koneksi, "
    UPDATE t_users 
    SET password='$password_guru', role='guru'
    WHERE email='user2@sekolah.sch.id'
");

echo "Password berhasil dibuat!";