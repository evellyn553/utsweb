<?php
include 'auth.php'; 
checkLogin(); 

// Pastikan role adalah admin
if ($_SESSION['role'] !== 'admin') {
    echo "Akses ditolak!";
    exit();
}

// Koneksi ke database
$dsn = 'mysql:host=localhost;dbname=backend';
$username = 'root';
$password = '';

try {
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Could not connect to the database: " . $e->getMessage());
}

// Cek apakah ada pesan dari query string
$message = '';
if (isset($_GET['message'])) {
    $message = $_GET['message'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Proses untuk menambah data
    if (isset($_POST['add_user'])) {
        $username = $_POST['username'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $role = $_POST['role'];

        // Menyimpan data pengguna ke database
        $stmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
        $stmt->execute([$username, $password, $role]);

        // Redirect dengan pesan
        header("Location: admin_dashboard.php?message=User  berhasil ditambahkan!");
        exit();
    }

    // Proses untuk menghapus data berdasarkan username
    if (isset($_POST['delete_user'])) {
        $username_to_delete = $_POST['username'];
    
        // Validasi bahwa username tidak kosong
        if (empty($username_to_delete)) {
            header("Location: admin_dashboard.php?message=Username tidak boleh kosong!");
            exit();
        }
    
        // Ambil ID pengguna berdasarkan username
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username_to_delete]);
        $user = $stmt->fetch();
    
        if ($user) {
            // Hapus data terkait di tabel grades
            $stmt = $pdo->prepare("DELETE FROM grades WHERE mahasiswa_id = ?");
            $stmt->execute([$user['id']]);
    
            // Hapus pengguna dari tabel users
            $stmt = $pdo->prepare("DELETE FROM users WHERE username = ?");
            $stmt->execute([$username_to_delete]);
    
            // Reset auto increment ID di tabel users
            $stmt = $pdo->prepare("SET @count = 0; UPDATE users SET id = @count := (@count + 1); ALTER TABLE users AUTO_INCREMENT = 1;");
            $stmt->execute();
    
            // Redirect dengan pesan
            header("Location: admin_dashboard.php?message=User   berhasil dihapus dan ID direset!");
            exit();
        } else {
            header("Location: admin_dashboard.php?message=User   tidak ditemukan!");
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f3f4f6;
            color: #333;
        }
        .navbar {
            background-color: #343a40;
        }
        .navbar-brand, .navbar a {
            color: #fff !important;
        }
        .container {
            margin-top: 60px;
        }
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: all 0.3s;
        }
        .card:hover {
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
        }
        .btn-primary {
            background-color: #4CAF50;
            border: none;
        }
        .btn-danger {
            background-color: #E57373;
            border: none;
        }
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.7);
        }
        .modal-content {
            background-color: #fff;
            border-radius: 8px;
            padding: 30px;
            width: 90%;
            max-width: 500px;
            margin: 10% auto;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            text-align: center;
        }
        .close {
            color: #555;
            float: right;
            font-size: 24px;
            cursor: pointer;
        }
        .close:hover {
            color: #000;
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <a class="navbar-brand" href="#">Admin Dashboard</a>
        <div class="ml-auto">
            <a href="logout.php" class="btn btn-outline-light">Logout</a>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container">
        <h2 class="text-center my-5">Welcome to the Admin Dashboard</h2>

        <div class="row">
            <!-- Add User Card -->
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Add User</h5>
                        <form method="POST">
                            <div class="form-group">
                                <input type="text" name="username" placeholder="Username" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <input type="password" name="password" placeholder="Password" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <select name="role" class="form-control" required>
                                    <option value="mahasiswa">Mahasiswa</option>
                                    <option value="dosen">Dosen</option>
                                    <option value="admin">Admin</option>
                                </select>
                            </div>
                            <button type="submit" name="add_user" class="btn btn-primary btn-block">Add User</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Delete User Card -->
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Delete User</h5>
                        <form method="POST">
                            <div class="form-group">
                                <input type="text" name="username" placeholder="Username" class="form-control" required>
                            </div>
                            <button type="submit" name="delete_user" class="btn btn-danger btn-block">Delete User</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div id="messageModal" class="modal">
        <div class="modal-content">
            <span class="close" id="closeModal">&times;</span>
            <p id="modalMessage"><?php echo htmlspecialchars($message); ?></p>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        // Show modal if there's a message
        const modal = document.getElementById("messageModal");
        const closeModal = document.getElementById("closeModal");

        <?php if ($message): ?>
            modal.style.display = "block";
            // Automatically close modal after 3 seconds
            setTimeout(() => {
                modal.style.display = "none";
            }, 3000);
        <?php endif; ?>

        // Close modal
        closeModal.onclick = function() {
            modal.style.display = "none";
        }

        // Close modal if clicked outside of it
        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    </script>
</body>
</html>
