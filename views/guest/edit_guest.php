<?php
require_once '../../functions/auth.php';
require_once '../../functions/guest_functions.php';
require_once '../../functions/utils.php';

// Kiểm tra đăng nhập
checkLogin('../../index.php');
$currentUser = getCurrentUser();

// Thiết lập thông tin trang
$pageTitle = 'Chỉnh sửa khách hàng';
$baseUrl = '../../';

// Lấy ID từ URL
$id = $_GET['id'] ?? 0;

if (empty($id)) {
    $_SESSION['error'] = 'Không tìm thấy khách hàng!';
    header('Location: ../guest.php');
    exit();
}

// Lấy thông tin khách hàng
$guest = getGuestById($id);

if (!$guest) {
    $_SESSION['error'] = 'Không tìm thấy khách hàng!';
    header('Location: ../guest.php');
    exit();
}

// Include layout header
include '../../layout/admin_header.php';
?>
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center mb-4">
                    <a href="../guest.php" class="btn btn-outline-secondary me-3">
                        <i class="bi bi-arrow-left"></i>
                    </a>
                    <div>
                        <h5 class="card-title mb-0"><i class="bi bi-person-gear me-2"></i>Chỉnh sửa khách hàng</h5>
                        <small class="text-muted">Cập nhật thông tin khách hàng</small>
                    </div>
                </div>

                <!-- Alert Messages -->
                <?php if (isset($_SESSION['error'])): ?>
                    <?= showAlert('danger', $_SESSION['error']) ?>
                    <?php unset($_SESSION['error']); ?>
                <?php endif; ?>

                <form action="../../handle/guest_process.php" method="POST">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="id" value="<?= e($guest['id']) ?>">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="full_name" class="form-label">Họ và tên <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="full_name" name="full_name" 
                                       value="<?= e($guest['full_name']) ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?= e($guest['email']) ?>" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="phone_number" class="form-label">Số điện thoại <span class="text-danger">*</span></label>
                                <input type="tel" class="form-control" id="phone_number" name="phone_number" 
                                       value="<?= e($guest['phone_number']) ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="id_card_number" class="form-label">Số CMND/CCCD <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="id_card_number" name="id_card_number" 
                                       value="<?= e($guest['id_card_number']) ?>" required>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="../guest.php" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-1"></i>Quay lại
                        </a>
                        <button type="submit" class="btn btn-warning">
                            <i class="bi bi-check-lg me-1"></i>Cập nhật khách hàng
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../../layout/admin_footer.php'; ?>