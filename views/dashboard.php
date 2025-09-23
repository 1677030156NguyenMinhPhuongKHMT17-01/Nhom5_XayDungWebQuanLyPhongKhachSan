<?php
require_once '../functions/auth.php';
require_once '../functions/guest_functions.php';
require_once '../functions/room_functions.php';
require_once '../functions/booking_functions.php';
require_once '../functions/roomtype_functions.php';

// Kiểm tra đăng nhập
checkLogin('../index.php');
$currentUser = getCurrentUser();

// Thiết lập thông tin trang
$pageTitle = 'Dashboard';
$baseUrl = '../';

// Lấy thống kê tổng quan
$totalGuests = count(getAllGuests());
$totalRooms = count(getAllRooms());
$totalBookings = count(getAllBookings());
$totalRoomTypes = count(getAllRoomTypes());

// Lấy thống kê phòng theo trạng thái
$allRooms = getAllRooms();
$availableRooms = array_filter($allRooms, function($room) { return $room['status'] === 'available'; });
$occupiedRooms = array_filter($allRooms, function($room) { return $room['status'] === 'occupied'; });
$maintenanceRooms = array_filter($allRooms, function($room) { return $room['status'] === 'maintenance'; });

// Lấy booking gần đây
$recentBookings = array_slice(getAllBookings(), 0, 5);

// Include layout header
include '../layout/admin_header.php';
?>
        <!-- Welcome Section -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="pagetitle">
                    <h1><i class="bi bi-grid text-primary"></i> Dashboard</h1>
                    <nav>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="dashboard.php">Trang chủ</a></li>
                            <li class="breadcrumb-item active">Dashboard</li>
                        </ol>
                    </nav>
                </div>
                <p class="text-muted">Tổng quan hệ thống quản lý khách sạn - Chào mừng, <?= htmlspecialchars($currentUser['username'] ?? 'Admin') ?>!</p>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row">
            <div class="col-xxl-3 col-md-6">
                <div class="card info-card sales-card">
                    <div class="card-body">
                        <h5 class="card-title">Khách hàng</h5>
                        <div class="d-flex align-items-center">
                            <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                <i class="bi bi-people"></i>
                            </div>
                            <div class="ps-3">
                                <h6><?= $totalGuests ?></h6>
                                <span class="text-success small pt-1 fw-bold">Tổng số</span> <span class="text-muted small pt-2 ps-1">khách hàng</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xxl-3 col-md-6">
                <div class="card info-card revenue-card">
                    <div class="card-body">
                        <h5 class="card-title">Phòng</h5>
                        <div class="d-flex align-items-center">
                            <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                <i class="bi bi-door-open"></i>
                            </div>
                            <div class="ps-3">
                                <h6><?= $totalRooms ?></h6>
                                <span class="text-success small pt-1 fw-bold">Tổng số</span> <span class="text-muted small pt-2 ps-1">phòng</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xxl-3 col-md-6">
                <div class="card info-card customers-card">
                    <div class="card-body">
                        <h5 class="card-title">Đặt phòng</h5>
                        <div class="d-flex align-items-center">
                            <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                <i class="bi bi-calendar-check"></i>
                            </div>
                            <div class="ps-3">
                                <h6><?= $totalBookings ?></h6>
                                <span class="text-danger small pt-1 fw-bold">Tổng số</span> <span class="text-muted small pt-2 ps-1">booking</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xxl-3 col-xl-12">
                <div class="card info-card sales-card">
                    <div class="card-body">
                        <h5 class="card-title">Loại phòng</h5>
                        <div class="d-flex align-items-center">
                            <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                <i class="bi bi-house-door"></i>
                            </div>
                            <div class="ps-3">
                                <h6><?= $totalRoomTypes ?></h6>
                                <span class="text-success small pt-1 fw-bold">Tổng số</span> <span class="text-muted small pt-2 ps-1">loại phòng</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- End Statistics Cards -->

        <!-- Quick Access Section -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="bi bi-lightning text-warning"></i> Truy cập nhanh
                        </h5>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <a href="checkin_dashboard.php" class="btn btn-primary w-100 p-3 text-start">
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-calendar-check-fill me-3 fs-3"></i>
                                        <div>
                                            <h6 class="mb-1">Check-in/Check-out</h6>
                                            <small class="text-light">Quản lý check-in và check-out</small>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <div class="col-md-4 mb-3">
                                <a href="reports.php" class="btn btn-success w-100 p-3 text-start">
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-graph-up me-3 fs-3"></i>
                                        <div>
                                            <h6 class="mb-1">Báo cáo & Thống kê</h6>
                                            <small class="text-light">Doanh thu và phân tích</small>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <div class="col-md-4 mb-3">
                                <a href="booking.php" class="btn btn-info w-100 p-3 text-start">
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-plus-circle-fill me-3 fs-3"></i>
                                        <div>
                                            <h6 class="mb-1">Đặt phòng mới</h6>
                                            <small class="text-light">Tạo booking mới</small>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- End Quick Access Section -->

            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4><?= $totalRooms ?></h4>
                                <p class="mb-0">Tổng số phòng</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-door-open fa-2x"></i>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer d-flex align-items-center justify-content-between">
                        <a class="small text-white stretched-link" href="room.php">Xem chi tiết</a>
                        <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4><?= $totalBookings ?></h4>
                                <p class="mb-0">Đặt phòng</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-calendar-check fa-2x"></i>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer d-flex align-items-center justify-content-between">
                        <a class="small text-white stretched-link" href="booking.php">Xem chi tiết</a>
                        <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4><?= $totalRoomTypes ?></h4>
                                <p class="mb-0">Loại phòng</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-bed fa-2x"></i>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer d-flex align-items-center justify-content-between">
                        <a class="small text-white stretched-link" href="roomtype.php">Xem chi tiết</a>
                        <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Room Status & Recent Bookings -->
        <div class="row">
            <!-- Room Status Chart -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-chart-pie text-primary"></i> Trạng thái phòng</h5>
                    </div>
                    <div class="card-body">
                        <?php if ($totalRooms > 0): ?>
                            <div class="row text-center">
                                <div class="col-4">
                                    <div class="p-3 border rounded bg-light">
                                        <h4 class="text-success"><?= count($availableRooms) ?></h4>
                                        <p class="mb-0 small">Phòng trống</p>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="p-3 border rounded bg-light">
                                        <h4 class="text-danger"><?= count($occupiedRooms) ?></h4>
                                        <p class="mb-0 small">Đã thuê</p>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="p-3 border rounded bg-light">
                                        <h4 class="text-warning"><?= count($maintenanceRooms) ?></h4>
                                        <p class="mb-0 small">Bảo trì</p>
                                    </div>
                                </div>
                            </div>
                            <div class="progress mt-3" style="height: 20px;">
                                <div class="progress-bar bg-success" role="progressbar" 
                                     style="width: <?= ($totalRooms > 0) ? (count($availableRooms) / $totalRooms * 100) : 0 ?>%">
                                    <?= round(count($availableRooms) / $totalRooms * 100, 1) ?>%
                                </div>
                                <div class="progress-bar bg-danger" role="progressbar" 
                                     style="width: <?= ($totalRooms > 0) ? (count($occupiedRooms) / $totalRooms * 100) : 0 ?>%">
                                    <?= round(count($occupiedRooms) / $totalRooms * 100, 1) ?>%
                                </div>
                                <div class="progress-bar bg-warning" role="progressbar" 
                                     style="width: <?= ($totalRooms > 0) ? (count($maintenanceRooms) / $totalRooms * 100) : 0 ?>%">
                                    <?= round(count($maintenanceRooms) / $totalRooms * 100, 1) ?>%
                                </div>
                            </div>
                        <?php else: ?>
                            <p class="text-center text-muted">Chưa có phòng nào</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Recent Bookings -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-clock text-primary"></i> Đặt phòng gần đây</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($recentBookings)): ?>
                            <p class="text-center text-muted">Chưa có đặt phòng nào</p>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Khách hàng</th>
                                            <th>Phòng</th>
                                            <th>Trạng thái</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($recentBookings as $booking): ?>
                                            <tr>
                                                <td><?= htmlspecialchars(substr($booking['full_name'] ?? 'N/A', 0, 15)) ?>...</td>
                                                <td><?= htmlspecialchars($booking['room_number'] ?? 'N/A') ?></td>
                                                <td>
                                                    <?php
                                                    $statusClass = '';
                                                    $statusText = '';
                                                    switch ($booking['status']) {
                                                        case 'pending':
                                                            $statusClass = 'warning';
                                                            $statusText = 'Chờ';
                                                            break;
                                                        case 'confirmed':
                                                            $statusClass = 'success';
                                                            $statusText = 'Xác nhận';
                                                            break;
                                                        case 'cancelled':
                                                            $statusClass = 'danger';
                                                            $statusText = 'Hủy';
                                                            break;
                                                        case 'completed':
                                                            $statusClass = 'info';
                                                            $statusText = 'Hoàn thành';
                                                            break;
                                                        default:
                                                            $statusClass = 'secondary';
                                                            $statusText = ucfirst($booking['status']);
                                                    }
                                                    ?>
                                                    <span class="badge bg-<?= $statusClass ?>"><?= $statusText ?></span>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="text-center">
                                <a href="booking.php" class="btn btn-sm btn-outline-primary">Xem tất cả</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title"><i class="bi bi-lightning text-primary"></i> Thao tác nhanh</h5>
                        <div class="row">
                            <div class="col-md-3">
                                <a href="guest/create_guest.php" class="btn btn-outline-primary w-100 mb-2">
                                    <i class="bi bi-person-plus"></i><br>Thêm khách hàng
                                </a>
                            </div>
                            <div class="col-md-3">
                                <a href="room/create_room.php" class="btn btn-outline-success w-100 mb-2">
                                    <i class="bi bi-door-open"></i><br>Thêm phòng
                                </a>
                            </div>
                            <div class="col-md-3">
                                <a href="booking/create_booking.php" class="btn btn-outline-warning w-100 mb-2">
                                    <i class="bi bi-calendar-plus"></i><br>Tạo đặt phòng
                                </a>
                            </div>
                            <div class="col-md-3">
                                <a href="roomtype/create_roomtype.php" class="btn btn-outline-info w-100 mb-2">
                                    <i class="bi bi-house-add"></i><br>Thêm loại phòng
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

<?php include '../layout/admin_footer.php'; ?>
