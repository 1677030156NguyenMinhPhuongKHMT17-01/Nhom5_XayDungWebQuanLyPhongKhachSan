<?php
session_start();
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="./css/footer.css" rel="stylesheet">
    <link href="./css/login.css" rel="stylesheet">
    <!-- Dark Mode CSS -->
    <link href="./css/dark-mode.css" rel="stylesheet">
    <title>Hotel Management System - FITDNU</title>
</head>

<body>
    <section class="vh-100 d-flex justify-content-center align-items-center login-container">
        <div class="container-fluid h-custom">
            <div class="d-flex flex-row align-items-center justify-content-center mb-4">
                <h2 class="text-primary" style="color: #f66600;">HOTEL MANAGEMENT SYSTEM</h2>
                <!-- Theme toggle button for login page -->
                <button type="button" class="theme-toggle btn ms-3" id="login-theme-toggle" title="Chuyển đổi giao diện tối/sáng">
                    <span class="theme-icon">
                        <i class="fas fa-moon"></i>
                    </span>
                </button>
            </div>
            <div class="row d-flex justify-content-center align-items-center h-100">
                <div class="col-md-9 col-lg-6 col-xl-5">
                    <img src="./images/draw2.webp" style="width: 100%; height: 100%; object-fit: cover;" class="img-fluid"
                        alt="Sample image">
                </div>
                <div class="col-md-8 col-lg-6 col-xl-4 offset-xl-1">
                    <div class="card login-card p-4 shadow">
                        <form action="./handle/login_process.php" method="POST">
                        <!-- Username input -->
                        <div data-mdb-input-init class="form-outline mb-4">
                            <input type="text" name="username" id="form3Example3" class="form-control form-control-lg"
                                placeholder="Nhập username" required />
                            <label class="form-label" for="form3Example3">Username</label>
                        </div>

                        <!-- Password input -->
                        <div data-mdb-input-init class="form-outline mb-3">
                            <input type="password" name="password" id="form3Example4" class="form-control form-control-lg"
                                placeholder="Nhập mật khẩu" required />
                            <label class="form-label" for="form3Example4">Password</label>
                        </div>
                        <!-- Thông báo lỗi sử dụng session -->
                        <?php if (isset($_SESSION['error'])): ?>
                            <div class="alert alert-danger" role="alert">
                                <?php 
                                echo $_SESSION['error']; 
                                unset($_SESSION['error']);
                                ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (isset($_SESSION['success'])): ?>
                            <div class="alert alert-success" role="alert">
                                <?php 
                                echo $_SESSION['success']; 
                                unset($_SESSION['success']);
                                ?>
                            </div>
                        <?php endif; ?>

                        <!-- <div class="d-flex justify-content-between align-items-center">

                            <div class="form-check mb-0">
                                <input class="form-check-input me-2" type="checkbox" value="" id="form2Example3" />
                                <label class="form-check-label" for="form2Example3">
                                    Remember me
                                </label>
                            </div>
                        </div> -->

                        <div class="text-center text-lg-start mt-4 pt-2">
                            <button type="submit" data-mdb-button-init data-mdb-ripple-init
                                name="login"
                                class="btn btn-primary btn-lg"
                                style="padding-left: 2.5rem; padding-right: 2.5rem;">Login</button>
                        </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>

    </section>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Dark Mode JS -->
    <script src="./js/dark-mode.js"></script>
    <script>
        // Custom toggle for login page
        document.addEventListener('DOMContentLoaded', function() {
            const loginToggle = document.getElementById('login-theme-toggle');
            if (loginToggle && window.themeManager) {
                loginToggle.addEventListener('click', function() {
                    window.themeManager.toggleTheme();
                });
                
                // Update icon based on current theme
                window.addEventListener('themeChanged', function(e) {
                    const icon = loginToggle.querySelector('i');
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
<footer class="footer">
    Copyright © 2025 - FITDNU
</footer>

</html>