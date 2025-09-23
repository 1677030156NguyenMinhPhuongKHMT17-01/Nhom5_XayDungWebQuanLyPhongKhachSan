<?php
session_start();

// Kiểm tra nếu user đã đăng nhập thì chuyển hướng về dashboard
if (isset($_SESSION['user_id']) && isset($_SESSION['username'])) {
    header('Location: ./views/dashboard.php');
    exit();
}

$pageTitle = 'Đăng nhập - Hotel Management System';
$baseUrl = './';
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title><?= $pageTitle ?></title>
    <meta content="" name="description">
    <meta content="" name="keywords">

    <!-- Favicons -->
    <link href="./images/fitdnu_logo.png" rel="icon">
    <link href="./images/fitdnu_logo.png" rel="apple-touch-icon">

    <!-- Google Fonts -->
    <link href="https://fonts.gstatic.com" rel="preconnect">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

    <!-- Vendor CSS Files -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <!-- Template Main CSS File -->
    <link href="./css/admin-style.css" rel="stylesheet">
    <!-- Dark Mode CSS -->
    <link href="./css/dark-mode.css" rel="stylesheet">
    
    <style>
        .login-container {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            position: relative;
        }
        
        .login-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('./images/draw2.webp') center/cover;
            opacity: 0.1;
            z-index: 1;
        }
        
        .login-wrapper {
            position: relative;
            z-index: 2;
        }
        
        .login-card {
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.95);
            border: none;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        }
        
        [data-theme="dark"] .login-container {
            background: linear-gradient(135deg, #2d3748 0%, #1a202c 100%);
        }
        
        [data-theme="dark"] .login-card {
            background: rgba(45, 55, 72, 0.95);
            color: #e2e8f0;
        }
        
        .theme-toggle-btn {
            position: absolute;
            top: 20px;
            right: 20px;
            z-index: 10;
            background: rgba(255, 255, 255, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: white;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }
        
        .theme-toggle-btn:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: scale(1.1);
        }
    </style>
</head>

<body class="login-container">

    <!-- Theme Toggle Button -->
    <button type="button" class="theme-toggle-btn" id="theme-toggle" title="Chuyển đổi giao diện">
        <i class="fas fa-moon"></i>
    </button>

    <main class="login-wrapper">
        <div class="container">

            <section class="section register min-vh-100 d-flex flex-column align-items-center justify-content-center py-4">
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-lg-4 col-md-6 d-flex flex-column align-items-center justify-content-center">

                            <div class="d-flex justify-content-center py-4">
                                <a href="index.php" class="logo d-flex align-items-center w-auto">
                                    <img src="./images/fitdnu_logo.png" alt="">
                                    <span class="d-none d-lg-block">FITDNU Hotel</span>
                                </a>
                            </div><!-- End Logo -->

                            <div class="card mb-3 login-card">

                                <div class="card-body p-4">

                                    <div class="pt-2 pb-4 text-center">
                                        <h5 class="card-title text-center pb-0 fs-4 fw-bold">Đăng nhập hệ thống</h5>
                                        <p class="text-center small text-muted">Nhập username và mật khẩu để đăng nhập vào hệ thống quản lý khách sạn</p>
                                    </div>

                                    <!-- Thông báo lỗi sử dụng session -->
                                    <?php if (isset($_SESSION['error'])): ?>
                                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                            <?php 
                                            echo $_SESSION['error']; 
                                            unset($_SESSION['error']);
                                            ?>
                                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <?php if (isset($_SESSION['success'])): ?>
                                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                                            <?php 
                                            echo $_SESSION['success']; 
                                            unset($_SESSION['success']);
                                            ?>
                                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                        </div>
                                    <?php endif; ?>

                                    <form action="./handle/login_process.php" method="POST" class="row g-3 needs-validation" novalidate>

                                        <div class="col-12">
                                            <label for="yourUsername" class="form-label">Username</label>
                                            <div class="input-group has-validation">
                                                <span class="input-group-text" id="inputGroupPrepend"><i class="bi bi-person"></i></span>
                                                <input type="text" name="username" class="form-control" id="yourUsername" placeholder="Nhập username" required>
                                                <div class="invalid-feedback">Vui lòng nhập username.</div>
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <label for="yourPassword" class="form-label">Password</label>
                                            <div class="input-group has-validation">
                                                <span class="input-group-text" id="inputGroupPrepend"><i class="bi bi-lock"></i></span>
                                                <input type="password" name="password" class="form-control" id="yourPassword" placeholder="Nhập mật khẩu" required>
                                                <div class="invalid-feedback">Vui lòng nhập mật khẩu!</div>
                                            </div>
                                        </div>

                                        <div class="col-12 mb-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="remember" value="true" id="rememberMe">
                                                <label class="form-check-label" for="rememberMe">Ghi nhớ đăng nhập</label>
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <button class="btn btn-primary w-100 py-2" type="submit" name="login">
                                                <i class="bi bi-box-arrow-in-right me-2"></i>Đăng nhập
                                            </button>
                                        </div>

                                        <div class="col-12 text-center">
                                            <p class="small mb-0 mt-3">Chưa có tài khoản? <a href="register.php" class="text-decoration-none">Đăng ký ngay</a></p>
                                        </div>

                                    </form>

                                </div>
                            </div>

                            <div class="credits text-center">
                                <small class="text-muted">Designed by <strong>BTL Team - FITDNU</strong></small>
                            </div>

                        </div>
                    </div>
                </div>

            </section>

        </div>
    </main><!-- End #main -->

    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Dark Mode JS -->
    <script src="./js/dark-mode.js"></script>
    <!-- Admin Main JS -->
    <script src="./js/admin-main.js"></script>

    <script>
        // Custom toggle for login page
        document.addEventListener('DOMContentLoaded', function() {
            const themeToggle = document.getElementById('theme-toggle');
            if (themeToggle && window.themeManager) {
                // Khởi tạo icon đúng ngay từ đầu
                const currentTheme = localStorage.getItem('btl-theme') || 'light';
                const icon = themeToggle.querySelector('i');
                if (currentTheme === 'dark') {
                    icon.className = 'fas fa-sun';
                } else {
                    icon.className = 'fas fa-moon';
                }
                
                themeToggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    window.themeManager.toggleTheme();
                });
                
                // Update icon based on current theme
                window.addEventListener('themeChanged', function(e) {
                    const icon = themeToggle.querySelector('i');
                    if (e.detail.theme === 'dark') {
                        icon.className = 'fas fa-sun';
                    } else {
                        icon.className = 'fas fa-moon';
                    }
                });
    <script>
        // Custom toggle for login page
        document.addEventListener('DOMContentLoaded', function() {
            const themeToggle = document.getElementById('theme-toggle');
            if (themeToggle && window.themeManager) {
                // Khởi tạo icon đúng ngay từ đầu
                const currentTheme = localStorage.getItem('btl-theme') || 'light';
                const icon = themeToggle.querySelector('i');
                if (currentTheme === 'dark') {
                    icon.className = 'fas fa-sun';
                } else {
                    icon.className = 'fas fa-moon';
                }
                
                themeToggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    window.themeManager.toggleTheme();
                });
                
                // Update icon based on current theme
                window.addEventListener('themeChanged', function(e) {
                    const icon = themeToggle.querySelector('i');
                    if (e.detail.theme === 'dark') {
                        icon.className = 'fas fa-sun';
                    } else {
                        icon.className = 'fas fa-moon';
                    }
                });
            }
        });
    </script>

</body>

</html>