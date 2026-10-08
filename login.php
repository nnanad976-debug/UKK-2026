<?php
session_start();

$pesan_error = $_SESSION['pesan_error'] ?? '';
unset($_SESSION['pesan_error']);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login - Sistem Informasi Pelanggaran Siswa</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >
</head>

<body class="bg-light">

<div class="d-flex justify-content-center align-items-center min-vh-100">

    <div class="card shadow-sm border-0 p-4" style="width: 380px;">

        <div class="text-center mb-4">
            <h3 class="mb-1">Sistem Informasi</h3>
            <h5 class="text-muted">Pelanggaran Siswa</h5>
        </div>

        <?php if ($pesan_error): ?>
            <div class="alert alert-danger">
                <?= htmlspecialchars($pesan_error); ?>
            </div>
        <?php endif; ?>

        <form action="proses_login.php" method="POST">

            <div class="mb-3">
                <label for="email" class="form-label">Email</label>

                <input
                    type="email"
                    name="email"
                    id="email"
                    class="form-control"
                    placeholder="Masukkan email"
                    required
                >
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password</label>

                <input
                    type="password"
                    name="password"
                    id="password"
                    class="form-control"
                    placeholder="Masukkan password"
                    required
                >
            </div>

            <button type="submit" class="btn btn-primary w-100">
                Login
            </button>

        </form>

        <p class="text-muted small text-center mt-4 mb-0">
            © Nadia Zulfa
        </p>

    </div>

</div>

</body>
</html>