<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Sistem Informasi Produksi</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
            background-color: #f8f9fa;
        }

        .register-container {
            display: flex;
            background-color: #eff5fd;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            width: 800px;
        }

        .form-container {
            padding: 40px;
            width: 100%;
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
        }

        .btn-register {
            background-color: #75BCF7;
            border: none;
            color: white;
            cursor: pointer;
            width: 100%;
        }

        .error-message {
            color: red;
            font-size: 0.9em;
        }

        .form-row {
            margin-bottom: 15px;
        }

        .col-half {
            flex: 1;
            padding: 10px;
        }

        .form-row {
            display: flex;
        }
    </style>
</head>

<body>

    <div class="register-container">
        <div class="form-container">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <h1>Daftar</h1>
            <form method="POST" action="{{ url('register') }}">
                @csrf
                <div class="form-row">
                    <div class="col-half">
                        <div class="form-group">
                            <label for="nama">Nama</label>
                            <input type="text" class="form-control" id="nama" name="nama"
                                value="{{ old('nama') }}" required>
                            @error('nama')
                                <p class="error-message">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="username">Username</label>
                            <input type="text" class="form-control" id="username" name="username"
                                value="{{ old('username') }}" required>
                            @error('username')
                                <p class="error-message">Username sudah ada.</p>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="password">Kata Sandi</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                            @error('password')
                                <p class="error-message">Kata sandi harus minimal 8 karakter.</p>
                            @enderror
                        </div>
                    </div>
                    <div class="col-half">
                        <div class="form-group">
                            <label for="level">Level</label>
                            <select class="form-control" id="level" name="level" required>
                                <option value="" disabled selected>Pilih Level</option>
                                <option value="Owner" {{ old('level') == 'Owner' ? 'selected' : '' }}>Owner</option>
                                <option value="Kepala Produksi"
                                    {{ old('level') == 'Kepala Produksi' ? 'selected' : '' }}>Kepala Produksi</option>
                                <option value="Customer Service"
                                    {{ old('level') == 'Customer Service' ? 'selected' : '' }}>Customer Service
                                </option>
                            </select>
                            @error('level')
                                <p class="error-message">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="no_hp">Nomor Telepon</label>
                            <input type="text" class="form-control" id="no_hp" name="no_hp"
                                value="{{ old('no_hp') }}">
                            @error('no_hp')
                                <p class="error-message">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="password_confirmation">Konfirmasi Kata Sandi</label>
                             <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                             @error('password_confirmation')
                             <p class="error-message">Konfirmasi kata sandi tidak sesuai.</p>
                            @enderror
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-register">Daftar</button>
            </form>
            <p class="text-center mt-3">Sudah punya akun? <a href="{{ url('login') }}">Masuk di sini</a></p>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
</body>

</html>
