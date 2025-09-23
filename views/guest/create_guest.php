<?php
require_once '../../functions/auth.php';
require_once '../../functions/guest_functions.php';

// Kiểm tra đăng nhập
checkLogin('../../index.php');
$currentUser = getCurrentUser();

// Thiết lập thông tin trang
$pageTitle = 'Thêm khách hàng mới';
$baseUrl = '../../';

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
                        <h5 class="card-title mb-0"><i class="bi bi-person-plus me-2"></i>Thêm khách hàng mới</h5>
                        <small class="text-muted">Điền thông tin khách hàng mới</small>
                    </div>
                </div>

                <!-- Hiển thị thông báo lỗi -->
                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-triangle me-2"></i><?= $_SESSION['error'] ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php unset($_SESSION['error']); ?>
                <?php endif; ?>

                <form action="../../handle/guest_process.php" method="POST">
                    <input type="hidden" name="action" value="create">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="full_name" class="form-label">Họ và tên <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="full_name" name="full_name" 
                                       value="<?= isset($_POST['full_name']) ? htmlspecialchars($_POST['full_name']) : '' ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="phone_number" class="form-label">Số điện thoại <span class="text-danger">*</span></label>
                                <input type="tel" class="form-control" id="phone_number" name="phone_number" 
                                       value="<?= isset($_POST['phone_number']) ? htmlspecialchars($_POST['phone_number']) : '' ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="id_card_number" class="form-label">Số CMND/CCCD <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="id_card_number" name="id_card_number" 
                                       value="<?= isset($_POST['id_card_number']) ? htmlspecialchars($_POST['id_card_number']) : '' ?>" required>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="../guest.php" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-1"></i>Quay lại
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-1"></i>Lưu khách hàng
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../../layout/admin_footer.php'; ?>
