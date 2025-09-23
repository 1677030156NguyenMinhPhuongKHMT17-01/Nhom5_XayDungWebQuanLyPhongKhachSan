<?php
require_once '../../functions/auth.php';
require_once '../../functions/booking_functions.php';
require_once '../../functions/guest_functions.php';
require_once '../../functions/room_functions.php';
require_once '../../functions/utils.php';

// Kiểm tra đăng nhập
checkLogin('../../index.php');
$currentUser = getCurrentUser();

// Thiết lập thông tin trang
$pageTitle = 'Tạo đặt phòng mới';
$baseUrl = '../../';

// Lấy danh sách khách hàng và phòng có sẵn
$guests = getAllGuests();
$availableRooms = getRoomsByStatus('available');

// Include layout header
include '../../layout/admin_header.php';
?>
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center mb-4">
                    <a href="../booking.php" class="btn btn-outline-secondary me-3">
                        <i class="bi bi-arrow-left"></i>
                    </a>
                    <div>
                        <h5 class="card-title mb-0"><i class="bi bi-calendar-plus me-2"></i>Tạo đặt phòng mới</h5>
                        <small class="text-muted">Đặt phòng cho khách hàng</small>
                    </div>
                </div>

                <!-- Alert Messages -->
                <?php if (isset($_SESSION['error'])): ?>
                    <?= showAlert('danger', $_SESSION['error']) ?>
                    <?php unset($_SESSION['error']); ?>
                <?php endif; ?>

                <?php if (empty($guests)): ?>
                    <div class="alert alert-warning" role="alert">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        Chưa có khách hàng nào. Vui lòng <a href="../guest/create_guest.php" class="alert-link">tạo khách hàng</a> trước khi đặt phòng.
                    </div>
                <?php elseif (empty($availableRooms)): ?>
                    <div class="alert alert-warning" role="alert">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        Không có phòng nào có sẵn. Vui lòng kiểm tra lại trạng thái phòng.
                    </div>
                <?php else: ?>
                    <form action="../../handle/booking_process.php" method="POST">
                        <input type="hidden" name="action" value="create">
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="guest_id" class="form-label">Khách hàng <span class="text-danger">*</span></label>
                                    <select class="form-select" id="guest_id" name="guest_id" required>
                                        <option value="">-- Chọn khách hàng --</option>
                                        <?php foreach ($guests as $guest): ?>
                                            <option value="<?= e($guest['id']) ?>" 
                                                <?= ($_POST['guest_id'] ?? '') == $guest['id'] ? 'selected' : '' ?>>
                                                <?= e($guest['full_name']) ?> - <?= e($guest['email']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="room_id" class="form-label">Phòng <span class="text-danger">*</span></label>
                                    <select class="form-select" id="room_id" name="room_id" required>
                                        <option value="">-- Chọn phòng --</option>
                                        <?php foreach ($availableRooms as $room): ?>
                                            <option value="<?= e($room['id']) ?>" 
                                                <?= ($_POST['room_id'] ?? '') == $room['id'] ? 'selected' : '' ?>
                                                data-price="<?= e($room['price_per_night'] ?? 0) ?>">
                                                Phòng <?= e($room['room_number']) ?> - <?= e($room['name_Room_Type'] ?? 'N/A') ?>
                                                (<?= formatCurrency($room['price_per_night'] ?? 0) ?>/đêm)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="check_in_date" class="form-label">Ngày check-in <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="check_in_date" name="check_in_date" 
                                           value="<?= e($_POST['check_in_date'] ?? date('Y-m-d')) ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="check_out_date" class="form-label">Ngày check-out <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="check_out_date" name="check_out_date" 
                                           value="<?= e($_POST['check_out_date'] ?? date('Y-m-d', strtotime('+1 day'))) ?>" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="nights" class="form-label">Số đêm</label>
                                    <input type="number" class="form-control" id="nights" name="nights" readonly>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="total_price" class="form-label">Tổng tiền (VNĐ) <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="total_price" name="total_price" 
                                           value="<?= e($_POST['total_price'] ?? '') ?>" min="0" step="1000" required readonly>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="status" class="form-label">Trạng thái <span class="text-danger">*</span></label>
                                    <select class="form-select" id="status" name="status" required>
                                        <option value="pending" <?= ($_POST['status'] ?? '') == 'pending' ? 'selected' : '' ?>>Chờ xác nhận</option>
                                        <option value="confirmed" <?= ($_POST['status'] ?? '') == 'confirmed' ? 'selected' : '' ?>>Đã xác nhận</option>
                                        <option value="checked_in" <?= ($_POST['status'] ?? '') == 'checked_in' ? 'selected' : '' ?>>Đã nhận phòng</option>
                                        <option value="checked_out" <?= ($_POST['status'] ?? '') == 'checked_out' ? 'selected' : '' ?>>Đã trả phòng</option>
                                        <option value="cancelled" <?= ($_POST['status'] ?? '') == 'cancelled' ? 'selected' : '' ?>>Đã hủy</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="../booking.php" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-1"></i>Quay lại
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg me-1"></i>Tạo đặt phòng
                            </button>
                        </div>
                    </form>

                    <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const checkInDate = document.getElementById('check_in_date');
                        const checkOutDate = document.getElementById('check_out_date');
                        const nightsField = document.getElementById('nights');
                        const roomSelect = document.getElementById('room_id');
                        const totalPriceField = document.getElementById('total_price');

                        // Set minimum dates
                        const today = new Date().toISOString().split('T')[0];
                        checkInDate.min = today;
                        
                        // Auto-calculate nights and total price
                        function calculateBooking() {
                            const checkIn = new Date(checkInDate.value);
                            const checkOut = new Date(checkOutDate.value);
                            const selectedRoom = roomSelect.options[roomSelect.selectedIndex];
                            
                            if (checkInDate.value && checkOutDate.value && checkOut > checkIn) {
                                const diffTime = Math.abs(checkOut - checkIn);
                                const nights = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                                nightsField.value = nights;
                                
                                // Update checkout min date
                                checkOutDate.min = checkInDate.value;
                                
                                // Calculate total price
                                if (selectedRoom && selectedRoom.dataset.price) {
                                    const pricePerNight = parseFloat(selectedRoom.dataset.price);
                                    const totalPrice = nights * pricePerNight;
                                    totalPriceField.value = totalPrice;
                                }
                            } else {
                                nightsField.value = '';
                                totalPriceField.value = '';
                            }
                        }

                        // Event listeners
                        checkInDate.addEventListener('change', calculateBooking);
                        checkOutDate.addEventListener('change', calculateBooking);
                        roomSelect.addEventListener('change', calculateBooking);
                        
                        // Initial calculation
                        calculateBooking();
                    });
                    </script>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include '../../layout/admin_footer.php'; ?>