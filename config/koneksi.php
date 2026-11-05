<?php

$koneksi = mysqli_connect(
    "localhost",
    "root",
    "",
    "db_ukk_2026_pulih"
);

if (!$koneksi) {
    die("Koneksi database gagal");
}