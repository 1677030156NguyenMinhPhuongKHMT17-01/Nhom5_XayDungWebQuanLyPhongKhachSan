<?php
require_once '../functions/guest_functions.php';
require_once '../functions/room_functions.php';
require_once '../functions/booking_functions.php';
require_once '../functions/roomtype_functions.php';
require_once 'menu.php';

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
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Hotel Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    <div class="container-fluid mt-4">
        <!-- Welcome Section -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2><i class="fas fa-tachometer-alt text-primary"></i> Dashboard</h2>
                        <p class="text-muted">Tổng quan hệ thống quản lý khách sạn</p>
                    </div>
                    <div class="text-end">
                        <small class="text-muted">Chào mừng, <?= htmlspecialchars($currentUser['username'] ?? 'Admin') ?>!</small><br>
                        <small class="text-muted"><?= date('d/m/Y H:i') ?></small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4><?= $totalGuests ?></h4>
                                <p class="mb-0">Khách hàng</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-users fa-2x"></i>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer d-flex align-items-center justify-content-between">
                        <a class="small text-white stretched-link" href="guest.php">Xem chi tiết</a>
                        <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                    </div>
                </div>
            </div>

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
                    <div class="card-header">
                        <h5><i class="fas fa-bolt text-primary"></i> Thao tác nhanh</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <a href="guest/create_guest.php" class="btn btn-outline-primary w-100 mb-2">
                                    <i class="fas fa-user-plus"></i><br>Thêm khách hàng
                                </a>
                            </div>
                            <div class="col-md-3">
                                <a href="room/create_room.php" class="btn btn-outline-success w-100 mb-2">
                                    <i class="fas fa-door-open"></i><br>Thêm phòng
                                </a>
                            </div>
                            <div class="col-md-3">
                                <a href="booking/create_booking.php" class="btn btn-outline-warning w-100 mb-2">
                                    <i class="fas fa-calendar-plus"></i><br>Tạo đặt phòng
                                </a>
                            </div>
                            <div class="col-md-3">
                                <a href="roomtype/create_roomtype.php" class="btn btn-outline-info w-100 mb-2">
                                    <i class="fas fa-bed"></i><br>Thêm loại phòng
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
