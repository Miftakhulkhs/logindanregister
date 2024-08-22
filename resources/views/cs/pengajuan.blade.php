<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SPK Pengajuan</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .sidebar {
            background-color: #007bff;
            color: white;
            padding: 20px;
            width: 250px;
        }

        .sidebar h2 {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }

        .sidebar a {
            color: white;
            text-decoration: none;
            display: block;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .sidebar a:hover {
            background-color: rgba(255, 255, 255, 0.2);
        }

        .active {
            background-color: #dc3545;
        }

        .main-content {
            padding: 10px;
            flex-grow: 1;
        }

        .form-section {
            background-color: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .btn-custom {
            background-color: rgb(18, 175, 240);
            color: white;
        }

        .table-section {
            margin-top: 20px;
        }

        .table th,
        .table td {
            vertical-align: middle;
        }

        .logo {
            height: 40px;
            margin-right: 10px;
        }

        .size-input {
            padding: 0;
            margin-bottom: 20px;
        }

        .button-group {
            display: flex;
            justify-content: flex-end;
            margin-top: 20px;
        }

        .section-title {
            margin-top: 10px;
        }
    </style>
</head>

<body>
    <div class="d-flex">
        <div class="sidebar">
            <h2><img src="{{ asset('images/indokonveksilogo.png') }}" alt="Logo" class="logo"> CS</h2>
            <a href="#" class="menu-item" onclick="setActive(this)">Pengajuan SPK</a>
            <a href="#" class="menu-item" onclick="setActive(this)">Jadwal SPK</a>
            <a href="#" class="menu-item" onclick="confirmLogout(event)">Logout</a>
        </div>

        <div class="main-content">
            @if(session('success'))
                 <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif
            <div class="form-section">
                <h3 class="mb-4 section-title">Pengajuan SPK</h3>
                <form action="{{ route('spk.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="kodeProduksi">Kode Produksi (Auto-generated)</label>
                                <input type="text" class="form-control" id="kodeProduksi" name="kode_produksi"
                                    value="{{ $kodeProduksi }}" readonly>
                            </div>
                            <div class="form-group">
                                <label for="katalogProduksi">Katalog Produksi</label>
                                <input type="text" class="form-control" id="katalogProduksi"
                                    placeholder="Masukkan Katalog Produksi">
                            </div>
                            <div class="form-group">
                                <label for="material">Material</label>
                                <input type="text" class="form-control" id="material"
                                    placeholder="Masukkan Material">
                            </div>
                            <div class="form-group">
                                <label for="warna">Warna</label>
                                <input type="text" class="form-control" id="warna" placeholder="Masukkan Warna">
                            </div>
                            <div class="form-group">
                                <label for="jumlah">Jumlah</label>
                                <input type="number" class="form-control" id="jumlah" placeholder="Masukkan Jumlah">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="tanggalMasuk">Tanggal Masuk</label>
                                <input type="date" class="form-control" id="tanggalMasuk">
                            </div>
                            <div class="form-group">
                                <label for="deadline">Deadline</label>
                                <input type="date" class="form-control" id="deadline">
                            </div>
                            <div class="form-group">
                                <label for="detail">Detail</label>
                                <input type="text" class="form-control" id="detail" placeholder="Masukkan Detail">
                            </div>
                            <div class="form-group">
                                <label for="desain">Desain (Upload PDF/Image)</label>
                                <input type="file" class="form-control" id="desain">
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="vendor">ID Vendor</label>
                                        <select class="form-control" id="vendor">
                                            <option value="">Pilih Vendor</option>
                                            <option value="Vendor A">Vendor A</option>
                                            <option value="Vendor B">Vendor B</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                         <label for="idPengguna">ID Pengguna</label>
                                            <input type="text" class="form-control" id="idPengguna" name="id_pengguna"
                                             value="{{ $id_pengguna }}" readonly> <!-- Display ID pengguna yang benar -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Jumlah Setiap Ukuran</label>
                        <div class="row">
                            <div class="col">
                                <label for="sizeS">S</label>
                                <input type="number" class="form-control" id="sizeS" placeholder="0">
                            </div>
                            <div class="col">
                                <label for="sizeM">M</label>
                                <input type="number" class="form-control" id="sizeM" placeholder="0">
                            </div>
                            <div class="col">
                                <label for="sizeL">L</label>
                                <input type="number" class="form-control" id="sizeL" placeholder="0">
                            </div>
                            <div class="col">
                                <label for="sizeXL">XL</label>
                                <input type="number" class="form-control" id="sizeXL" placeholder="0">
                            </div>
                            <div class="col">
                                <label for="sizeXXL">XXL</label>
                                <input type="number" class="form-control" id="sizeXXL" placeholder="0">
                            </div>
                        </div>
                    </div>
                    <div class="button-group">
                        <button class="btn btn-success mr-2">Ajukan</button>
                        <button class="btn btn-primary mr-2">Update</button>
                        <button class="btn btn-danger">Batal</button>
                    </div>
            </div>

            <div class="table-section">
                <h3>Data SPK</h3>
                <input type="text" class="form-control" placeholder="Search">
                <table class="table table-striped mt-2">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Kode Produksi</th>
                            <th>Jumlah</th>
                            <th>Tanggal Masuk</th>
                            <th>Deadline</th>
                            <th>Vendor</th>
                            <th>Status SPK</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Table starts empty -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        // Simulated user ID for demonstration purposes
        document.addEventListener("DOMContentLoaded", function() {
            // Set the ID Pengguna field with the logged-in user's ID
            document.getElementById("idPengguna").value = userId;
        });

        function setActive(element) {
            const menuItems = document.querySelectorAll('.menu-item');
            menuItems.forEach(item => item.classList.remove('active'));
            element.classList.add('active');
        }

        function confirmLogout(event) {
            event.preventDefault(); // Prevent the default link behavior
            if (confirm("Apakah Anda yakin ingin keluar?")) {
                window.location.href = "/login"; // Redirect to the login page
            }
        }
    </script>
</body>

</html>
