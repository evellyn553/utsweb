<?php
session_start();
include 'db.php'; // Memasukkan koneksi database

// Cek apakah pengguna adalah dosen
if ($_SESSION['role'] !== 'dosen') {
    header("Location: login.php");
    exit();
}

// Memperbarui nilai mahasiswa
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['grades'])) {
        foreach ($_POST['grades'] as $grade_id => $new_grade) {
            // Validasi dan sanitasi input
            $new_grade = floatval($new_grade);
            if ($new_grade < 0 || $new_grade > 100) {
                continue; // Skip jika nilai tidak valid
            }

            // Update nilai di database
            $stmt = $conn->prepare("UPDATE grades SET grade = ? WHERE id = ?");
            $stmt->bind_param("di", $new_grade, $grade_id);
            if (!$stmt->execute()) {
                echo "Error updating record: " . $stmt->error;
            }
        }
    }
    // Setelah update, kembali ke halaman dosen
    header("Location: dosen_dashboard.php");
    exit();
} else {
    echo "Tidak ada data yang dikirim untuk diperbarui.";
}
?>
