<?php
require_once '../functions/auth.php';
require_once '../functions/roomtype_functions.php';
require_once '../functions/utils.php'; // Sử dụng utility functions

// Kiểm tra đăng nhập
checkLogin('../index.php');
$currentUser = getCurrentUser();

// Thiết lập thông tin trang
$pageTitle = 'Quản lý loại phòng';
$baseUrl = '../';

// Xử lý tìm kiếm - đơn giản hóa
$searchKeyword = trim($_GET['search'] ?? '');
$roomTypes = $searchKeyword ? searchRoomTypes($searchKeyword) : getAllRoomTypes();

// Include layout header
include '../layout/admin_header.php';
?>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="card-title"><i class="bi bi-house-door me-2"></i>Danh sách loại phòng</h5>
                    <a href="roomtype/create_roomtype.php" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-1"></i>Thêm loại phòng
                    </a>
                </div>

                <!-- Search Form - sử dụng utility function -->
                <?= searchForm($searchKeyword, 'Tìm kiếm theo tên loại phòng, mô tả...', 'roomtype.php') ?>

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
                <?php if (empty($roomTypes)): ?>
                    <?= emptyState(
                        'bi bi-house-door', 
                        $searchKeyword ? 'Không tìm thấy loại phòng nào' : 'Chưa có loại phòng nào',
                        $searchKeyword ? "Không tìm thấy loại phòng phù hợp với \"$searchKeyword\"" : 'Hãy thêm loại phòng đầu tiên để bắt đầu',
                        'roomtype/create_roomtype.php',
                        'Thêm loại phòng mới'
                    ) ?>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped datatable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Tên loại phòng</th>
                                    <th>Mô tả</th>
                                    <th>Hình ảnh</th>
                                    <th>Giá/đêm (VNĐ)</th>
                                    <th>Sức chứa</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($roomTypes as $roomType): ?>
                                    <tr>
                                        <td><?= e($roomType['id']) ?></td>
                                        <td><strong><?= e($roomType['name_Room_Type']) ?></strong></td>
                                        <td>
                                            <div class="text-truncate" style="max-width: 200px;" title="<?= e($roomType['description'] ?? '') ?>">
                                                <?= e($roomType['description'] ?? 'Chưa có mô tả') ?>
                                            </div>
                                        </td>
                                        <td>
                                            <?php if (!empty($roomType['image'])): ?>
                                                <img src="../images/rooms/<?= e($roomType['image']) ?>" 
                                                     alt="<?= e($roomType['name_Room_Type']) ?>" 
                                                     class="img-thumbnail" 
                                                     style="width: 60px; height: 60px; object-fit: cover;"
                                                     onerror="this.src='../images/no-image.png'">
                                            <?php else: ?>
                                                <span class="text-muted">Chưa có ảnh</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= formatCurrency($roomType['price_per_night']) ?></td>
                                        <td><?= e($roomType['capacity']) ?> người</td>
                                        <td>
                                            <?= getActionButtons(
                                                $roomType['id'],
                                                "roomtype/edit_roomtype.php?id={$roomType['id']}",
                                                "../handle/roomtype_process.php",
                                                $roomType['name_Room_Type']
                                            ) ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <?php if ($searchKeyword): ?>
                        <div class="text-center mt-3">
                            <a href="roomtype.php" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-1"></i>Xem tất cả loại phòng
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