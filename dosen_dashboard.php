<?php
session_start();
include 'db.php'; // Memasukkan koneksi database

// Cek apakah pengguna adalah dosen
if ($_SESSION['role'] !== 'dosen') {
    header("Location: login.php");
    exit();
}

// Ambil data nilai mahasiswa
$grades = $conn->query("SELECT grades.id, grades.course_name, grades.grade, users.username AS mahasiswa_name
                        FROM grades
                        JOIN users ON grades.mahasiswa_id = users.id");

// Cek apakah query berhasil
if (!$grades) {
    die("Query gagal: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dosen Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-blue-50 p-8">
    <div class="container mx-auto">
        <h2 class="text-3xl font-bold mb-8 text-center text-blue-700">Dosen Dashboard</h2>

        <!-- Tautan Logout -->
        <div class="flex justify-end mb-8">
            <a href="logout.php" class="text-blue-600 hover:text-blue-800 font-semibold transition duration-200">Logout</a>
        </div>

        <!-- Daftar Nilai Mahasiswa dengan Formulir untuk Edit -->
        <div class="bg-white p-6 rounded-lg shadow-md mb-8">
            <h3 class="text-xl font-semibold mb-4 text-blue-700">Daftar Nilai Mahasiswa</h3>
            <form method="POST" action="update_grade.php">
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white rounded-lg shadow-lg">
                        <thead class="bg-blue-100">
                            <tr>
                                <th class="py-3 px-6 border-b border-gray-200">ID</th>
                                <th class="py-3 px-6 border-b border-gray-200">Mahasiswa</th>
                                <th class="py-3 px-6 border-b border-gray-200">Mata Kuliah</th>
                                <th class="py-3 px-6 border-b border-gray-200">Nilai</th>
                                <th class="py-3 px-6 border-b border-gray-200">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($grades->num_rows > 0) {
                                while ($row = $grades->fetch_assoc()) {
                            ?>
                                <tr class="hover:bg-blue-50 transition duration-200">
                                    <td class="py-4 px-6 border-b"><?php echo $row['id']; ?></td>
                                    <td class="py-4 px-6 border-b"><?php echo $row['mahasiswa_name']; ?></td>
                                    <td class="py-4 px-6 border-b"><?php echo $row['course_name']; ?></td>
                                    <td class="py-4 px-6 border-b">
                                        <input type="number" step="0.01" name="grades[<?php echo $row['id']; ?>]"
                                               value="<?php echo $row['grade']; ?>"
                                               class="w-full p-2 border border-gray-300 rounded text-center focus:outline-none focus:ring-2 focus:ring-blue-400">
                                    </td>
                                    <td class="py-4 px-6 border-b text-center">
                                        <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded-full hover:bg-green-600 transition duration-200">
                                            Simpan
                                        </button>
                                    </td>
                                </tr>
                            <?php
                                }
                            } else {
                                echo "<tr><td colspan='5' class='py-4 px-6 text-center text-gray-500'>Tidak ada data yang ditemukan.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </form>
        </div>

        <!-- Formulir untuk Menambah Nilai Baru -->
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h3 class="text-xl font-semibold mb-4 text-blue-700">Tambah Nilai Mahasiswa</h3>
            <form method="POST" action="add_grade.php">
                <div class="mb-4">
                    <label for="mahasiswa_id" class="block text-gray-700 font-medium">Mahasiswa</label>
                    <select name="mahasiswa_id" id="mahasiswa_id" class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400">
                        <?php
                        // Ambil daftar mahasiswa
                        $students = $conn->query("SELECT id, username FROM users WHERE role = 'mahasiswa'");
                        while ($student = $students->fetch_assoc()) {
                            echo "<option value='" . $student['id'] . "'>" . $student['username'] . "</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="mb-4">
                    <label for="course_name" class="block text-gray-700 font-medium">Mata Kuliah</label>
                    <input type="text" name="course_name" id="course_name" class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" required>
                </div>
                <div class="mb-4">
                    <label for="grade" class="block text-gray-700 font-medium">Nilai</label>
                    <input type="number" step="0.01" name="grade" id="grade" class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" required>
                </div>
                <button type="submit" class="bg-blue-500 text-white px-6 py-2 rounded-full hover:bg-blue-600 transition duration-200">
                    Tambah Nilai
                </button>
            </form>
        </div>
    </div>
</body>
</html>

