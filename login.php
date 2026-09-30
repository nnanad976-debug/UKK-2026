<?php

session_start();

if (isset($_SESSION['login']) && $_SESSION['login'] == true) {

    header("Location: dashboard.php");
    exit;

}

?>

<!DOCTYPE html>
<html>

<head>

    <title>Login</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet">

</head>

<body>

<div class="d-flex justify-content-center align-items-center min-vh-100">

    <div class="card shadow-sm p-4" style="width: 380px;">

        <div class="text-center mb-4">

            <h2>Sistem Informasi</h2>
            <h4>Pelanggaran Siswa</h4>

        </div>

        <form
            action="proses_login.php"
            method="POST"
            class="text-start">

            <div class="mb-3">

                <label class="form-label">
                    Email
                </label>

                <input
                    type="email"
                    name="email"
                    class="form-control"
                    placeholder="Masukkan email"
                    required>

            </div>


            <div class="mb-3">

                <label class="form-label">
                    Password
                </label>

                <input
                    type="password"
                    name="password"
                    class="form-control"
                    placeholder="Masukkan password"
                    required>

            </div>


            <button
                type="submit"
                class="btn btn-primary w-100">

                Login

            </button>

        </form>


        <p class="text-muted small mt-4">

            © nadiazulfa

        </p>

    </div>

</div>

</body>

</html>