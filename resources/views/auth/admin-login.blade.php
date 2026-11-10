<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin & Owner | Bucket Imy</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f5f5f5;
            color: #111;
        }

        .page-title {
            padding: 5px 10px 12px;
            color: #aaa;
            font-size: 16px;
        }

        .login-background {
            min-height: calc(100vh - 37px);
            background:
                radial-gradient(circle at 15% 15%, #ffe5e9 0, transparent 35%),
                linear-gradient(135deg, #fff0f2, #fff0f3);
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 35px 16px;
        }

        .login-card {
            width: 100%;
            max-width: 464px;
            background: #fff;
            border-radius: 14px;
            padding: 38px 40px 32px;
            box-shadow: 0 3px 3px rgba(0, 0, 0, .25);
        }

        .brand {
            text-align: center;
            color: #984653;
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 4px;
        }

        h1 {
            text-align: center;
            font-family: Georgia, serif;
            font-size: 40px;
            font-weight: normal;
            margin-bottom: 5px;
        }

        .subtitle {
            text-align: center;
            color: #666;
            font-size: 14px;
            margin-bottom: 15px;
        }

        .role-options {
            display: flex;
            gap: 12px;
            margin-bottom: 24px;
        }

        .role-button {
            flex: 1;
            height: 49px;
            border: 1px solid #984653;
            border-radius: 6px;
            background: white;
            color: #984653;
            font-size: 15px;
            cursor: pointer;
            transition: .2s;
        }

        .role-button.active {
            background: #984653;
            color: white;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            font-size: 15px;
            margin-bottom: 5px;
        }

        .input-wrapper {
            position: relative;
        }

        input {
            width: 100%;
            height: 42px;
            padding: 0 13px;
            border: 1px solid #efd0d4;
            background: #fff9fa;
            outline: none;
            font-size: 13px;
        }

        input:focus {
            border-color: #984653;
        }

        .password-input {
            padding-right: 45px;
        }

        .toggle-password {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            border: none;
            background: transparent;
            color: #888;
            cursor: pointer;
            font-size: 18px;
        }

        .error-message {
            color: #c62828;
            font-size: 13px;
            margin-top: 5px;
        }

        .status-message {
            color: #287a45;
            font-size: 13px;
            margin-bottom: 12px;
        }

        .submit-button {
            width: 100%;
            height: 39px;
            margin-top: 9px;
            background: #984653;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 14px;
            cursor: pointer;
        }

        .submit-button:hover {
            background: #803946;
        }

        .divider-text {
            display: flex;
            align-items: center;
            gap: 5px;
            margin-top: 22px;
            color: #666;
            font-size: 10px;
            white-space: nowrap;
        }

        .divider-text::before,
        .divider-text::after {
            content: "";
            height: 1px;
            background: #777;
            flex: 1;
        }

        @media (max-width: 480px) {
            .login-card {
                padding: 30px 23px;
            }

            h1 {
                font-size: 34px;
            }

            .role-options {
                gap: 9px;
            }
        }
    </style>
</head>
<body>

    <main class="login-background">
        <section class="login-card">
            <div class="brand">Login bucket.imy</div>

            <h1>Selamat Datang</h1>
            <p class="subtitle">Pilih Akses untuk masuk ke dashboard</p>

            <div class="role-options">
                <button type="button" class="role-button active"
                    data-role="admin">Admin</button>

                <button type="button" class="role-button"
                    data-role="owner">Owner</button>
            </div>

            @if (session('status'))
                <div class="status-message">{{ session('status') }}</div>
            @endif

            @if ($errors->any())
                <div class="error-message">
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('admin.login.process') }}">
                @csrf

                <input type="hidden" name="role" id="selected-role"
                    value="admin">

                <div class="form-group">
                    <label for="username">Username</label>
                    <div class="input-wrapper">
                        <input
                            type="text"
                            id="username"
                            name="username"
                            placeholder="Username"
                            value="{{ old('username') }}"
                            autocomplete="username"
                            required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-wrapper">
                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="password-input"
                            placeholder="Password"
                            autocomplete="current-password"
                            required>

                        <button type="button"
                            class="toggle-password"
                            id="toggle-password"
                            aria-label="Tampilkan password">
                            ◉
                        </button>
                    </div>
                </div>

                <button type="submit" class="submit-button">
                    Masuk Dashboard
                </button>
            </form>

            <div class="divider-text">
                Hanya untuk Admin dan Owner bucket.imy
            </div>
        </section>
    </main>

    <script>
        const roleButtons = document.querySelectorAll('.role-button');
        const selectedRole = document.getElementById('selected-role');

        roleButtons.forEach(button => {
            button.addEventListener('click', function () {
                roleButtons.forEach(item => item.classList.remove('active'));

                this.classList.add('active');
                selectedRole.value = this.dataset.role;
            });
        });

        const passwordInput = document.getElementById('password');
        const togglePassword = document.getElementById('toggle-password');

        togglePassword.addEventListener('click', function () {
            const isHidden = passwordInput.type === 'password';

            passwordInput.type = isHidden ? 'text' : 'password';
            this.textContent = isHidden ? '◎' : '◉';
            this.setAttribute(
                'aria-label',
                isHidden ? 'Sembunyikan password' : 'Tampilkan password'
            );
        });
    </script>

</body>
</html>