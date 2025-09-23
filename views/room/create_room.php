<?php
require_once '../../functions/auth.php';
require_once '../../functions/room_functions.php';
require_once '../../functions/roomtype_functions.php';
require_once '../../functions/utils.php';

// Kiểm tra đăng nhập
checkLogin('../../index.php');
$currentUser = getCurrentUser();

// Thiết lập thông tin trang
$pageTitle = 'Thêm phòng mới';
$baseUrl = '../../';

// Lấy danh sách loại phòng
$roomTypes = getAllRoomTypes();

// Include layout header
include '../../layout/admin_header.php';
?>
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center mb-4">
                    <a href="../room.php" class="btn btn-outline-secondary me-3">
                        <i class="bi bi-arrow-left"></i>
                    </a>
                    <div>
                        <h5 class="card-title mb-0"><i class="bi bi-door-open me-2"></i>Thêm phòng mới</h5>
                        <small class="text-muted">Tạo phòng mới cho khách sạn</small>
                    </div>
                </div>

                <!-- Alert Messages -->
                <?php if (isset($_SESSION['error'])): ?>
                    <?= showAlert('danger', $_SESSION['error']) ?>
                    <?php unset($_SESSION['error']); ?>
                <?php endif; ?>

                <?php if (empty($roomTypes)): ?>
                    <div class="alert alert-warning" role="alert">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        Chưa có loại phòng nào. Vui lòng <a href="../roomtype/create_roomtype.php" class="alert-link">tạo loại phòng</a> trước khi thêm phòng.
                    </div>
                <?php else: ?>
                    <form action="../../handle/room_process.php" method="POST">
                        <input type="hidden" name="action" value="create">
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="room_number" class="form-label">Số phòng <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="room_number" name="room_number" 
                                           value="<?= e($_POST['room_number'] ?? '') ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="room_type_id" class="form-label">Loại phòng <span class="text-danger">*</span></label>
                                    <select class="form-select" id="room_type_id" name="room_type_id" required>
                                        <option value="">-- Chọn loại phòng --</option>
                                        <?php foreach ($roomTypes as $type): ?>
                                            <option value="<?= e($type['id']) ?>" 
                                                <?= ($_POST['room_type_id'] ?? '') == $type['id'] ? 'selected' : '' ?>>
                                                <?= e($type['name_Room_Type']) ?> - <?= formatCurrency($type['price_per_night']) ?>/đêm
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label">Trạng thái <span class="text-danger">*</span></label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="available" <?= ($_POST['status'] ?? '') == 'available' ? 'selected' : '' ?>>Có sẵn</option>
                                <option value="occupied" <?= ($_POST['status'] ?? '') == 'occupied' ? 'selected' : '' ?>>Đã đặt</option>
                                <option value="maintenance" <?= ($_POST['status'] ?? '') == 'maintenance' ? 'selected' : '' ?>>Bảo trì</option>
                                <option value="out_of_order" <?= ($_POST['status'] ?? '') == 'out_of_order' ? 'selected' : '' ?>>Hỏng</option>
                            </select>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="../room.php" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-1"></i>Quay lại
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg me-1"></i>Lưu phòng
                            </button>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include '../../layout/admin_footer.php'; ?>