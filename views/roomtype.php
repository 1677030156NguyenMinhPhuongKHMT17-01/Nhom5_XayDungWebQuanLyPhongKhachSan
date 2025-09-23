<?php
require_once '../functions/auth.php';
require_once '../functions/roomtype_functions.php';

// Kiểm tra đăng nhập
checkLogin('../index.php');
$currentUser = getCurrentUser();

// Thiết lập thông tin trang
$pageTitle = 'Quản lý loại phòng';
$baseUrl = '../';

// Xử lý tìm kiếm
$roomTypes = [];
$searchKeyword = '';

if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
    $searchKeyword = trim($_GET['search']);
    $roomTypes = searchRoomTypes($searchKeyword);
} else {
    $roomTypes = getAllRoomTypes();
}

// Include layout header
include '../layout/admin_header.php';
?>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="card-title"><i class="bi bi-buildings me-2"></i>Danh sách loại phòng</h5>
                    <a href="roomtype/create_roomtype.php" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-1"></i>Thêm loại phòng
                    </a>
                </div>

                <!-- Search Form -->
                <form method="GET" class="mb-4">
                    <div class="row">
                        <div class="col-md-8">
                            <input type="text" name="search" class="form-control" 
                                   placeholder="Tìm kiếm theo tên loại phòng..." 
                                   value="<?= htmlspecialchars($searchKeyword) ?>">
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-outline-primary me-2">
                                <i class="bi bi-search me-1"></i>Tìm kiếm
                            </button>
                            <?php if ($searchKeyword): ?>
                                <a href="roomtype.php" class="btn btn-outline-secondary">
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

                <?php if (empty($roomTypes)): ?>
                    <div class="text-center py-5">
                        <i class="bi bi-buildings text-muted" style="font-size: 4rem;"></i>
                        <h5 class="text-muted mt-3">
                            <?php if ($searchKeyword): ?>
                                Không tìm thấy loại phòng nào phù hợp với từ khóa "<?= htmlspecialchars($searchKeyword) ?>"
                            <?php else: ?>
                                Chưa có loại phòng nào
                            <?php endif; ?>
                        </h5>
                        <a href="roomtype/create_roomtype.php" class="btn btn-primary mt-3">
                            <i class="bi bi-plus-circle me-1"></i>Thêm loại phòng đầu tiên
                        </a>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped datatable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Tên loại phòng</th>
                                    <th>Giá mỗi đêm</th>
                                    <th>Sức chứa</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($roomTypes as $roomType): ?>
                                    <tr>
                                        <td><?= $roomType['id'] ?></td>
                                        <td><strong><?= htmlspecialchars($roomType['name_Room_Type']) ?></strong></td>
                                        <td class="text-success fw-bold"><?= number_format($roomType['price_per_night'], 0, ',', '.') ?> VNĐ</td>
                                        <td><i class="bi bi-people me-1"></i><?= $roomType['capacity'] ?> người</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="roomtype/edit_roomtype.php?id=<?= $roomType['id'] ?>" 
                                                   class="btn btn-sm btn-outline-primary" title="Sửa">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-outline-danger" 
                                                        title="Xóa" onclick="confirmDelete(<?= $roomType['id'] ?>, '<?= addslashes($roomType['name_Room_Type']) ?>')">
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

<script>
function confirmDelete(id, name) {
    if (confirm('Bạn có chắc chắn muốn xóa loại phòng "' + name + '"?')) {
        window.location.href = '../handle/roomtype_process.php?action=delete&id=' + id;
    }
}
</script>

<?php include '../layout/admin_footer.php'; ?>
