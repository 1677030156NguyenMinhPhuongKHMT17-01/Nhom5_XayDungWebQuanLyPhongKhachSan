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
$pageTitle = 'Chỉnh sửa đặt phòng';
$baseUrl = '../../';

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
$availableRooms = getRoomsByStatus('available');
// Thêm phòng hiện tại vào danh sách nếu nó không có sẵn
$currentRoom = getRoomById($booking['room_id']);
if ($currentRoom && $currentRoom['status'] !== 'available') {
    $availableRooms[] = $currentRoom;
}

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
                        <h5 class="card-title mb-0"><i class="bi bi-calendar-event me-2"></i>Chỉnh sửa đặt phòng</h5>
                        <small class="text-muted">Cập nhật thông tin đặt phòng #<?= e($booking['id']) ?></small>
                    </div>
                </div>

                <!-- Thông tin booking hiện tại -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="alert alert-light border">
                            <h6><i class="bi bi-info-circle text-primary"></i> Thông tin hiện tại:</h6>
                            <div class="row">
                                <div class="col-md-3">
                                    <strong>Khách hàng:</strong><br>
                                    <span><?= e($booking['full_name']) ?></span><br>
                                    <small class="text-muted"><?= e($booking['email']) ?></small>
                                </div>
                                <div class="col-md-3">
                                    <strong>Phòng:</strong><br>
                                    <span>Phòng <?= e($booking['room_number']) ?></span><br>
                                    <small class="text-muted"><?= e($booking['name_Room_Type']) ?></small>
                                </div>
                                <div class="col-md-3">
                                    <strong>Ngày ở:</strong><br>
                                    <?php if (!empty($booking['check_in_date'])): ?>
                                        <span><?= date('d/m/Y', strtotime($booking['check_in_date'])) ?> - <?= date('d/m/Y', strtotime($booking['check_out_date'])) ?></span><br>
                                        <small class="text-muted"><?= $booking['nights'] ?> đêm</small>
                                    <?php else: ?>
                                        <span class="text-muted">Chưa có thông tin</span>
                                    <?php endif; ?>
                                </div>
                                <div class="col-md-3">
                                    <strong>Trạng thái:</strong><br>
                                    <?= getStatusBadge($booking['status'], 'booking') ?><br>
                                    <small class="text-muted"><?= formatCurrency($booking['total_price']) ?></small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Alert Messages -->
                <?php if (isset($_SESSION['error'])): ?>
                    <?= showAlert('danger', $_SESSION['error']) ?>
                    <?php unset($_SESSION['error']); ?>
                <?php endif; ?>

                <?php 
                // Kiểm tra xem booking có thể chỉnh sửa không
                $canEdit = !in_array($booking['status'], ['checked_in', 'checked_out']);
                $isCheckedIn = $booking['status'] === 'checked_in';
                $isCheckedOut = $booking['status'] === 'checked_out';
                ?>

                <?php if (!$canEdit): ?>
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle"></i> 
                        <strong>Lưu ý:</strong> Booking này đã <?= $isCheckedIn ? 'check-in' : 'check-out' ?>, 
                        chỉ có thể thay đổi trạng thái. Để thay đổi thông tin khác, vui lòng hủy và tạo booking mới.
                    </div>
                <?php endif; ?>

                <form action="../../handle/booking_process.php" method="POST">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="id" value="<?= e($booking['id']) ?>">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="guest_id" class="form-label">Khách hàng <span class="text-danger">*</span></label>
                                <select class="form-select" id="guest_id" name="guest_id" required <?= !$canEdit ? 'disabled' : '' ?>>
                                    <option value="">-- Chọn khách hàng --</option>
                                    <?php foreach ($guests as $guest): ?>
                                        <option value="<?= e($guest['id']) ?>" 
                                            <?= $booking['guest_id'] == $guest['id'] ? 'selected' : '' ?>>
                                            <?= e($guest['full_name']) ?> - <?= e($guest['email']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if (!$canEdit): ?>
                                    <input type="hidden" name="guest_id" value="<?= e($booking['guest_id']) ?>">
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="room_id" class="form-label">Phòng <span class="text-danger">*</span></label>
                                <select class="form-select" id="room_id" name="room_id" required <?= !$canEdit ? 'disabled' : '' ?>>
                                    <option value="">-- Chọn phòng --</option>
                                    <?php foreach ($availableRooms as $room): ?>
                                        <option value="<?= e($room['id']) ?>" 
                                            <?= $booking['room_id'] == $room['id'] ? 'selected' : '' ?>
                                            data-price="<?= e($room['price_per_night'] ?? 0) ?>">
                                            Phòng <?= e($room['room_number']) ?> - <?= e($room['name_Room_Type'] ?? 'N/A') ?>
                                            (<?= formatCurrency($room['price_per_night'] ?? 0) ?>/đêm)
                                            <?= $room['status'] !== 'available' ? ' - ' . ucfirst($room['status']) : '' ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if (!$canEdit): ?>
                                    <input type="hidden" name="room_id" value="<?= e($booking['room_id']) ?>">
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="check_in_date" class="form-label">Ngày check-in <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="check_in_date" name="check_in_date" 
                                       value="<?= e($booking['check_in_date'] ?? date('Y-m-d')) ?>" required <?= !$canEdit ? 'disabled' : '' ?>>
                                <?php if (!$canEdit): ?>
                                    <input type="hidden" name="check_in_date" value="<?= e($booking['check_in_date']) ?>">
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="check_out_date" class="form-label">Ngày check-out <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="check_out_date" name="check_out_date" 
                                       value="<?= e($booking['check_out_date'] ?? date('Y-m-d', strtotime('+1 day'))) ?>" required <?= !$canEdit ? 'disabled' : '' ?>>
                                <?php if (!$canEdit): ?>
                                    <input type="hidden" name="check_out_date" value="<?= e($booking['check_out_date']) ?>">
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="nights" class="form-label">Số đêm</label>
                                <input type="number" class="form-control" id="nights" name="nights" 
                                       value="<?= e($booking['nights'] ?? '') ?>" readonly>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="total_price" class="form-label">Tổng tiền (VNĐ) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="total_price" name="total_price" 
                                       value="<?= e($booking['total_price']) ?>" min="0" step="1000" required 
                                       <?= !$canEdit ? 'readonly' : 'readonly' ?>>
                                <?php if (!$canEdit): ?>
                                    <input type="hidden" name="total_price" value="<?= e($booking['total_price']) ?>">
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="status" class="form-label">Trạng thái <span class="text-danger">*</span></label>
                                <select class="form-select" id="status" name="status" required>
                                    <option value="pending" <?= $booking['status'] == 'pending' ? 'selected' : '' ?>>Chờ xác nhận</option>
                                    <option value="confirmed" <?= $booking['status'] == 'confirmed' ? 'selected' : '' ?>>Đã xác nhận</option>
                                    <option value="checked_in" <?= $booking['status'] == 'checked_in' ? 'selected' : '' ?>>Đã nhận phòng</option>
                                    <option value="checked_out" <?= $booking['status'] == 'checked_out' ? 'selected' : '' ?>>Đã trả phòng</option>
                                    <option value="cancelled" <?= $booking['status'] == 'cancelled' ? 'selected' : '' ?>>Đã hủy</option>
                                    <option value="no_show" <?= $booking['status'] == 'no_show' ? 'selected' : '' ?>>Không đến</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Hiển thị thông tin check-in/check-out thực tế -->
                    <?php if (!empty($booking['actual_check_in']) || !empty($booking['actual_check_out'])): ?>
                        <div class="row">
                            <div class="col-12">
                                <div class="alert alert-info">
                                    <h6><i class="bi bi-info-circle"></i> Thông tin Check-in/Check-out thực tế:</h6>
                                    <?php if (!empty($booking['actual_check_in'])): ?>
                                        <p class="mb-1"><strong>Check-in thực tế:</strong> <?= date('d/m/Y H:i', strtotime($booking['actual_check_in'])) ?></p>
                                    <?php endif; ?>
                                    <?php if (!empty($booking['actual_check_out'])): ?>
                                        <p class="mb-0"><strong>Check-out thực tế:</strong> <?= date('d/m/Y H:i', strtotime($booking['actual_check_out'])) ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="d-flex justify-content-between">
                        <a href="../booking.php" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-1"></i>Quay lại
                        </a>
                        <div class="btn-group">
                            <?php if ($booking['status'] === 'confirmed' && date('Y-m-d') >= $booking['check_in_date']): ?>
                                <a href="../checkin_dashboard.php" class="btn btn-success">
                                    <i class="bi bi-door-open me-1"></i>Check-in ngay
                                </a>
                            <?php endif; ?>
                            <?php if ($booking['status'] === 'checked_in'): ?>
                                <a href="../checkin_dashboard.php" class="btn btn-warning">
                                    <i class="bi bi-door-closed me-1"></i>Check-out ngay
                                </a>
                            <?php endif; ?>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg me-1"></i>Cập nhật đặt phòng
                            </button>
                        </div>
                    </div>
                </form>

                <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const checkInDate = document.getElementById('check_in_date');
                    const checkOutDate = document.getElementById('check_out_date');
                    const nightsField = document.getElementById('nights');
                    const roomSelect = document.getElementById('room_id');
                    const totalPriceField = document.getElementById('total_price');
                    const canEdit = <?= $canEdit ? 'true' : 'false' ?>;

                    // Set minimum dates chỉ khi có thể edit
                    if (canEdit) {
                        const today = new Date().toISOString().split('T')[0];
                        checkInDate.min = today;
                    }
                    
                    // Auto-calculate nights and total price
                    function calculateBooking() {
                        const checkIn = new Date(checkInDate.value);
                        const checkOut = new Date(checkOutDate.value);
                        const selectedRoom = roomSelect.options[roomSelect.selectedIndex];
                        
                        if (checkInDate.value && checkOutDate.value && checkOut > checkIn) {
                            const diffTime = Math.abs(checkOut - checkIn);
                            const nights = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                            nightsField.value = nights;
                            
                            // Update checkout min date chỉ khi có thể edit
                            if (canEdit) {
                                checkOutDate.min = checkInDate.value;
                            }
                            
                            // Calculate total price chỉ khi có thể edit
                            if (canEdit && selectedRoom && selectedRoom.dataset.price) {
                                const pricePerNight = parseFloat(selectedRoom.dataset.price);
                                const totalPrice = nights * pricePerNight;
                                totalPriceField.value = totalPrice;
                            }
                        } else {
                            nightsField.value = '';
                            if (canEdit) {
                                totalPriceField.value = '';
                            }
                        }
                    }

                    // Event listeners chỉ khi có thể edit
                    if (canEdit) {
                        checkInDate.addEventListener('change', calculateBooking);
                        checkOutDate.addEventListener('change', calculateBooking);
                        roomSelect.addEventListener('change', calculateBooking);
                    }
                    
                    // Initial calculation
                    calculateBooking();

                    // Validate form trước khi submit
                    document.querySelector('form').addEventListener('submit', function(e) {
                        const checkIn = new Date(checkInDate.value);
                        const checkOut = new Date(checkOutDate.value);
                        
                        if (checkOut <= checkIn) {
                            e.preventDefault();
                            alert('Ngày check-out phải sau ngày check-in!');
                            return false;
                        }
                    });
                });
                </script>
            </div>
        </div>
    </div>
</div>

<?php include '../../layout/admin_footer.php'; ?>