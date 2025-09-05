<?php
require_once '../../functions/booking_functions.php';
require_once '../../functions/guest_functions.php';
require_once '../../functions/room_functions.php';
require_once '../menu.php';

// Lấy danh sách khách hàng và phòng trống
$guests = getAllGuests();
$availableRooms = getAvailableRooms();
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tạo đặt phòng mới</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../../css/dark-mode.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">
                            <i class="fas fa-calendar-plus"></i> Tạo đặt phòng mới
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

                        <?php if (empty($guests)): ?>
                            <div class="alert alert-warning" role="alert">
                                <i class="fas fa-exclamation-triangle"></i>
                                Chưa có khách hàng nào. Vui lòng <a href="../guest/create_guest.php">tạo khách hàng</a> trước khi đặt phòng.
                            </div>
                        <?php elseif (empty($availableRooms)): ?>
                            <div class="alert alert-warning" role="alert">
                                <i class="fas fa-exclamation-triangle"></i>
                                Hiện tại không có phòng trống nào. Vui lòng kiểm tra lại sau.
                            </div>
                        <?php else: ?>
                            <form action="../../handle/booking_process.php" method="POST" id="bookingForm">
                                <input type="hidden" name="action" value="create">
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="guest_id" class="form-label">Khách hàng <span class="text-danger">*</span></label>
                                            <select class="form-select" id="guest_id" name="guest_id" required>
                                                <option value="">-- Chọn khách hàng --</option>
                                                <?php foreach ($guests as $guest): ?>
                                                    <option value="<?= $guest['id'] ?>" 
                                                            <?= (isset($_POST['guest_id']) && $_POST['guest_id'] == $guest['id']) ? 'selected' : '' ?>>
                                                        <?= htmlspecialchars($guest['full_name']) ?> - <?= htmlspecialchars($guest['phone_number']) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="room_id" class="form-label">Phòng <span class="text-danger">*</span></label>
                                            <select class="form-select" id="room_id" name="room_id" required onchange="updatePrice()">
                                                <option value="">-- Chọn phòng --</option>
                                                <?php foreach ($availableRooms as $room): ?>
                                                    <option value="<?= $room['id'] ?>" 
                                                            data-price="<?= $room['price_per_night'] ?>"
                                                            <?= (isset($_POST['room_id']) && $_POST['room_id'] == $room['id']) ? 'selected' : '' ?>>
                                                        <?= htmlspecialchars($room['room_number']) ?> - 
                                                        <?= htmlspecialchars($room['name_Room_Type']) ?> 
                                                        (<?= number_format($room['price_per_night'], 0, ',', '.') ?> VNĐ/đêm)
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="nights" class="form-label">Số đêm <span class="text-danger">*</span></label>
                                            <input type="number" class="form-control" id="nights" name="nights" 
                                                   placeholder="1" min="1" value="1" 
                                                   onchange="updatePrice()" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="total_price" class="form-label">Tổng tiền (VNĐ) <span class="text-danger">*</span></label>
                                            <input type="number" class="form-control" id="total_price" name="total_price" 
                                                   placeholder="0" min="0" readonly required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="status" class="form-label">Trạng thái <span class="text-danger">*</span></label>
                                            <select class="form-select" id="status" name="status" required>
                                                <option value="pending" <?= (isset($_POST['status']) && $_POST['status'] == 'pending') ? 'selected' : 'selected' ?>>
                                                    Chờ xác nhận (Pending)
                                                </option>
                                                <option value="confirmed" <?= (isset($_POST['status']) && $_POST['status'] == 'confirmed') ? 'selected' : '' ?>>
                                                    Đã xác nhận (Confirmed)
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between">
                                    <a href="../booking.php" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left"></i> Quay lại
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Tạo đặt phòng
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
    <script>
        function updatePrice() {
            const roomSelect = document.getElementById('room_id');
            const nightsInput = document.getElementById('nights');
            const totalPriceInput = document.getElementById('total_price');
            
            const selectedRoom = roomSelect.options[roomSelect.selectedIndex];
            const pricePerNight = selectedRoom ? parseFloat(selectedRoom.getAttribute('data-price')) : 0;
            const nights = parseInt(nightsInput.value) || 1;
            
            const totalPrice = pricePerNight * nights;
            totalPriceInput.value = totalPrice;
        }
        
        // Cập nhật giá khi trang load
        document.addEventListener('DOMContentLoaded', function() {
            updatePrice();
        });
    </script>
    <script src="../../js/dark-mode.js"></script>
</body>

</html>
