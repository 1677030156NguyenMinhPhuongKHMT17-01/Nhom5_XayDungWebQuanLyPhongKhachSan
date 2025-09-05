<?php
// Sử dụng __DIR__ để tính toán đường dẫn chính xác từ vị trí file hiện tại
require_once __DIR__ . '/../functions/auth.php';
checkLogin(__DIR__ . '/../index.php');
$currentUser = getCurrentUser();
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang chủ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Dark Mode CSS -->
    <link rel="stylesheet" href="../css/dark-mode.css">
</head>

<body>
    <?php
    // Lấy tên tệp hiện tại (ví dụ: "index.php", "ve-chung-toi.php")
    $currentPage = basename($_SERVER['PHP_SELF']);
    ?>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg bg-body-tertiary">
        <!-- Container wrapper -->
        <div class="container-fluid">
            <!-- Toggle button -->
            <button data-mdb-collapse-init class="navbar-toggler" type="button"
                data-mdb-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
                aria-label="Toggle navigation">
                <i class="fas fa-bars"></i>
            </button>

            <!-- Collapsible wrapper -->
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <!-- Navbar brand -->
                <a class="navbar-brand mt-2 mt-lg-0" href="#">
                    <img src="../images/fitdnu_logo.png" height="40"
                        alt="FIT-DNU Logo" loading="lazy" />
                </a>
                <!-- Left links -->
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="guest.php"><i class="fas fa-users"></i> Khách hàng</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="roomtype.php"><i class="fas fa-bed"></i> Loại phòng</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="room.php"><i class="fas fa-door-open"></i> Phòng</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="booking.php"><i class="fas fa-calendar-check"></i> Đặt phòng</a>
                    </li>
                </ul>
                <!-- Left links -->
            </div>
            <!-- Collapsible wrapper -->

            <!-- Right elements -->
            <div class="d-flex align-items-center">
                <!-- Hotel Icon -->
                <a class="text-reset me-3" href="#" title="Hotel Management">
                    <i class="fas fa-hotel"></i>
                </a>
                
                <!-- User dropdown -->
                <div class="dropdown">
                    <a class="dropdown-toggle d-flex align-items-center hidden-arrow" href="#"
                        id="navbarDropdownMenuAvatar" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="../images/aiotlab_logo.png" class="rounded-circle" height="25"
                            alt="Avatar" loading="lazy" />
                        <span class="ms-2"><?= htmlspecialchars($currentUser['username'] ?? 'User') ?></span>
                    </a>
                    
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdownMenuAvatar">
                        <li>
                            <a class="dropdown-item" href="#">
                                <i class="fas fa-user-circle me-2"></i>Hồ sơ cá nhân
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="#">
                                <i class="fas fa-cog me-2"></i>Cài đặt
                            </a>
                        </li>
                        <li><hr class="dropdown-divider" /></li>
                        <li>
                            <a class="dropdown-item" href="../handle/logout_process.php">
                                <i class="fas fa-sign-out-alt me-2"></i>Đăng xuất
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            <!-- Right elements -->
        </div>
        <!-- Container wrapper -->
    </nav>
    <!-- Navbar -->

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Dark Mode JS -->
    <script src="../js/dark-mode.js"></script>
</body>

</html>