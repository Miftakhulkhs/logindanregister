<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Informasi Produksi</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
            background-color: #f8f9fa;
        }

        .login-container {
            display: flex;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            width: 800px;
            /* Adjust width as needed */
        }

        .form-container {
            padding: 40px;
            width: 50%;
            background-color: #eff5fd;
            /* Light blue background for form */
        }

        .logo-container {
            width: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #75BCF7;
            /* Adjust background color as needed */
        }

        .logo {
            width: 80%;
            /* Adjust as needed */
        }

        h2 {
            font-weight: bold;
            margin-bottom: 20px;
        }

        .btn-primary {
            background-color: #75BCF7;
            /* Button color */
            border: none;
        }

        .input-group-text {
            cursor: pointer;
        }
    </style>
</head>

<body>
    <div class="login-container">
        <div class="form-container">
            @if(session('error'))
                <div class="alert alert-warning">
                    {{ session('error') }}
                </div>
            @endif

            <h2 class="text-center">Sistem Informasi Produksi</h2>
            <p class="text-center">Selamat Datang! <br> Silakan masuk akun anda</p>

            @if (session('status'))
                <div class="alert alert-info">{{ session('status') }}</div>
            @endif

            @if (session('info'))
                <div class="alert alert-warning">{{ session('info') }}</div>
            @endif

            <form method="POST" action="{{ url('login') }}">
                @csrf
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" class="form-control" id="username" name="username"
                        value="{{ old('username') }}" required>
                    @error('username')
                        <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-group">
                        <input type="password" class="form-control" id="password" name="password" required>
                        <div class="input-group-append">
                            <span class="input-group-text" onclick="togglePassword()">
                                <i id="toggleIcon" class="fas fa-eye"></i>
                            </span>
                        </div>
                    </div>
                    @error('password')
                        <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                </div>
                <button type="submit" class="btn btn-primary btn-block">Login</button>
            </form>
            <p class="text-center">Belum punya akun? <a href="{{ url('register') }}">Daftar di sini</a></p>
        </div>
        <div class="logo-container">
            <img src="{{ asset('images/logo.png') }}" alt="Login Illustration" class="logo">
        </div>
    </div>

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }
    </script>
</body>

</html>
