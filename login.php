
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

    <title>Login | SIKET - Sistem Informasi Ketertiban Siswa</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <style>
        * {
            box-sizing: border-box;
        }

        :root {
            --navy: #17365d;
            --blue: #2878cf;
            --blue-dark: #1d5fa8;
            --text: #25364b;
            --muted: #7c899b;
            --border: #e2eaf3;
        }

        body {
            min-height: 100vh;
            margin: 0;
            font-family: "Segoe UI", Arial, sans-serif;
            color: var(--text);
            background: #f3f6fb;
        }

        .login-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 30px 16px;
            background:
                radial-gradient(
                    circle at 10% 10%,
                    #e0ecfc 0,
                    transparent 32%
                ),
                radial-gradient(
                    circle at 90% 90%,
                    #e4edf9 0,
                    transparent 32%
                ),
                #f5f8fd;
        }

        .login-card {
            width: 100%;
            max-width: 410px;
            padding: 35px 34px 24px;
            background: #ffffff;
            border: 1px solid #e6edf6;
            border-radius: 18px;
            box-shadow: 0 18px 50px rgba(31, 65, 105, 0.10);
            animation: muncul 0.5s ease;
        }

        .logo-wrapper {
            width: 92px;
            height: 92px;
            margin: 0 auto 16px;
            padding: 7px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f7faff;
            border: 1px solid #e8eef7;
            border-radius: 15px;
        }

        .logo-wrapper img {
            display: block;
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .school-name {
            margin-bottom: 20px;
            color: #526b86;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 1px;
            line-height: 1.7;
            text-transform: uppercase;
        }

        .login-title {
            margin: 0;
            color: var(--navy);
            font-size: 36px;
            font-weight: 800;
            letter-spacing: 2px;
        }

        .login-description {
            margin: 5px 0 0;
            color: #526b86;
            font-size: 13px;
            font-weight: 600;
            line-height: 1.6;
        }

        .login-subtitle {
            margin: 12px 0 27px;
            color: var(--muted);
            font-size: 12px;
            line-height: 1.7;
        }

        .form-label {
            margin-bottom: 8px;
            color: #334b65;
            font-size: 12px;
            font-weight: 700;
        }

        .form-control {
            min-height: 47px;
            padding: 11px 13px;
            color: var(--text);
            background: #fafcff;
            border: 1px solid #dce5f0;
            border-radius: 9px;
            font-size: 13px;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .form-control::placeholder {
            color: #a0acba;
        }

        .form-control:focus {
            background: #ffffff;
            border-color: var(--blue);
            box-shadow: 0 0 0 4px rgba(40, 120, 207, 0.10);
        }

        .password-group {
            position: relative;
        }

        .password-group .form-control {
            padding-right: 110px;
        }

        .toggle-password {
            position: absolute;
            top: 50%;
            right: 12px;
            transform: translateY(-50%);
            padding: 5px 0;
            color: var(--blue);
            background: transparent;
            border: none;
            font-size: 11px;
            font-weight: 700;
            cursor: pointer;
        }

        .toggle-password:hover {
            color: var(--navy);
        }

        .btn-login {
            min-height: 47px;
            margin-top: 7px;
            color: #ffffff;
            background: var(--blue);
            border: none;
            border-radius: 9px;
            font-size: 13px;
            font-weight: 700;
            transition: background 0.2s, transform 0.2s;
        }

        .btn-login:hover {
            color: #ffffff;
            background: var(--blue-dark);
            box-shadow: 0 6px 16px rgba(40, 120, 207, 0.20);
            transform: translateY(-1px);
        }

        .btn-login:active {
            transform: scale(0.99);
        }

        .alert {
            border: none;
            border-radius: 9px;
            font-size: 12px;
            line-height: 1.6;
        }

        .login-footer {
            margin-top: 25px;
            padding-top: 16px;
            border-top: 1px solid #edf1f6;
            color: #8995a4;
            font-size: 10px;
            line-height: 1.8;
            text-align: center;
        }

        .footer-name {
            color: #526780;
            font-weight: 600;
        }

        @keyframes muncul {
            from {
                opacity: 0;
                transform: translateY(12px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 480px) {
            .login-wrapper {
                padding: 20px 14px;
            }

            .login-card {
                padding: 29px 23px 22px;
            }

            .logo-wrapper {
                width: 82px;
                height: 82px;
            }

            .login-title {
                font-size: 32px;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            *,
            *::before,
            *::after {
                animation: none !important;
                transition: none !important;
            }
        }
    </style>
</head>

<body>

<div class="login-wrapper">

    <main class="login-card">

        <header class="text-center">

            <div class="logo-wrapper">
                <img
                    src="assets/logo_sekolah.png"
                    alt="Logo SMK Muhammadiyah Tasikmalaya"
                >
            </div>

            <p class="school-name">
                SMK Muhammadiyah Tasikmalaya
            </p>

            <h1 class="login-title">SIKET</h1>

            <p class="login-description">
                Sistem Informasi Ketertiban Siswa
            </p>

            <p class="login-subtitle">
                Silakan masuk menggunakan akun Anda.
            </p>

        </header>

        <?php if ($pesan_error): ?>
            <div class="alert alert-danger" role="alert">
                <?= htmlspecialchars(
                    $pesan_error,
                    ENT_QUOTES,
                    'UTF-8'
                ); ?>
            </div>
        <?php endif; ?>

        <form action="proses_login.php" method="POST">

            <div class="mb-3">
                <label for="email" class="form-label">
                    Email
                </label>

                <input
                    type="email"
                    name="email"
                    id="email"
                    class="form-control"
                    placeholder="Masukkan email Anda"
                    autocomplete="username"
                    required
                >
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">
                    Password
                </label>

                <div class="password-group">
                    <input
                        type="password"
                        name="password"
                        id="password"
                        class="form-control"
                        placeholder="Masukkan password Anda"
                        autocomplete="current-password"
                        required
                    >

                    <button
                        type="button"
                        class="toggle-password"
                        id="togglePassword"
                        aria-controls="password"
                        aria-pressed="false"
                    >
                        Lihat password
                    </button>
                </div>
            </div>

            <button type="submit" class="btn btn-login w-100">
                Masuk ke Sistem
            </button>

        </form>

        <footer class="login-footer">
            <span class="footer-name">
                &copy; <?= date('Y'); ?> Nadia Zulfa
            </span>
            <br>
            SIKET - Sistem Informasi Ketertiban Siswa
        </footer>

    </main>

</div>

<script>
    const passwordInput = document.getElementById('password');
    const toggleButton = document.getElementById('togglePassword');

    toggleButton.addEventListener('click', function () {
        const tersembunyi = passwordInput.type === 'password';

        passwordInput.type = tersembunyi ? 'text' : 'password';

        toggleButton.textContent = tersembunyi
            ? 'Sembunyikan'
            : 'Lihat password';

        toggleButton.setAttribute(
            'aria-pressed',
            String(tersembunyi)
        );
    });
</script>

</body>
</html>
