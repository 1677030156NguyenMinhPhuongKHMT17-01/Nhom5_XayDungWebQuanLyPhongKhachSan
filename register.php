<?php
session_start();

// Kiểm tra nếu user đã đăng nhập thì chuyển hướng về dashboard
if (isset($_SESSION['user_id']) && isset($_SESSION['username'])) {
    header('Location: ./views/dashboard.php');
    exit();
}

$pageTitle = 'Đăng ký - Hotel Management System';
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
</head>

<body>

    <main>
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

                            <div class="card mb-3">

                                <div class="card-body">

                                    <div class="pt-4 pb-2">
                                        <h5 class="card-title text-center pb-0 fs-4">Tạo tài khoản</h5>
                                        <p class="text-center small">Nhập thông tin cá nhân để tạo tài khoản</p>
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

                                    <form action="./handle/register_process.php" method="POST" class="row g-3 needs-validation" novalidate>

                                        <div class="col-12">
                                            <label for="yourName" class="form-label">Họ và tên</label>
                                            <input type="text" name="fullname" class="form-control" id="yourName" placeholder="Nhập họ và tên" required>
                                            <div class="invalid-feedback">Vui lòng nhập họ và tên!</div>
                                        </div>

                                        <div class="col-12">
                                            <label for="yourEmail" class="form-label">Email</label>
                                            <input type="email" name="email" class="form-control" id="yourEmail" placeholder="Nhập địa chỉ email" required>
                                            <div class="invalid-feedback">Vui lòng nhập địa chỉ email hợp lệ!</div>
                                        </div>

                                        <div class="col-12">
                                            <label for="yourUsername" class="form-label">Username</label>
                                            <div class="input-group has-validation">
                                                <span class="input-group-text" id="inputGroupPrepend"><i class="bi bi-person"></i></span>
                                                <input type="text" name="username" class="form-control" id="yourUsername" placeholder="Chọn username" required>
                                                <div class="invalid-feedback">Vui lòng chọn username.</div>
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <label for="yourPassword" class="form-label">Mật khẩu</label>
                                            <div class="input-group has-validation">
                                                <span class="input-group-text" id="inputGroupPrepend"><i class="bi bi-lock"></i></span>
                                                <input type="password" name="password" class="form-control" id="yourPassword" placeholder="Nhập mật khẩu" required>
                                                <div class="invalid-feedback">Vui lòng nhập mật khẩu!</div>
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <label for="confirmPassword" class="form-label">Xác nhận mật khẩu</label>
                                            <div class="input-group has-validation">
                                                <span class="input-group-text" id="inputGroupPrepend"><i class="bi bi-lock-fill"></i></span>
                                                <input type="password" name="confirm_password" class="form-control" id="confirmPassword" placeholder="Nhập lại mật khẩu" required>
                                                <div class="invalid-feedback">Vui lòng xác nhận mật khẩu!</div>
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div class="form-check">
                                                    <input class="form-check-input" name="terms" type="checkbox" value="" id="acceptTerms" required>
                                                    <label class="form-check-label" for="acceptTerms">
                                                        Tôi đồng ý với <a href="#">điều khoản và điều kiện</a>
                                                    </label>
                                                    <div class="invalid-feedback">Bạn phải đồng ý trước khi gửi.</div>
                                                </div>
                                                <button type="button" class="btn btn-link p-0" id="theme-toggle" title="Chuyển đổi giao diện">
                                                    <i class="fas fa-moon"></i>
                                                </button>
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <button class="btn btn-primary w-100" type="submit" name="register">Tạo tài khoản</button>
                                        </div>

                                        <div class="col-12">
                                            <p class="small mb-0">Đã có tài khoản? <a href="index.php">Đăng nhập</a></p>
                                        </div>

                                    </form>

                                </div>
                            </div>

                            <div class="credits">
                                Designed by <strong>BTL Team - FITDNU</strong>
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
        // Custom toggle for register page
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