<?php
// Simplified header with default values
$pageTitle = $pageTitle ?? 'Hotel Management System - FITDNU';
$baseUrl = $baseUrl ?? '../';
$currentUser = $currentUser ?? ['username' => 'User', 'role' => 'Quản trị viên'];

include $baseUrl . 'layout/header.php';
?>

<header id="header" class="header fixed-top d-flex align-items-center">
    <!-- Logo and Sidebar Toggle -->
    <div class="d-flex align-items-center justify-content-between">
        <a href="<?= $baseUrl ?>views/dashboard.php" class="logo d-flex align-items-center">
            <img src="<?= $baseUrl ?>images/fitdnu_logo.png" alt="FITDNU Logo">
            <span class="d-none d-lg-block">FITDNU Hotel</span>
        </a>
        <i class="bi bi-list toggle-sidebar-btn"></i>
    </div>

    <!-- Search Bar -->
    <div class="search-bar">
        <form class="search-form d-flex align-items-center" method="POST" action="#">
            <input type="text" name="query" placeholder="Tìm kiếm..." title="Nhập từ khóa tìm kiếm">
            <button type="submit" title="Tìm kiếm"><i class="bi bi-search"></i></button>
        </form>
    </div>

    <!-- Header Navigation -->
    <nav class="header-nav ms-auto">
        <ul class="d-flex align-items-center">
            <!-- Mobile Search Toggle -->
            <li class="nav-item d-block d-lg-none">
                <a class="nav-link nav-icon search-bar-toggle" href="#">
                    <i class="bi bi-search"></i>
                </a>
            </li>

            <!-- Theme Toggle -->
            <li class="nav-item">
                <a class="nav-link nav-icon" href="#" id="theme-toggle" title="Chuyển đổi giao diện">
                    <i class="bi bi-moon"></i>
                </a>
            </li>

            <!-- Profile Dropdown -->
            <li class="nav-item dropdown pe-3">
                <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
                    <img src="<?= $baseUrl ?>images/fitdnu_logo.png" alt="Profile" class="rounded-circle">
                    <span class="d-none d-md-block dropdown-toggle ps-2"><?= htmlspecialchars($currentUser['username']) ?></span>
                </a>

                <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
                    <li class="dropdown-header">
                        <h6><?= htmlspecialchars($currentUser['username']) ?></h6>
                        <span><?= htmlspecialchars($currentUser['role']) ?></span>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <a class="dropdown-item d-flex align-items-center" href="<?= $baseUrl ?>views/profile.php">
                            <i class="bi bi-person"></i>
                            <span>Hồ sơ của tôi</span>
                        </a>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <a class="dropdown-item d-flex align-items-center" href="<?= $baseUrl ?>handle/logout_process.php">
                            <i class="bi bi-box-arrow-right"></i>
                            <span>Đăng xuất</span>
                        </a>
                    </li>
                </ul>
            </li>
        </ul>
    </nav>
</header>

<!-- Sidebar -->
<aside id="sidebar" class="sidebar">
    <ul class="sidebar-nav" id="sidebar-nav">
        <?php
        // Get current page for active navigation
        $currentPage = basename($_SERVER['PHP_SELF']);
        
        // Navigation items
        $navItems = [
            ['href' => 'dashboard.php', 'icon' => 'bi-grid', 'label' => 'Dashboard'],
            ['href' => 'guest.php', 'icon' => 'bi-people', 'label' => 'Quản lý khách hàng'],
            ['href' => 'roomtype.php', 'icon' => 'bi-house-door', 'label' => 'Loại phòng'],
            ['href' => 'room.php', 'icon' => 'bi-door-open', 'label' => 'Quản lý phòng'],
            ['href' => 'booking.php', 'icon' => 'bi-calendar-check', 'label' => 'Đặt phòng']
        ];
        
        foreach ($navItems as $item):
            $isActive = ($currentPage === $item['href']) ? '' : 'collapsed';
        ?>
        <li class="nav-item">
            <a class="nav-link <?= $isActive ?>" href="<?= $baseUrl ?>views/<?= $item['href'] ?>">
                <i class="<?= $item['icon'] ?>"></i>
                <span><?= $item['label'] ?></span>
            </a>
        </li>
        <?php endforeach; ?>
    </ul>
</aside>

<main id="main" class="main">
    <div class="pagetitle">
        <h1><?= htmlspecialchars(str_replace('Hotel Management System - FITDNU', '', $pageTitle)) ?></h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= $baseUrl ?>views/dashboard.php">Trang chủ</a></li>
                <?php if ($currentPage !== 'dashboard.php'): ?>
                <li class="breadcrumb-item active"><?= htmlspecialchars(str_replace('Hotel Management System - FITDNU', '', $pageTitle)) ?></li>
                <?php endif; ?>
            </ol>
        </nav>
    </div>

    <section class="section"><?php // Content will be inserted here ?>