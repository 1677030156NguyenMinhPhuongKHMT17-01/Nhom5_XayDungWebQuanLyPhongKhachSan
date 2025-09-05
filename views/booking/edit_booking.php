<?php
require_once '../../functions/booking_functions.php';
require_once '../../functions/guest_functions.php';
require_once '../../functions/room_functions.php';
require_once '../menu.php';

// Lấy ID từ URL
$id = $_GET['id'] ?? 0;

if (empty($id)) {
    $_SESSION['error'] = 'Không tìm thấy đặt phòng!';
    header('Location: ../booking.php');
    exit();
}

// Lấy thông tin booking
$booking = getBookingById($id);

if (!$booking) {
    $_SESSION['error'] = 'Không tìm thấy đặt phòng!';
    header('Location: ../booking.php');
    exit();
}

// Lấy danh sách khách hàng và phòng
$guests = getAllGuests();
$rooms = getAllRooms();
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chỉnh sửa đặt phòng</title>
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
                            <i class="fas fa-calendar-edit"></i> Chỉnh sửa đặt phòng
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

                        <!-- Hiển thị thông tin hiện tại -->
                        <div class="alert alert-info">
                            <h6><i class="fas fa-info-circle"></i> Thông tin hiện tại:</h6>
                            <p class="mb-1"><strong>Khách hàng:</strong> <?= htmlspecialchars($booking['full_name']) ?></p>
                            <p class="mb-1"><strong>Phòng:</strong> <?= htmlspecialchars($booking['room_number']) ?> - <?= htmlspecialchars($booking['name_Room_Type']) ?></p>
                            <p class="mb-0"><strong>Tổng tiền:</strong> <?= number_format($booking['total_price'], 0, ',', '.') ?> VNĐ</p>
                        </div>

                        <?php if (empty($guests)): ?>
                            <div class="alert alert-warning" role="alert">
                                <i class="fas fa-exclamation-triangle"></i>
                                Chưa có khách hàng nào. Vui lòng <a href="../guest/create_guest.php">tạo khách hàng</a> trước.
                            </div>
                        <?php elseif (empty($rooms)): ?>
                            <div class="alert alert-warning" role="alert">
                                <i class="fas fa-exclamation-triangle"></i>
                                Chưa có phòng nào. Vui lòng <a href="../room/create_room.php">tạo phòng</a> trước.
                            </div>
                        <?php else: ?>
                            <form action="../../handle/booking_process.php" method="POST" id="editBookingForm">
                                <input type="hidden" name="action" value="update">
                                <input type="hidden" name="id" value="<?= $booking['id'] ?>">
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="guest_id" class="form-label">Khách hàng <span class="text-danger">*</span></label>
                                            <select class="form-select" id="guest_id" name="guest_id" required>
                                                <option value="">-- Chọn khách hàng --</option>
                                                <?php foreach ($guests as $guest): ?>
                                                    <option value="<?= $guest['id'] ?>" 
                                                            <?= ($booking['guest_id'] == $guest['id']) ? 'selected' : '' ?>>
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
                                                <?php foreach ($rooms as $room): ?>
                                                    <?php 
                                                    // Cho phép chọn phòng hiện tại hoặc phòng trống
                                                    $canSelect = ($room['id'] == $booking['room_id']) || ($room['status'] == 'available');
                                                    ?>
                                                    <option value="<?= $room['id'] ?>" 
                                                            data-price="<?= $room['price_per_night'] ?? 0 ?>"
                                                            <?= ($booking['room_id'] == $room['id']) ? 'selected' : '' ?>
                                                            <?= !$canSelect ? 'disabled' : '' ?>>
                                                        <?= htmlspecialchars($room['room_number']) ?> - 
                                                        <?= htmlspecialchars($room['name_Room_Type'] ?? 'N/A') ?> 
                                                        (<?= number_format($room['price_per_night'] ?? 0, 0, ',', '.') ?> VNĐ/đêm)
                                                        <?= !$canSelect ? ' - KHÔNG AVAILABLE' : '' ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label for="nights" class="form-label">Số đêm <span class="text-danger">*</span></label>
                                            <input type="number" class="form-control" id="nights" name="nights" 
                                                   placeholder="1" min="1" value="1" 
                                                   onchange="updatePrice()" required>
                                        </div>
                                    </div>
                                    <div class="col-md-5">
                                        <div class="mb-3">
                                            <label for="total_price" class="form-label">Tổng tiền (VNĐ) <span class="text-danger">*</span></label>
                                            <input type="number" class="form-control" id="total_price" name="total_price" 
                                                   placeholder="0" min="0" 
                                                   value="<?= $booking['total_price'] ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="status" class="form-label">Trạng thái <span class="text-danger">*</span></label>
                                            <select class="form-select" id="status" name="status" required>
                                                <option value="pending" <?= ($booking['status'] == 'pending') ? 'selected' : '' ?>>
                                                    Chờ xác nhận (Pending)
                                                </option>
                                                <option value="confirmed" <?= ($booking['status'] == 'confirmed') ? 'selected' : '' ?>>
                                                    Đã xác nhận (Confirmed)
                                                </option>
                                                <option value="cancelled" <?= ($booking['status'] == 'cancelled') ? 'selected' : '' ?>>
                                                    Đã hủy (Cancelled)
                                                </option>
                                                <option value="completed" <?= ($booking['status'] == 'completed') ? 'selected' : '' ?>>
                                                    Hoàn thành (Completed)
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between">
                                    <a href="../booking.php" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left"></i> Quay lại
                                    </a>
                                    <button type="submit" class="btn btn-warning">
                                        <i class="fas fa-save"></i> Cập nhật đặt phòng
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
            if (totalPrice > 0) {
                totalPriceInput.value = totalPrice;
            }
        }
        
        // Tính toán số đêm từ tổng tiền hiện tại
        document.addEventListener('DOMContentLoaded', function() {
            const roomSelect = document.getElementById('room_id');
            const nightsInput = document.getElementById('nights');
            const totalPriceInput = document.getElementById('total_price');
            
            const selectedRoom = roomSelect.options[roomSelect.selectedIndex];
            const pricePerNight = selectedRoom ? parseFloat(selectedRoom.getAttribute('data-price')) : 0;
            const currentTotalPrice = parseFloat(totalPriceInput.value) || 0;
            
            if (pricePerNight > 0 && currentTotalPrice > 0) {
                const calculatedNights = Math.round(currentTotalPrice / pricePerNight);
                nightsInput.value = calculatedNights > 0 ? calculatedNights : 1;
            }
        });
    </script>
    <script src="../../js/dark-mode.js"></script>
</body>

</html>
