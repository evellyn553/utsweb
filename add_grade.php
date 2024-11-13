<?php
session_start();
include 'db.php'; // Memasukkan koneksi database

// Cek apakah pengguna adalah dosen
if ($_SESSION['role'] !== 'dosen') {
    header("Location: login.php");
    exit();
}

// Menambahkan nilai baru
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mahasiswa_id = $_POST['mahasiswa_id'];
    $course_name = $_POST['course_name'];
    $grade = $_POST['grade'];

    // Pastikan nilai valid (misalnya antara 0 dan 100)
    if ($grade >= 0 && $grade <= 100) {
        // Query untuk menyimpan nilai
        $stmt = $conn->prepare("INSERT INTO grades (mahasiswa_id, course_name, grade) VALUES (?, ?, ?)");
        $stmt->bind_param("isd", $mahasiswa_id, $course_name, $grade);

        if ($stmt->execute()) {
            header("Location: dosen_dashboard.php");
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }
    } else {
        echo "Nilai tidak valid!";
    }
}
?>
