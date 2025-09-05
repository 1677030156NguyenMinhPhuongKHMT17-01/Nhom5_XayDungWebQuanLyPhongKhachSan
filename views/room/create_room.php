<?php
require_once '../../functions/room_functions.php';
require_once '../../functions/roomtype_functions.php';
require_once '../menu.php';

// Lấy danh sách loại phòng để hiển thị trong dropdown
$roomTypes = getAllRoomTypes();
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm phòng mới</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../../css/dark-mode.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">
                            <i class="fas fa-door-open"></i> Thêm phòng mới
                        </h4>
                    </div>
                    <div class="card-body">
                        <!-- Hiển thị thông báo lỗi -->
                        <?php if (isset($_SESSION['error'])): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <?= $_SESSION['error'] ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                            <?php unset($_SESSION['error']); ?>
                        <?php endif; ?>

                        <?php if (empty($roomTypes)): ?>
                            <div class="alert alert-warning" role="alert">
                                <i class="fas fa-exclamation-triangle"></i>
                                Chưa có loại phòng nào. Vui lòng <a href="../roomtype/create_roomtype.php">tạo loại phòng</a> trước khi thêm phòng.
                            </div>
                        <?php else: ?>
                            <form action="../../handle/room_process.php" method="POST">
                                <input type="hidden" name="action" value="create">
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="room_number" class="form-label">Số phòng <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="room_number" name="room_number" 
                                                   placeholder="VD: 101, A01, B203..."
                                                   value="<?= isset($_POST['room_number']) ? htmlspecialchars($_POST['room_number']) : '' ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="room_type_id" class="form-label">Loại phòng <span class="text-danger">*</span></label>
                                            <select class="form-select" id="room_type_id" name="room_type_id" required>
                                                <option value="">-- Chọn loại phòng --</option>
                                                <?php foreach ($roomTypes as $roomType): ?>
                                                    <option value="<?= $roomType['id'] ?>" 
                                                            <?= (isset($_POST['room_type_id']) && $_POST['room_type_id'] == $roomType['id']) ? 'selected' : '' ?>>
                                                        <?= htmlspecialchars($roomType['name_Room_Type']) ?> 
                                                        (<?= number_format($roomType['price_per_night'], 0, ',', '.') ?> VNĐ/đêm)
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="status" class="form-label">Trạng thái <span class="text-danger">*</span></label>
                                            <select class="form-select" id="status" name="status" required>
                                                <option value="available" <?= (isset($_POST['status']) && $_POST['status'] == 'available') ? 'selected' : 'selected' ?>>
                                                    Trống (Available)
                                                </option>
                                                <option value="occupied" <?= (isset($_POST['status']) && $_POST['status'] == 'occupied') ? 'selected' : '' ?>>
                                                    Đã thuê (Occupied)
                                                </option>
                                                <option value="maintenance" <?= (isset($_POST['status']) && $_POST['status'] == 'maintenance') ? 'selected' : '' ?>>
                                                    Bảo trì (Maintenance)
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between">
                                    <a href="../room.php" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left"></i> Quay lại
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Lưu phòng
                                    </button>
                                </div>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://kit.fontawesome.com/your-fontawesome-kit.js"></script>
    <script src="../../js/dark-mode.js"></script>
</body>

</html>
