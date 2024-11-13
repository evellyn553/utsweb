<?php
session_start();
include 'db.php';

// Proses registrasi mahasiswa
$notification = '';  // Variable to hold notification messages
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = 'mahasiswa';

    // Check if the passwords match
    if ($_POST['password'] !== $_POST['confirm_password']) {
        $notification = "<div id='notification' class='notification bg-red-600 text-white text-center py-3 rounded-lg mb-6'>Password dan Konfirmasi Password tidak cocok.</div>";
    } else {
        $query = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
        $query->bind_param("sss", $username, $password, $role);

        if ($query->execute()) {
            $notification = "<div id='notification' class='notification bg-green-600 text-white text-center py-3 rounded-lg mb-6'>Registrasi berhasil!</div>";
        } else {
            $notification = "<div id='notification' class='notification bg-red-600 text-white text-center py-3 rounded-lg mb-6'>Registrasi gagal. Silakan coba lagi.</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
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
        <h2 class="text-3xl font-semibold text-center text-gray-700">Create an Account</h2>
        <p class="text-center text-gray-500">Isi form dibawah untuk mendaftar</p>

        <!-- Register Form -->
        <form method="POST" action="">
            <!-- Username Field -->
            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-2" for="username">Username</label>
                <input class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" 
                       id="username" name="username" type="text" placeholder="Masukkan Username" required/>
            </div>

            <!-- Email Field -->
            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-2" for="email">Email</label>
                <input class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" 
                       id="email" name="email" type="email" placeholder="Masukkan Email" required/>
            </div>

            <!-- Password Field -->
            <div class="mb-6">
                <label class="block text-gray-700 font-medium mb-2" for="password">Password</label>
                <input class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" 
                       id="password" name="password" type="password" placeholder="Masukkan Password" required/>
            </div>

            <!-- Register Button -->
            <button class="w-full bg-blue-600 text-white py-2 rounded-md hover:bg-blue-700 transition duration-200">
                Register
            </button>
        </form>

        <!-- Login Redirect Link -->
        <p class="text-center text-gray-500">Sudah punya akun? 
            <a href="login.php" class="text-blue-400 font-semibold hover:underline">Login disini</a>
        </p>
    </div>

</body>
</html>
