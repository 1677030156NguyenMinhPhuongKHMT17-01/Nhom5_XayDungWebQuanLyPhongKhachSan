<?php
require_once '../functions/auth.php';
require_once '../functions/room_functions.php';
require_once '../functions/utils.php'; // Sử dụng utility functions

// Kiểm tra đăng nhập
checkLogin('../index.php');
$currentUser = getCurrentUser();

// Thiết lập thông tin trang
$pageTitle = 'Quản lý phòng';
$baseUrl = '../';

// Xử lý tìm kiếm - đơn giản hóa
$searchKeyword = trim($_GET['search'] ?? '');
$rooms = $searchKeyword ? searchRooms($searchKeyword) : getAllRooms();

// Include layout header
include '../layout/admin_header.php';
?>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="card-title"><i class="bi bi-door-open me-2"></i>Danh sách phòng</h5>
                    <a href="room/create_room.php" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-1"></i>Thêm phòng
                    </a>
                </div>

                <!-- Search Form - sử dụng utility function -->
                <?= searchForm($searchKeyword, 'Tìm kiếm theo số phòng, loại phòng, trạng thái...', 'room.php') ?>

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
                <?php if (empty($rooms)): ?>
                    <?= emptyState(
                        'bi bi-door-open', 
                        $searchKeyword ? 'Không tìm thấy phòng nào' : 'Chưa có phòng nào',
                        $searchKeyword ? "Không tìm thấy phòng phù hợp với \"$searchKeyword\"" : 'Hãy thêm phòng đầu tiên để bắt đầu',
                        'room/create_room.php',
                        'Thêm phòng mới'
                    ) ?>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped datatable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Số phòng</th>
                                    <th>Loại phòng</th>
                                    <th>Giá phòng</th>
                                    <th>Trạng thái</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($rooms as $room): ?>
                                    <tr>
                                        <td><?= e($room['id']) ?></td>
                                        <td><strong><?= e($room['room_number']) ?></strong></td>
                                        <td><?= e($room['name_Room_Type'] ?? 'N/A') ?></td>
                                        <td><?= formatCurrency($room['price_per_night'] ?? 0) ?></td>
                                        <td><?= getStatusBadge($room['status'] ?? 'available', 'room') ?></td>
                                        <td>
                                            <?= getActionButtons(
                                                $room['id'],
                                                "room/edit_room.php?id={$room['id']}",
                                                "../handle/room_process.php",
                                                $room['room_number']
                                            ) ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <?php if ($searchKeyword): ?>
                        <div class="text-center mt-3">
                            <a href="room.php" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-1"></i>Xem tất cả phòng
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