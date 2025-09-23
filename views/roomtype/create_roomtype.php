<?php
require_once '../../functions/auth.php';
require_once '../../functions/roomtype_functions.php';
require_once '../../functions/utils.php';

// Kiểm tra đăng nhập
checkLogin('../../index.php');
$currentUser = getCurrentUser();

// Thiết lập thông tin trang
$pageTitle = 'Thêm loại phòng mới';
$baseUrl = '../../';

// Include layout header
include '../../layout/admin_header.php';
?>
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center mb-4">
                    <a href="../roomtype.php" class="btn btn-outline-secondary me-3">
                        <i class="bi bi-arrow-left"></i>
                    </a>
                    <div>
                        <h5 class="card-title mb-0"><i class="bi bi-house-add me-2"></i>Thêm loại phòng mới</h5>
                        <small class="text-muted">Tạo loại phòng mới cho khách sạn</small>
                    </div>
                </div>

                <!-- Alert Messages -->
                <?php if (isset($_SESSION['error'])): ?>
                    <?= showAlert('danger', $_SESSION['error']) ?>
                    <?php unset($_SESSION['error']); ?>
                <?php endif; ?>

                <form action="../../handle/roomtype_process.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="create">
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="name_Room_Type" class="form-label">Tên loại phòng <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name_Room_Type" name="name_Room_Type" 
                                       value="<?= e($_POST['name_Room_Type'] ?? '') ?>" required
                                       placeholder="Ví dụ: Phòng Standard, Phòng Deluxe, Suite...">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="description" class="form-label">Mô tả</label>
                                <textarea class="form-control" id="description" name="description" rows="3"
                                          placeholder="Mô tả chi tiết về loại phòng..."><?= e($_POST['description'] ?? '') ?></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="image" class="form-label">Hình ảnh phòng</label>
                                <input type="file" class="form-control" id="image" name="image" 
                                       accept="image/*">
                                <div class="form-text">Chọn file ảnh (JPG, PNG, GIF). Tối đa 2MB.</div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="price_per_night" class="form-label">Giá mỗi đêm (VNĐ) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="price_per_night" name="price_per_night" 
                                       value="<?= e($_POST['price_per_night'] ?? '') ?>" min="0" step="1000" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="capacity" class="form-label">Sức chứa (người) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="capacity" name="capacity" 
                                       value="<?= e($_POST['capacity'] ?? '') ?>" min="1" max="10" required>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="../roomtype.php" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-1"></i>Quay lại
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-1"></i>Lưu loại phòng
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../../layout/admin_footer.php'; ?>