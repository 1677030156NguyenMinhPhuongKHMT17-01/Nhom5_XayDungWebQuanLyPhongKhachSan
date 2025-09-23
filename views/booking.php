<?php
require_once '../functions/auth.php';
require_once '../functions/booking_functions.php';

// Kiểm tra đăng nhập
checkLogin('../index.php');
$currentUser = getCurrentUser();

// Thiết lập thông tin trang
$pageTitle = 'Quản lý đặt phòng';
$baseUrl = '../';

// Xử lý tìm kiếm
$bookings = [];
$searchKeyword = '';

if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
    $searchKeyword = trim($_GET['search']);
    $bookings = searchBookings($searchKeyword);
} else {
    $bookings = getAllBookings();
}

// Include layout header
include '../layout/admin_header.php';
?>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="card-title"><i class="bi bi-calendar-check me-2"></i>Danh sách đặt phòng</h5>
                    <a href="booking/create_booking.php" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-1"></i>Tạo đặt phòng
                    </a>
                </div>

                <!-- Search Form -->
                <form method="GET" class="mb-4">
                    <div class="row">
                        <div class="col-md-8">
                            <input type="text" name="search" class="form-control" 
                                   placeholder="Tìm kiếm theo tên khách hàng, email, số phòng, trạng thái..." 
                                   value="<?= htmlspecialchars($searchKeyword) ?>">
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-outline-primary me-2">
                                <i class="bi bi-search me-1"></i>Tìm kiếm
                            </button>
                            <?php if ($searchKeyword): ?>
                                <a href="booking.php" class="btn btn-outline-secondary">
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

                <?php if (empty($bookings)): ?>
                    <div class="text-center py-5">
                        <i class="bi bi-calendar-check text-muted" style="font-size: 4rem;"></i>
                        <h5 class="text-muted mt-3">
                            <?php if ($searchKeyword): ?>
                                Không tìm thấy đặt phòng nào phù hợp với từ khóa "<?= htmlspecialchars($searchKeyword) ?>"
                            <?php else: ?>
                                Chưa có đặt phòng nào
                            <?php endif; ?>
                        </h5>
                        <a href="booking/create_booking.php" class="btn btn-primary mt-3">
                            <i class="bi bi-plus-circle me-1"></i>Tạo đặt phòng đầu tiên
                        </a>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped datatable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Khách hàng</th>
                                    <th>Số phòng</th>
                                    <th>Loại phòng</th>
                                    <th>Ngày đến</th>
                                    <th>Ngày đi</th>
                                    <th>Tổng tiền</th>
                                    <th>Trạng thái</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($bookings as $booking): ?>
                                    <tr>
                                        <td><?= $booking['id'] ?></td>
                                        <td>
                                            <div>
                                                <strong><?= htmlspecialchars($booking['full_name'] ?? 'N/A') ?></strong><br>
                                                <small class="text-muted"><?= htmlspecialchars($booking['email'] ?? 'N/A') ?></small>
                                            </div>
                                        </td>
                                        <td><strong><?= htmlspecialchars($booking['room_number'] ?? 'N/A') ?></strong></td>
                                        <td><?= htmlspecialchars($booking['name_Room_Type'] ?? 'N/A') ?></td>
                                        <td><?= isset($booking['check_in_date']) ? date('d/m/Y', strtotime($booking['check_in_date'])) : 'N/A' ?></td>
                                        <td><?= isset($booking['check_out_date']) ? date('d/m/Y', strtotime($booking['check_out_date'])) : 'N/A' ?></td>
                                        <td class="text-success fw-bold"><?= number_format($booking['total_price'] ?? 0, 0, ',', '.') ?> VNĐ</td>
                                        <td>
                                            <?php
                                            $statusClass = '';
                                            $statusText = '';
                                            $statusIcon = '';
                                            switch ($booking['status']) {
                                                case 'pending':
                                                    $statusClass = 'warning';
                                                    $statusText = 'Chờ xác nhận';
                                                    $statusIcon = 'bi-clock';
                                                    break;
                                                case 'confirmed':
                                                    $statusClass = 'success';
                                                    $statusText = 'Đã xác nhận';
                                                    $statusIcon = 'bi-check-circle';
                                                    break;
                                                case 'cancelled':
                                                    $statusClass = 'danger';
                                                    $statusText = 'Đã hủy';
                                                    $statusIcon = 'bi-x-circle';
                                                    break;
                                                case 'completed':
                                                    $statusClass = 'info';
                                                    $statusText = 'Hoàn thành';
                                                    $statusIcon = 'bi-check-all';
                                                    break;
                                                default:
                                                    $statusClass = 'secondary';
                                                    $statusText = ucfirst($booking['status']);
                                                    $statusIcon = 'bi-question-circle';
                                            }
                                            ?>
                                            <span class="badge bg-<?= $statusClass ?>">
                                                <i class="<?= $statusIcon ?> me-1"></i><?= $statusText ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="booking/edit_booking.php?id=<?= $booking['id'] ?>" 
                                                   class="btn btn-sm btn-outline-primary" title="Sửa">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-outline-danger" 
                                                        title="Xóa" onclick="confirmDelete(<?= $booking['id'] ?>, '<?= addslashes($booking['full_name'] ?? 'N/A') ?>')">
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
                            <a href="booking.php" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-1"></i>Xem tất cả đặt phòng
                            </a>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDelete(id, guestName) {
    if (confirm('Bạn có chắc chắn muốn xóa đặt phòng của "' + guestName + '"?')) {
        window.location.href = '../handle/booking_process.php?action=delete&id=' + id;
    }
}
</script>

<?php include '../layout/admin_footer.php'; ?>
