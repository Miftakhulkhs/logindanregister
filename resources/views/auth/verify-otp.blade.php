<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi OTP</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #eff5fd;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .container {
            background-color: white;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
            border-radius: 8px;
            padding: 30px;
            width: 100%;
            max-width: 400px;
            text-align: center;
        }

        h1 {
            margin-bottom: 20px;
            color: #333;
        }

        input[type="text"] {
            width: 100%;
            padding: 15px;
            border: 2px solid lightgray;
            border-radius: 5px;
            margin-bottom: 20px;
            font-size: 16px;
            box-sizing: border-box;
        }

        button {
            background-color: darkturquoise;
            border: none;
            padding: 15px;
            color: white;
            cursor: pointer;
            border-radius: 5px;
            width: 100%;
            margin-bottom: 10px;
            font-size: 16px;
        }

        .message {
            margin-top: 10px;
            color: red;
        }

        a {
            color: darkturquoise;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class="container">
        @if(session('success'))
            <p>{{ session('success') }}</p>
        @endif

        @if(session('error'))
            <p class="message">{{ session('error') }}</p>
        @endif

        @if(session('info'))
            <p>{{ session('info') }}</p>
        @endif

        <form method="POST" action="{{ route('otp.verify') }}">
            @csrf
            <h1>Verifikasi OTP</h1>
            <input placeholder="xxxxxx" name="otp" type="text" id="otp" required />
            <button type="submit">Verifikasi OTP</button>
        </form>

        <p style="margin-top: 10px;">Belum menerima OTP? <a href="#" onclick="event.preventDefault(); document.getElementById('resend-otp-form').submit();">Minta OTP Lagi</a></p>
        <form id="resend-otp-form" method="POST" action="{{ route('otp.request.new') }}" style="display: none;">
            @csrf
        </form>
    </div>
</body>

</html>
