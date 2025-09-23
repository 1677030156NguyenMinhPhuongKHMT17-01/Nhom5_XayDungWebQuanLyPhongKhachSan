<?php
require_once '../functions/auth.php';
require_once '../functions/booking_functions.php';
require_once '../functions/utils.php'; // Sử dụng utility functions

// Kiểm tra đăng nhập
checkLogin('../index.php');
$currentUser = getCurrentUser();

// Thiết lập thông tin trang
$pageTitle = 'Quản lý đặt phòng';
$baseUrl = '../';

// Xử lý tìm kiếm - đơn giản hóa
$searchKeyword = trim($_GET['search'] ?? '');
$bookings = $searchKeyword ? searchBookings($searchKeyword) : getAllBookings();

// Include layout header
include '../layout/admin_header.php';
?>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="card-title"><i class="bi bi-calendar-check me-2"></i>Danh sách đặt phòng</h5>
                    <a href="booking/create_booking.php" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-1"></i>Tạo đặt phòng
                    </a>
                </div>

                <!-- Search Form - sử dụng utility function -->
                <?= searchForm($searchKeyword, 'Tìm kiếm theo tên khách hàng, email, số phòng, trạng thái...', 'booking.php') ?>

                <!-- Alert Messages - sử dụng utility function -->
                <?php if (isset($_SESSION['success'])): ?>
                    <?= showAlert('success', $_SESSION['success']) ?>
                    <?php unset($_SESSION['success']); ?>
                <?php endif; ?>

                <?php if (isset($_SESSION['error'])): ?>
                    <?= showAlert('danger', $_SESSION['error']) ?>
                    <?php unset($_SESSION['error']); ?>
                <?php endif; ?>

                <!-- Search Results Info -->
                <?php if ($searchKeyword): ?>
                    <?= showAlert('info', "Kết quả tìm kiếm cho: \"" . e($searchKeyword) . "\"") ?>
                <?php endif; ?>

                <!-- Content -->
                <?php if (empty($bookings)): ?>
                    <?= emptyState(
                        'bi bi-calendar-check', 
                        $searchKeyword ? 'Không tìm thấy đặt phòng nào' : 'Chưa có đặt phòng nào',
                        $searchKeyword ? "Không tìm thấy đặt phòng phù hợp với \"$searchKeyword\"" : 'Hãy tạo đặt phòng đầu tiên để bắt đầu',
                        'booking/create_booking.php',
                        'Tạo đặt phòng mới'
                    ) ?>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped datatable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Khách hàng</th>
                                    <th>Phòng</th>
                                    <th>Check-in/Check-out</th>
                                    <th>Đêm</th>
                                    <th>Tổng tiền</th>
                                    <th>Trạng thái</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($bookings as $booking): ?>
                                    <tr>
                                        <td><?= e($booking['id']) ?></td>
                                        <td>
                                            <strong><?= e($booking['full_name'] ?? 'N/A') ?></strong><br>
                                            <small class="text-muted"><?= e($booking['email'] ?? 'N/A') ?></small>
                                        </td>
                                        <td>
                                            <strong><?= e($booking['room_number'] ?? 'N/A') ?></strong><br>
                                            <small class="text-muted"><?= e($booking['name_Room_Type'] ?? 'N/A') ?></small>
                                        </td>
                                        <td>
                                            <?php if (!empty($booking['check_in_date'])): ?>
                                                <strong>In:</strong> <?= date('d/m/Y', strtotime($booking['check_in_date'])) ?><br>
                                                <strong>Out:</strong> <?= date('d/m/Y', strtotime($booking['check_out_date'])) ?>
                                            <?php else: ?>
                                                <span class="text-muted">Chưa có</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if (!empty($booking['nights'])): ?>
                                                <span class="badge bg-info"><?= $booking['nights'] ?> đêm</span>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= formatCurrency($booking['total_price'] ?? 0) ?></td>
                                        <td><?= getStatusBadge($booking['status'] ?? 'pending', 'booking') ?></td>
                                        <td>
                                            <?= getActionButtons(
                                                $booking['id'],
                                                "booking/edit_booking.php?id={$booking['id']}",
                                                "../handle/booking_process.php",
                                                $booking['full_name'] ?? 'N/A'
                                            ) ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <?php if ($searchKeyword): ?>
                        <div class="text-center mt-3">
                            <a href="booking.php" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-1"></i>Xem tất cả đặt phòng
                            </a>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?= deleteConfirmScript() ?>

<?php include '../layout/admin_footer.php'; ?>