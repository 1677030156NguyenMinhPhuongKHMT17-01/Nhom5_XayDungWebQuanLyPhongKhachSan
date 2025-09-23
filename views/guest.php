<?php
require_once '../functions/auth.php';
require_once '../functions/guest_functions.php';

// Kiểm tra đăng nhập
checkLogin('../index.php');
$currentUser = getCurrentUser();

// Thiết lập thông tin trang
$pageTitle = 'Quản lý khách hàng';
$baseUrl = '../';

// Xử lý tìm kiếm
$guests = [];
$searchKeyword = '';

if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
    $searchKeyword = trim($_GET['search']);
    $guests = searchGuests($searchKeyword);
} else {
    $guests = getAllGuests();
}

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

                <!-- Search Form -->
                <form method="GET" class="mb-4">
                    <div class="row">
                        <div class="col-md-8">
                            <input type="text" name="search" class="form-control" 
                                   placeholder="Tìm kiếm theo tên, email, số điện thoại..." 
                                   value="<?= htmlspecialchars($searchKeyword) ?>">
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-outline-primary me-2">
                                <i class="bi bi-search me-1"></i>Tìm kiếm
                            </button>
                            <?php if ($searchKeyword): ?>
                                <a href="guest.php" class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-clockwise me-1"></i>Reset
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </form>

                <!-- Hiển thị thông báo -->
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle me-2"></i><?= $_SESSION['success'] ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php unset($_SESSION['success']); ?>
                <?php endif; ?>

                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-triangle me-2"></i><?= $_SESSION['error'] ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php unset($_SESSION['error']); ?>
                <?php endif; ?>

                <?php if ($searchKeyword): ?>
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        Kết quả tìm kiếm cho: "<strong><?= htmlspecialchars($searchKeyword) ?></strong>"
                    </div>
                <?php endif; ?>

                <?php if (empty($guests)): ?>
                    <div class="text-center py-5">
                        <i class="bi bi-people text-muted" style="font-size: 4rem;"></i>
                        <h5 class="text-muted mt-3">
                            <?php if ($searchKeyword): ?>
                                Không tìm thấy khách hàng nào phù hợp với từ khóa "<?= htmlspecialchars($searchKeyword) ?>"
                            <?php else: ?>
                                Chưa có khách hàng nào
                            <?php endif; ?>
                        </h5>
                        <a href="guest/create_guest.php" class="btn btn-primary mt-3">
                            <i class="bi bi-person-plus me-1"></i>Thêm khách hàng đầu tiên
                        </a>
                    </div>
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
                                    <th>Ngày tạo</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($guests as $guest): ?>
                                    <tr>
                                        <td><?= $guest['id'] ?></td>
                                        <td><?= htmlspecialchars($guest['full_name']) ?></td>
                                        <td><?= htmlspecialchars($guest['email']) ?></td>
                                        <td><?= htmlspecialchars($guest['phone_number']) ?></td>
                                        <td><?= htmlspecialchars($guest['id_card_number']) ?></td>
                                        <td><?= date('d/m/Y', strtotime($guest['created_at'])) ?></td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="guest/edit_guest.php?id=<?= $guest['id'] ?>" 
                                                   class="btn btn-sm btn-outline-primary" title="Sửa">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-outline-danger" 
                                                        title="Xóa" onclick="confirmDelete(<?= $guest['id'] ?>, '<?= addslashes($guest['full_name']) ?>')">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <?php if ($searchKeyword): ?>
                        <div class="text-center mt-3">
                            <a href="guest.php" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-1"></i>Xem tất cả khách hàng
                            </a>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDelete(id, name) {
    if (confirm('Bạn có chắc chắn muốn xóa khách hàng "' + name + '"?')) {
        window.location.href = '../handle/guest_process.php?action=delete&id=' + id;
    }
}
</script>

<?php include '../layout/admin_footer.php'; ?>
