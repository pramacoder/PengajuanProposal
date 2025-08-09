<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Get Started - Pilih Role</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="bg-white p-8 rounded shadow-md w-full max-w-md">
        <h1 class="text-2xl font-bold mb-6 text-center">Pilih Jenis Pengguna</h1>
        <form id="roleForm" method="GET" action="{{ route('login') }}">
            <div class="mb-4">
                <select name="user_type" id="user_type" class="w-full border rounded px-3 py-2" required>
                    <option value="" disabled selected>Pilih Role</option>
                    <option value="mahasiswa">Mahasiswa</option>
                    <option value="dosen">Dosen</option>
                    <option value="reviewer">Reviewer</option>
                    <option value="operator">Operator</option>
                </select>
            </div>
            <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700">Lanjutkan</button>
        </form>
    </div>
    <script>
        document.getElementById('roleForm').addEventListener('submit', function(e) {
            var userType = document.getElementById('user_type').value;
            if (!userType) {
                e.preventDefault();
                alert('Silakan pilih role terlebih dahulu.');
            }
        });
    </script>
</body>
</html>