<?php
require_once '../functions/auth.php';
require_once '../functions/checkin_functions.php';
require_once '../functions/utils.php';

// Kiểm tra đăng nhập
checkLogin('../index.php');
$currentUser = getCurrentUser();

// Thiết lập thông tin trang
$pageTitle = 'Check-in/Check-out Dashboard';
$baseUrl = '../';

// Lấy dữ liệu
$todayCheckIns = getTodayCheckIns();
$todayCheckOuts = getTodayCheckOuts();
$occupancyStats = getOccupancyRate();

// Include layout header
include '../layout/admin_header.php';
?>

<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="bi bi-door-open-fill text-primary"></i> Check-in/Check-out Dashboard
        </h1>
        <small class="text-muted">Quản lý check-in và check-out hôm nay</small>
    </div>

    <!-- Alert Messages -->
    <?php if (isset($_SESSION['success'])): ?>
        <?= showAlert('success', $_SESSION['success']) ?>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <?= showAlert('danger', $_SESSION['error']) ?>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Tỷ lệ lấp đầy
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= $occupancyStats['occupancy_rate'] ?>%
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-percentage fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Phòng trống
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= $occupancyStats['available_rooms'] ?>/<?= $occupancyStats['total_rooms'] ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-bed fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Check-in hôm nay
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= count($todayCheckIns) ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-sign-in-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Check-out hôm nay
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= count($todayCheckOuts) ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-sign-out-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Check-ins Today -->
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="bi bi-box-arrow-in-right"></i> Check-in hôm nay
                    </h6>
                    <span class="badge bg-primary"><?= count($todayCheckIns) ?></span>
                </div>
                <div class="card-body">
                    <?php if (empty($todayCheckIns)): ?>
                        <div class="text-center text-muted py-3">
                            <i class="bi bi-calendar-x fa-3x mb-3"></i>
                            <p>Không có check-in nào hôm nay</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Khách hàng</th>
                                        <th>Phòng</th>
                                        <th>Loại phòng</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($todayCheckIns as $checkin): ?>
                                        <tr>
                                            <td>
                                                <strong><?= e($checkin['full_name']) ?></strong><br>
                                                <small class="text-muted"><?= e($checkin['phone_number']) ?></small>
                                            </td>
                                            <td><span class="badge bg-info"><?= e($checkin['room_number']) ?></span></td>
                                            <td><?= e($checkin['name_Room_Type']) ?></td>
                                            <td>
                                                <button class="btn btn-success btn-sm" 
                                                        onclick="performCheckIn(<?= $checkin['id'] ?>)">
                                                    <i class="bi bi-check-circle"></i> Check-in
                                                </button>
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

        <!-- Check-outs Today -->
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-warning">
                        <i class="bi bi-box-arrow-right"></i> Check-out hôm nay
                    </h6>
                    <span class="badge bg-warning"><?= count($todayCheckOuts) ?></span>
                </div>
                <div class="card-body">
                    <?php if (empty($todayCheckOuts)): ?>
                        <div class="text-center text-muted py-3">
                            <i class="bi bi-calendar-x fa-3x mb-3"></i>
                            <p>Không có check-out nào hôm nay</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Khách hàng</th>
                                        <th>Phòng</th>
                                        <th>Loại phòng</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($todayCheckOuts as $checkout): ?>
                                        <tr>
                                            <td>
                                                <strong><?= e($checkout['full_name']) ?></strong><br>
                                                <small class="text-muted"><?= e($checkout['phone_number']) ?></small>
                                            </td>
                                            <td><span class="badge bg-warning"><?= e($checkout['room_number']) ?></span></td>
                                            <td><?= e($checkout['name_Room_Type']) ?></td>
                                            <td>
                                                <button class="btn btn-warning btn-sm" 
                                                        onclick="performCheckOut(<?= $checkout['id'] ?>)">
                                                    <i class="bi bi-box-arrow-right"></i> Check-out
                                                </button>
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
</div>

<script>
function performCheckIn(bookingId) {
    if (confirm('Xác nhận check-in cho booking này?')) {
        fetch('../handle/checkin_process.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: `action=checkin&booking_id=${bookingId}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert('Lỗi: ' + data.message);
            }
        })
        .catch(error => {
            alert('Có lỗi xảy ra: ' + error);
        });
    }
}

function performCheckOut(bookingId) {
    const additionalCharges = prompt('Nhập phí phát sinh (nếu có):', '0');
    if (additionalCharges !== null) {
        fetch('../handle/checkin_process.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: `action=checkout&booking_id=${bookingId}&additional_charges=${additionalCharges}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert('Lỗi: ' + data.message);
            }
        })
        .catch(error => {
            alert('Có lỗi xảy ra: ' + error);
        });
    }
}
</script>

<?php include '../layout/admin_footer.php'; ?>