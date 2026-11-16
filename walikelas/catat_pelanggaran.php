
<?php
include __DIR__ . "/../middleware/auth.php";
include __DIR__ . "/../config/koneksi.php";

$role = strtolower(trim($_SESSION['role'] ?? ''));

if ($role !== 'guru' || empty($_SESSION['is_wali_kelas'])) {
    http_response_code(403);
    exit("Anda tidak memiliki akses.");
}

$pesan = '';
$jenis_pesan = '';

$user_id = (int)($_SESSION['user_id'] ?? 0);
$session_guru_id = (int)($_SESSION['guru_id'] ?? 0);
$nama_login = $_SESSION['nama'] ?? 'Wali Kelas';

/* =========================================
   FUNGSI BANTU
========================================= */

function e($value): string
{
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}

function labelKelas($nama, $tingkat, $jurusan): string
{
    $nama = trim((string)$nama);
    $tingkat = trim((string)$tingkat);
    $jurusan = trim((string)$jurusan);

    if (
        $nama !== '' &&
        stripos($nama, $tingkat) !== false &&
        stripos($nama, $jurusan) !== false
    ) {
        return $nama;
    }

    if ($nama !== '' && ctype_digit($nama)) {
        return trim("$tingkat $jurusan $nama");
    }

    if ($nama === '' || strcasecmp($nama, $jurusan) === 0) {
        return trim("$tingkat $jurusan");
    }

    return trim("$tingkat $jurusan $nama");
}

/*
 * Mengambil label tahun ajaran tanpa mengasumsikan
 * satu nama kolom tertentu.
 */
function labelTahunAjaran($data): string
{
    if (!$data) {
        return 'Tahun Ajaran Aktif';
    }

    $kemungkinanKolom = [
        'nama_tahun_ajaran',
        'tahun_ajaran',
        'nama',
        'tahun',
        'periode'
    ];

    foreach ($kemungkinanKolom as $kolom) {
        if (!empty($data[$kolom])) {
            return trim((string)$data[$kolom]);
        }
    }

    if (
        !empty($data['tahun_mulai']) &&
        !empty($data['tahun_selesai'])
    ) {
        return $data['tahun_mulai'] . '/' . $data['tahun_selesai'];
    }

    return 'Tahun Ajaran Aktif';
}

/* =========================================
   DATA GURU
========================================= */

$data_guru = null;

if ($user_id > 0) {
    $stmt = mysqli_prepare(
        $koneksi,
        "SELECT id, nama
         FROM t_guru
         WHERE user_id = ?
         LIMIT 1"
    );

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $user_id);
        mysqli_stmt_execute($stmt);
        $data_guru = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
        mysqli_stmt_close($stmt);
    }
}

if (!$data_guru && $session_guru_id > 0) {
    $stmt = mysqli_prepare(
        $koneksi,
        "SELECT id, nama
         FROM t_guru
         WHERE id = ?
         LIMIT 1"
    );

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $session_guru_id);
        mysqli_stmt_execute($stmt);
        $data_guru = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
        mysqli_stmt_close($stmt);
    }
}

$guru_id = (int)($data_guru['id'] ?? 0);
$nama_guru = $data_guru['nama'] ?? $nama_login;

if ($guru_id <= 0) {
    $pesan = 'Data guru tidak ditemukan. Silakan hubungi administrator.';
    $jenis_pesan = 'danger';
}

/* =========================================
   TAHUN AJARAN AKTIF
========================================= */

$data_tahun = null;

$query_tahun = mysqli_query(
    $koneksi,
    "SELECT *
     FROM t_tahun_ajaran
     WHERE status_aktif = 1
     ORDER BY id DESC
     LIMIT 1"
);

if ($query_tahun) {
    $data_tahun = mysqli_fetch_assoc($query_tahun);
}

$tahun_ajaran_id = (int)($data_tahun['id'] ?? 0);
$nama_tahun_ajaran = labelTahunAjaran($data_tahun);

/* =========================================
   PROSES SIMPAN TINDAKAN
========================================= */

if (isset($_POST['simpan_tindakan'])) {

    $pelanggaran_siswa_id = (int)($_POST['pelanggaran_siswa_id'] ?? 0);
    $tindakan_baru = trim($_POST['tindakan_baru'] ?? '');
    $status_baru = trim($_POST['status_baru'] ?? '');

    $status_diizinkan = [
        'Diproses',
        'Ditindaklanjuti',
        'Selesai'
    ];

    if ($guru_id <= 0 || $tahun_ajaran_id <= 0) {

        $pesan = 'Data akun atau tahun ajaran tidak valid.';
        $jenis_pesan = 'danger';

    } elseif (
        $pelanggaran_siswa_id <= 0 ||
        $tindakan_baru === '' ||
        !in_array($status_baru, $status_diizinkan, true)
    ) {

        $pesan = 'Lengkapi tindakan dan pilih status yang tersedia.';
        $jenis_pesan = 'danger';

    } else {

        $stmt = mysqli_prepare(
            $koneksi,
            "SELECT id
             FROM t_pelanggaran_siswa
             WHERE id = ?
               AND guru_id = ?
               AND tahun_ajaran_id = ?
               AND COALESCE(status, '') <> 'Selesai'
             LIMIT 1"
        );

        $data_valid = false;

        if ($stmt) {
            mysqli_stmt_bind_param(
                $stmt,
                "iii",
                $pelanggaran_siswa_id,
                $guru_id,
                $tahun_ajaran_id
            );

            mysqli_stmt_execute($stmt);
            $data_valid = (bool)mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
            mysqli_stmt_close($stmt);
        }

        if (!$data_valid) {

            $pesan = 'Data pelanggaran tidak ditemukan, bukan milik akun Anda, atau sudah selesai.';
            $jenis_pesan = 'warning';

        } else {

            $stmt = mysqli_prepare(
                $koneksi,
                "UPDATE t_pelanggaran_siswa
                 SET tindakan = ?, status = ?
                 WHERE id = ?
                   AND guru_id = ?
                   AND tahun_ajaran_id = ?
                   AND COALESCE(status, '') <> 'Selesai'"
            );

            if ($stmt) {
                mysqli_stmt_bind_param(
                    $stmt,
                    "ssiii",
                    $tindakan_baru,
                    $status_baru,
                    $pelanggaran_siswa_id,
                    $guru_id,
                    $tahun_ajaran_id
                );

                $berhasil = mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);

                if ($berhasil) {
                    header("Location: catat_pelanggaran.php?tindakan_berhasil=1#riwayat");
                    exit;
                }

                $pesan = 'Tindakan gagal diperbarui. Silakan coba kembali.';
                $jenis_pesan = 'danger';

            } else {
                $pesan = 'Terjadi kesalahan saat menyiapkan perubahan tindakan.';
                $jenis_pesan = 'danger';
            }
        }
    }
}

/* =========================================
   DAFTAR SISWA DAN PENEMPATAN AKTIF
   KELAS DIAMBIL OTOMATIS DARI DATABASE
========================================= */

$daftar_siswa = [];

if ($tahun_ajaran_id > 0) {

    $stmt = mysqli_prepare(
        $koneksi,
        "SELECT
            s.id,
            s.nis,
            s.nama,
            ks.kelas_id,
            k.nama AS nama_kelas,
            k.tingkat,
            k.jurusan
         FROM t_siswa s
         INNER JOIN t_kelas_siswa ks
            ON ks.siswa_id = s.id
         INNER JOIN t_kelas k
            ON k.id = ks.kelas_id
         WHERE ks.tahun_ajaran_id = ?
           AND ks.status_aktif = 1
           AND ks.tanggal_mulai <= CURDATE()
           AND (
                ks.tanggal_selesai IS NULL
                OR ks.tanggal_selesai >= CURDATE()
           )
           AND s.status_aktif = 1
           AND k.status_aktif = 1
         ORDER BY s.nama ASC, ks.id DESC"
    );

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $tahun_ajaran_id);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);

        /*
         * Satu siswa hanya ditampilkan satu kali.
         * Penempatan aktif terbaru digunakan jika ada
         * data penempatan ganda.
         */
        $siswaSudahAda = [];

        while ($row = mysqli_fetch_assoc($result)) {

            $idSiswa = (int)$row['id'];

            if (isset($siswaSudahAda[$idSiswa])) {
                continue;
            }

            $row['label_kelas'] = labelKelas(
                $row['nama_kelas'],
                $row['tingkat'],
                $row['jurusan']
            );

            $daftar_siswa[] = $row;
            $siswaSudahAda[$idSiswa] = true;
        }

        mysqli_stmt_close($stmt);
    }
}

/* =========================================
   JENIS PELANGGARAN AKTIF
========================================= */

$daftar_jenis = [];

$query_jenis = mysqli_query(
    $koneksi,
    "SELECT id, kode, nama, poin, tingkat, pelanggaran_kategori_id
     FROM t_pelanggaran
     WHERE status_aktif = 1
     ORDER BY id ASC"
);

if ($query_jenis) {
    while ($row = mysqli_fetch_assoc($query_jenis)) {
        $daftar_jenis[] = $row;
    }
}

/* =========================================
   DATA FORM AWAL
========================================= */

$siswa_awal = (int)($_POST['siswa_id'] ?? $_GET['siswa_id'] ?? 0);
$jenis_awal = (int)($_POST['pelanggaran_id'] ?? 0);

/* =========================================
   PROSES SIMPAN PELANGGARAN
========================================= */

if (isset($_POST['simpan'])) {

    $siswa_id = (int)($_POST['siswa_id'] ?? 0);
    $pelanggaran_id = (int)($_POST['pelanggaran_id'] ?? 0);

    $tanggal = trim($_POST['tanggal'] ?? '');
    $keterangan = trim($_POST['keterangan'] ?? '');

    $tanggal_obj = DateTime::createFromFormat('!Y-m-d', $tanggal);

    $tanggal_valid = (
        $tanggal_obj !== false &&
        $tanggal_obj->format('Y-m-d') === $tanggal
    );

    if ($guru_id <= 0) {

        $pesan = 'Data akun Wali Kelas tidak ditemukan.';
        $jenis_pesan = 'danger';

    } elseif ($tahun_ajaran_id <= 0) {

        $pesan = 'Tahun ajaran aktif belum tersedia.';
        $jenis_pesan = 'danger';

    } elseif (
        $siswa_id <= 0 ||
        $pelanggaran_id <= 0 ||
        !$tanggal_valid ||
        $keterangan === ''
    ) {

        $pesan = 'Lengkapi semua data pelanggaran terlebih dahulu.';
        $jenis_pesan = 'danger';

    } else {

        /*
         * KELAS TIDAK DIAMBIL DARI INPUT FORM.
         * Kelas siswa divalidasi ulang dari database.
         */

        $stmt = mysqli_prepare(
            $koneksi,
            "SELECT
                s.id,
                s.nama,
                s.nis,
                ks.kelas_id,
                k.nama AS nama_kelas,
                k.tingkat,
                k.jurusan
             FROM t_siswa s
             INNER JOIN t_kelas_siswa ks
                ON ks.siswa_id = s.id
             INNER JOIN t_kelas k
                ON k.id = ks.kelas_id
             WHERE s.id = ?
               AND ks.tahun_ajaran_id = ?
               AND s.status_aktif = 1
               AND ks.status_aktif = 1
               AND ks.tanggal_mulai <= CURDATE()
               AND (
                    ks.tanggal_selesai IS NULL
                    OR ks.tanggal_selesai >= CURDATE()
               )
               AND k.status_aktif = 1
             ORDER BY ks.id DESC
             LIMIT 1"
        );

        $data_siswa = null;

        if ($stmt) {
            mysqli_stmt_bind_param(
                $stmt,
                "ii",
                $siswa_id,
                $tahun_ajaran_id
            );

            mysqli_stmt_execute($stmt);
            $data_siswa = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
            mysqli_stmt_close($stmt);
        }

        if (!$data_siswa) {

            $pesan = 'Siswa tidak ditemukan atau penempatannya tidak aktif pada tahun ajaran ini.';
            $jenis_pesan = 'danger';

        } else {

            $stmt = mysqli_prepare(
                $koneksi,
                "SELECT id, nama, poin, pelanggaran_kategori_id
                 FROM t_pelanggaran
                 WHERE id = ?
                   AND status_aktif = 1
                 LIMIT 1"
            );

            $data_pelanggaran = null;

            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "i", $pelanggaran_id);
                mysqli_stmt_execute($stmt);
                $data_pelanggaran = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
                mysqli_stmt_close($stmt);
            }

            if (!$data_pelanggaran) {

                $pesan = 'Jenis pelanggaran tidak ditemukan atau tidak aktif.';
                $jenis_pesan = 'danger';

            } else {

                $nama_siswa = $data_siswa['nama'];
                $nama_pelanggaran = $data_pelanggaran['nama'];
                $kategori_id = (int)($data_pelanggaran['pelanggaran_kategori_id'] ?? 0);
                $poin = (int)$data_pelanggaran['poin'];

                $kelas_id = (int)$data_siswa['kelas_id'];

                $nama_kelas = labelKelas(
                    $data_siswa['nama_kelas'],
                    $data_siswa['tingkat'],
                    $data_siswa['jurusan']
                );

                $nama_guru_simpan = $nama_guru;
                $tindakan = '';
                $status = 'Diproses';

                $stmt = mysqli_prepare(
                    $koneksi,
                    "INSERT INTO t_pelanggaran_siswa (
                        tahun_ajaran_id,
                        siswa_id,
                        nama_siswa,
                        pelanggaran_id,
                        nama_pelanggaran,
                        pelanggaran_kategori_id,
                        guru_id,
                        nama_guru,
                        tanggal,
                        keterangan,
                        poin,
                        tindakan,
                        status,
                        kelas_id,
                        nama_kelas
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
                );

                if ($stmt) {

                    mysqli_stmt_bind_param(
                        $stmt,
                        "iisisiisssissis",
                        $tahun_ajaran_id,
                        $siswa_id,
                        $nama_siswa,
                        $pelanggaran_id,
                        $nama_pelanggaran,
                        $kategori_id,
                        $guru_id,
                        $nama_guru_simpan,
                        $tanggal,
                        $keterangan,
                        $poin,
                        $tindakan,
                        $status,
                        $kelas_id,
                        $nama_kelas
                    );

                    $berhasil = mysqli_stmt_execute($stmt);
                    mysqli_stmt_close($stmt);

                    if ($berhasil) {
                        header("Location: catat_pelanggaran.php?berhasil=1#riwayat");
                        exit;
                    }
                }

                $pesan = 'Gagal menyimpan pelanggaran. Periksa struktur tabel database.';
                $jenis_pesan = 'danger';
            }
        }
    }
}

/* =========================================
   PESAN BERHASIL
========================================= */

if (($_GET['berhasil'] ?? '') === '1') {
    $pesan = 'Pelanggaran berhasil dicatat! Data sudah masuk ke riwayat pelanggaran.';
    $jenis_pesan = 'success';
}

if (($_GET['tindakan_berhasil'] ?? '') === '1') {
    $pesan = 'Tindakan dan status pelanggaran berhasil diperbarui!';
    $jenis_pesan = 'success';
}

/* =========================================
   RIWAYAT PELANGGARAN
========================================= */

$daftar_riwayat = [];

if ($guru_id > 0 && $tahun_ajaran_id > 0) {

    $stmt = mysqli_prepare(
        $koneksi,
        "SELECT
            ps.*,
            k.nama AS nama_kelas_asli,
            k.tingkat,
            k.jurusan
         FROM t_pelanggaran_siswa ps
         LEFT JOIN t_kelas k
            ON k.id = ps.kelas_id
         WHERE ps.guru_id = ?
           AND ps.tahun_ajaran_id = ?
         ORDER BY ps.id DESC"
    );

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ii", $guru_id, $tahun_ajaran_id);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);

        while ($row = mysqli_fetch_assoc($result)) {
            $daftar_riwayat[] = $row;
        }

        mysqli_stmt_close($stmt);
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Catat Pelanggaran - Wali Kelas</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.datatables.net/2.3.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">

<style>
:root {
    --blue: #2878cf;
    --blue-dark: #203e68;
    --blue-soft: #eaf3ff;
    --page: #f3f7fc;
    --text: #263f63;
    --muted: #7890ad;
    --line: #dce9f8;
    --white: #ffffff;
    --shadow: 0 3px 12px rgba(35, 78, 120, .045);
    --sidebar-width: 282px;
    --font: "Segoe UI", Arial, sans-serif;
}

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

html {
    min-height: 100%;
    overflow-y: scroll;
}

body {
    width: 100%;
    min-height: 100vh;
    overflow-x: hidden;
    background: var(--page);
    color: var(--text);
    font-family: var(--font);
    font-size: 15px;
    line-height: 1.5;
    -webkit-font-smoothing: antialiased;
}

a {
    color: inherit;
    text-decoration: none;
}

/* SIDEBAR */

.sidebar {
    position: fixed;
    top: 0;
    left: 0;
    z-index: 1000;
    width: var(--sidebar-width);
    height: 100vh;
    padding: 25px 14px 16px;
    display: flex;
    flex-direction: column;
    background: var(--white);
    border-right: 1px solid var(--line);
    overflow-x: hidden;
    overflow-y: auto;
}

.brand {
    flex-shrink: 0;
    padding: 0 14px 20px;
    margin-bottom: 22px;
    border-bottom: 1px solid var(--line);
}

.brand-title {
    color: var(--blue-dark);
    font-size: 19px;
    font-weight: 700;
    line-height: 1.4;
    margin-bottom: 5px;
}

.brand-subtitle {
    color: var(--muted);
    font-size: 12px;
}

.menu-title {
    flex-shrink: 0;
    margin: 0 14px 11px;
    color: #91a5bd;
    font-size: 10px;
    font-weight: 700;
    letter-spacing: 1px;
    text-transform: uppercase;
}

.menu {
    display: flex;
    flex-direction: column;
    gap: 5px;
}

.menu-link {
    display: flex;
    align-items: center;
    flex-shrink: 0;
    min-height: 42px;
    padding: 11px 14px;
    border-radius: 9px;
    color: #5e7695;
    font-size: 13px;
    transition: none;
}

.menu-link:hover {
    background: #f2f7fd;
    color: var(--blue);
}

.menu-link.active {
    background: var(--blue-soft);
    color: var(--blue);
    font-weight: 600;
    box-shadow: inset 3px 0 0 var(--blue);
}

.logout {
    flex-shrink: 0;
    margin-top: auto;
    padding-top: 14px;
    border-top: 1px solid var(--line);
}

.logout .menu-link {
    color: #d9534f;
}

.logout .menu-link:hover {
    background: #fff0ef;
}

/* KONTEN */

.main {
    width: calc(100% - var(--sidebar-width));
    min-width: 0;
    min-height: 100vh;
    margin-left: var(--sidebar-width);
    padding: 24px 28px 40px;
}

.page-container {
    width: 100%;
    max-width: 100%;
    min-width: 0;
    margin: 0 auto;
}

.page-head {
    width: 100%;
    padding: 20px;
    margin-bottom: 22px;
    background: var(--white);
    border: 1px solid var(--line);
    border-radius: 12px;
    box-shadow: var(--shadow);
}

.page-head h1 {
    color: #234e78;
    font-size: 30px;
    font-weight: 600;
    line-height: 1.3;
    margin-bottom: 5px;
}

.page-head p {
    color: var(--muted);
    font-size: 14px;
    margin: 0;
}

/* PANEL */

.panel {
    width: 100%;
    min-width: 0;
    padding: 20px;
    margin-bottom: 22px;
    background: var(--white);
    border: 1px solid var(--line);
    border-radius: 12px;
    box-shadow: var(--shadow);
}

.panel-header {
    margin-bottom: 20px;
}

.panel-header h2 {
    color: #234e78;
    font-size: 21px;
    font-weight: 600;
    margin-bottom: 5px;
}

.panel-header p {
    color: var(--muted);
    font-size: 13px;
    margin: 0;
}

/* FORM */

.form-label {
    display: block;
    color: #405873;
    font-size: 13px;
    font-weight: 600;
    margin-bottom: 8px;
}

.form-control,
.form-select {
    width: 100%;
    min-height: 42px;
    padding: 9px 12px;
    border: 1px solid var(--line);
    border-radius: 8px;
    color: var(--text);
    background-color: var(--white);
    font-family: var(--font);
    font-size: 13px;
    box-shadow: none;
}

.form-control:focus,
.form-select:focus {
    border-color: var(--blue);
    box-shadow: 0 0 0 3px rgba(40, 120, 207, .10);
}

.form-control[readonly] {
    background: #f5f8fc;
    color: #526b89;
    font-weight: 600;
}

textarea.form-control {
    min-height: 110px;
    resize: vertical;
}

.form-text {
    color: var(--muted);
    font-size: 11px;
    margin-top: 6px;
}

.select2-container {
    width: 100% !important;
}

.select2-container--default .select2-selection--single {
    height: 42px;
    padding: 6px 9px;
    border: 1px solid var(--line);
    border-radius: 8px;
}

.select2-container--default .select2-selection--single .select2-selection__rendered {
    color: var(--text);
    font-size: 13px;
    line-height: 28px;
}

.select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 40px;
    right: 5px;
}

.select2-dropdown {
    border: 1px solid var(--line);
    border-radius: 8px;
    overflow: hidden;
}

.select2-container--default .select2-search--dropdown .select2-search__field {
    padding: 8px;
    border: 1px solid var(--line);
    border-radius: 6px;
    outline: none;
    font-size: 13px;
}

.select2-results__option {
    padding: 9px 12px;
    font-size: 13px;
}

.select2-container--default .select2-results__option--highlighted.select2-results__option--selectable {
    background: var(--blue);
}

/* TOMBOL */

.btn {
    font-family: var(--font);
    font-size: 13px;
    font-weight: 600;
}

.btn-primary {
    min-height: 38px;
    padding: 9px 16px;
    background: var(--blue);
    border: 1px solid var(--blue);
    border-radius: 8px;
}

.btn-primary:hover,
.btn-primary:focus {
    background: #2168b4;
    border-color: #2168b4;
}

.btn-secondary {
    min-height: 38px;
    padding: 9px 15px;
    background: #edf3fa;
    border: 1px solid var(--line);
    color: #526b89;
    border-radius: 8px;
}

.alert {
    border-radius: 9px;
    font-size: 13px;
    padding: 12px 15px;
}

/* TABEL */

.table-responsive {
    width: 100%;
    overflow-x: auto;
}

#tabelRiwayat {
    width: 100% !important;
    font-family: var(--font);
    font-size: 13px;
}

#tabelRiwayat thead th {
    padding: 13px 12px;
    background: #f5f8fc;
    color: #7188a5;
    font-size: 12px;
    font-weight: 700;
    white-space: nowrap;
    border-bottom: 1px solid var(--line);
}

#tabelRiwayat tbody td {
    padding: 12px;
    color: #526b89;
    font-size: 13px;
    vertical-align: middle;
    border-bottom: 1px solid #edf2f8;
}

#tabelRiwayat tbody tr:hover {
    background: #fafcff;
}

.keterangan,
.tindakan-text {
    min-width: 150px;
    max-width: 260px;
    white-space: normal;
    overflow-wrap: anywhere;
}

.status-badge {
    display: inline-block;
    padding: 6px 10px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
    white-space: nowrap;
}

.status-diproses {
    background: #fff3d9;
    color: #946b17;
}

.status-ditindaklanjuti {
    background: #eaf0ff;
    color: #4265a5;
}

.status-selesai {
    background: #e4f3e9;
    color: #34734b;
}

.status-lain {
    background: #eaf0f7;
    color: #526b85;
}

.btn-tindakan,
.btn-terkunci {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-height: 32px;
    padding: 7px 10px;
    border-radius: 7px;
    font-family: var(--font);
    font-size: 12px;
    font-weight: 600;
    white-space: nowrap;
}

.btn-tindakan {
    background: var(--blue-soft);
    color: var(--blue);
    border: 1px solid #d3e5f8;
}

.btn-tindakan:hover {
    background: #dcecff;
    color: #1e62a8;
}

.btn-terkunci {
    background: #e4f3e9;
    color: #34734b;
}

.empty {
    padding: 28px 15px;
    color: #8498b1;
    font-size: 13px;
    text-align: center;
}

/* DATATABLES */

.dt-container {
    width: 100%;
    font-family: var(--font);
    font-size: 13px;
    color: var(--muted);
}

.dt-container .dt-layout-row {
    margin: 13px 0;
    gap: 12px;
}

.dt-container .dt-layout-row:first-child {
    margin-top: 18px;
}

.dt-container .dt-layout-cell {
    min-width: 0;
    display: flex;
    align-items: center;
}

.dt-container .dt-length,
.dt-container .dt-search {
    display: flex;
    align-items: center;
    gap: 8px;
}

.dt-container .dt-length label,
.dt-container .dt-search label {
    display: flex;
    align-items: center;
    gap: 8px;
    margin: 0;
    font-size: 12px;
    white-space: nowrap;
}

.dt-container .dt-length select {
    width: 65px !important;
    min-width: 65px;
    height: 36px;
    padding: 5px 8px;
    border: 1px solid var(--line);
    border-radius: 8px;
    background: var(--white);
    color: var(--text);
    font-size: 12px;
}

.dt-container .dt-search input {
    width: 190px;
    height: 36px;
    padding: 7px 11px;
    margin-left: 0 !important;
    border: 1px solid var(--line);
    border-radius: 8px;
    color: var(--text);
    font-size: 12px;
}

.dt-container .dt-info {
    font-size: 12px;
    color: var(--muted);
}

.dt-container .dt-paging .dt-paging-button {
    border-radius: 7px !important;
    font-size: 12px;
}

.dt-container .dt-paging .dt-paging-button.current {
    background: var(--blue-soft) !important;
    border-color: var(--line) !important;
    color: var(--blue) !important;
}

/* MODAL */

.modal-content {
    border: 1px solid var(--line);
    border-radius: 12px;
    box-shadow: var(--shadow);
}

.modal-header {
    padding: 18px 20px;
    border-bottom: 1px solid var(--line);
}

.modal-title {
    color: var(--blue-dark);
    font-size: 18px;
    font-weight: 600;
}

.modal-body {
    padding: 20px;
}

.modal-footer {
    padding: 14px 20px;
    border-top: 1px solid var(--line);
}

/* FOOTER */

.footer {
    margin-top: 28px;
    color: #9aabc0;
    font-size: 11px;
    text-align: center;
}

@media (max-width: 1100px) {
    .main {
        padding-right: 22px;
        padding-left: 22px;
    }
}

@media (max-width: 900px) {
    .sidebar {
        position: relative;
        width: 100%;
        height: auto;
        padding: 20px 14px 16px;
        overflow: visible;
    }

    .logout {
        margin-top: 14px;
    }

    .main {
        width: 100%;
        margin-left: 0;
        padding: 20px 18px 30px;
    }
}

@media (max-width: 650px) {
    .main {
        padding: 16px 12px 25px;
    }

    .page-head,
    .panel {
        padding: 16px;
    }

    .page-head h1 {
        font-size: 25px;
    }

    .panel-header h2 {
        font-size: 19px;
    }

    .dt-container .dt-layout-row {
        flex-wrap: wrap;
    }

    .dt-container .dt-search input {
        width: 140px;
    }
}
</style>
</head>

<body>

<!-- SIDEBAR -->

<aside class="sidebar">

    <div class="brand">
        <div class="brand-title">Sistem Pelanggaran</div>
        <div class="brand-subtitle">SMK Muhammadiyah</div>
    </div>

    <div class="menu-title">Menu Utama</div>

    <nav class="menu">

        <a href="../dashboard.php" class="menu-link">Dashboard</a>

        <a href="catat_pelanggaran.php"
           class="menu-link active"
           aria-current="page">
            Catat Pelanggaran
        </a>

        <a href="riwayat.php" class="menu-link">Riwayat</a>
        <a href="data_siswa.php" class="menu-link">Data Siswa</a>
        <a href="rekap_poin.php" class="menu-link">Rekap Poin Siswa</a>
        <a href="rekap_pelanggaran.php" class="menu-link">Rekap Pelanggaran</a>
        <a href="cetak_rekap.php" class="menu-link">Laporan</a>
        <a href="../about.php" class="menu-link">About Me</a>

    </nav>

    <div class="logout">
        <a href="../logout.php" class="menu-link">Logout</a>
    </div>

</aside>

<!-- KONTEN -->

<main class="main">
<div class="page-container">

    <header class="page-head">
        <h1>Catat Pelanggaran</h1>
        <p>Catat dan kelola pelanggaran siswa pada kelas yang menjadi tanggung jawab Anda.</p>
    </header>

    <?php if ($pesan !== ''): ?>
        <div class="alert alert-<?= e($jenis_pesan) ?> mb-4">
            <?= e($pesan) ?>
        </div>
    <?php endif; ?>

    <?php if ($tahun_ajaran_id <= 0): ?>

        <div class="alert alert-warning">
            Tahun ajaran aktif belum ditemukan.
        </div>

    <?php elseif (count($daftar_siswa) === 0): ?>

        <div class="alert alert-warning">
            Belum ada siswa dengan penempatan aktif pada tahun ajaran ini.
            Silakan hubungi administrator.
        </div>

    <?php endif; ?>

    <!-- FORM CATAT PELANGGARAN -->

    <section class="panel">

        <div class="panel-header">
            <h2>Tambah Pelanggaran</h2>
            <p>
                Pilih nama siswa terlebih dahulu. Kelas dan tahun ajaran
                akan terisi otomatis berdasarkan penempatan siswa.
            </p>
        </div>

        <form method="POST" action="catat_pelanggaran.php">

            <div class="mb-3">
                <label for="nama_guru" class="form-label">
                    Wali Kelas yang Mencatat
                </label>

                <input
                    type="text"
                    id="nama_guru"
                    class="form-control"
                    value="<?= e($nama_guru) ?>"
                    readonly
                >

                <div class="form-text">
                    Nama pencatat diambil otomatis dari akun yang sedang login.
                </div>
            </div>

            <div class="mb-3">
                <label for="siswa_id" class="form-label">
                    Nama Siswa
                </label>

                <select
                    id="siswa_id"
                    name="siswa_id"
                    class="form-select"
                    required
                    <?= (
                        count($daftar_siswa) === 0 ||
                        $guru_id <= 0 ||
                        $tahun_ajaran_id <= 0
                    ) ? 'disabled' : '' ?>
                >
                    <option value="">-- Cari dan Pilih Nama Siswa --</option>

                    <?php foreach ($daftar_siswa as $s): ?>

                        <option
                            value="<?= (int)$s['id'] ?>"
                            data-kelas="<?= e($s['label_kelas']) ?>"
                            <?= $siswa_awal === (int)$s['id'] ? 'selected' : '' ?>
                        >
                            <?= e($s['nis'] . ' - ' . $s['nama']) ?>
                        </option>

                    <?php endforeach; ?>

                </select>

                <div class="form-text">
                    Ketik nama atau NIS siswa untuk mencari data.
                </div>
            </div>

            <div class="mb-3">
                <label for="kelas_otomatis" class="form-label">
                    Kelas
                </label>

                <input
                    type="text"
                    id="kelas_otomatis"
                    class="form-control"
                    value=""
                    placeholder="Kelas akan muncul otomatis setelah siswa dipilih"
                    readonly
                >
            </div>

            <div class="mb-3">
                <label for="tahun_otomatis" class="form-label">
                    Tahun Ajaran
                </label>

                <input
                    type="text"
                    id="tahun_otomatis"
                    class="form-control"
                    value="<?= e($nama_tahun_ajaran) ?>"
                    readonly
                >
            </div>

            <div class="mb-3">
                <label for="pelanggaran_id" class="form-label">
                    Jenis Pelanggaran
                </label>

                <select
                    id="pelanggaran_id"
                    name="pelanggaran_id"
                    class="form-select"
                    required
                    <?= count($daftar_jenis) === 0 ? 'disabled' : '' ?>
                >
                    <option value="">-- Pilih Jenis Pelanggaran --</option>

                    <?php foreach ($daftar_jenis as $p): ?>

                        <option
                            value="<?= (int)$p['id'] ?>"
                            <?= $jenis_awal === (int)$p['id'] ? 'selected' : '' ?>
                        >
                            <?= e($p['kode'] . ' - ' . $p['nama']) ?>
                            (<?= e($p['tingkat']) ?>, <?= (int)$p['poin'] ?> poin)
                        </option>

                    <?php endforeach; ?>

                </select>
            </div>

            <div class="mb-3">
                <label for="tanggal" class="form-label">
                    Tanggal
                </label>

                <input
                    type="date"
                    id="tanggal"
                    name="tanggal"
                    class="form-control"
                    value="<?= e($_POST['tanggal'] ?? date('Y-m-d')) ?>"
                    required
                >
            </div>

            <div class="mb-4">
                <label for="keterangan" class="form-label">
                    Keterangan
                </label>

                <textarea
                    id="keterangan"
                    name="keterangan"
                    class="form-control"
                    rows="4"
                    placeholder="Contoh: Siswa membolos pada saat jam pelajaran."
                    required
                ><?= e($_POST['keterangan'] ?? '') ?></textarea>
            </div>

            <button
                type="submit"
                name="simpan"
                class="btn btn-primary"
                <?= (
                    count($daftar_siswa) === 0 ||
                    count($daftar_jenis) === 0 ||
                    $guru_id <= 0 ||
                    $tahun_ajaran_id <= 0
                ) ? 'disabled' : '' ?>
            >
                Simpan Pelanggaran
            </button>

        </form>

    </section>

    <!-- RIWAYAT PELANGGARAN -->

    <section class="panel" id="riwayat">

        <div class="panel-header">
            <h2>Riwayat Pelanggaran</h2>
            <p>
                Daftar pelanggaran yang telah dicatat oleh Anda pada tahun ajaran aktif.
                Klik tombol Tindakan untuk mengisi atau memperbarui tindak lanjut.
            </p>
        </div>

        <?php if (count($daftar_riwayat) > 0): ?>

            <div class="table-responsive">

                <table id="tabelRiwayat" class="table table-hover align-middle">

                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Nama Siswa</th>
                            <th>Kelas</th>
                            <th>Jenis Pelanggaran</th>
                            <th>Wali Kelas yang Mencatat</th>
                            <th>Keterangan</th>
                            <th>Tindakan</th>
                            <th>Poin</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>

                        <?php foreach ($daftar_riwayat as $i => $data): ?>

                            <?php
                            $kelas_tampil = $data['nama_kelas'] ?? '-';

                            if (!empty($data['nama_kelas_asli'])) {
                                $kelas_tampil = labelKelas(
                                    $data['nama_kelas_asli'],
                                    $data['tingkat'],
                                    $data['jurusan']
                                );
                            }

                            $status = trim((string)($data['status'] ?? 'Diproses'));
                            $status_class = 'status-lain';

                            if (strcasecmp($status, 'Diproses') === 0) {
                                $status_class = 'status-diproses';
                            } elseif (strcasecmp($status, 'Ditindaklanjuti') === 0) {
                                $status_class = 'status-ditindaklanjuti';
                            } elseif (strcasecmp($status, 'Selesai') === 0) {
                                $status_class = 'status-selesai';
                            }

                            $tindakan_tampil = trim((string)($data['tindakan'] ?? ''));
                            ?>

                            <tr>
                                <td><?= $i + 1 ?></td>
                                <td><?= e($data['tanggal']) ?></td>
                                <td><?= e($data['nama_siswa']) ?></td>
                                <td><?= e($kelas_tampil) ?></td>
                                <td><?= e($data['nama_pelanggaran']) ?></td>
                                <td><?= e($data['nama_guru'] ?? '-') ?></td>

                                <td>
                                    <div class="keterangan">
                                        <?= e($data['keterangan']) ?>
                                    </div>
                                </td>

                                <td>
                                    <div class="tindakan-text">
                                        <?php if ($tindakan_tampil !== ''): ?>
                                            <?= nl2br(e($tindakan_tampil)) ?>
                                        <?php else: ?>
                                            <span class="text-muted">Belum ada tindakan</span>
                                        <?php endif; ?>
                                    </div>
                                </td>

                                <td><?= (int)$data['poin'] ?></td>

                                <td>
                                    <span class="status-badge <?= e($status_class) ?>">
                                        <?= e($status) ?>
                                    </span>
                                </td>

                                <td>
                                    <?php if (strcasecmp($status, 'Selesai') === 0): ?>

                                        <span class="btn-terkunci">Selesai</span>

                                    <?php else: ?>

                                        <button
                                            type="button"
                                            class="btn-tindakan btn-buka-tindakan"
                                            data-id="<?= (int)$data['id'] ?>"
                                            data-siswa="<?= e($data['nama_siswa']) ?>"
                                            data-pelanggaran="<?= e($data['nama_pelanggaran']) ?>"
                                            data-tindakan="<?= e($tindakan_tampil) ?>"
                                            data-status="<?= e($status) ?>"
                                        >
                                            <?= $tindakan_tampil !== ''
                                                ? 'Ubah Tindakan'
                                                : 'Tambah Tindakan' ?>
                                        </button>

                                    <?php endif; ?>
                                </td>
                            </tr>

                        <?php endforeach; ?>

                    </tbody>

                </table>

            </div>

        <?php else: ?>

            <div class="empty">
                Belum ada riwayat pelanggaran yang dicatat oleh akun ini pada tahun ajaran aktif.
            </div>

        <?php endif; ?>

    </section>

    <footer class="footer">
        Sistem Pelanggaran Sekolah · Project Rekayasa Perangkat Lunak
    </footer>

</div>
</main>

<!-- MODAL TINDAKAN -->

<div
    class="modal fade"
    id="modalTindakan"
    tabindex="-1"
    aria-labelledby="judulModalTindakan"
    aria-hidden="true"
>
    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content">

            <form method="POST" action="catat_pelanggaran.php">

                <div class="modal-header">
                    <div>
                        <h5 class="modal-title" id="judulModalTindakan">
                            Tindakan Pelanggaran
                        </h5>

                        <small class="text-muted" id="infoPelanggaran">
                            Isi tindakan untuk siswa.
                        </small>
                    </div>

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Tutup"
                    ></button>
                </div>

                <div class="modal-body">

                    <input
                        type="hidden"
                        name="pelanggaran_siswa_id"
                        id="pelanggaran_siswa_id"
                    >

                    <div class="mb-3">
                        <label for="tindakan_baru" class="form-label">
                            Tindakan yang Diberikan
                        </label>

                        <textarea
                            name="tindakan_baru"
                            id="tindakan_baru"
                            class="form-control"
                            rows="4"
                            placeholder="Contoh: Siswa diberikan teguran dan diminta membuat surat pernyataan."
                            required
                        ></textarea>
                    </div>

                    <div class="mb-2">
                        <label for="status_baru" class="form-label">
                            Status Pelanggaran
                        </label>

                        <select
                            name="status_baru"
                            id="status_baru"
                            class="form-select"
                            required
                        >
                            <option value="Diproses">Diproses</option>
                            <option value="Ditindaklanjuti">Ditindaklanjuti</option>
                            <option value="Selesai">Selesai</option>
                        </select>
                    </div>

                    <small class="text-muted">
                        Jika status diubah menjadi Selesai,
                        catatan tindakan tidak dapat diubah kembali.
                    </small>

                </div>

                <div class="modal-footer">

                    <button
                        type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal"
                    >
                        Batal
                    </button>

                    <button
                        type="submit"
                        name="simpan_tindakan"
                        class="btn btn-primary"
                    >
                        Simpan Tindakan
                    </button>

                </div>

            </form>

        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

<script src="https://cdn.datatables.net/2.3.4/js/dataTables.min.js"></script>
<script src="https://cdn.datatables.net/2.3.4/js/dataTables.bootstrap5.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>

/* =========================================
   PILIH SISWA DAN ISI KELAS OTOMATIS
========================================= */

$(document).ready(function() {

    $('#siswa_id').select2({
        placeholder: '-- Cari dan Pilih Nama Siswa --',
        allowClear: true,
        width: '100%',
        language: {
            noResults: function() {
                return 'Nama siswa tidak ditemukan';
            },
            searching: function() {
                return 'Sedang mencari...';
            }
        }
    });

    function isiKelasOtomatis() {

        const siswaTerpilih = $('#siswa_id').find(':selected');
        const kelas = siswaTerpilih.attr('data-kelas') || '';

        $('#kelas_otomatis').val(kelas);
    }

    $('#siswa_id').on('change', function() {
        isiKelasOtomatis();
    });

    // Mengisi kelas ketika halaman dimuat ulang
    // setelah validasi form gagal.
    isiKelasOtomatis();

});


/* =========================================
   MODAL TINDAKAN
========================================= */

const modalTindakanElement = document.getElementById('modalTindakan');
const modalTindakan = new bootstrap.Modal(modalTindakanElement);

const inputId = document.getElementById('pelanggaran_siswa_id');
const inputTindakan = document.getElementById('tindakan_baru');
const inputStatus = document.getElementById('status_baru');
const infoPelanggaran = document.getElementById('infoPelanggaran');

document.querySelectorAll('.btn-buka-tindakan').forEach(function(button) {

    button.addEventListener('click', function() {

        inputId.value = this.dataset.id || '';
        inputTindakan.value = this.dataset.tindakan || '';
        inputStatus.value = this.dataset.status || 'Diproses';

        infoPelanggaran.textContent =
            (this.dataset.siswa || '') +
            ' - ' +
            (this.dataset.pelanggaran || '');

        modalTindakan.show();
    });

});


/* =========================================
   DATATABLES
========================================= */

$(document).ready(function() {

    const jumlahRiwayat = <?= count($daftar_riwayat) ?>;

    if (jumlahRiwayat > 0) {

        $('#tabelRiwayat').DataTable({

            pageLength: 10,
            lengthMenu: [10, 25, 50, 100],
            order: [],
            scrollX: true,
            autoWidth: false,

            language: {
                search: "Cari:",
                searchPlaceholder: "Cari riwayat...",
                lengthMenu: "Tampilkan _MENU_ data",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ riwayat",
                infoEmpty: "Tidak ada riwayat pelanggaran",
                infoFiltered: "(disaring dari _MAX_ data)",
                zeroRecords: "Riwayat tidak ditemukan",
                emptyTable: "Belum ada riwayat pelanggaran",

                paginate: {
                    previous: "Sebelumnya",
                    next: "Berikutnya"
                }
            }

        });

    }

});

</script>

</body>
</html>
