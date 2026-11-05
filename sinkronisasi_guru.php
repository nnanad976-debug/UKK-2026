
<?php
session_start();
include __DIR__ . "/config/koneksi.php";

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

if (strtolower($_SESSION['role'] ?? '') !== 'admin') {
    exit("Akses ditolak. Login sebagai admin terlebih dahulu.");
}

$guru = [
"Wawan Kurniawan, S.Pd.I",
"Ai Nuryaidah, S.Pd",
"Maya Permata Endah, S. Pd",
"Dody Jaenudin, S.Pd.",
"Dedi Kardiman, S. Pd",
"Uca Musa Yusenda, S.Pd",
"Drs. Ahmad Nur Mukhlis",
"Drs. Heri",
"Asep Bastaman",
"H. Encep Maemun, S.T., M.Pd",
"Opik",
"Ida Farida, S. Pd",
"Idah Mahmudah, S.Pd",
"Drs. Muhamad Ali",
"Helmi Ali Akbar, S.Pd.I",
"Syeni Suswarini, S.Pd",
"Wiwid Yulianti, S.Pd",
"Ansor Muhajir, S.T",
"Ogi Gaosun Gias, S.Pd.I",
"Sandi Mulyana, S.Pd",
"Erna Suryani, S.Pd",
"Ai Wahidah, S.Ag",
"Chandra Aditya Yusup, S.Pd",
"Arif Kusmawan, S.Pd",
"Asep Taufiq Hidayat, S.Kom",
"Rendy Ichsan Kusumah, S.T",
"Feri Rahmawan, S.Pd",
"Ari Sandriana, S.Kom",
"Yaneu Nurdian, S.Pd, M.Pd",
"Ucu Muhammad Khoer N, S.Pd",
"Mira Dwi Rahmawati, S.Ag",
"Aldi Maulana, S.Pd",
"Eli Rosliah, S.Pd",
"Isni Nur Fitriani, S.Pd",
"Aanisah Putri Nur Aini, S.Pd",
"Yusi Luthfiani, S.Sos.I",
"Lilis Rustandy Mawardi, S.Pd., M.Pd",
"Yusrin Noer Sabrina, S.Pd",
"Senny Indayani, S.Pd",
"Ilma Amalia, S.Pd",
"Alvira Sofwatunnisa, S.Pd",
"Risna Andriyani, S.Pd",
"Dra. Tintin Noerhayati",
"Rizkia Fahira, S.Pd",
"Frasetio",
"Ratnaningsih Hatil Aeni, S.Pd.",
"Dheny Darajatul Hasani, S.Pd.",
"Fahrullah, S.Pd",
"Aan Andriana, S.T",
"Fitri Fauziah, S.T",
"Nida Aulia, S.Pd",
"Sri Nurrizki Putri Krisnadi, S.Pd",
"Riesy Putri Navia, S.Kom",
"Rurry Febriani Maryadi, S.Pd",
"Irvan Rizal, S.Pd"
];

function namaDasar($nama) {
    $nama = preg_replace('/,\s*.*$/u', '', $nama);
    $nama = preg_replace('/^(Drs\.|Dra\.|Dr\.|H\.|Hj\.)\s*/iu', '', $nama);
    return trim($nama);
}

function emailGuru($nama) {
    $slug = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', namaDasar($nama));
    $slug = strtolower($slug);
    $slug = preg_replace('/[^a-z0-9]+/', '.', $slug);
    return trim($slug, '.') . '@gmail.com';
}

function passwordGuru($nama) {
    $kata = preg_split('/\s+/', namaDasar($nama));
    return strtolower($kata[0]) . '123';
}

function cekEmail($koneksi, $email, $userId, $guruId) {
    $stmt = $koneksi->prepare(
        "SELECT id FROM t_users WHERE email=? AND id<>?"
    );
    $stmt->bind_param("si", $email, $userId);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        throw new Exception("Email akun sudah digunakan: " . $email);
    }
    $stmt->close();

    $stmt = $koneksi->prepare(
        "SELECT id FROM t_guru WHERE email=? AND id<>?"
    );
    $stmt->bind_param("si", $email, $guruId);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        throw new Exception("Email guru sudah digunakan: " . $email);
    }
    $stmt->close();
}

// Pasangan kelas_id dan guru_id untuk 38 wali kelas.
$wali = [
    [1,18], [16,38], [21,32], [13,27],
    [27,2], [2,39], [63,20], [64,46],
    [39,42], [6,28], [9,36], [11,54],
    [42,47], [25,35], [24,55], [3,29],
    [59,41], [22,48], [8,16], [37,34],
    [7,21], [5,23], [17,26], [29,11],
    [4,15], [60,33], [12,40], [40,37],
    [25,50], [20,49], [30,52], [45,53],
    [15,22], [44,30], [19,56], [34,44],
    [14,31], [49,24]
];

if (count($guru) !== 55 || count($wali) !== 38) {
    exit("Jumlah data tidak sesuai. Tidak ada perubahan dilakukan.");
}

try {
    mysqli_begin_transaction($koneksi);

    // Pastikan data guru lama tersedia.
    $cek = $koneksi->query(
        "SELECT COUNT(*) AS total FROM t_guru WHERE id BETWEEN 1 AND 50"
    )->fetch_assoc();

    if ((int)$cek['total'] !== 50) {
        throw new Exception("Data guru ID 1-50 tidak lengkap.");
    }

    // Pastikan akun guru ID 2-50 tersedia.
    $cek = $koneksi->query(
        "SELECT COUNT(*) AS total FROM t_users
         WHERE id BETWEEN 2 AND 50 AND role='guru'"
    )->fetch_assoc();

    if ((int)$cek['total'] !== 49) {
        throw new Exception("Akun guru lama ID 2-50 tidak lengkap.");
    }

    // Pastikan data wali kelas dan backup tersedia.
    $cek = $koneksi->query(
        "SELECT COUNT(*) AS total FROM t_wali_kelas
         WHERE tahun_ajaran_id=50"
    )->fetch_assoc();

    if ((int)$cek['total'] !== 52) {
        throw new Exception("Jumlah wali kelas lama tidak sesuai 52 data.");
    }

    $cek = $koneksi->query(
        "SELECT COUNT(*) AS total FROM backup_t_wali_kelas_20261105"
    )->fetch_assoc();

    if ((int)$cek['total'] !== 52) {
        throw new Exception("Backup wali kelas tidak ditemukan atau tidak lengkap.");
    }

    // Buat akun baru khusus guru pertama.
    $nama = $guru[0];
    $email = emailGuru($nama);
    cekEmail($koneksi, $email, 0, 1);

    $hash = password_hash(passwordGuru($nama), PASSWORD_DEFAULT);

    $stmt = $koneksi->prepare(
        "INSERT INTO t_users (name,email,password,role)
         VALUES (?, ?, ?, 'guru')"
    );
    $stmt->bind_param("sss", $nama, $email, $hash);
    $stmt->execute();
    $userPertama = $koneksi->insert_id;
    $stmt->close();

    // Sinkronisasi guru ID 1-50.
    for ($i = 0; $i < 50; $i++) {
        $idGuru = $i + 1;
        $nama = $guru[$i];
        $email = emailGuru($nama);
        $hash = password_hash(passwordGuru($nama), PASSWORD_DEFAULT);

        $userId = ($idGuru === 1) ? $userPertama : $idGuru;

        cekEmail($koneksi, $email, $userId, $idGuru);

        $stmt = $koneksi->prepare(
            "UPDATE t_users
             SET name=?, email=?, password=?, role='guru'
             WHERE id=? AND role='guru'"
        );

        if ($idGuru === 1) {
            // Akun admin ID 1 tidak disentuh.
            // Akun guru pertama sudah dibuat di atas.
        } else {
            $stmt->bind_param("sssi", $nama, $email, $hash, $userId);
            $stmt->execute();
        }
        $stmt->close();

        $stmt = $koneksi->prepare(
            "UPDATE t_guru
             SET nama=?, email=?, user_id=?, status_aktif=1
             WHERE id=?"
        );
        $stmt->bind_param("ssii", $nama, $email, $userId, $idGuru);
        $stmt->execute();
        $stmt->close();
    }

    // Tambahkan lima guru resmi berikutnya sebagai ID 52-56.
    for ($i = 50; $i < 55; $i++) {
        $idGuru = $i + 2;
        $nama = $guru[$i];
        $email = emailGuru($nama);
        $hash = password_hash(passwordGuru($nama), PASSWORD_DEFAULT);

        cekEmail($koneksi, $email, 0, 0);

        $stmt = $koneksi->prepare(
            "INSERT INTO t_users (name,email,password,role)
             VALUES (?, ?, ?, 'guru')"
        );
        $stmt->bind_param("sss", $nama, $email, $hash);
        $stmt->execute();
        $userId = $koneksi->insert_id;
        $stmt->close();

        $nip = "BELUM-ADA-" . str_pad($idGuru, 3, "0", STR_PAD_LEFT);
        $status = 1;

        $stmt = $koneksi->prepare(
            "INSERT INTO t_guru
             (nip,nama,email,status_aktif,user_id)
             VALUES (?, ?, ?, ?, ?)"
        );
        $stmt->bind_param("sssii", $nip, $nama, $email, $status, $userId);
        $stmt->execute();
        $stmt->close();
    }

    // Nonaktifkan data percobaan Nadia, tanpa menghapusnya.
    $koneksi->query(
        "UPDATE t_guru
         SET status_aktif=0, user_id=NULL
         WHERE id=51"
    );

    $koneksi->query(
        "UPDATE t_users SET role='nonaktif' WHERE id=51"
    );

    // Perbarui nama guru pada riwayat percobaan.
    $koneksi->query(
        "UPDATE t_pelanggaran_siswa p
         INNER JOIN t_guru g ON p.guru_id=g.id
         SET p.nama_guru=g.nama
         WHERE g.id BETWEEN 1 AND 50
            OR g.id BETWEEN 52 AND 56"
    );

    // Ambil 52 penugasan lama untuk tahun ajaran 2026/2027.
    $hasil = $koneksi->query(
        "SELECT id FROM t_wali_kelas
         WHERE tahun_ajaran_id=50
         ORDER BY id"
    );

    $ids = [];
    while ($baris = $hasil->fetch_assoc()) {
        $ids[] = (int)$baris['id'];
    }

    // Perbarui 38 penugasan sesuai daftar resmi.
    for ($i = 0; $i < 38; $i++) {
        $idBaris = $ids[$i];
        $kelasId = $wali[$i][0];
        $guruId = $wali[$i][1];

        $stmt = $koneksi->prepare(
            "UPDATE t_wali_kelas
             SET kelas_id=?, guru_id=?, status_aktif=1
             WHERE id=? AND tahun_ajaran_id=50"
        );
        $stmt->bind_param("iii", $kelasId, $guruId, $idBaris);
        $stmt->execute();
        $stmt->close();
    }

    // Hapus 14 penugasan percobaan yang tersisa.
    for ($i = 38; $i < count($ids); $i++) {
        $idBaris = $ids[$i];

        $stmt = $koneksi->prepare(
            "DELETE FROM t_wali_kelas
             WHERE id=? AND tahun_ajaran_id=50"
        );
        $stmt->bind_param("i", $idBaris);
        $stmt->execute();
        $stmt->close();
    }

    mysqli_commit($koneksi);

    echo "<h2>Sinkronisasi berhasil!</h2>";
    echo "<p>55 guru resmi sudah disiapkan.</p>";
    echo "<p>38 wali kelas sudah diperbarui.</p>";
    echo "<p>Akun admin tetap dipertahankan.</p>";
    echo "<p>Nadia dinonaktifkan sebagai data percobaan.</p>";
    echo "<h3>Daftar akun login guru</h3>";
    echo "<table border='1' cellpadding='7'>";
    echo "<tr><th>Nama</th><th>Email</th><th>Password awal</th></tr>";

    foreach ($guru as $nama) {
        echo "<tr><td>" . htmlspecialchars($nama) . "</td>";
        echo "<td>" . htmlspecialchars(emailGuru($nama)) . "</td>";
        echo "<td>" . htmlspecialchars(passwordGuru($nama)) . "</td></tr>";
    }

    echo "</table>";
    echo "<p>Simpan daftar akun ini. Setelah selesai, hapus file sinkronisasi_guru.php.</p>";

} catch (Throwable $e) {
    mysqli_rollback($koneksi);
    echo "<h3>Sinkronisasi dibatalkan</h3>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
}
