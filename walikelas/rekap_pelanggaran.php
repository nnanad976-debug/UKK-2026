
<?php
include __DIR__ . "/../middleware/auth.php";
include __DIR__ . "/../config/koneksi.php";

/* =========================================
   AKSES WALI KELAS
========================================= */
$role = strtolower(trim($_SESSION['role'] ?? ''));

if ($role !== 'guru' || empty($_SESSION['is_wali_kelas'])) {
    http_response_code(403);
    exit("Anda tidak memiliki akses.");
}

/* =========================================
   FUNGSI BANTU
========================================= */
function e($value): string
{
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}

function tanggalValid($tanggal): bool
{
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', (string)$tanggal)) {
        return false;
    }

    [$tahun, $bulan, $hari] = array_map('intval', explode('-', $tanggal));
    return checkdate($bulan, $hari, $tahun);
}

function tanggalIndonesia($tanggal): string
{
    if (!$tanggal || $tanggal === '0000-00-00') {
        return '-';
    }

    $bulan = [
        1 => 'Januari', 'Februari', 'Maret', 'April',
        'Mei', 'Juni', 'Juli', 'Agustus', 'September',
        'Oktober', 'November', 'Desember'
    ];

    $pecah = explode('-', $tanggal);

    if (count($pecah) !== 3) {
        return '-';
    }

    $tahun = (int)$pecah[0];
    $bln = (int)$pecah[1];
    $hari = (int)$pecah[2];

    if (!checkdate($bln, $hari, $tahun)) {
        return '-';
    }

    return $hari . ' ' . $bulan[$bln] . ' ' . $tahun;
}

function hariIndonesia($tanggal): string
{
    if (!tanggalValid((string)$tanggal)) {
        return '-';
    }

    $hari = [
        'Minggu', 'Senin', 'Selasa', 'Rabu',
        'Kamis', 'Jumat', 'Sabtu'
    ];

    return $hari[(int)date('w', strtotime($tanggal))];
}

/* =========================================
   IDENTITAS GURU
========================================= */
$user_id = (int)($_SESSION['user_id'] ?? 0);
$guru_id = (int)($_SESSION['guru_id'] ?? 0);

if ($guru_id <= 0 && $user_id > 0) {
    $stmt = mysqli_prepare(
        $koneksi,
        "SELECT id FROM t_guru WHERE user_id = ? LIMIT 1"
    );

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $user_id);
        mysqli_stmt_execute($stmt);
        $hasil = mysqli_stmt_get_result($stmt);
        $guru_id = (int)(mysqli_fetch_assoc($hasil)['id'] ?? 0);
        mysqli_stmt_close($stmt);
    }
}

if ($guru_id <= 0) {
    http_response_code(403);
    exit("Data guru tidak ditemukan. Silakan login kembali.");
}

$nama_login = $_SESSION['nama'] ?? 'Wali Kelas';

$stmt = mysqli_prepare(
    $koneksi,
    "SELECT nama FROM t_guru WHERE id = ? LIMIT 1"
);

if ($stmt) {
    mysqli_stmt_bind_param($stmt, "i", $guru_id);
    mysqli_stmt_execute($stmt);
    $hasil = mysqli_stmt_get_result($stmt);
    $nama_login = mysqli_fetch_assoc($hasil)['nama'] ?? $nama_login;
    mysqli_stmt_close($stmt);
}

/* =========================================
   TAHUN AJARAN AKTIF
========================================= */
$query_tahun = mysqli_query(
    $koneksi,
    "SELECT id, nama
     FROM t_tahun_ajaran
     WHERE status_aktif = 1
     ORDER BY id DESC
     LIMIT 1"
);

if (!$query_tahun) {
    exit("Gagal mengambil tahun ajaran: " . e(mysqli_error($koneksi)));
}

$tahun_aktif = mysqli_fetch_assoc($query_tahun);
$tahun_ajaran_id = (int)($tahun_aktif['id'] ?? 0);
$nama_tahun_ajaran = $tahun_aktif['nama'] ?? '-';

/* =========================================
   FILTER
========================================= */
$filter_kelas = max(0, (int)($_GET['kelas_id'] ?? 0));
$tanggal_mulai = trim($_GET['tanggal_mulai'] ?? '');
$tanggal_selesai = trim($_GET['tanggal_selesai'] ?? '');
$pesan_filter = '';

if (
    ($tanggal_mulai !== '' && !tanggalValid($tanggal_mulai)) ||
    ($tanggal_selesai !== '' && !tanggalValid($tanggal_selesai))
) {
    $tanggal_mulai = '';
    $tanggal_selesai = '';
    $pesan_filter = 'Format tanggal tidak valid. Silakan pilih tanggal kembali.';
}

if (
    $tanggal_mulai !== '' &&
    $tanggal_selesai !== '' &&
    $tanggal_mulai > $tanggal_selesai
) {
    $tanggal_mulai = '';
    $tanggal_selesai = '';
    $pesan_filter = 'Tanggal mulai tidak boleh melebihi tanggal selesai.';
}

/* =========================================
   DAFTAR KELAS WALI
========================================= */
$daftar_kelas = [];

if ($tahun_ajaran_id > 0) {
    $sql_kelas = "
        SELECT DISTINCT
            wk.kelas_id,
            COALESCE(k.nama, CONCAT('Kelas ID ', wk.kelas_id)) AS nama_kelas
        FROM t_wali_kelas wk
        LEFT JOIN t_kelas k ON k.id = wk.kelas_id
        WHERE wk.guru_id = ?
          AND wk.tahun_ajaran_id = ?
          AND wk.status_aktif = 1
          AND wk.tanggal_mulai <= CURDATE()
          AND (
                wk.tanggal_selesai IS NULL
                OR wk.tanggal_selesai >= CURDATE()
          )
        ORDER BY nama_kelas ASC
    ";

    $stmt = mysqli_prepare($koneksi, $sql_kelas);

    if (!$stmt) {
        exit("Gagal menyiapkan daftar kelas: " . e(mysqli_error($koneksi)));
    }

    mysqli_stmt_bind_param($stmt, "ii", $guru_id, $tahun_ajaran_id);
    mysqli_stmt_execute($stmt);
    $hasil = mysqli_stmt_get_result($stmt);

    while ($row = mysqli_fetch_assoc($hasil)) {
        $daftar_kelas[] = $row;
    }

    mysqli_stmt_close($stmt);
}

/* =========================================
   VALIDASI FILTER KELAS
========================================= */
$kelas_valid = false;

foreach ($daftar_kelas as $kelas) {
    if ((int)$kelas['kelas_id'] === $filter_kelas) {
        $kelas_valid = true;
        break;
    }
}

if ($filter_kelas > 0 && !$kelas_valid) {
    $filter_kelas = 0;
}

/* =========================================
   DATA REKAP
========================================= */
$data_rekap = [];

if ($tahun_ajaran_id > 0 && count($daftar_kelas) > 0) {
    $sql_rekap = "
        SELECT
            ps.siswa_id,
            ps.kelas_id,
            COALESCE(s.nis, '-') AS nis,
            COALESCE(s.nisn, '-') AS nisn,
            COALESCE(s.nama, ps.nama_siswa, '-') AS nama_siswa,
            COALESCE(
                k.nama,
                MAX(NULLIF(ps.nama_kelas, '')),
                CONCAT('Kelas ID ', ps.kelas_id)
            ) AS nama_kelas,
            COUNT(ps.id) AS jumlah_pelanggaran,
            COALESCE(SUM(ps.poin), 0) AS total_poin,
            GROUP_CONCAT(
                CONCAT(
                    COALESCE(NULLIF(ps.nama_pelanggaran, ''), 'Pelanggaran'),
                    ' — Dicatat oleh: ',
                    COALESCE(
                        NULLIF(ps.nama_guru, ''),
                        NULLIF(g.nama, ''),
                        'Tidak diketahui'
                    )
                )
                ORDER BY ps.tanggal ASC, ps.id ASC
                SEPARATOR '\n'
            ) AS daftar_pelanggaran,
            MAX(ps.tanggal) AS tanggal_terakhir
        FROM t_pelanggaran_siswa ps
        LEFT JOIN t_siswa s ON s.id = ps.siswa_id
        LEFT JOIN t_kelas k ON k.id = ps.kelas_id
        LEFT JOIN t_guru g ON g.id = ps.guru_id
        WHERE ps.tahun_ajaran_id = ?
          AND EXISTS (
                SELECT 1
                FROM t_wali_kelas wk
                WHERE wk.guru_id = ?
                  AND wk.kelas_id = ps.kelas_id
                  AND wk.tahun_ajaran_id = ?
                  AND wk.status_aktif = 1
                  AND wk.tanggal_mulai <= CURDATE()
                  AND (
                        wk.tanggal_selesai IS NULL
                        OR wk.tanggal_selesai >= CURDATE()
                  )
          )
          AND EXISTS (
                SELECT 1
                FROM t_kelas_siswa ks
                WHERE ks.siswa_id = ps.siswa_id
                  AND ks.kelas_id = ps.kelas_id
                  AND ks.tahun_ajaran_id = ?
                  AND ks.status_aktif = 1
                  AND ks.tanggal_mulai <= CURDATE()
                  AND (
                        ks.tanggal_selesai IS NULL
                        OR ks.tanggal_selesai >= CURDATE()
                  )
          )
          AND (? = 0 OR ps.kelas_id = ?)
          AND (? = '' OR ps.tanggal >= ?)
          AND (? = '' OR ps.tanggal <= ?)
        GROUP BY
            ps.siswa_id,
            ps.kelas_id,
            s.nis,
            s.nisn,
            s.nama,
            ps.nama_siswa,
            k.nama
        ORDER BY total_poin DESC, nama_siswa ASC
    ";

    $stmt = mysqli_prepare($koneksi, $sql_rekap);

    if (!$stmt) {
        exit("Gagal menyiapkan data rekap: " . e(mysqli_error($koneksi)));
    }

    mysqli_stmt_bind_param(
        $stmt,
        "iiiiiissss",
        $tahun_ajaran_id,
        $guru_id,
        $tahun_ajaran_id,
        $tahun_ajaran_id,
        $filter_kelas,
        $filter_kelas,
        $tanggal_mulai,
        $tanggal_mulai,
        $tanggal_selesai,
        $tanggal_selesai
    );

    mysqli_stmt_execute($stmt);
    $hasil = mysqli_stmt_get_result($stmt);

    while ($row = mysqli_fetch_assoc($hasil)) {
        $data_rekap[] = $row;
    }

    mysqli_stmt_close($stmt);
}

/* =========================================
   PROSES SURAT PEMANGGILAN
========================================= */
$surat_data = null;
$pesan_surat = '';

if (
    $_SERVER['REQUEST_METHOD'] === 'POST' &&
    ($_POST['aksi'] ?? '') === 'buat_surat'
) {
    $siswa_id_post = (int)($_POST['siswa_id'] ?? 0);
    $kelas_id_post = (int)($_POST['kelas_id'] ?? 0);
    $tanggal_pemanggilan = trim($_POST['tanggal_pemanggilan'] ?? '');
    $waktu_pemanggilan = trim($_POST['waktu_pemanggilan'] ?? '');
    $keperluan = trim($_POST['keperluan'] ?? '');

    $kelas_milik_wali = false;

    foreach ($daftar_kelas as $kelas_wali) {
        if ((int)$kelas_wali['kelas_id'] === $kelas_id_post) {
            $kelas_milik_wali = true;
            break;
        }
    }

    if ($siswa_id_post <= 0 || !$kelas_milik_wali) {
        $pesan_surat = 'Data siswa atau kelas tidak valid untuk akun Wali Kelas ini.';
    } elseif (!tanggalValid($tanggal_pemanggilan)) {
        $pesan_surat = 'Tanggal pemanggilan tidak valid.';
    } elseif (!preg_match('/^(?:[01]\d|2[0-3]):[0-5]\d$/', $waktu_pemanggilan)) {
        $pesan_surat = 'Waktu pemanggilan tidak valid.';
    } elseif ($keperluan === '') {
        $pesan_surat = 'Keperluan pemanggilan harus diisi.';
    } else {
        $sql_surat = "
            SELECT
                ps.siswa_id,
                ps.kelas_id,
                COALESCE(s.nis, '-') AS nis,
                COALESCE(s.nisn, '-') AS nisn,
                COALESCE(
                    NULLIF(s.nama, ''),
                    MAX(NULLIF(ps.nama_siswa, '')),
                    '-'
                ) AS nama_siswa,
                COALESCE(
                    NULLIF(k.nama, ''),
                    MAX(NULLIF(ps.nama_kelas, '')),
                    CONCAT('Kelas ID ', ps.kelas_id)
                ) AS nama_kelas,
                COUNT(ps.id) AS jumlah_pelanggaran,
                COALESCE(SUM(ps.poin), 0) AS total_poin,
                GROUP_CONCAT(
                    CONCAT(
                        COALESCE(NULLIF(ps.nama_pelanggaran, ''), 'Pelanggaran'),
                        ' — Dicatat oleh: ',
                        COALESCE(
                            NULLIF(ps.nama_guru, ''),
                            NULLIF(g.nama, ''),
                            'Tidak diketahui'
                        )
                    )
                    ORDER BY ps.tanggal ASC, ps.id ASC
                    SEPARATOR '\n'
                ) AS daftar_pelanggaran,
                MAX(ps.tanggal) AS tanggal_terakhir
            FROM t_pelanggaran_siswa ps
            LEFT JOIN t_siswa s ON s.id = ps.siswa_id
            LEFT JOIN t_kelas k ON k.id = ps.kelas_id
            LEFT JOIN t_guru g ON g.id = ps.guru_id
            WHERE ps.siswa_id = ?
              AND ps.kelas_id = ?
              AND ps.tahun_ajaran_id = ?
              AND EXISTS (
                    SELECT 1
                    FROM t_wali_kelas wk
                    WHERE wk.guru_id = ?
                      AND wk.kelas_id = ps.kelas_id
                      AND wk.tahun_ajaran_id = ?
                      AND wk.status_aktif = 1
                      AND wk.tanggal_mulai <= CURDATE()
                      AND (
                            wk.tanggal_selesai IS NULL
                            OR wk.tanggal_selesai >= CURDATE()
                      )
              )
              AND EXISTS (
                    SELECT 1
                    FROM t_kelas_siswa ks
                    WHERE ks.siswa_id = ps.siswa_id
                      AND ks.kelas_id = ps.kelas_id
                      AND ks.tahun_ajaran_id = ?
                      AND ks.status_aktif = 1
                      AND ks.tanggal_mulai <= CURDATE()
                      AND (
                            ks.tanggal_selesai IS NULL
                            OR ks.tanggal_selesai >= CURDATE()
                      )
              )
              AND (? = '' OR ps.tanggal >= ?)
              AND (? = '' OR ps.tanggal <= ?)
            GROUP BY ps.siswa_id, ps.kelas_id, s.nis, s.nisn, s.nama, k.nama
            LIMIT 1
        ";

        $stmt = mysqli_prepare($koneksi, $sql_surat);

        if (!$stmt) {
            $pesan_surat = 'Gagal menyiapkan data surat: ' . mysqli_error($koneksi);
        } else {
            mysqli_stmt_bind_param(
                $stmt,
                'iiiiiissss',
                $siswa_id_post,
                $kelas_id_post,
                $tahun_ajaran_id,
                $guru_id,
                $tahun_ajaran_id,
                $tahun_ajaran_id,
                $tanggal_mulai,
                $tanggal_mulai,
                $tanggal_selesai,
                $tanggal_selesai
            );

            mysqli_stmt_execute($stmt);
            $hasil = mysqli_stmt_get_result($stmt);
            $siswa_ditemukan = $hasil ? mysqli_fetch_assoc($hasil) : null;
            mysqli_stmt_close($stmt);

            if (!$siswa_ditemukan) {
                $pesan_surat = 'Data pelanggaran siswa tidak ditemukan pada kelas dan tahun ajaran aktif. Pastikan siswa masih memiliki catatan pelanggaran pada filter tanggal yang dipilih.';
            } else {
                $surat_data = $siswa_ditemukan;
                $surat_data['tanggal_pemanggilan'] = $tanggal_pemanggilan;
                $surat_data['waktu_pemanggilan'] = $waktu_pemanggilan;
                $surat_data['keperluan'] = $keperluan;
            }
        }
    }
}

/* =========================================
   RINGKASAN DAN FILTER
========================================= */
$total_siswa = count($data_rekap);
$total_pelanggaran = 0;
$total_poin = 0;

foreach ($data_rekap as $data) {
    $total_pelanggaran += (int)$data['jumlah_pelanggaran'];
    $total_poin += (int)$data['total_poin'];
}

$parameter_filter = [
    'kelas_id' => $filter_kelas,
    'tanggal_mulai' => $tanggal_mulai,
    'tanggal_selesai' => $tanggal_selesai
];

/* =========================================
   EKSPOR EXCEL
========================================= */
if (isset($_GET['export']) && $_GET['export'] === 'excel') {
    $nama_file = 'rekap-pelanggaran-' . date('Y-m-d') . '.xls';

    while (ob_get_level() > 0) {
        ob_end_clean();
    }

    header('Content-Type: application/vnd.ms-excel; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $nama_file . '"');
    header('Cache-Control: max-age=0');
    header('Pragma: public');

    echo "\xEF\xBB\xBF";
    ?>
    <!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="UTF-8">
        <style>
            body { font-family: Arial, sans-serif; font-size: 10pt; }
            h2, p { text-align: center; }
            table { width: 100%; border-collapse: collapse; }
            th {
                background: #dce9f8;
                color: #203e68;
                font-weight: bold;
                text-align: center;
            }
            th, td {
                border: 1px solid #9eafc3;
                padding: 7px;
                vertical-align: top;
            }
            .center { text-align: center; }
            .text { mso-number-format: "\@"; }
            .keterangan { width: 420px; white-space: normal; mso-wrap-text: yes; }
            .tanggal { width: 110px; text-align: center; }
        </style>
    </head>
    <body>
        <h2>REKAP PELANGGARAN SISWA</h2>
        <p>SMK Muhammadiyah Tasikmalaya</p>
        <p>
            Tahun Ajaran: <?= e($nama_tahun_ajaran) ?><br>
            Dicetak: <?= date('d-m-Y') ?>
        </p>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>NIS</th>
                    <th>NISN</th>
                    <th>Nama Siswa</th>
                    <th>Kelas</th>
                    <th>Jumlah Pelanggaran</th>
                    <th>Total Poin</th>
                    <th>Keterangan Pelanggaran dan Nama Pencatat</th>
                    <th>Tanggal Pelanggaran Terakhir</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($data_rekap): ?>
                    <?php foreach ($data_rekap as $i => $data): ?>
                        <tr>
                            <td class="center"><?= $i + 1 ?></td>
                            <td class="text"><?= e($data['nis']) ?></td>
                            <td class="text"><?= e($data['nisn']) ?></td>
                            <td><?= e($data['nama_siswa']) ?></td>
                            <td><?= e($data['nama_kelas']) ?></td>
                            <td class="center"><?= (int)$data['jumlah_pelanggaran'] ?></td>
                            <td class="center"><?= (int)$data['total_poin'] ?></td>
                            <td class="keterangan"><?= nl2br(e($data['daftar_pelanggaran'] ?: '-')) ?></td>
                            <td class="tanggal"><?= e(tanggalIndonesia($data['tanggal_terakhir'] ?? '')) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="9" class="center">Belum ada data pelanggaran.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </body>
    </html>
    <?php
    exit;
}

$halamanAktif = basename($_SERVER['PHP_SELF'] ?? '');
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekap Pelanggaran - Sistem Pelanggaran</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/2.3.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">

    <style>
        :root {
            --blue: #2878cf;
            --blue-dark: #203e68;
            --blue-soft: #eaf3ff;
            --page: #f3f7fc;
            --white: #fff;
            --text: #263f63;
            --muted: #7890ad;
            --line: #dce9f8;
            --shadow: 0 8px 22px rgba(32, 62, 104, .07);
        }

        * {
            box-sizing: border-box;
        }

        html {
            min-height: 100%;
            overflow-y: scroll;
            scrollbar-gutter: stable;
        }

        body {
            width: 100%;
            min-height: 100vh;
            margin: 0;
            overflow-x: hidden;
            font-family: "Segoe UI", Arial, sans-serif;
            font-size: 15px;
            line-height: 1.5;
            background: var(--page);
            color: var(--text);
        }

        a {
            text-decoration: none;
        }

        button,
        input,
        select,
        textarea {
            font-family: inherit;
        }

        .sidebar {
            position: fixed;
            inset: 0 auto 0 0;
            width: 282px;
            height: 100vh;
            padding: 25px 14px 16px;
            display: flex;
            flex-direction: column;
            background: #fff;
            border-right: 1px solid var(--line);
            z-index: 1000;
            overflow-y: auto;
            overflow-x: hidden;
        }

        .brand {
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
            font-weight: 400;
        }

        .menu-title {
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
            min-height: 42px;
            padding: 11px 14px;
            border-radius: 9px;
            color: #5e7695;
            font-size: 13px;
            font-weight: 400;
            line-height: 1.4;
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

        .main {
            width: calc(100% - 282px);
            min-width: 0;
            min-height: 100vh;
            margin-left: 282px;
            padding: 24px 28px 40px;
        }

        .page-container {
            width: 100%;
            max-width: 100%;
            min-width: 0;
            margin: 0 auto;
        }

        .page-head {
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 11px;
            padding: 24px 25px;
            margin-bottom: 22px;
            box-shadow: 0 5px 15px rgba(32, 62, 104, .04);
        }

        .page-head h3 {
            font-size: 26px;
            font-weight: 600;
            line-height: 1.35;
            color: var(--blue-dark);
            margin: 0 0 5px;
        }

        .page-head p {
            color: var(--muted);
            font-size: 13px;
            line-height: 1.6;
            margin: 0;
        }

        .year-label {
            display: inline-block;
            margin-top: 12px;
            padding: 8px 12px;
            background: var(--blue-soft);
            color: var(--blue);
            border-radius: 8px;
            font-size: 12px;
            font-weight: 600;
        }

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 18px;
            margin-bottom: 22px;
        }

        .summary-card {
            min-width: 0;
            min-height: 135px;
            padding: 21px;
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 14px;
            box-shadow: var(--shadow);
        }

        .summary-label {
            margin-bottom: 12px;
            color: var(--muted);
            font-size: 12px;
            font-weight: 400;
        }

        .summary-value {
            color: var(--blue-dark);
            font-size: 27px;
            font-weight: 700;
            line-height: 1.3;
            font-variant-numeric: tabular-nums;
        }

        .panel {
            min-width: 0;
            padding: 23px;
            margin-bottom: 22px;
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 14px;
            box-shadow: var(--shadow);
        }

        .panel-heading {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
            margin-bottom: 19px;
        }

        .panel-title {
            margin: 0;
            color: var(--blue-dark);
            font-size: 16px;
            font-weight: 700;
        }

        .filter-form {
            display: flex;
            flex-wrap: wrap;
            align-items: flex-end;
            gap: 13px;
        }

        .form-group {
            min-width: 180px;
            flex: 1;
        }

        .form-label {
            display: block;
            margin-bottom: 7px;
            color: #405873;
            font-size: 13px;
            font-weight: 600;
        }

        .form-control,
        .form-select {
            width: 100%;
            min-height: 40px;
            padding: 9px 12px;
            border: 1px solid var(--line);
            border-radius: 8px;
            color: var(--text);
            background-color: #fff;
            font-family: inherit;
            font-size: 13px;
            box-shadow: none;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--blue);
            box-shadow: 0 0 0 3px rgba(40, 120, 207, .10);
        }

        .button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 38px;
            padding: 0 13px;
            border: 1px solid transparent;
            border-radius: 8px;
            cursor: pointer;
            font-family: inherit;
            font-size: 12px;
            font-weight: 600;
            line-height: 1.4;
            white-space: nowrap;
        }

        .button-primary {
            background: var(--blue);
            color: #fff;
        }

        .button-primary:hover {
            background: var(--blue-dark);
            color: #fff;
        }

        .button-light {
            background: #edf3fa;
            border-color: var(--line);
            color: #526b89;
        }

        .button-light:hover {
            background: #e3edf8;
            color: var(--blue-dark);
        }

        .button-green {
            background: #e9f7ef;
            color: #23844d;
        }

        .button-green:hover {
            background: #d9f0e3;
            color: #176b3b;
        }

        .button-dark {
            background: var(--blue-dark);
            color: #fff;
        }

        .button-dark:hover {
            background: #172f50;
            color: #fff;
        }

        .action-group {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .table-responsive {
            width: 100%;
            overflow-x: auto;
            scrollbar-gutter: stable;
        }

        #tabelRekap {
            width: 100% !important;
            margin-bottom: 0;
            border-collapse: collapse;
            vertical-align: middle;
        }

        #tabelRekap thead th {
            padding: 13px 12px;
            background: #f5f8fc;
            color: #7188a5;
            font-size: 12px;
            font-weight: 700;
            text-align: left;
            white-space: nowrap;
            border-bottom: 1px solid var(--line);
        }

        #tabelRekap tbody td {
            padding: 12px;
            color: #526b89;
            font-size: 13px;
            font-weight: 400;
            line-height: 1.5;
            vertical-align: middle;
            border-bottom: 1px solid #edf2f8;
        }

        #tabelRekap tbody tr:hover {
            background: #fafcff;
        }

        .student-name {
            color: var(--blue-dark);
            font-weight: 600;
        }

        .point-value {
            color: #d17a25;
            font-weight: 700;
        }

        .violation-list {
            min-width: 250px;
            max-width: 380px;
            white-space: normal;
            overflow-wrap: anywhere;
            line-height: 1.7;
        }

        .empty-state {
            padding: 28px !important;
            color: #8498b1 !important;
            font-size: 13px !important;
            text-align: center !important;
        }

        /* DATATABLES */
        .dt-container {
            width: 100%;
            color: var(--muted);
            font-family: inherit;
            font-size: 13px;
        }

        .dt-container .dt-layout-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
            margin: 13px 0;
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
            color: var(--muted);
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
            background: #fff;
            color: var(--text);
            font-family: inherit;
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
            background: #fff;
            font-family: inherit;
            font-size: 12px;
        }

        .dt-container .dt-search input:focus,
        .dt-container .dt-length select:focus {
            outline: none;
            border-color: var(--blue);
            box-shadow: 0 0 0 3px rgba(40, 120, 207, .10);
        }

        .dt-container .dt-info {
            color: var(--muted);
            font-size: 12px;
        }

        .dt-container .dt-paging .dt-paging-button {
            min-width: 32px;
            border-radius: 7px !important;
            font-family: inherit;
            font-size: 12px;
        }

        .dt-container .dt-paging .dt-paging-button.current {
            background: var(--blue-soft) !important;
            border-color: var(--line) !important;
            color: var(--blue) !important;
        }

        .footer {
            margin-top: 28px;
            color: #9aabc0;
            font-size: 11px;
            text-align: center;
        }

        /* SURAT PEMANGGILAN */
        .surat-area {
            width: 100%;
            max-width: 900px;
            margin: 0 auto;
            padding: 35px 45px;
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 12px;
            box-shadow: var(--shadow);
            color: #222;
        }

        .surat-kop {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 18px;
            padding-bottom: 12px;
            margin-bottom: 18px;
            border-bottom: 3px double #222;
            text-align: center;
        }

        .surat-logo {
            width: 82px;
            height: 82px;
            flex: 0 0 82px;
            object-fit: contain;
        }

        .surat-kop-text {
            flex: 1;
        }

        .surat-kop h2 {
            margin: 0 0 5px;
            color: #172f50;
            font-size: 19px;
            font-weight: 700;
        }

        .surat-kop p {
            margin: 0;
            font-size: 12px;
            line-height: 1.5;
        }

        .surat-judul {
            margin: 22px 0;
            text-align: center;
        }

        .surat-judul h3 {
            margin-bottom: 5px;
            font-size: 17px;
            font-weight: 700;
            text-decoration: underline;
        }

        .surat-isi {
            font-size: 14px;
            line-height: 1.8;
            text-align: justify;
        }

        .surat-data {
            margin: 15px 0 18px 25px;
        }

        .surat-data td {
            padding: 3px 8px 3px 0;
            vertical-align: top;
        }

        .surat-detail {
            width: 100%;
            margin: 15px 0;
            border-collapse: collapse;
            font-size: 12px;
        }

        .surat-detail th,
        .surat-detail td {
            padding: 7px;
            border: 1px solid #888;
            vertical-align: top;
        }

        .surat-detail th {
            background: #edf3fa;
            text-align: center;
        }

        .surat-ttd {
            width: 260px;
            margin: 30px 0 0 auto;
            font-size: 13px;
            line-height: 1.6;
            text-align: center;
        }

        .surat-ttd .nama-ttd {
            margin-top: 65px;
            font-weight: 700;
            text-decoration: underline;
        }

        .surat-toolbar {
            width: 100%;
            max-width: 900px;
            display: flex;
            justify-content: space-between;
            gap: 10px;
            margin: 0 auto 15px;
        }

        @media (max-width: 1100px) {
            .summary-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 900px) {
            .sidebar {
                position: static;
                width: 100%;
                height: auto;
            }

            .logout {
                margin-top: 15px;
            }

            .main {
                width: 100%;
                margin-left: 0;
            }
        }

        @media (max-width: 650px) {
            .main {
                padding: 18px 14px 30px;
            }

            .page-head {
                padding: 20px;
            }

            .page-head h3 {
                font-size: 23px;
            }

            .summary-grid {
                grid-template-columns: 1fr;
                gap: 13px;
            }

            .summary-card {
                min-height: 110px;
                padding: 18px;
            }

            .panel {
                padding: 17px;
            }

            .panel-heading {
                align-items: flex-start;
                flex-direction: column;
            }

            .filter-form .form-group {
                min-width: 100%;
            }

            .filter-form button,
            .filter-form a {
                flex: 1;
            }

            .action-group {
                width: 100%;
            }

            .action-group .button {
                flex: 1;
            }

            .dt-container .dt-layout-row {
                align-items: flex-start;
            }

            .dt-container .dt-search input {
                width: 140px;
            }

            .surat-area {
                padding: 22px 18px;
            }

            .surat-toolbar {
                flex-wrap: wrap;
            }
        }

        @page {
            size: A4 portrait;
            margin: 12mm 15mm;
        }

        @media print {
            body {
                background: #fff !important;
            }

            body.mode-surat .surat-toolbar {
                display: none !important;
            }

            body.mode-surat .surat-area {
                max-width: none !important;
                margin: 0 !important;
                padding: 0 !important;
                border: 0 !important;
                border-radius: 0 !important;
                box-shadow: none !important;
            }

            body.mode-surat .surat-isi {
                font-size: 11px !important;
                line-height: 1.45 !important;
            }

            body.mode-surat .surat-kop {
                margin-bottom: 12px !important;
                padding-bottom: 8px !important;
            }

            body.mode-surat .surat-logo {
                width: 68px !important;
                height: 68px !important;
                flex-basis: 68px !important;
            }

            body.mode-surat .surat-judul {
                margin: 12px 0 !important;
            }

            body.mode-surat .surat-data {
                margin: 8px 0 10px 18px !important;
            }

            body.mode-surat .surat-detail {
                margin: 8px 0 !important;
                font-size: 10px !important;
            }

            body.mode-surat .surat-detail th,
            body.mode-surat .surat-detail td {
                padding: 4px !important;
            }

            body.mode-surat .surat-ttd {
                margin-top: 12px !important;
            }

            body.mode-surat .surat-ttd .nama-ttd {
                margin-top: 42px !important;
            }

            body.mode-surat .surat-kop h2 {
                font-size: 17px !important;
            }

            body.mode-rekap {
                background: #fff !important;
            }

            body.mode-rekap .sidebar,
            body.mode-rekap .filter-panel,
            body.mode-rekap .action-group,
            body.mode-rekap .dt-container .dt-layout-row,
            body.mode-rekap .footer,
            body.mode-rekap .no-print,
            body.mode-rekap .alert {
                display: none !important;
            }

            body.mode-rekap .main {
                width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
            }

            body.mode-rekap .page-head {
                text-align: center;
                border: 0;
                border-bottom: 1px solid #777;
                border-radius: 0;
                box-shadow: none;
            }

            body.mode-rekap .summary-grid {
                grid-template-columns: repeat(3, 1fr);
                gap: 8px;
            }

            body.mode-rekap .summary-card {
                min-height: 0;
                padding: 9px;
                border: 1px solid #aaa;
                border-radius: 5px;
                box-shadow: none;
            }

            body.mode-rekap .panel {
                margin: 0;
                padding: 0;
                border: 0;
                box-shadow: none;
            }

            body.mode-rekap #tabelRekap {
                width: 100% !important;
                min-width: 0 !important;
                table-layout: fixed !important;
                border-collapse: collapse !important;
                font-size: 8px !important;
            }

            body.mode-rekap #tabelRekap th,
            body.mode-rekap #tabelRekap td {
                padding: 5px 4px !important;
                border: 1px solid #aaa !important;
                color: #222 !important;
                font-size: 8px !important;
                white-space: normal !important;
                overflow-wrap: anywhere !important;
            }

            body.mode-rekap #tabelRekap th:nth-child(1),
            body.mode-rekap #tabelRekap td:nth-child(1) { width: 3% !important; }

            body.mode-rekap #tabelRekap th:nth-child(2),
            body.mode-rekap #tabelRekap td:nth-child(2) { width: 8% !important; }

            body.mode-rekap #tabelRekap th:nth-child(3),
            body.mode-rekap #tabelRekap td:nth-child(3) { width: 9% !important; }

            body.mode-rekap #tabelRekap th:nth-child(4),
            body.mode-rekap #tabelRekap td:nth-child(4) { width: 16% !important; }

            body.mode-rekap #tabelRekap th:nth-child(5),
            body.mode-rekap #tabelRekap td:nth-child(5) { width: 9% !important; }

            body.mode-rekap #tabelRekap th:nth-child(6),
            body.mode-rekap #tabelRekap td:nth-child(6) { width: 8% !important; }

            body.mode-rekap #tabelRekap th:nth-child(7),
            body.mode-rekap #tabelRekap td:nth-child(7) { width: 7% !important; }

            body.mode-rekap #tabelRekap th:nth-child(8),
            body.mode-rekap #tabelRekap td:nth-child(8) { width: 30% !important; }

            body.mode-rekap #tabelRekap th:nth-child(9),
            body.mode-rekap #tabelRekap td:nth-child(9) { width: 10% !important; }
        }
    </style>
</head>

<body class="<?= $surat_data ? 'mode-surat' : 'mode-rekap' ?>">

<?php if ($surat_data): ?>

    <div class="surat-toolbar">
        <a
            href="rekap_pelanggaran.php?<?= e(http_build_query($parameter_filter)) ?>"
            class="button button-light"
        >
            Kembali ke Rekap
        </a>

        <button type="button" onclick="window.print()" class="button button-primary">
            Cetak / Simpan PDF
        </button>
    </div>

    <section class="surat-area">
        <div class="surat-kop">
            <img
                src="../assets/logo_sekolah.png"
                alt="Logo SMK Muhammadiyah Tasikmalaya"
                class="surat-logo"
            >
            <div class="surat-kop-text">
                <h2>SMK MUHAMMADIYAH TASIKMALAYA</h2>
                <p>
                    SEKOLAH MENENGAH KEJURUAN MUHAMMADIYAH TASIKMALAYA<br>
                    Jl. Rumah Sakit No. 29, Tasikmalaya
                </p>
            </div>
        </div>

        <div class="surat-judul">
            <h3>SURAT PEMANGGILAN ORANG TUA / WALI SISWA</h3>
        </div>

        <div class="surat-isi">
            <p>Dengan hormat,</p>

            <p>
                Dalam rangka meningkatkan pembinaan dan ketertiban peserta didik
                di lingkungan sekolah, kami mengharapkan kehadiran Bapak/Ibu
                Orang Tua/Wali dari:
            </p>

            <table class="surat-data">
                <tr>
                    <td width="130">Nama Siswa</td>
                    <td width="12">:</td>
                    <td><strong><?= e($surat_data['nama_siswa']) ?></strong></td>
                </tr>
                <tr>
                    <td>NIS</td>
                    <td>:</td>
                    <td><?= e($surat_data['nis']) ?></td>
                </tr>
                <tr>
                    <td>NISN</td>
                    <td>:</td>
                    <td><?= e($surat_data['nisn']) ?></td>
                </tr>
                <tr>
                    <td>Kelas</td>
                    <td>:</td>
                    <td><?= e($surat_data['nama_kelas']) ?></td>
                </tr>
            </table>

            <p>
                Sehubungan dengan adanya catatan pelanggaran tata tertib sekolah,
                kami mengundang Bapak/Ibu untuk hadir pada:
            </p>

            <table class="surat-data">
                <tr>
                    <td width="130">Hari / Tanggal</td>
                    <td width="12">:</td>
                    <td>
                        <?= e(hariIndonesia($surat_data['tanggal_pemanggilan'])) ?>,
                        <?= e(tanggalIndonesia($surat_data['tanggal_pemanggilan'])) ?>
                    </td>
                </tr>
                <tr>
                    <td>Waktu</td>
                    <td>:</td>
                    <td><?= e($surat_data['waktu_pemanggilan']) ?> WIB</td>
                </tr>
                <tr>
                    <td>Tempat</td>
                    <td>:</td>
                    <td>SMK Muhammadiyah Tasikmalaya</td>
                </tr>
                <tr>
                    <td>Keperluan</td>
                    <td>:</td>
                    <td><?= nl2br(e($surat_data['keperluan'])) ?></td>
                </tr>
            </table>

            <p><strong>Ringkasan catatan pelanggaran siswa:</strong></p>

            <table class="surat-detail">
                <thead>
                    <tr>
                        <th width="8%">No.</th>
                        <th>Keterangan Pelanggaran</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $daftar_surat = preg_split(
                        '/\r\n|\r|\n/',
                        (string)$surat_data['daftar_pelanggaran']
                    );
                    $nomor_surat = 1;
                    ?>

                    <?php foreach ($daftar_surat as $item_surat): ?>
                        <?php if (trim($item_surat) !== ''): ?>
                            <tr>
                                <td style="text-align:center;"><?= $nomor_surat++ ?></td>
                                <td><?= e($item_surat) ?></td>
                            </tr>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <p>
                Berdasarkan data rekapitulasi, siswa tersebut memiliki
                <strong><?= (int)$surat_data['jumlah_pelanggaran'] ?> catatan pelanggaran</strong>
                dengan total
                <strong><?= (int)$surat_data['total_poin'] ?> poin</strong>.
            </p>

            <p>
                Kami mengharapkan kehadiran Bapak/Ibu untuk bersama-sama
                melakukan pembinaan dan mencari solusi terbaik demi perkembangan
                serta kedisiplinan peserta didik.
            </p>

            <p>
                Demikian surat pemanggilan ini kami sampaikan. Atas perhatian
                dan kerja sama Bapak/Ibu, kami mengucapkan terima kasih.
            </p>

            <p>Hormat kami,</p>
        </div>

        <div class="surat-ttd">
            <p>Wali Kelas <?= e($surat_data['nama_kelas']) ?></p>
            <div class="nama-ttd"><?= e($nama_login) ?></div>
        </div>

        <p style="margin-top:35px; font-size:11px; color:#555;">
            Kepada Yth.<br>
            <strong>
                Bapak/Ibu Orang Tua/Wali dari Ananda
                <?= e($surat_data['nama_siswa']) ?>
            </strong><br>
            di Tempat
        </p>
    </section>

<?php else: ?>

    <aside class="sidebar">
        <div class="brand">
            <div class="brand-title">Sistem Pelanggaran</div>
            <div class="brand-subtitle">SMK Muhammadiyah</div>
        </div>

        <div class="menu-title">Menu Utama</div>

        <nav class="menu">
            <a href="../dashboard.php" class="menu-link">Dashboard</a>
            <a href="catat_pelanggaran.php" class="menu-link">Catat Pelanggaran</a>
            <a href="riwayat.php" class="menu-link">Riwayat</a>
            <a href="data_siswa.php" class="menu-link">Data Siswa</a>
            <a href="rekap_poin.php" class="menu-link">Rekap Poin Siswa</a>
            <a
                href="rekap_pelanggaran.php"
                class="menu-link <?= $halamanAktif === 'rekap_pelanggaran.php' ? 'active' : '' ?>"
                aria-current="page"
            >
                Rekap Pelanggaran
            </a>
            <a href="cetak_rekap.php" class="menu-link">Laporan</a>
            <a href="../about.php" class="menu-link">About Me</a>
        </nav>

        <div class="logout">
            <a href="../logout.php" class="menu-link">Logout</a>
        </div>
    </aside>

    <main class="main">
        <div class="page-container">

            <header class="page-head">
                <h3>Rekap Pelanggaran</h3>
                <p>Ringkasan pelanggaran siswa pada kelas yang menjadi tanggung jawab Anda.</p>
                <div class="year-label">
                    Tahun Ajaran: <?= e($nama_tahun_ajaran) ?>
                </div>
            </header>

            <?php if ($tahun_ajaran_id <= 0): ?>
                <div class="alert alert-warning">
                    Tahun ajaran aktif belum tersedia. Periksa kembali data tahun ajaran.
                </div>
            <?php elseif (count($daftar_kelas) === 0): ?>
                <div class="alert alert-warning">
                    Belum ditemukan kelas yang ditugaskan kepada akun Wali Kelas ini
                    pada tahun ajaran <?= e($nama_tahun_ajaran) ?>.
                </div>
            <?php endif; ?>

            <?php if ($pesan_filter !== ''): ?>
                <div class="alert alert-warning"><?= e($pesan_filter) ?></div>
            <?php endif; ?>

            <?php if ($pesan_surat !== ''): ?>
                <div class="alert alert-danger"><?= e($pesan_surat) ?></div>
            <?php endif; ?>

            <section class="summary-grid">
                <div class="summary-card">
                    <div class="summary-label">Jumlah Siswa dengan Pelanggaran</div>
                    <div class="summary-value"><?= number_format($total_siswa, 0, ',', '.') ?></div>
                </div>

                <div class="summary-card">
                    <div class="summary-label">Total Pelanggaran</div>
                    <div class="summary-value"><?= number_format($total_pelanggaran, 0, ',', '.') ?></div>
                </div>

                <div class="summary-card">
                    <div class="summary-label">Total Poin Pelanggaran</div>
                    <div class="summary-value"><?= number_format($total_poin, 0, ',', '.') ?></div>
                </div>
            </section>

            <section class="panel filter-panel">
                <div class="panel-heading">
                    <h4 class="panel-title">Filter Rekap</h4>
                </div>

                <form method="GET" class="filter-form">
                    <div class="form-group">
                        <label for="kelas_id" class="form-label">Pilih Kelas</label>
                        <select name="kelas_id" id="kelas_id" class="form-select">
                            <option value="0">Semua Kelas Wali Kelas</option>
                            <?php foreach ($daftar_kelas as $kelas): ?>
                                <option
                                    value="<?= (int)$kelas['kelas_id'] ?>"
                                    <?= $filter_kelas === (int)$kelas['kelas_id'] ? 'selected' : '' ?>
                                >
                                    <?= e($kelas['nama_kelas']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="tanggal_mulai" class="form-label">Tanggal Mulai</label>
                        <input
                            type="date"
                            name="tanggal_mulai"
                            id="tanggal_mulai"
                            class="form-control"
                            value="<?= e($tanggal_mulai) ?>"
                        >
                    </div>

                    <div class="form-group">
                        <label for="tanggal_selesai" class="form-label">Tanggal Selesai</label>
                        <input
                            type="date"
                            name="tanggal_selesai"
                            id="tanggal_selesai"
                            class="form-control"
                            value="<?= e($tanggal_selesai) ?>"
                        >
                    </div>

                    <button type="submit" class="button button-primary">Tampilkan</button>
                    <a href="rekap_pelanggaran.php" class="button button-light">Reset</a>
                </form>
            </section>

            <section class="panel">
                <div class="panel-heading">
                    <h4 class="panel-title">Data Rekap Siswa</h4>

                    <div class="action-group">
                        <a
                            href="rekap_pelanggaran.php?<?= e(http_build_query(array_merge($parameter_filter, ['export' => 'excel']))) ?>"
                            class="button button-green"
                        >
                            Ekspor Excel
                        </a>

                        <button type="button" onclick="cetakRekap()" class="button button-dark">
                            Cetak Rekap
                        </button>
                    </div>
                </div>

                <div class="table-responsive">
                    <table id="tabelRekap" class="table table-hover align-middle w-100">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>NIS</th>
                                <th>NISN</th>
                                <th>Nama Siswa</th>
                                <th>Kelas</th>
                                <th>Jumlah Pelanggaran</th>
                                <th>Total Poin</th>
                                <th>Keterangan Pelanggaran</th>
                                <th>Tanggal Terakhir</th>
                                <th class="no-print">Aksi</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php if (count($data_rekap) > 0): ?>
                                <?php foreach ($data_rekap as $i => $data): ?>
                                    <tr>
                                        <td><?= $i + 1 ?></td>
                                        <td><?= e($data['nis']) ?></td>
                                        <td><?= e($data['nisn']) ?></td>
                                        <td>
                                            <span class="student-name">
                                                <?= e($data['nama_siswa']) ?>
                                            </span>
                                        </td>
                                        <td><?= e($data['nama_kelas']) ?></td>
                                        <td><?= number_format((int)$data['jumlah_pelanggaran'], 0, ',', '.') ?></td>
                                        <td>
                                            <span class="point-value">
                                                <?= number_format((int)$data['total_poin'], 0, ',', '.') ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="violation-list">
                                                <?= $data['daftar_pelanggaran']
                                                    ? nl2br(e($data['daftar_pelanggaran']))
                                                    : '-' ?>
                                            </div>
                                        </td>
                                        <td><?= e(tanggalIndonesia($data['tanggal_terakhir'] ?? '')) ?></td>
                                        <td class="no-print">
                                            <button
                                                type="button"
                                                class="button button-primary"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modalSurat"
                                                data-siswa="<?= (int)$data['siswa_id'] ?>"
                                                data-kelas="<?= (int)$data['kelas_id'] ?>"
                                                data-nama="<?= e($data['nama_siswa']) ?>"
                                                data-kelas-nama="<?= e($data['nama_kelas']) ?>"
                                            >
                                                Buat Surat
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="10" class="empty-state">
                                        Belum ada data pelanggaran untuk kelas dan tanggal yang dipilih.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </section>

            <div class="footer">
                Sistem Pelanggaran Sekolah · Project Rekayasa Perangkat Lunak
            </div>

        </div>
    </main>

    <!-- MODAL SURAT -->
    <div class="modal fade" id="modalSurat" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border:0; border-radius:14px; overflow:hidden;">

                <div class="modal-header" style="background:#eaf3ff; border-bottom:1px solid #dce9f8;">
                    <div>
                        <h5 class="modal-title" style="color:#203e68; font-weight:700;">
                            Buat Surat Pemanggilan
                        </h5>
                        <small style="color:#7890ad;">
                            Lengkapi jadwal dan keperluan pemanggilan.
                        </small>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <form method="POST" action="rekap_pelanggaran.php?<?= e(http_build_query($parameter_filter)) ?>">
                    <div class="modal-body p-4">
                        <input type="hidden" name="aksi" value="buat_surat">
                        <input type="hidden" name="siswa_id" id="surat_siswa_id">
                        <input type="hidden" name="kelas_id" id="surat_kelas_id">

                        <div class="mb-3">
                            <label class="form-label">Nama Siswa</label>
                            <input type="text" id="surat_nama_siswa" class="form-control" readonly>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Kelas</label>
                            <input type="text" id="surat_nama_kelas" class="form-control" readonly>
                        </div>

                        <div class="mb-3">
                            <label for="tanggal_pemanggilan" class="form-label">
                                Tanggal Pemanggilan
                            </label>
                            <input
                                type="date"
                                name="tanggal_pemanggilan"
                                id="tanggal_pemanggilan"
                                class="form-control"
                                min="<?= date('Y-m-d') ?>"
                                required
                            >
                        </div>

                        <div class="mb-3">
                            <label for="waktu_pemanggilan" class="form-label">
                                Waktu Pemanggilan
                            </label>
                            <input
                                type="time"
                                name="waktu_pemanggilan"
                                id="waktu_pemanggilan"
                                class="form-control"
                                required
                            >
                        </div>

                        <div class="mb-2">
                            <label for="keperluan" class="form-label">
                                Keperluan Pemanggilan
                            </label>
                            <textarea
                                name="keperluan"
                                id="keperluan"
                                class="form-control"
                                rows="3"
                                placeholder="Contoh: Pembinaan dan konsultasi terkait catatan pelanggaran tata tertib siswa."
                                required
                            ></textarea>
                        </div>

                        <div class="alert alert-info mt-3 mb-0" style="font-size:12px;">
                            Surat akan ditujukan kepada Bapak/Ibu Orang Tua/Wali
                            dari siswa yang dipilih. Nama orang tua tidak perlu diisi.
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button
                            type="button"
                            class="button button-light"
                            data-bs-dismiss="modal"
                        >
                            Batal
                        </button>
                        <button type="submit" class="button button-primary">
                            Buat Surat
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

<?php endif; ?>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/2.3.4/js/dataTables.min.js"></script>
<script src="https://cdn.datatables.net/2.3.4/js/dataTables.bootstrap5.min.js"></script>

<script>
<?php if (!$surat_data): ?>

let tabelRekap = null;
let panjangHalamanSebelumnya = 10;
let sedangMencetak = false;

$(function () {
    const jumlahData = <?= count($data_rekap) ?>;

    if (jumlahData > 0) {
        tabelRekap = $('#tabelRekap').DataTable({
            pageLength: 10,
            lengthMenu: [10, 25, 50, 100],
            order: [],
            autoWidth: false,
            deferRender: true,
            language: {
                search: "Cari:",
                searchPlaceholder: "Cari nama, NIS, atau pelanggaran...",
                lengthMenu: "Tampilkan _MENU_ data",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ siswa",
                infoEmpty: "Tidak ada data rekap",
                infoFiltered: "(disaring dari _MAX_ data)",
                zeroRecords: "Data rekap tidak ditemukan",
                emptyTable: "Belum ada data pelanggaran",
                paginate: {
                    previous: "Sebelumnya",
                    next: "Berikutnya"
                }
            }
        });
    }
});

/* CETAK SELURUH DATA */
function cetakRekap() {
    if (sedangMencetak) return;

    sedangMencetak = true;

    if (tabelRekap) {
        panjangHalamanSebelumnya = tabelRekap.page.len();
        tabelRekap.page.len(-1).draw();
    }

    requestAnimationFrame(function () {
        requestAnimationFrame(function () {
            window.print();
        });
    });
}

window.addEventListener('afterprint', function () {
    if (tabelRekap && tabelRekap.page.len() !== panjangHalamanSebelumnya) {
        tabelRekap.page.len(panjangHalamanSebelumnya).draw();
    }

    sedangMencetak = false;
});

/* ISI DATA MODAL SURAT */
const modalSurat = document.getElementById('modalSurat');

if (modalSurat) {
    modalSurat.addEventListener('show.bs.modal', function (event) {
        const tombol = event.relatedTarget;

        if (!tombol) return;

        document.getElementById('surat_siswa_id').value =
            tombol.getAttribute('data-siswa') || '';

        document.getElementById('surat_kelas_id').value =
            tombol.getAttribute('data-kelas') || '';

        document.getElementById('surat_nama_siswa').value =
            tombol.getAttribute('data-nama') || '';

        document.getElementById('surat_nama_kelas').value =
            tombol.getAttribute('data-kelas-nama') || '';
    });
}

<?php endif; ?>
</script>

</body>
</html>
