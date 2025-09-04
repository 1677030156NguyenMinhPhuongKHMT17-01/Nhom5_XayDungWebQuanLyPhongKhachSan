<?php
require_once '../functions/booking_functions.php';
require_once 'menu.php';

// Xử lý tìm kiếm
$bookings = [];
$searchKeyword = '';

if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
    $searchKeyword = trim($_GET['search']);
    $bookings = searchBookings($searchKeyword);
} else {
    $bookings = getAllBookings();
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý đặt phòng</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-4">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Quản lý đặt phòng</h2>
                    <a href="booking/create_booking.php" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Tạo đặt phòng mới
                    </a>
                </div>

                <!-- Form tìm kiếm -->
                <div class="card mb-4">
                    <div class="card-body">
                        <form method="GET" action="">
                            <div class="row">
                                <div class="col-md-10">
                                    <input type="text" class="form-control" name="search" 
                                           placeholder="Tìm kiếm theo tên khách hàng, email, số phòng, trạng thái..." 
                                           value="<?= htmlspecialchars($searchKeyword) ?>">
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-outline-primary w-100">
                                        <i class="fas fa-search"></i> Tìm kiếm
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Hiển thị thông báo -->
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?= $_SESSION['success'] ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php unset($_SESSION['success']); ?>
                <?php endif; ?>

                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?= $_SESSION['error'] ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php unset($_SESSION['error']); ?>
                <?php endif; ?>

                <!-- Bảng danh sách đặt phòng -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            Danh sách đặt phòng 
                            <?php if ($searchKeyword): ?>
                                <small class="text-muted">(Kết quả tìm kiếm cho: "<?= htmlspecialchars($searchKeyword) ?>")</small>
                            <?php endif; ?>
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($bookings)): ?>
                            <div class="text-center py-4">
                                <i class="fas fa-calendar-check fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">
                                    <?php if ($searchKeyword): ?>
                                        Không tìm thấy đặt phòng nào phù hợp với từ khóa "<?= htmlspecialchars($searchKeyword) ?>"
                                    <?php else: ?>
                                        Chưa có đặt phòng nào
                                    <?php endif; ?>
                                </h5>
                                <?php if ($searchKeyword): ?>
                                    <a href="booking.php" class="btn btn-outline-primary">Xem tất cả đặt phòng</a>
                                <?php else: ?>
                                    <a href="booking/create_booking.php" class="btn btn-primary">Tạo đặt phòng đầu tiên</a>
                                <?php endif; ?>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>ID</th>
                                            <th>Khách hàng</th>
                                            <th>Số phòng</th>
                                            <th>Loại phòng</th>
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
                                                <td><?= htmlspecialchars($booking['room_number'] ?? 'N/A') ?></td>
                                                <td><?= htmlspecialchars($booking['name_Room_Type'] ?? 'N/A') ?></td>
                                                <td><?= number_format($booking['total_price'] ?? 0, 0, ',', '.') ?> VNĐ</td>
                                                <td>
                                                    <?php
                                                    $statusClass = '';
                                                    $statusText = '';
                                                    switch ($booking['status']) {
                                                        case 'pending':
                                                            $statusClass = 'warning';
                                                            $statusText = 'Chờ xác nhận';
                                                            break;
                                                        case 'confirmed':
                                                            $statusClass = 'success';
                                                            $statusText = 'Đã xác nhận';
                                                            break;
                                                        case 'cancelled':
                                                            $statusClass = 'danger';
                                                            $statusText = 'Đã hủy';
                                                            break;
                                                        case 'completed':
                                                            $statusClass = 'info';
                                                            $statusText = 'Hoàn thành';
                                                            break;
                                                        default:
                                                            $statusClass = 'secondary';
                                                            $statusText = ucfirst($booking['status']);
                                                    }
                                                    ?>
                                                    <span class="badge bg-<?= $statusClass ?>"><?= $statusText ?></span>
                                                </td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <a href="booking/edit_booking.php?id=<?= $booking['id'] ?>" 
                                                           class="btn btn-sm btn-outline-warning">
                                                            <i class="fas fa-edit"></i> Sửa
                                                        </a>
                                                        <a href="../handle/booking_process.php?action=delete&id=<?= $booking['id'] ?>" 
                                                           class="btn btn-sm btn-outline-danger"
                                                           onclick="return confirm('Bạn có chắc chắn muốn xóa đặt phòng này?')">
                                                            <i class="fas fa-trash"></i> Xóa
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>

                            <?php if ($searchKeyword): ?>
                                <div class="text-center mt-3">
                                    <a href="booking.php" class="btn btn-outline-secondary">Xem tất cả đặt phòng</a>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://kit.fontawesome.com/your-fontawesome-kit.js"></script>
</body>

</html>
