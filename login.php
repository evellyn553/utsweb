<?php
session_start();
include 'db.php'; // Memasukkan koneksi database

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Cek username dan password di database
    $result = $conn->query("SELECT * FROM users WHERE username='$username'");
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            // Set sesi
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            // Arahkan ke dashboard berdasarkan role
            if ($user['role'] === 'admin') {
                header("Location: admin_dashboard.php");
            } elseif ($user['role'] === 'dosen') {
                header("Location: dosen_dashboard.php");
            } elseif ($user['role'] === 'mahasiswa') {
                header("Location: mahasiswa_dashboard.php");
            }
            exit();
        } else {
            echo "Login gagal. Cek username dan password.";
        }
    } else {
        echo "Login gagal. Cek username dan password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet"/>
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">

    <div class="bg-white rounded-xl shadow-lg w-full max-w-md p-8 space-y-6">
        <h2 class="text-3xl font-semibold text-center text-gray-700">Login</h2>
        <p class="text-center text-gray-500">Masukkan Username dan Password Anda</p>

        <!-- Login Form -->
        <form method="POST" action="">
            <!-- Username Field -->
            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-2" for="username">Username</label>
                <input class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" 
                       id="username" name="username" type="text" placeholder="Masukkan Username" required/>
            </div>

            <!-- Password Field -->
            <div class="mb-6">
                <label class="block text-gray-700 font-medium mb-2" for="password">Password</label>
                <input class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" 
                       id="password" name="password" type="password" placeholder="Masukkan Password" required/>
            </div>

            <!-- Login Button -->
            <button class="w-full bg-blue-600 text-white py-2 rounded-md hover:bg-blue-700 transition duration-200">
                Login
            </button>
        </form>

        <!-- Register Redirect Link -->
        <p class="text-center text-gray-500">Belum punya akun? 
            <a href="register.php" class="text-blue-400 font-semibold hover:underline">Daftar disini</a>
        </p>
    </div>

</body>
</html>

