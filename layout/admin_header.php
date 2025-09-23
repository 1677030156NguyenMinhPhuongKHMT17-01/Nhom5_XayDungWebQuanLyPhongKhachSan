<?php
$pageTitle = isset($pageTitle) ? $pageTitle : 'Hotel Management System - FITDNU';
$baseUrl = isset($baseUrl) ? $baseUrl : '../';
include $baseUrl . 'layout/header.php';
?>

<!-- ======= Header ======= -->
<header id="header" class="header fixed-top d-flex align-items-center">
    <div class="d-flex align-items-center justify-content-between">
        <a href="<?= $baseUrl ?>views/dashboard.php" class="logo d-flex align-items-center">
            <img src="<?= $baseUrl ?>images/fitdnu_logo.png" alt="">
            <span class="d-none d-lg-block">FITDNU Hotel</span>
        </a>
        <i class="bi bi-list toggle-sidebar-btn"></i>
    </div><!-- End Logo -->

    <div class="search-bar">
        <form class="search-form d-flex align-items-center" method="POST" action="#">
            <input type="text" name="query" placeholder="Tìm kiếm..." title="Nhập từ khóa tìm kiếm">
            <button type="submit" title="Tìm kiếm"><i class="bi bi-search"></i></button>
        </form>
    </div><!-- End Search Bar -->

    <nav class="header-nav ms-auto">
        <ul class="d-flex align-items-center">
            <li class="nav-item d-block d-lg-none">
                <a class="nav-link nav-icon search-bar-toggle " href="#">
                    <i class="bi bi-search"></i>
                </a>
            </li><!-- End Search Icon-->

            <!-- Theme Toggle Button -->
            <li class="nav-item">
                <a class="nav-link nav-icon" href="#" id="theme-toggle" title="Chuyển đổi giao diện tối/sáng">
                    <i class="bi bi-moon"></i>
                </a>
            </li>

            <li class="nav-item dropdown pe-3">
                <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
                    <img src="<?= $baseUrl ?>images/fitdnu_logo.png" alt="Profile" class="rounded-circle">
                    <span class="d-none d-md-block dropdown-toggle ps-2"><?= htmlspecialchars($currentUser['username'] ?? 'User') ?></span>
                </a><!-- End Profile Image Icon -->

                <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
                    <li class="dropdown-header">
                        <h6><?= htmlspecialchars($currentUser['username'] ?? 'User') ?></h6>
                        <span><?= htmlspecialchars($currentUser['role'] ?? 'Quản trị viên') ?></span>
                    </li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>

                    <li>
                        <a class="dropdown-item d-flex align-items-center" href="<?= $baseUrl ?>views/profile.php">
                            <i class="bi bi-person"></i>
                            <span>Hồ sơ của tôi</span>
                        </a>
                    </li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>

                    <li>
                        <a class="dropdown-item d-flex align-items-center" href="<?= $baseUrl ?>handle/logout_process.php">
                            <i class="bi bi-box-arrow-right"></i>
                            <span>Đăng xuất</span>
                        </a>
                    </li>

                </ul><!-- End Profile Dropdown Items -->
            </li><!-- End Profile Nav -->

        </ul>
    </nav><!-- End Icons Navigation -->

</header><!-- End Header -->

<!-- ======= Sidebar ======= -->
<aside id="sidebar" class="sidebar">

    <ul class="sidebar-nav" id="sidebar-nav">

        <li class="nav-item">
            <a class="nav-link <?= (basename($_SERVER['PHP_SELF']) == 'dashboard.php') ? '' : 'collapsed' ?>" href="<?= $baseUrl ?>views/dashboard.php">
                <i class="bi bi-grid"></i>
                <span>Dashboard</span>
            </a>
        </li><!-- End Dashboard Nav -->

        <li class="nav-item">
            <a class="nav-link <?= (basename($_SERVER['PHP_SELF']) == 'guest.php') ? '' : 'collapsed' ?>" href="<?= $baseUrl ?>views/guest.php">
                <i class="bi bi-people"></i>
                <span>Quản lý khách hàng</span>
            </a>
        </li><!-- End Guests Nav -->

        <li class="nav-item">
            <a class="nav-link <?= (basename($_SERVER['PHP_SELF']) == 'roomtype.php') ? '' : 'collapsed' ?>" href="<?= $baseUrl ?>views/roomtype.php">
                <i class="bi bi-house-door"></i>
                <span>Loại phòng</span>
            </a>
        </li><!-- End Room Types Nav -->

        <li class="nav-item">
            <a class="nav-link <?= (basename($_SERVER['PHP_SELF']) == 'room.php') ? '' : 'collapsed' ?>" href="<?= $baseUrl ?>views/room.php">
                <i class="bi bi-door-open"></i>
                <span>Quản lý phòng</span>
            </a>
        </li><!-- End Rooms Nav -->

        <li class="nav-item">
            <a class="nav-link <?= (basename($_SERVER['PHP_SELF']) == 'booking.php') ? '' : 'collapsed' ?>" href="<?= $baseUrl ?>views/booking.php">
                <i class="bi bi-calendar-check"></i>
                <span>Đặt phòng</span>
            </a>
        </li><!-- End Bookings Nav -->

        <li class="nav-heading">Khác</li>

        <li class="nav-item">
            <a class="nav-link collapsed" href="<?= $baseUrl ?>views/profile.php">
                <i class="bi bi-person"></i>
                <span>Hồ sơ</span>
            </a>
        </li><!-- End Profile Page Nav -->

        <li class="nav-item">
            <a class="nav-link collapsed" href="<?= $baseUrl ?>handle/logout_process.php">
                <i class="bi bi-box-arrow-in-right"></i>
                <span>Đăng xuất</span>
            </a>
        </li><!-- End Logout Nav -->

    </ul>

</aside><!-- End Sidebar-->

<main id="main" class="main">

    <?php if (isset($pageTitle) && $pageTitle != 'Dashboard'): ?>
    <div class="pagetitle">
        <h1><?= $pageTitle ?></h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= $baseUrl ?>views/dashboard.php">Trang chủ</a></li>
                <li class="breadcrumb-item active"><?= $pageTitle ?></li>
            </ol>
        </nav>
    </div><!-- End Page Title -->
    <?php endif; ?>

    <!-- Page content will be inserted here -->
    <section class="section">
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