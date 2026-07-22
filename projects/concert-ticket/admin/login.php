<?php
// Memulai session
session_start();

// Memuat file fungsi helper dari subfolder
require_once '../includes/functions.php';

// Jika admin sudah login, langsung arahkan ke dashboard admin
if (is_admin_logged_in()) {
    header("Location: index.php");
    exit();
}

$username = '';
$error = '';

// Proses form login jika dikirim
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (empty($username) || empty($password)) {
        $error = "Username dan Password wajib diisi.";
    } else {
        // Melakukan login
        if (login_admin($pdo, $username, $password)) {
            $_SESSION['success'] = "Selamat datang kembali, <strong>" . htmlspecialchars($username) . "</strong>!";
            header("Location: index.php");
            exit();
        } else {
            $error = "Username atau Password salah.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - AidFest</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts Outfit -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 100%);
            color: #f8fafc;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        body::before {
            content: '';
            position: absolute;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(99, 102, 241, 0.15) 0%, transparent 70%);
            top: -100px;
            left: -100px;
            pointer-events: none;
        }

        body::after {
            content: '';
            position: absolute;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(6, 182, 212, 0.15) 0%, transparent 70%);
            bottom: -100px;
            right: -100px;
            pointer-events: none;
        }

        .login-card {
            background: rgba(30, 41, 59, 0.7);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
            width: 100%;
            max-width: 420px;
            overflow: hidden;
        }

        .login-header {
            background: linear-gradient(135deg, #4f46e5 0%, #06b6d4 100%);
            padding: 2.5rem 2rem 2rem 2rem;
            text-align: center;
            position: relative;
        }

        .login-brand {
            font-weight: 800;
            font-size: 1.8rem;
            letter-spacing: -0.5px;
            color: white;
            text-shadow: 0 2px 4px rgba(0,0,0,0.15);
        }

        .login-body {
            padding: 2.5rem 2rem;
        }

        .form-control-dark {
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: white;
            border-radius: 12px;
            padding: 0.75rem 1rem;
            transition: all 0.2s ease;
        }

        .form-control-dark:focus {
            background: rgba(15, 23, 42, 0.8);
            border-color: #6366f1;
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.25);
            color: white;
        }

        .btn-login {
            background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
            border: none;
            color: white;
            font-weight: 600;
            border-radius: 12px;
            padding: 0.75rem;
            transition: all 0.3s ease;
            box-shadow: 0 4px 14px rgba(99, 102, 241, 0.4);
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(99, 102, 241, 0.5);
            background: linear-gradient(135deg, #4f46e5 0%, #4338ca 100%);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .alert-login {
            background: rgba(239, 68, 68, 0.15);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #fca5a5;
            border-radius: 12px;
            padding: 0.75rem 1rem;
        }
    </style>
</head>
<body>

    <div class="login-card">
        <!-- Header -->
        <div class="login-header">
            <div class="login-brand d-flex align-items-center justify-content-center mb-1">
                <i class="fa-solid fa-compact-disc fa-spin text-white me-2" style="animation-duration: 4s;"></i>
                <span>AidFest Admin</span>
            </div>
            <p class="mb-0 text-white-50 fs-6">Masukkan kredensial Anda untuk masuk ke dashboard.</p>
        </div>

        <!-- Body -->
        <div class="login-body">
            <!-- Alert Session Error -->
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-login d-flex align-items-center mb-4" role="alert">
                    <i class="fa-solid fa-circle-exclamation fs-5 me-2"></i>
                    <div><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
                </div>
            <?php endif; ?>

            <!-- Alert Form Error -->
            <?php if ($error): ?>
                <div class="alert alert-login d-flex align-items-center mb-4" role="alert">
                    <i class="fa-solid fa-circle-exclamation fs-5 me-2"></i>
                    <div><?php echo $error; ?></div>
                </div>
            <?php endif; ?>

            <form action="login.php" method="POST">
                <!-- Username Input -->
                <div class="mb-3">
                    <label for="username" class="form-label fw-medium text-white-700">Username</label>
                    <div class="input-group">
                        <span class="input-group-text bg-transparent border-0 text-white-50"><i class="fa-solid fa-user"></i></span>
                        <input type="text" 
                               name="username" 
                               id="username" 
                               class="form-control form-control-dark" 
                               placeholder="admin" 
                               value="<?php echo htmlspecialchars($username); ?>" 
                               required 
                               autocomplete="username">
                    </div>
                </div>

                <!-- Password Input -->
                <div class="mb-4">
                    <label for="password" class="form-label fw-medium text-white-700">Password</label>
                    <div class="input-group">
                        <span class="input-group-text bg-transparent border-0 text-white-50"><i class="fa-solid fa-key"></i></span>
                        <input type="password" 
                               name="password" 
                               id="password" 
                               class="form-control form-control-dark" 
                               placeholder="••••••••" 
                               required 
                               autocomplete="current-password">
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn btn-login w-100 mb-3">
                    <i class="fa-solid fa-right-to-bracket me-2"></i>Masuk Ke Dashboard
                </button>
                
                <div class="text-center">
                    <a href="../index.php" class="text-decoration-none text-white-50 fs-7 hover-white"><i class="fa-solid fa-arrow-left me-1"></i>Kembali ke Portal Pembeli</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
