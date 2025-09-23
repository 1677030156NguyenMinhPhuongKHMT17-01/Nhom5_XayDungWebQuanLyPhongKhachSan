<?php
require_once '../functions/auth.php';
require_once '../functions/guest_functions.php';
require_once '../functions/utils.php';

// Kiểm tra đăng nhập
checkLogin('../index.php');
$currentUser = getCurrentUser();

// Thiết lập thông tin trang
$pageTitle = 'Quản lý khách hàng';
$baseUrl = '../';

// Xử lý tìm kiếm
$searchKeyword = trim($_GET['search'] ?? '');
$guests = $searchKeyword ? searchGuests($searchKeyword) : getAllGuests();

// Include layout header
include '../layout/admin_header.php';
?>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="card-title"><i class="bi bi-people me-2"></i>Danh sách khách hàng</h5>
                    <a href="guest/create_guest.php" class="btn btn-primary">
                        <i class="bi bi-person-plus me-1"></i>Thêm khách hàng
                    </a>
                </div>

                <!-- Search Form - sử dụng utility function -->
                <?= searchForm($searchKeyword, 'Tìm kiếm theo tên, email, số điện thoại...', 'guest.php') ?>

                <!-- Alert Messages -->
                <?php if (isset($_SESSION['success'])): ?>
                    <?= showAlert('success', $_SESSION['success']) ?>
                    <?php unset($_SESSION['success']); ?>
                <?php endif; ?>

                <?php if (isset($_SESSION['error'])): ?>
                    <?= showAlert('danger', $_SESSION['error']) ?>
                    <?php unset($_SESSION['error']); ?>
                <?php endif; ?>

                <!-- Data Table or Empty State -->
                <?php if (empty($guests)): ?>
                    <?= emptyState(
                        'bi bi-people',
                        $searchKeyword ? 'Không tìm thấy khách hàng nào' : 'Chưa có khách hàng nào',
                        $searchKeyword ? "Không tìm thấy khách hàng phù hợp với \"$searchKeyword\"" : 'Hãy thêm khách hàng đầu tiên để bắt đầu',
                        'guest/create_guest.php',
                        'Thêm khách hàng mới'
                    ) ?>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped datatable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Họ và tên</th>
                                    <th>Email</th>
                                    <th>Số điện thoại</th>
                                    <th>CMND/CCCD</th>
                                    <th>Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($guests as $guest): ?>
                                    <tr>
                                        <td><?= e($guest['id']) ?></td>
                                        <td><strong><?= e($guest['full_name']) ?></strong></td>
                                        <td><?= e($guest['email']) ?></td>
                                        <td><?= e($guest['phone_number']) ?></td>
                                        <td><?= e($guest['id_card_number']) ?></td>
                                        <td>
                                            <?= getActionButtons(
                                                $guest['id'],
                                                "guest/edit_guest.php?id={$guest['id']}",
                                                "../handle/guest_process.php",
                                                $guest['full_name']
                                            ) ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Script -->
<?= deleteConfirmScript() ?>

<?php include '../layout/admin_footer.php'; ?>