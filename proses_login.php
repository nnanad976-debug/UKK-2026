
<?php
session_start();

include __DIR__ . "/config/koneksi.php";

// 1. Pastikan request berasal dari form login
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: login.php");
    exit;
}

// 2. Ambil data login
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if ($email === '' || $password === '') {
    $_SESSION['pesan_error'] = 'Email dan password wajib diisi.';
    header("Location: login.php");
    exit;
}

// 3. Cari akun berdasarkan email
$stmt = mysqli_prepare(
    $koneksi,
    "SELECT id, name, email, password, role
     FROM t_users
     WHERE email = ?
     LIMIT 1"
);

if (!$stmt) {
    error_log("Login prepare gagal: " . mysqli_error($koneksi));
    $_SESSION['pesan_error'] = 'Terjadi kesalahan pada sistem login.';
    header("Location: login.php");
    exit;
}

mysqli_stmt_bind_param($stmt, "s", $email);
mysqli_stmt_execute($stmt);

mysqli_stmt_bind_result(
    $stmt,
    $id,
    $name,
    $db_email,
    $db_password,
    $role
);

$user_ditemukan = mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);

// 4. Verifikasi akun dan password
if (!$user_ditemukan || !password_verify($password, $db_password)) {
    $_SESSION['pesan_error'] = 'Email atau password salah.';
    header("Location: login.php");
    exit;
}

// 5. Normalisasi role
$role = strtolower(trim($role));
$role = str_replace([' ', '-'], '_', $role);

if (!in_array($role, ['admin', 'guru', 'wali_kelas'], true)) {
    $_SESSION['pesan_error'] = 'Role akun tidak valid.';
    header("Location: login.php");
    exit;
}

// 6. Siapkan data guru dan penugasan Wali Kelas
$guru_id = null;
$daftar_penugasan = [];

if ($role === 'guru' || $role === 'wali_kelas') {

    $stmt_guru = mysqli_prepare(
        $koneksi,
        "SELECT id
         FROM t_guru
         WHERE user_id = ?
           AND status_aktif = 1
         LIMIT 1"
    );

    if (!$stmt_guru) {
        error_log("Query guru gagal: " . mysqli_error($koneksi));
        $_SESSION['pesan_error'] = 'Terjadi kesalahan saat memeriksa data guru.';
        header("Location: login.php");
        exit;
    }

    mysqli_stmt_bind_param($stmt_guru, "i", $id);
    mysqli_stmt_execute($stmt_guru);
    mysqli_stmt_bind_result($stmt_guru, $guru_id);

    $guru_ditemukan = mysqli_stmt_fetch($stmt_guru);
    mysqli_stmt_close($stmt_guru);

    if (!$guru_ditemukan) {
        $_SESSION['pesan_error'] = 'Data guru tidak ditemukan atau tidak aktif.';
        header("Location: login.php");
        exit;
    }

    // Periksa penugasan Wali Kelas yang masih berlaku
    $stmt_wali = mysqli_prepare(
        $koneksi,
        "SELECT id, kelas_id, tahun_ajaran_id
         FROM t_wali_kelas
         WHERE guru_id = ?
           AND status_aktif = 1
           AND tanggal_mulai <= CURDATE()
           AND (
                tanggal_selesai IS NULL
                OR tanggal_selesai >= CURDATE()
           )"
    );

    if (!$stmt_wali) {
        error_log("Query wali kelas gagal: " . mysqli_error($koneksi));
        $_SESSION['pesan_error'] = 'Terjadi kesalahan saat memeriksa penugasan.';
        header("Location: login.php");
        exit;
    }

    mysqli_stmt_bind_param($stmt_wali, "i", $guru_id);
    mysqli_stmt_execute($stmt_wali);

    mysqli_stmt_bind_result(
        $stmt_wali,
        $penugasan_id,
        $kelas_id,
        $tahun_ajaran_id
    );

    while (mysqli_stmt_fetch($stmt_wali)) {
        $daftar_penugasan[] = [
            'id' => $penugasan_id,
            'kelas_id' => $kelas_id,
            'tahun_ajaran_id' => $tahun_ajaran_id
        ];
    }

    mysqli_stmt_close($stmt_wali);

    if ($role === 'wali_kelas' && empty($daftar_penugasan)) {
        $_SESSION['pesan_error'] = 'Anda belum memiliki penugasan Wali Kelas aktif.';
        header("Location: login.php");
        exit;
    }
}

// 7. Buat session login
session_regenerate_id(true);

$_SESSION['login'] = true;
$_SESSION['user_id'] = $id;
$_SESSION['nama'] = $name;
$_SESSION['email'] = $db_email;

// 8. Tentukan dashboard berdasarkan role
if ($role === 'admin') {

    $_SESSION['role'] = 'admin';
    $_SESSION['is_wali_kelas'] = false;
    $_SESSION['wali_kelas_penugasan'] = [];

    header("Location: dashboard.php");
    exit;
}

// Akun guru yang memiliki penugasan Wali Kelas
$_SESSION['role'] = 'guru';
$_SESSION['guru_id'] = $guru_id;

if (!empty($daftar_penugasan)) {
    $_SESSION['is_wali_kelas'] = true;
    $_SESSION['wali_kelas_penugasan'] = $daftar_penugasan;
} else {
    $_SESSION['is_wali_kelas'] = false;
    $_SESSION['wali_kelas_penugasan'] = [];
}

header("Location: dashboard.php");
exit;
?>
