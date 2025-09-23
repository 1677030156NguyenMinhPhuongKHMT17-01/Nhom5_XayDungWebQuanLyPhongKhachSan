<?php
require_once '../../functions/auth.php';
require_once '../../functions/room_functions.php';
require_once '../../functions/roomtype_functions.php';
require_once '../../functions/utils.php';

// Kiểm tra đăng nhập
checkLogin('../../index.php');
$currentUser = getCurrentUser();

// Thiết lập thông tin trang
$pageTitle = 'Chỉnh sửa phòng';
$baseUrl = '../../';

// Lấy ID từ URL
$id = $_GET['id'] ?? 0;

if (empty($id)) {
    $_SESSION['error'] = 'Không tìm thấy phòng!';
    header('Location: ../room.php');
    exit();
}

// Lấy thông tin phòng
$room = getRoomById($id);

if (!$room) {
    $_SESSION['error'] = 'Không tìm thấy phòng!';
    header('Location: ../room.php');
    exit();
}

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
                        <h5 class="card-title mb-0"><i class="bi bi-door-closed me-2"></i>Chỉnh sửa phòng</h5>
                        <small class="text-muted">Cập nhật thông tin phòng <?= e($room['room_number']) ?></small>
                    </div>
                </div>

                <!-- Alert Messages -->
                <?php if (isset($_SESSION['error'])): ?>
                    <?= showAlert('danger', $_SESSION['error']) ?>
                    <?php unset($_SESSION['error']); ?>
                <?php endif; ?>

                <form action="../../handle/room_process.php" method="POST">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="id" value="<?= e($room['id']) ?>">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="room_number" class="form-label">Số phòng <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="room_number" name="room_number" 
                                       value="<?= e($room['room_number']) ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="room_type_id" class="form-label">Loại phòng <span class="text-danger">*</span></label>
                                <select class="form-select" id="room_type_id" name="room_type_id" required>
                                    <option value="">-- Chọn loại phòng --</option>
                                    <?php foreach ($roomTypes as $type): ?>
                                        <option value="<?= e($type['id']) ?>" 
                                            <?= $room['room_type_id'] == $type['id'] ? 'selected' : '' ?>>
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
                            <option value="available" <?= $room['status'] == 'available' ? 'selected' : '' ?>>Có sẵn</option>
                            <option value="occupied" <?= $room['status'] == 'occupied' ? 'selected' : '' ?>>Đã đặt</option>
                            <option value="maintenance" <?= $room['status'] == 'maintenance' ? 'selected' : '' ?>>Bảo trì</option>
                            <option value="out_of_order" <?= $room['status'] == 'out_of_order' ? 'selected' : '' ?>>Hỏng</option>
                        </select>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="../room.php" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-1"></i>Quay lại
                        </a>
                        <button type="submit" class="btn btn-warning">
                            <i class="bi bi-check-lg me-1"></i>Cập nhật phòng
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../../layout/admin_footer.php'; ?>