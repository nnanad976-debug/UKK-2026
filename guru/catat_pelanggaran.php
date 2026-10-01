
<?php
include "../middleware/auth.php";
include "../config/koneksi.php";

if ($_SESSION['role'] != 'guru') {
    echo "Anda tidak memiliki akses";
    exit;
}

$user_id = (int) $_SESSION['user_id'];

// DATA GURU
$query_guru = mysqli_query(
    $koneksi,
    "SELECT id, nama FROM t_guru
     WHERE user_id='$user_id' LIMIT 1"
);
$data_guru = mysqli_fetch_assoc($query_guru);
$guru_id = $data_guru['id'] ?? 0;
$nama_guru = $data_guru['nama'] ?? $_SESSION['nama'];

// DATA KELAS: TAMPILKAN KOMBINASI TINGKAT + JURUSAN YANG UNIK
$query_kelas = mysqli_query(
    $koneksi,
    "SELECT DISTINCT k.tingkat, k.jurusan
     FROM t_kelas k
     JOIN t_kelas_siswa ks ON k.id = ks.kelas_id
     WHERE k.status_aktif = 1
       AND ks.status_aktif = 1
       AND ks.tahun_ajaran_id = 50
     ORDER BY
        FIELD(k.jurusan, 'RPL', 'TKJ', 'BD', 'TKR', 'TSM'),
        FIELD(k.tingkat, 'X', 'XI', 'XII')"
);

// DATA SISWA BESERTA TINGKAT DAN JURUSAN
$query_siswa = mysqli_query(
    $koneksi,
    "SELECT
        s.id,
        s.nis,
        s.nama,
        ks.kelas_id,
        k.tingkat,
        k.jurusan
     FROM t_siswa s
     JOIN t_kelas_siswa ks ON s.id = ks.siswa_id
     JOIN t_kelas k ON k.id = ks.kelas_id
     WHERE s.status_aktif = 1
       AND ks.status_aktif = 1
       AND ks.tahun_ajaran_id = 50
       AND k.status_aktif = 1
     ORDER BY s.nama ASC"
);

// DATA PELANGGARAN
$query_jenis = mysqli_query(
    $koneksi,
    "SELECT id, kode, nama, poin
     FROM t_pelanggaran
     WHERE status_aktif = 1
     ORDER BY nama ASC"
);

// SIMPAN PELANGGARAN
if (isset($_POST['simpan'])) {
    $siswa_id = (int) ($_POST['siswa_id'] ?? 0);
    $kelas_key = $_POST['kelas_key'] ?? '';
    $pelanggaran_id = (int) ($_POST['pelanggaran_id'] ?? 0);
    $tanggal = mysqli_real_escape_string(
        $koneksi,
        $_POST['tanggal'] ?? ''
    );
    $keterangan = mysqli_real_escape_string(
        $koneksi,
        $_POST['keterangan'] ?? ''
    );

    // PASTIKAN SISWA BERASAL DARI KELAS YANG DIPILIH
    $kelas_key_db = mysqli_real_escape_string($koneksi, $kelas_key);

    $query_siswa_data = mysqli_query(
        $koneksi,
        "SELECT
            s.nama,
            ks.kelas_id,
            k.nama AS nama_kelas_asli,
            k.tingkat,
            k.jurusan
         FROM t_siswa s
         JOIN t_kelas_siswa ks ON s.id = ks.siswa_id
         JOIN t_kelas k ON k.id = ks.kelas_id
         WHERE s.id = '$siswa_id'
           AND s.status_aktif = 1
           AND ks.status_aktif = 1
           AND ks.tahun_ajaran_id = 50
           AND k.status_aktif = 1
           AND CONCAT(k.tingkat, '|', k.jurusan) = '$kelas_key_db'
         LIMIT 1"
    );

    $data_siswa = mysqli_fetch_assoc($query_siswa_data);

    if (!$data_siswa) {
        echo "<script>alert('Siswa tidak sesuai dengan kelas yang dipilih!');</script>";
    } else {
        $nama_siswa = mysqli_real_escape_string(
            $koneksi,
            $data_siswa['nama']
        );
        $kelas_id = (int) $data_siswa['kelas_id'];

        // DATA PELANGGARAN
        $query_pelanggaran = mysqli_query(
            $koneksi,
            "SELECT nama, poin, pelanggaran_kategori_id
             FROM t_pelanggaran
             WHERE id='$pelanggaran_id'
               AND status_aktif = 1
             LIMIT 1"
        );
        $data_pelanggaran = mysqli_fetch_assoc($query_pelanggaran);

        if (!$data_pelanggaran) {
            echo "<script>alert('Jenis pelanggaran tidak valid!');</script>";
        } else {
            $nama_pelanggaran = mysqli_real_escape_string(
                $koneksi,
                $data_pelanggaran['nama']
            );
            $poin = (int) $data_pelanggaran['poin'];
            $pelanggaran_kategori_id =
                (int) ($data_pelanggaran['pelanggaran_kategori_id'] ?? 0);

            $nama_kelas = mysqli_real_escape_string(
                $koneksi,
                $data_siswa['nama_kelas_asli'] . ' - ' .
                $data_siswa['tingkat'] . ' - ' .
                $data_siswa['jurusan']
            );

            // SIMPAN
            $query_simpan = mysqli_query(
                $koneksi,
                "INSERT INTO t_pelanggaran_siswa
                (
                    siswa_id, nama_siswa, pelanggaran_id,
                    nama_pelanggaran, pelanggaran_kategori_id,
                    nama_kategori, tanggal, keterangan, poin,
                    guru_id, nama_guru, tindakan, status,
                    kelas_id, nama_kelas
                )
                VALUES
                (
                    '$siswa_id', '$nama_siswa', '$pelanggaran_id',
                    '$nama_pelanggaran', '$pelanggaran_kategori_id',
                    '', '$tanggal', '$keterangan', '$poin',
                    '$guru_id', '$nama_guru', '', 'Diproses',
                    '$kelas_id', '$nama_kelas'
                )"
            );

            if ($query_simpan) {
                echo "<script>
                    alert('Pelanggaran berhasil dicatat!');
                    window.location.href = 'catat_pelanggaran.php';
                </script>";
                exit;
            } else {
                echo "<script>alert('Pelanggaran gagal dicatat!');</script>";
            }
        }
    }
}

// RIWAYAT GURU
$query_riwayat = mysqli_query(
    $koneksi,
    "SELECT *
     FROM t_pelanggaran_siswa
     WHERE guru_id='$guru_id'
     ORDER BY tanggal DESC, id DESC"
);
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Catat Pelanggaran</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body {
    background-color: #f3f7fc;
    color: #2f435a;
    font-family: "Segoe UI", Arial, sans-serif;
}
.container { max-width: 1120px; }
.judul, h4 { color: #234e78; }
.card {
    border: 1px solid #e1eaf4 !important;
    border-radius: 10px;
    box-shadow: 0 3px 12px rgba(35,78,120,.05) !important;
}
.card-body { padding: 24px; }
.form-label { color: #405873; font-weight: 600; }
.form-control, .form-select {
    border-color: #d7e3ef;
    border-radius: 7px;
    padding: 10px 12px;
}
.form-control:focus, .form-select:focus {
    border-color: #8eb5d8;
    box-shadow: 0 0 0 .2rem rgba(87,143,190,.14);
}
.btn-primary {
    --bs-btn-bg: #5f91ba;
    --bs-btn-border-color: #5f91ba;
    --bs-btn-hover-bg: #4b7fa9;
    --bs-btn-hover-border-color: #4b7fa9;
    border-radius: 7px;
    padding: 8px 15px;
}
.btn-secondary {
    --bs-btn-bg: #e8f0f8;
    --bs-btn-border-color: #d8e5f1;
    --bs-btn-color: #426584;
    --bs-btn-hover-bg: #dceaf6;
    --bs-btn-hover-border-color: #cbddeb;
    --bs-btn-hover-color: #234e78;
    border-radius: 7px;
}
.table { color: #344b63; vertical-align: middle; }
.table thead th {
    background-color: #eaf2fa;
    color: #315b82;
    border-color: #dce7f1;
    font-weight: 600;
    padding: 12px;
}
.table tbody td { border-color: #e5edf5; padding: 11px 12px; }
.table-hover tbody tr:hover > * {
    --bs-table-accent-bg: #f4f8fc;
}
@media (max-width: 576px) {
    .container { padding-left: 14px; padding-right: 14px; }
    .card-body { padding: 17px; }
}
</style>
</head>

<body>
<div class="container py-4">

    <a href="../dashboard.php" class="btn btn-secondary mb-3">
        Kembali ke Dashboard
    </a>

    <div class="card mb-4">
        <div class="card-body">
            <h2 class="judul">Catat Pelanggaran</h2>
            <p class="text-muted mb-0">Catat pelanggaran siswa.</p>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <h4 class="mb-4">Tambah Pelanggaran</h4>

            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Kelas</label>
                    <select id="kelas_id" name="kelas_key"
                            class="form-select" required>
                        <option value="">-- Pilih Kelas --</option>
                        <?php while ($k = mysqli_fetch_assoc($query_kelas)) {
                            $kelas_key = $k['tingkat'] . '|' . $k['jurusan'];
                        ?>
                            <option value="<?= htmlspecialchars($kelas_key); ?>">
                                <?= htmlspecialchars($k['tingkat'] . ' - ' . $k['jurusan']); ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Nama Siswa</label>
                    <select id="siswa_id" name="siswa_id"
                            class="form-select" required disabled>
                        <option value="">-- Pilih kelas terlebih dahulu --</option>
                        <?php while ($s = mysqli_fetch_assoc($query_siswa)) {
                            $siswa_key = $s['tingkat'] . '|' . $s['jurusan'];
                        ?>
                            <option
                                value="<?= (int) $s['id']; ?>"
                                data-kelas="<?= htmlspecialchars($siswa_key); ?>">
                                <?= htmlspecialchars($s['nis'] . ' - ' . $s['nama']); ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Jenis Pelanggaran</label>
                    <select name="pelanggaran_id" class="form-select" required>
                        <option value="">-- Pilih Pelanggaran --</option>
                        <?php while ($p = mysqli_fetch_assoc($query_jenis)) { ?>
                            <option value="<?= (int) $p['id']; ?>">
                                <?= htmlspecialchars($p['kode'] . ' - ' . $p['nama']); ?>
                                (<?= (int) $p['poin']; ?> poin)
                            </option>
                        <?php } ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Tanggal</label>
                    <input type="date" name="tanggal" class="form-control"
                           value="<?= date('Y-m-d'); ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Keterangan</label>
                    <textarea name="keterangan" class="form-control" rows="4"
                              placeholder="Masukkan keterangan pelanggaran"
                              required></textarea>
                </div>

                <button type="submit" name="simpan" class="btn btn-primary">
                    Simpan Pelanggaran
                </button>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <h4 class="mb-3">Riwayat Pelanggaran</h4>
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Siswa</th>
                            <th>Kelas</th>
                            <th>Pelanggaran</th>
                            <th>Poin</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    $no = 1;
                    while ($data = mysqli_fetch_assoc($query_riwayat)) {
                    ?>
                        <tr>
                            <td><?= $no++; ?></td>
                            <td><?= htmlspecialchars($data['tanggal']); ?></td>
                            <td><?= htmlspecialchars($data['nama_siswa']); ?></td>
                            <td><?= htmlspecialchars($data['nama_kelas']); ?></td>
                            <td><?= htmlspecialchars($data['nama_pelanggaran']); ?></td>
                            <td><?= htmlspecialchars($data['poin']); ?></td>
                            <td><?= htmlspecialchars($data['status']); ?></td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
const kelas = document.getElementById('kelas_id');
const siswa = document.getElementById('siswa_id');
const semuaSiswa = Array.from(siswa.options).slice(1);

kelas.addEventListener('change', function () {
    const kelasDipilih = this.value;
    siswa.innerHTML = '';

    const awal = document.createElement('option');
    awal.value = '';
    awal.textContent = kelasDipilih
        ? '-- Pilih Nama Siswa --'
        : '-- Pilih kelas terlebih dahulu --';
    siswa.appendChild(awal);

    semuaSiswa.forEach(function (option) {
        if (option.dataset.kelas === kelasDipilih) {
            siswa.appendChild(option.cloneNode(true));
        }
    });

    siswa.disabled = !kelasDipilih;

    if (kelasDipilih && siswa.options.length === 1) {
        awal.textContent = '-- Belum ada siswa di kelas ini --';
    }
});
</script>
</body>
</html>