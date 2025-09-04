<?php
require_once '../functions/room_functions.php';
require_once 'menu.php';

// Xử lý tìm kiếm
$rooms = [];
$searchKeyword = '';

if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
    $searchKeyword = trim($_GET['search']);
    $rooms = searchRooms($searchKeyword);
} else {
    $rooms = getAllRooms();
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý phòng</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-4">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Quản lý phòng</h2>
                    <a href="room/create_room.php" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Thêm phòng mới
                    </a>
                </div>

                <!-- Form tìm kiếm -->
                <div class="card mb-4">
                    <div class="card-body">
                        <form method="GET" action="">
                            <div class="row">
                                <div class="col-md-10">
                                    <input type="text" class="form-control" name="search" 
                                           placeholder="Tìm kiếm theo số phòng, loại phòng, trạng thái..." 
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

                <!-- Bảng danh sách phòng -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            Danh sách phòng 
                            <?php if ($searchKeyword): ?>
                                <small class="text-muted">(Kết quả tìm kiếm cho: "<?= htmlspecialchars($searchKeyword) ?>")</small>
                            <?php endif; ?>
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($rooms)): ?>
                            <div class="text-center py-4">
                                <i class="fas fa-door-open fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">
                                    <?php if ($searchKeyword): ?>
                                        Không tìm thấy phòng nào phù hợp với từ khóa "<?= htmlspecialchars($searchKeyword) ?>"
                                    <?php else: ?>
                                        Chưa có phòng nào
                                    <?php endif; ?>
                                </h5>
                                <?php if ($searchKeyword): ?>
                                    <a href="room.php" class="btn btn-outline-primary">Xem tất cả phòng</a>
                                <?php else: ?>
                                    <a href="room/create_room.php" class="btn btn-primary">Thêm phòng đầu tiên</a>
                                <?php endif; ?>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>ID</th>
                                            <th>Số phòng</th>
                                            <th>Loại phòng</th>
                                            <th>Giá/đêm</th>
                                            <th>Sức chứa</th>
                                            <th>Trạng thái</th>
                                            <th>Thao tác</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($rooms as $room): ?>
                                            <tr>
                                                <td><?= $room['id'] ?></td>
                                                <td><?= htmlspecialchars($room['room_number']) ?></td>
                                                <td><?= htmlspecialchars($room['name_Room_Type'] ?? 'N/A') ?></td>
                                                <td><?= number_format($room['price_per_night'] ?? 0, 0, ',', '.') ?> VNĐ</td>
                                                <td><?= $room['capacity'] ?? 'N/A' ?> người</td>
                                                <td>
                                                    <?php
                                                    $statusClass = '';
                                                    $statusText = '';
                                                    switch ($room['status']) {
                                                        case 'available':
                                                            $statusClass = 'success';
                                                            $statusText = 'Trống';
                                                            break;
                                                        case 'occupied':
                                                            $statusClass = 'danger';
                                                            $statusText = 'Đã thuê';
                                                            break;
                                                        case 'maintenance':
                                                            $statusClass = 'warning';
                                                            $statusText = 'Bảo trì';
                                                            break;
                                                        default:
                                                            $statusClass = 'secondary';
                                                            $statusText = ucfirst($room['status']);
                                                    }
                                                    ?>
                                                    <span class="badge bg-<?= $statusClass ?>"><?= $statusText ?></span>
                                                </td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <a href="room/edit_room.php?id=<?= $room['id'] ?>" 
                                                           class="btn btn-sm btn-outline-warning">
                                                            <i class="fas fa-edit"></i> Sửa
                                                        </a>
                                                        <a href="../handle/room_process.php?action=delete&id=<?= $room['id'] ?>" 
                                                           class="btn btn-sm btn-outline-danger"
                                                           onclick="return confirm('Bạn có chắc chắn muốn xóa phòng này?')">
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
                                    <a href="room.php" class="btn btn-outline-secondary">Xem tất cả phòng</a>
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
