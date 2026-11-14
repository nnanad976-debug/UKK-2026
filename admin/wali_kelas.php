
<?php
include "../middleware/auth.php";
include "../config/koneksi.php";

if (strtolower(trim($_SESSION['role'] ?? '')) !== 'admin') {
    http_response_code(403);
    exit("Anda tidak memiliki akses");
}

mysqli_report(MYSQLI_REPORT_OFF);
mysqli_set_charset($koneksi, 'utf8mb4');

function e($nilai) {
    return htmlspecialchars((string)($nilai ?? ''), ENT_QUOTES, 'UTF-8');
}

function tanggalIndonesia($tanggal) {
    if (empty($tanggal) || $tanggal === '0000-00-00') {
        return '-';
    }

    $waktu = strtotime($tanggal);
    return $waktu ? date('d-m-Y', $waktu) : '-';
}

function tanggalValid($tanggal) {
    if (
        !is_string($tanggal) ||
        !preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal)
    ) {
        return false;
    }

    $bagian = explode('-', $tanggal);

    return checkdate(
        (int)$bagian[1],
        (int)$bagian[2],
        (int)$bagian[0]
    );
}

function ambilSatu($koneksi, $sql, $types = '', ...$params) {
    $stmt = mysqli_prepare($koneksi, $sql);

    if (!$stmt) {
        return false;
    }

    if ($types !== '') {
        mysqli_stmt_bind_param($stmt, $types, ...$params);
    }

    if (!mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        return false;
    }

    $hasil = mysqli_stmt_get_result($stmt);
    $data = $hasil ? mysqli_fetch_assoc($hasil) : null;

    mysqli_stmt_close($stmt);

    return $data;
}

/* =========================
   AMBIL TAHUN AJARAN AKTIF
========================= */

$tahunAktif = ambilSatu(
    $koneksi,
    "SELECT id, nama, tanggal_mulai, tanggal_selesai
     FROM t_tahun_ajaran
     WHERE status_aktif = 1
     ORDER BY tanggal_mulai DESC, id DESC
     LIMIT 1"
);

$tahunAktifId = $tahunAktif ? (int)$tahunAktif['id'] : 0;

$pesan = '';
$jenis_pesan = '';
$bukaForm = false;
$modeEdit = false;
$dataEdit = null;

/* =========================
   VALIDASI RENTANG TAHUN
========================= */

function tanggalDalamTahunAjaran($mulai, $selesai, $tahun) {
    if (
        !$tahun ||
        !tanggalValid($mulai) ||
        !tanggalValid($selesai) ||
        empty($tahun['tanggal_mulai']) ||
        empty($tahun['tanggal_selesai']) ||
        !tanggalValid($tahun['tanggal_mulai']) ||
        !tanggalValid($tahun['tanggal_selesai'])
    ) {
        return false;
    }

    return (
        $mulai >= $tahun['tanggal_mulai'] &&
        $selesai >= $mulai &&
        $selesai <= $tahun['tanggal_selesai']
    );
}

/* =========================
   PROSES TAMBAH WALI KELAS
========================= */

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['simpan'])) {

    $tahun_ajaran_id = (int)($_POST['tahun_ajaran_id'] ?? 0);
    $kelas_id = (int)($_POST['kelas_id'] ?? 0);
    $guru_id = (int)($_POST['guru_id'] ?? 0);
    $tanggal_mulai = trim($_POST['tanggal_mulai'] ?? '');
    $tanggal_selesai = trim($_POST['tanggal_selesai'] ?? '');

    $bukaForm = true;

    if (!$tahunAktif) {
        $pesan = 'Belum ada tahun ajaran aktif. Aktifkan tahun ajaran terlebih dahulu.';
        $jenis_pesan = 'danger';

    } elseif ($tahun_ajaran_id !== $tahunAktifId) {
        $pesan = 'Penugasan wali kelas hanya dapat dilakukan untuk tahun ajaran aktif: ' . $tahunAktif['nama'] . '.';
        $jenis_pesan = 'danger';

    } elseif (
        $kelas_id <= 0 ||
        $guru_id <= 0 ||
        !tanggalValid($tanggal_mulai)
    ) {
        $pesan = 'Semua data wajib diisi dengan benar.';
        $jenis_pesan = 'danger';

    } else {

        $selesai = $tanggal_selesai !== ''
            ? $tanggal_selesai
            : ($tahunAktif['tanggal_selesai'] ?? '');

        if (!tanggalValid($selesai)) {
            $pesan = 'Tanggal selesai tahun ajaran belum diatur atau tidak valid.';
            $jenis_pesan = 'danger';

        } elseif (!tanggalDalamTahunAjaran($tanggal_mulai, $selesai, $tahunAktif)) {
            $pesan = 'Tanggal penugasan harus berada dalam rentang tahun ajaran aktif (' .
                tanggalIndonesia($tahunAktif['tanggal_mulai']) . ' sampai ' .
                tanggalIndonesia($tahunAktif['tanggal_selesai']) . ').';
            $jenis_pesan = 'danger';

        } else {

            $kelas = ambilSatu(
                $koneksi,
                "SELECT id FROM t_kelas
                 WHERE id = ? AND status_aktif = 1
                 LIMIT 1",
                'i',
                $kelas_id
            );

            // Guru wajib masih aktif.
            $guru = ambilSatu(
                $koneksi,
                "SELECT id FROM t_guru
                 WHERE id = ? AND status_aktif = 1
                 LIMIT 1",
                'i',
                $guru_id
            );

            // Penugasan guru yang sudah tidak aktif tidak menghalangi
            // penetapan wali kelas pengganti.
            $duplikat = ambilSatu(
                $koneksi,
                "SELECT wk.id
                 FROM t_wali_kelas wk
                 INNER JOIN t_tahun_ajaran ta
                    ON ta.id = wk.tahun_ajaran_id
                 INNER JOIN t_guru g
                    ON g.id = wk.guru_id
                 WHERE wk.tahun_ajaran_id = ?
                   AND wk.kelas_id = ?
                   AND wk.status_aktif = 1
                   AND ta.status_aktif = 1
                   AND g.status_aktif = 1
                   AND CURDATE() >= wk.tanggal_mulai
                   AND CURDATE() <= COALESCE(
                       NULLIF(wk.tanggal_selesai, '0000-00-00'),
                       ta.tanggal_selesai
                   )
                 LIMIT 1",
                'ii',
                $tahunAktifId,
                $kelas_id
            );

            if (
                $kelas === false ||
                $guru === false ||
                $duplikat === false
            ) {
                $pesan = 'Gagal memeriksa data: ' . mysqli_error($koneksi);
                $jenis_pesan = 'danger';

            } elseif (!$kelas || !$guru) {
                $pesan = 'Kelas atau guru tidak valid maupun tidak aktif.';
                $jenis_pesan = 'danger';

            } elseif ($duplikat) {
                $pesan = 'Kelas tersebut sudah memiliki wali kelas yang masih aktif pada tahun ajaran ini.';
                $jenis_pesan = 'danger';

            } else {

                $stmt = mysqli_prepare(
                    $koneksi,
                    "INSERT INTO t_wali_kelas
                    (
                        tahun_ajaran_id,
                        kelas_id,
                        guru_id,
                        tanggal_mulai,
                        tanggal_selesai,
                        status_aktif
                    )
                    VALUES (?, ?, ?, ?, ?, 1)"
                );

                if ($stmt) {

                    mysqli_stmt_bind_param(
                        $stmt,
                        'iiiss',
                        $tahunAktifId,
                        $kelas_id,
                        $guru_id,
                        $tanggal_mulai,
                        $selesai
                    );

                    if (mysqli_stmt_execute($stmt)) {
                        mysqli_stmt_close($stmt);

                        header('Location: wali_kelas.php?berhasil=tambah');
                        exit;
                    }

                    $pesan = 'Data wali kelas gagal disimpan: ' . mysqli_stmt_error($stmt);
                    mysqli_stmt_close($stmt);

                } else {
                    $pesan = 'Data wali kelas gagal disimpan: ' . mysqli_error($koneksi);
                }

                $jenis_pesan = 'danger';
            }
        }
    }
}

/* =========================
   PROSES EDIT WALI KELAS
========================= */

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {

    $id = (int)($_POST['id'] ?? 0);
    $tahun_ajaran_id = (int)($_POST['tahun_ajaran_id'] ?? 0);
    $kelas_id = (int)($_POST['kelas_id'] ?? 0);
    $guru_id = (int)($_POST['guru_id'] ?? 0);
    $tanggal_mulai = trim($_POST['tanggal_mulai'] ?? '');
    $tanggal_selesai = trim($_POST['tanggal_selesai'] ?? '');

    $bukaForm = true;
    $modeEdit = true;

    if (!$tahunAktif) {
        $pesan = 'Belum ada tahun ajaran aktif.';
        $jenis_pesan = 'danger';

    } elseif ($tahun_ajaran_id !== $tahunAktifId) {
        $pesan = 'Pengeditan hanya dapat dilakukan untuk tahun ajaran aktif: ' . $tahunAktif['nama'] . '.';
        $jenis_pesan = 'danger';

    } elseif (
        $id <= 0 ||
        $kelas_id <= 0 ||
        $guru_id <= 0 ||
        !tanggalValid($tanggal_mulai)
    ) {
        $pesan = 'Data edit tidak lengkap atau tidak valid.';
        $jenis_pesan = 'danger';

    } else {

        $selesai = $tanggal_selesai !== ''
            ? $tanggal_selesai
            : ($tahunAktif['tanggal_selesai'] ?? '');

        if (!tanggalValid($selesai)) {
            $pesan = 'Tanggal selesai tahun ajaran belum diatur atau tidak valid.';
            $jenis_pesan = 'danger';

        } elseif (!tanggalDalamTahunAjaran($tanggal_mulai, $selesai, $tahunAktif)) {
            $pesan = 'Tanggal penugasan harus berada dalam rentang tahun ajaran aktif (' .
                tanggalIndonesia($tahunAktif['tanggal_mulai']) . ' sampai ' .
                tanggalIndonesia($tahunAktif['tanggal_selesai']) . ').';
            $jenis_pesan = 'danger';

        } else {

            // Status berlaku juga bergantung pada status guru.
            $lama = ambilSatu(
                $koneksi,
                "SELECT
                    wk.id,
                    wk.tahun_ajaran_id,
                    wk.status_aktif,
                    ta.status_aktif AS tahun_status_aktif,
                    g.status_aktif AS guru_status_aktif,
                    CASE
                        WHEN wk.status_aktif = 1
                         AND ta.status_aktif = 1
                         AND g.status_aktif = 1
                         AND CURDATE() >= wk.tanggal_mulai
                         AND CURDATE() <= COALESCE(
                             NULLIF(wk.tanggal_selesai, '0000-00-00'),
                             ta.tanggal_selesai
                         )
                        THEN 1
                        ELSE 0
                    END AS status_berlaku
                 FROM t_wali_kelas wk
                 INNER JOIN t_tahun_ajaran ta
                    ON ta.id = wk.tahun_ajaran_id
                 INNER JOIN t_guru g
                    ON g.id = wk.guru_id
                 WHERE wk.id = ?
                 LIMIT 1",
                'i',
                $id
            );

            $kelas = ambilSatu(
                $koneksi,
                "SELECT id FROM t_kelas
                 WHERE id = ? AND status_aktif = 1
                 LIMIT 1",
                'i',
                $kelas_id
            );

            $guru = ambilSatu(
                $koneksi,
                "SELECT id FROM t_guru
                 WHERE id = ? AND status_aktif = 1
                 LIMIT 1",
                'i',
                $guru_id
            );

            $duplikat = ambilSatu(
                $koneksi,
                "SELECT wk.id
                 FROM t_wali_kelas wk
                 INNER JOIN t_tahun_ajaran ta
                    ON ta.id = wk.tahun_ajaran_id
                 INNER JOIN t_guru g
                    ON g.id = wk.guru_id
                 WHERE wk.tahun_ajaran_id = ?
                   AND wk.kelas_id = ?
                   AND wk.status_aktif = 1
                   AND ta.status_aktif = 1
                   AND g.status_aktif = 1
                   AND wk.id <> ?
                   AND CURDATE() >= wk.tanggal_mulai
                   AND CURDATE() <= COALESCE(
                       NULLIF(wk.tanggal_selesai, '0000-00-00'),
                       ta.tanggal_selesai
                   )
                 LIMIT 1",
                'iii',
                $tahunAktifId,
                $kelas_id,
                $id
            );

            if (
                $lama === false ||
                $kelas === false ||
                $guru === false ||
                $duplikat === false
            ) {
                $pesan = 'Gagal memeriksa data edit: ' . mysqli_error($koneksi);
                $jenis_pesan = 'danger';

            } elseif (!$lama) {
                $pesan = 'Data wali kelas tidak ditemukan.';
                $jenis_pesan = 'danger';

            } elseif ((int)$lama['status_berlaku'] !== 1) {
                $pesan = 'Penugasan sudah tidak aktif, guru sudah dinonaktifkan, atau tanggalnya sudah berakhir sehingga tidak dapat diedit.';
                $jenis_pesan = 'danger';

            } elseif ((int)$lama['tahun_ajaran_id'] !== $tahunAktifId) {
                $pesan = 'Riwayat penugasan tahun ajaran sebelumnya tidak dapat diedit.';
                $jenis_pesan = 'danger';

            } elseif (!$kelas || !$guru) {
                $pesan = 'Kelas atau guru tidak valid maupun tidak aktif.';
                $jenis_pesan = 'danger';

            } elseif ($duplikat) {
                $pesan = 'Kelas tersebut sudah memiliki wali kelas yang masih aktif pada tahun ajaran ini.';
                $jenis_pesan = 'danger';

            } else {

                $stmt = mysqli_prepare(
                    $koneksi,
                    "UPDATE t_wali_kelas
                     SET tahun_ajaran_id = ?,
                         kelas_id = ?,
                         guru_id = ?,
                         tanggal_mulai = ?,
                         tanggal_selesai = ?
                     WHERE id = ?
                       AND status_aktif = 1
                       AND tahun_ajaran_id = ?"
                );

                if ($stmt) {

                    mysqli_stmt_bind_param(
                        $stmt,
                        'iiissii',
                        $tahunAktifId,
                        $kelas_id,
                        $guru_id,
                        $tanggal_mulai,
                        $selesai,
                        $id,
                        $tahunAktifId
                    );

                    if (mysqli_stmt_execute($stmt)) {
                        mysqli_stmt_close($stmt);

                        header('Location: wali_kelas.php?berhasil=edit');
                        exit;
                    }

                    $pesan = 'Data wali kelas gagal diperbarui: ' . mysqli_stmt_error($stmt);
                    mysqli_stmt_close($stmt);

                } else {
                    $pesan = 'Data wali kelas gagal diperbarui: ' . mysqli_error($koneksi);
                }

                $jenis_pesan = 'danger';
            }
        }
    }

    $dataEdit = [
        'id' => $id,
        'tahun_ajaran_id' => $tahun_ajaran_id,
        'kelas_id' => $kelas_id,
        'guru_id' => $guru_id,
        'tanggal_mulai' => $tanggal_mulai,
        'tanggal_selesai' => $tanggal_selesai
    ];
}

/* =========================
   PROSES HAPUS / NONAKTIFKAN
========================= */

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['hapus'])) {

    $id = (int)($_POST['id'] ?? 0);

    if ($id <= 0) {
        $pesan = 'ID wali kelas tidak valid.';
        $jenis_pesan = 'danger';

    } else {

        $stmt = mysqli_prepare(
            $koneksi,
            "UPDATE t_wali_kelas
             SET status_aktif = 0
             WHERE id = ? AND status_aktif = 1"
        );

        if ($stmt) {

            mysqli_stmt_bind_param($stmt, 'i', $id);

            if (
                mysqli_stmt_execute($stmt) &&
                mysqli_stmt_affected_rows($stmt) > 0
            ) {
                mysqli_stmt_close($stmt);

                header('Location: wali_kelas.php?berhasil=hapus');
                exit;
            }

            $pesan = 'Data tidak ditemukan, atau penugasan sudah tidak aktif.';
            mysqli_stmt_close($stmt);

        } else {
            $pesan = 'Gagal menonaktifkan penugasan: ' . mysqli_error($koneksi);
        }

        $jenis_pesan = 'danger';
    }
}

/* =========================
   PESAN BERHASIL
========================= */

if (isset($_GET['berhasil'])) {

    $kode = $_GET['berhasil'];

    if ($kode === 'tambah') {
        $pesan = 'Data wali kelas berhasil disimpan!';
        $jenis_pesan = 'success';

    } elseif ($kode === 'edit') {
        $pesan = 'Data wali kelas berhasil diperbarui!';
        $jenis_pesan = 'success';

    } elseif ($kode === 'hapus') {
        $pesan = 'Penugasan wali kelas berhasil dinonaktifkan.';
        $jenis_pesan = 'success';
    }
}

/* =========================
   AMBIL DATA FORM
========================= */

$query_tahun = mysqli_query(
    $koneksi,
    "SELECT id, nama, tanggal_mulai, tanggal_selesai, status_aktif
     FROM t_tahun_ajaran
     WHERE status_aktif = 1
     ORDER BY tanggal_mulai DESC, id DESC
     LIMIT 1"
);

$query_kelas = mysqli_query(
    $koneksi,
    "SELECT * FROM t_kelas
     WHERE status_aktif = 1
     ORDER BY jurusan ASC, tingkat ASC, nama ASC"
);

// Hanya guru aktif yang dapat dipilih.
$query_guru = mysqli_query(
    $koneksi,
    "SELECT * FROM t_guru
     WHERE status_aktif = 1
     ORDER BY nama ASC"
);

$tahunList = [];
$kelasList = [];
$guruList = [];

if ($query_tahun) {
    while ($row = mysqli_fetch_assoc($query_tahun)) {
        $tahunList[] = $row;
    }
}

if ($query_kelas) {
    while ($row = mysqli_fetch_assoc($query_kelas)) {
        $kelasList[] = $row;
    }
}

if ($query_guru) {
    while ($row = mysqli_fetch_assoc($query_guru)) {
        $guruList[] = $row;
    }
}

/* =========================
   AMBIL DATA EDIT
========================= */

if (isset($_GET['edit']) && !$dataEdit) {

    $idEdit = (int)$_GET['edit'];

    if ($idEdit > 0) {

        $dataEdit = ambilSatu(
            $koneksi,
            "SELECT
                wk.id,
                wk.tahun_ajaran_id,
                wk.kelas_id,
                wk.guru_id,
                wk.tanggal_mulai,
                wk.tanggal_selesai,
                wk.status_aktif,
                g.status_aktif AS guru_status_aktif,
                CASE
                    WHEN wk.status_aktif = 1
                     AND ta.status_aktif = 1
                     AND g.status_aktif = 1
                     AND CURDATE() >= wk.tanggal_mulai
                     AND CURDATE() <= COALESCE(
                         NULLIF(wk.tanggal_selesai, '0000-00-00'),
                         ta.tanggal_selesai
                     )
                    THEN 1
                    ELSE 0
                END AS status_berlaku
             FROM t_wali_kelas wk
             INNER JOIN t_tahun_ajaran ta
                ON ta.id = wk.tahun_ajaran_id
             INNER JOIN t_guru g
                ON g.id = wk.guru_id
             WHERE wk.id = ?
             LIMIT 1",
            'i',
            $idEdit
        );

        if (
            $dataEdit &&
            (int)$dataEdit['status_berlaku'] === 1 &&
            (int)$dataEdit['tahun_ajaran_id'] === $tahunAktifId
        ) {
            $modeEdit = true;
            $bukaForm = true;

            if (empty($dataEdit['tanggal_selesai']) && $tahunAktif) {
                $dataEdit['tanggal_selesai'] = $tahunAktif['tanggal_selesai'];
            }

        } else {
            $dataEdit = null;
            $pesan = 'Data riwayat tahun ajaran sebelumnya, guru yang sudah tidak aktif, atau penugasan yang sudah berakhir tidak dapat diedit. Pengeditan hanya tersedia untuk penugasan yang masih berlaku pada tahun ajaran aktif.';
            $jenis_pesan = 'danger';
        }
    }
}

/* =========================
   DATA SELURUH RIWAYAT WALI KELAS
========================= */

$query = mysqli_query(
    $koneksi,
    "SELECT
        wk.id,
        wk.tahun_ajaran_id,
        wk.kelas_id,
        wk.guru_id,
        ta.nama AS tahun_ajaran,
        k.nama AS nama_kelas,
        k.tingkat,
        k.jurusan,
        g.nip,
        g.nama AS nama_guru,
        g.status_aktif AS status_guru,
        wk.tanggal_mulai,

        COALESCE(
            NULLIF(wk.tanggal_selesai, '0000-00-00'),
            ta.tanggal_selesai
        ) AS tanggal_selesai,

        CASE
            WHEN wk.status_aktif = 1
             AND ta.status_aktif = 1
             AND g.status_aktif = 1
             AND CURDATE() >= wk.tanggal_mulai
             AND CURDATE() <= COALESCE(
                 NULLIF(wk.tanggal_selesai, '0000-00-00'),
                 ta.tanggal_selesai
             )
            THEN 1
            ELSE 0
        END AS status_aktif

     FROM t_wali_kelas wk

     INNER JOIN t_tahun_ajaran ta
        ON wk.tahun_ajaran_id = ta.id

     INNER JOIN t_kelas k
        ON wk.kelas_id = k.id

     INNER JOIN t_guru g
        ON wk.guru_id = g.id

     ORDER BY wk.id DESC"
);

if (!$query_tahun || !$query_kelas || !$query_guru || !$query) {
    die('Gagal mengambil data: ' . e(mysqli_error($koneksi)));
}

/* =========================
   NILAI FORM
========================= */

$nilaiForm = $dataEdit ?: [
    'tahun_ajaran_id' => $tahunAktifId,
    'kelas_id' => $_POST['kelas_id'] ?? '',
    'guru_id' => $_POST['guru_id'] ?? '',
    'tanggal_mulai' => $_POST['tanggal_mulai'] ?? ($tahunAktif['tanggal_mulai'] ?? ''),
    'tanggal_selesai' => $_POST['tanggal_selesai'] ?? ($tahunAktif['tanggal_selesai'] ?? '')
];

if (!$modeEdit && empty($nilaiForm['tanggal_selesai']) && $tahunAktif) {
    $nilaiForm['tanggal_selesai'] = $tahunAktif['tanggal_selesai'];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Kelola Wali Kelas</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.datatables.net/2.3.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">

<style>
:root {
    --blue:#2878cf;
    --blue-dark:#203e68;
    --blue-soft:#eaf3ff;
    --muted:#7890ad;
    --line:#dce9f8;
}

* {
    box-sizing:border-box;
}

body {
    margin:0;
    background:#f3f7fc;
    color:#2f435a;
    font-family:"Segoe UI",Arial,sans-serif;
    font-size:16px;
}

a {
    text-decoration:none;
}

/* SIDEBAR */

.sidebar {
    position:fixed;
    left:0;
    top:0;
    width:282px;
    height:100vh;
    overflow-y:auto;
    background:#fff;
    border-right:1px solid var(--line);
    padding:25px 14px 16px;
    display:flex;
    flex-direction:column;
    z-index:1000;
}

.brand {
    padding:0 15px 22px;
    border-bottom:1px solid var(--line);
    margin-bottom:22px;
}

.brand-title {
    font-size:18px;
    font-weight:700;
    color:var(--blue-dark);
    margin-bottom:5px;
}

.brand-subtitle {
    font-size:12px;
    color:var(--muted);
}

.menu-title {
    font-size:10px;
    font-weight:700;
    color:#9aabc0;
    letter-spacing:1px;
    text-transform:uppercase;
    margin:0 15px 10px;
}

.menu {
    display:flex;
    flex-direction:column;
    gap:3px;
}

.menu-link {
    min-height:43px;
    display:flex;
    align-items:center;
    padding:10px 15px;
    border-radius:10px;
    color:#5e7695;
    font-size:13px;
    transition:.2s ease;
}

.menu-link:hover {
    background:var(--blue-soft);
    color:var(--blue);
}

.menu-link.active {
    background:var(--blue-soft);
    color:var(--blue);
    font-weight:600;
    box-shadow:inset 4px 0 0 var(--blue);
}

.logout {
    margin-top:auto;
    padding-top:14px;
    border-top:1px solid var(--line);
}

.logout .menu-link {
    color:#d9534f;
}

.logout .menu-link:hover {
    background:#fff0ef;
    color:#c03935;
}

/* KONTEN */

.main {
    margin-left:282px;
    min-height:100vh;
    padding:24px 28px 40px;
}

.container {
    width:100%;
    max-width:none!important;
    margin:0 auto;
    padding:0!important;
}

.judul,
h4 {
    color:#234e78;
}

.card {
    border:1px solid #e1eaf4!important;
    border-radius:10px;
    box-shadow:0 3px 12px rgba(35,78,120,.05)!important;
}

.card-body {
    padding:24px;
}

/* FORM */

.form-label {
    color:#405873;
    font-weight:600;
}

.form-control,
.form-select {
    border-color:#d7e3ef;
    border-radius:7px;
    padding:10px 12px;
    font-family:"Segoe UI",Arial,sans-serif;
    font-size:16px;
}

.form-control:focus,
.form-select:focus {
    border-color:#8eb5d8;
    box-shadow:0 0 0 .2rem rgba(87,143,190,.14);
}

/* TOMBOL */

.btn {
    border-radius:7px;
    font-family:"Segoe UI",Arial,sans-serif;
    font-size:16px;
    font-weight:400;
    line-height:1.5;
    padding:8px 15px;
    transition:background-color .15s ease,border-color .15s ease;
}

.btn-primary {
    --bs-btn-color:#fff;
    --bs-btn-bg:#5f91ba;
    --bs-btn-border-color:#5f91ba;
    --bs-btn-hover-color:#fff;
    --bs-btn-hover-bg:#4b7fa9;
    --bs-btn-hover-border-color:#4b7fa9;
}

.btn-secondary {
    --bs-btn-bg:#e8f0f8;
    --bs-btn-border-color:#d8e5f1;
    --bs-btn-color:#426584;
    --bs-btn-hover-bg:#dceaf6;
    --bs-btn-hover-border-color:#cbddeb;
    --bs-btn-hover-color:#234e78;
}

.btn-warning {
    --bs-btn-color:#fff;
    --bs-btn-bg:#d6a34c;
    --bs-btn-border-color:#d6a34c;
    --bs-btn-hover-color:#fff;
    --bs-btn-hover-bg:#bd8d3c;
    --bs-btn-hover-border-color:#bd8d3c;
}

.btn-danger {
    --bs-btn-bg:#d66b6b;
    --bs-btn-border-color:#d66b6b;
    --bs-btn-hover-bg:#bf5656;
    --bs-btn-hover-border-color:#bf5656;
}

/* TABEL */

.table {
    color:#344b63;
    vertical-align:middle;
    margin-bottom:0;
    font-size:16px;
}

.table thead th {
    background:#eaf2fa;
    color:#315b82;
    border-color:#dce7f1;
    font-weight:600;
    padding:12px;
    white-space:nowrap;
}

.table tbody td {
    border-color:#e5edf5;
    padding:11px 12px;
}

.table-hover tbody tr:hover>* {
    --bs-table-accent-bg:#f4f8fc;
}

/* STATUS */

.status-aktif,
.status-nonaktif {
    display:inline-block;
    padding:5px 10px;
    border-radius:20px;
    font-size:12px;
    font-weight:600;
    white-space:nowrap;
}

.status-aktif {
    background:#e4f3e9;
    color:#34734b;
}

.status-nonaktif {
    background:#fce9e9;
    color:#a34f4f;
}

.keterangan {
    color:#71849a;
    font-size:16px;
}

.aksi {
    display:flex;
    align-items:center;
    gap:6px;
    white-space:nowrap;
}

/* TOMBOL AKSI TABEL */

.aksi .btn {
    padding:6px 10px;
    font-size:12px;
    line-height:1.5;
    border-radius:6px;
}

.aksi .btn-edit {
    color:#fff!important;
    background:#5f91ba!important;
    border:1px solid #5f91ba!important;
}

.aksi .btn-edit:hover,
.aksi .btn-edit:focus {
    color:#fff!important;
    background:#4b7fa9!important;
    border-color:#4b7fa9!important;
}

.aksi .btn-hapus {
    color:#fff!important;
    background:#d77f7f!important;
    border:1px solid #d77f7f!important;
}

.aksi .btn-hapus:hover,
.aksi .btn-hapus:focus {
    color:#fff!important;
    background:#c96868!important;
    border-color:#c96868!important;
}

/* DATATABLES */

.dt-container .dt-layout-row {
    margin:12px 0;
}

.dt-container .dt-layout-row:first-child {
    margin-top:22px;
    margin-bottom:8px;
}

.dt-container .dt-layout-cell {
    display:flex;
    align-items:center;
}

.dt-container .dt-length,
.dt-container .dt-search {
    display:flex;
    align-items:center;
    gap:8px;
}

.dt-container .dt-length label,
.dt-container .dt-search label {
    display:flex;
    align-items:center;
    gap:8px;
    margin:0;
    font-size:13px;
    white-space:nowrap;
}

.dt-container .dt-length select {
    width:45px!important;
    min-width:45px;
    height:38px;
    padding:6px 5px;
    border:1px solid #d7e3ef;
    border-radius:7px;
    background-color:#fff;
    color:#344b63;
}

.dt-container .dt-search input {
    width:175px;
    height:38px;
    padding:7px 12px;
    border:1px solid #d7e3ef;
    border-radius:7px;
    margin-left:0!important;
}

.dt-container .dt-length select:focus,
.dt-container .dt-search input:focus {
    outline:none;
    border-color:#8eb5d8;
    box-shadow:0 0 0 .15rem rgba(87,143,190,.14);
}

.dt-container .dt-layout-table {
    margin-top:8px;
}

.table-responsive {
    width:100%;
}

/* RESPONSIVE */

@media(max-width:900px) {
    .sidebar {
        position:relative;
        width:100%;
        height:auto;
        min-height:auto;
    }

    .main {
        margin-left:0;
        padding:18px 14px 30px;
    }

    .logout {
        margin-top:14px;
    }
}

@media(max-width:576px) {
    .card-body {
        padding:17px;
    }

    .dt-container .dt-search input {
        width:140px;
    }
}
</style>
</head>

<body>

<aside class="sidebar">

    <div class="brand">
        <div class="brand-title">Sistem Pelanggaran</div>
        <div class="brand-subtitle">SMK Muhammadiyah</div>
    </div>

    <div class="menu-title">Menu Utama</div>

    <nav class="menu">
        <a href="../dashboard.php" class="menu-link">Dashboard</a>
        <a href="guru.php" class="menu-link">Kelola Guru</a>
        <a href="siswa.php" class="menu-link">Kelola Siswa</a>
        <a href="kelas.php" class="menu-link">Kelola Kelas</a>
        <a href="tahun_ajaran.php" class="menu-link">Tahun Ajaran</a>
        <a href="penempatan_siswa.php" class="menu-link">Penempatan Siswa</a>
        <a href="wali_kelas.php" class="menu-link active">Wali Kelas</a>
        <a href="kategori_pelanggaran.php" class="menu-link">Kategori Pelanggaran</a>
        <a href="jenis_pelanggaran.php" class="menu-link">Jenis Pelanggaran</a>
        <a href="cetak_export.php" class="menu-link">Laporan</a>
        <a href="../about.php" class="menu-link">About Me</a>
    </nav>

    <div class="logout">
        <a href="../logout.php" class="menu-link">Logout</a>
    </div>

</aside>

<main class="main">
<div class="container py-4">

    <!-- TOMBOL BAGIAN ATAS -->

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">

        <a href="../dashboard.php" class="btn btn-secondary">
            &larr; Kembali ke Dashboard
        </a>

        <?php if (!$bukaForm): ?>
            <button
                type="button"
                class="btn btn-primary"
                id="btnTambahWali"
            >
                + Tambah Wali Kelas
            </button>
        <?php endif; ?>

    </div>

    <!-- JUDUL HALAMAN -->

    <div class="card mb-4">
        <div class="card-body">
            <h2 class="judul mb-1">Kelola Wali Kelas</h2>

            <p class="text-muted mb-0">
                Kelola penugasan guru sebagai wali kelas berdasarkan tahun ajaran dan kelas.
            </p>
        </div>
    </div>

    <!-- PESAN -->

    <?php if ($pesan !== ''): ?>
        <div class="alert alert-<?= e($jenis_pesan); ?> alert-dismissible fade show" role="alert">

            <?= e($pesan); ?>

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert"
                aria-label="Tutup"
            ></button>

        </div>
    <?php endif; ?>

    <!-- FORM TAMBAH / EDIT -->

    <div
        class="card mb-4 <?= $bukaForm ? '' : 'd-none'; ?>"
        id="formTambahWali"
    >
        <div class="card-body">

            <h4 class="mb-4">
                <?= $modeEdit ? 'Edit Wali Kelas' : 'Tambah Wali Kelas'; ?>
            </h4>

            <?php if (!$tahunAktif): ?>

                <div class="alert alert-warning">
                    Belum ada tahun ajaran yang aktif. Aktifkan tahun ajaran terlebih dahulu pada menu Tahun Ajaran.
                </div>

            <?php else: ?>

                <?php if (!$modeEdit): ?>
                    <div class="alert alert-info">
                        Penugasan wali kelas hanya dapat dilakukan untuk tahun ajaran aktif:
                        <strong><?= e($tahunAktif['nama']); ?></strong>.
                    </div>
                <?php endif; ?>

                <form
                    method="POST"
                    action="wali_kelas.php<?= $modeEdit && !empty($nilaiForm['id']) ? '?edit='.(int)$nilaiForm['id'] : ''; ?>"
                >

                    <?php if ($modeEdit): ?>
                        <input
                            type="hidden"
                            name="id"
                            value="<?= (int)$nilaiForm['id']; ?>"
                        >
                    <?php endif; ?>

                    <div class="mb-3">

                        <label for="tahun_ajaran_id" class="form-label">
                            Tahun Ajaran
                        </label>

                        <select
                            name="tahun_ajaran_id"
                            id="tahun_ajaran_id"
                            class="form-select"
                            required
                        >
                            <option value="<?= $tahunAktifId; ?>" selected>
                                <?= e($tahunAktif['nama']); ?> (Aktif)
                            </option>
                        </select>

                    </div>

                    <div class="mb-3">

                        <label for="kelas_id" class="form-label">
                            Kelas
                        </label>

                        <select
                            name="kelas_id"
                            id="kelas_id"
                            class="form-select"
                            required
                        >
                            <option value="">-- Pilih Kelas --</option>

                            <?php foreach ($kelasList as $k): ?>
                                <option
                                    value="<?= (int)$k['id']; ?>"
                                    <?= ((int)$nilaiForm['kelas_id'] === (int)$k['id']) ? 'selected' : ''; ?>
                                >
                                    <?= e($k['nama'].' ('.$k['tingkat'].' - '.$k['jurusan'].')'); ?>
                                </option>
                            <?php endforeach; ?>

                        </select>

                    </div>

                    <div class="mb-3">

                        <label for="guru_id" class="form-label">
                            Guru
                        </label>

                        <select
                            name="guru_id"
                            id="guru_id"
                            class="form-select"
                            required
                        >
                            <option value="">-- Pilih Guru --</option>

                            <?php foreach ($guruList as $g): ?>
                                <option
                                    value="<?= (int)$g['id']; ?>"
                                    <?= ((int)$nilaiForm['guru_id'] === (int)$g['id']) ? 'selected' : ''; ?>
                                >
                                    <?= e(($g['nip'] ?? '-').' - '.$g['nama']); ?>
                                </option>
                            <?php endforeach; ?>

                        </select>

                    </div>

                    <div class="mb-3">

                        <label for="tanggal_mulai" class="form-label">
                            Tanggal Mulai
                        </label>

                        <input
                            type="date"
                            name="tanggal_mulai"
                            id="tanggal_mulai"
                            class="form-control"
                            min="<?= e($tahunAktif['tanggal_mulai']); ?>"
                            max="<?= e($tahunAktif['tanggal_selesai']); ?>"
                            value="<?= e($nilaiForm['tanggal_mulai']); ?>"
                            required
                        >

                    </div>

                    <div class="mb-4">

                        <label for="tanggal_selesai" class="form-label">
                            Tanggal Selesai
                        </label>

                        <input
                            type="date"
                            name="tanggal_selesai"
                            id="tanggal_selesai"
                            class="form-control"
                            min="<?= e($tahunAktif['tanggal_mulai']); ?>"
                            max="<?= e($tahunAktif['tanggal_selesai']); ?>"
                            value="<?= e($nilaiForm['tanggal_selesai']); ?>"
                        >

                        <small class="text-muted">
                            Tanggal penugasan harus berada dalam rentang tahun ajaran aktif.
                        </small>

                    </div>

                    <button
                        type="submit"
                        name="<?= $modeEdit ? 'update' : 'simpan'; ?>"
                        class="btn btn-primary"
                    >
                        <?= $modeEdit ? 'Simpan Perubahan' : 'Simpan Wali Kelas'; ?>
                    </button>

                    <a href="wali_kelas.php" class="btn btn-secondary">
                        Batal
                    </a>

                </form>

            <?php endif; ?>

        </div>
    </div>

    <!-- TABEL DATA WALI KELAS -->

    <div class="card">
        <div class="card-body">

            <h4 class="mb-1">Data Wali Kelas</h4>

            <p class="keterangan mb-3">
                Daftar penugasan wali kelas, termasuk riwayat tahun ajaran sebelumnya.
            </p>

            <div class="table-responsive">

                <table
                    id="tabelWaliKelas"
                    class="table table-bordered table-hover align-middle w-100"
                >

                    <thead>
                        <tr>
                            <th style="width:60px;">No</th>
                            <th>Tahun Ajaran</th>
                            <th>Kelas</th>
                            <th>Tingkat</th>
                            <th>Jurusan</th>
                            <th>NIP</th>
                            <th>Nama Guru</th>
                            <th>Tanggal Mulai</th>
                            <th>Tanggal Selesai</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>

                    <?php while ($d = mysqli_fetch_assoc($query)): ?>

                        <tr>

                            <td></td>

                            <td><?= e($d['tahun_ajaran']); ?></td>

                            <td><?= e($d['nama_kelas']); ?></td>

                            <td><?= e($d['tingkat']); ?></td>

                            <td><?= e($d['jurusan']); ?></td>

                            <td><?= e($d['nip'] ?? '-'); ?></td>

                            <td><?= e($d['nama_guru']); ?></td>

                            <td>
                                <?= e(tanggalIndonesia($d['tanggal_mulai'])); ?>
                            </td>

                            <td>
                                <?= e(tanggalIndonesia($d['tanggal_selesai'])); ?>
                            </td>

                            <td>
                                <?php if ((int)$d['status_aktif'] === 1): ?>
                                    <span class="status-aktif">Aktif</span>
                                <?php else: ?>
                                    <span class="status-nonaktif">Tidak Aktif</span>
                                <?php endif; ?>
                            </td>

                            <td>

                                <div class="aksi">

                                    <?php if (
                                        (int)$d['status_aktif'] === 1 &&
                                        (int)$d['tahun_ajaran_id'] === $tahunAktifId
                                    ): ?>

                                        <a
                                            href="wali_kelas.php?edit=<?= (int)$d['id']; ?>"
                                            class="btn btn-edit"
                                        >
                                            Edit
                                        </a>

                                        <form
                                            method="POST"
                                            action="wali_kelas.php"
                                            onsubmit="return confirm('Yakin ingin menghapus penugasan wali kelas ini? Data akan dinonaktifkan dan riwayatnya tetap tersimpan.');"
                                        >

                                            <input
                                                type="hidden"
                                                name="id"
                                                value="<?= (int)$d['id']; ?>"
                                            >

                                            <button
                                                type="submit"
                                                name="hapus"
                                                class="btn btn-hapus"
                                            >
                                                Hapus
                                            </button>

                                        </form>

                                    <?php else: ?>

                                        <span class="text-muted">-</span>

                                    <?php endif; ?>

                                </div>

                            </td>

                        </tr>

                    <?php endwhile; ?>

                    </tbody>

                </table>

            </div>

        </div>
    </div>

</div>
</main>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/2.3.4/js/dataTables.min.js"></script>
<script src="https://cdn.datatables.net/2.3.4/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const tombolTambah = document.getElementById('btnTambahWali');
    const form = document.getElementById('formTambahWali');

    if (tombolTambah && form) {

        tombolTambah.addEventListener('click', function () {

            form.classList.remove('d-none');
            tombolTambah.classList.add('d-none');

            form.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });

        });

    }

    const tanggalMulai = document.getElementById('tanggal_mulai');
    const tanggalSelesai = document.getElementById('tanggal_selesai');

    if (tanggalMulai && tanggalSelesai) {

        function aturTanggal() {

            tanggalSelesai.min = tanggalMulai.value;

            if (
                tanggalSelesai.value &&
                tanggalSelesai.value < tanggalMulai.value
            ) {
                tanggalSelesai.setCustomValidity(
                    'Tanggal selesai tidak boleh lebih awal dari tanggal mulai.'
                );
            } else {
                tanggalSelesai.setCustomValidity('');
            }

        }

        tanggalMulai.addEventListener('change', aturTanggal);
        tanggalSelesai.addEventListener('change', aturTanggal);

        aturTanggal();

    }

});

/* DATATABLES */

$(document).ready(function () {

    $('#tabelWaliKelas').DataTable({

        pageLength: 10,

        lengthMenu: [5, 10, 25, 50, 100],

        ordering: true,

        searching: true,

        paging: true,

        autoWidth: false,

        columnDefs: [
            {
                targets: [0, 10],
                orderable: false,
                searchable: false
            }
        ],

        language: {

            search: 'Cari:',

            searchPlaceholder: 'Cari wali kelas...',

            lengthMenu: 'Tampilkan _MENU_ data',

            info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ wali kelas',

            infoEmpty: 'Menampilkan 0 sampai 0 dari 0 wali kelas',

            infoFiltered: '(difilter dari _MAX_ total wali kelas)',

            zeroRecords: 'Data wali kelas tidak ditemukan',

            emptyTable: 'Belum ada data wali kelas',

            paginate: {
                previous: 'Sebelumnya',
                next: 'Berikutnya'
            }

        },

        drawCallback: function () {

            const api = this.api();
            const info = api.page.info();

            api.column(0, {
                page: 'current',
                search: 'applied',
                order: 'applied'
            }).nodes().each(function (cell, i) {

                cell.textContent = info.start + i + 1;

            });

        }

    });

});
</script>

</body>
</html>
