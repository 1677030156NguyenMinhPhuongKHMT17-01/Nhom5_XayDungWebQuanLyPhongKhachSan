<?php
require_once '../functions/auth.php';
require_once '../functions/report_functions.php';
require_once '../functions/utils.php';

// Kiểm tra đăng nhập
checkLogin('../index.php');
$currentUser = getCurrentUser();

// Thiết lập thông tin trang
$pageTitle = 'Báo cáo và Thống kê';
$baseUrl = '../';

// Lấy tham số
$year = $_GET['year'] ?? date('Y');
$month = $_GET['month'] ?? date('m');

// Lấy dữ liệu báo cáo
$generalStats = getGeneralStats();
$monthlyRevenue = getRevenueByMonth($year, $month);
$occupancyStats = getOccupancyRateByMonth($year, $month);
$topCustomers = getTopCustomers(5);
$popularRoomTypes = getPopularRoomTypes();
$bookingStatus = getBookingsByStatus();
$yearlyRevenue = getRevenueByYear($year);

// Include layout header
include '../layout/admin_header.php';
?>

<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="bi bi-bar-chart-fill text-primary"></i> Báo cáo và Thống kê
        </h1>
        <div class="btn-group">
            <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown">
                <i class="bi bi-calendar"></i> Tháng <?= $month ?>/<?= $year ?>
            </button>
            <ul class="dropdown-menu">
                <?php for($m = 1; $m <= 12; $m++): ?>
                    <li><a class="dropdown-item" href="?year=<?= $year ?>&month=<?= $m ?>">
                        Tháng <?= $m ?>/<?= $year ?>
                    </a></li>
                <?php endfor; ?>
            </ul>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Doanh thu hôm nay
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= formatCurrency($generalStats['today_revenue']) ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Doanh thu tháng <?= $month ?>
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= formatCurrency($generalStats['month_revenue']) ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Tỷ lệ lấp đầy
                            </div>
                            <div class="row no-gutters align-items-center">
                                <div class="col-auto">
                                    <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800">
                                        <?= $occupancyStats['occupancy_rate'] ?>%
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="progress progress-sm mr-2">
                                        <div class="progress-bar bg-info" style="width: <?= $occupancyStats['occupancy_rate'] ?>%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Phòng đang sử dụng
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= $generalStats['occupied_rooms'] ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-bed fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Biểu đồ doanh thu theo tháng -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Doanh thu năm <?= $year ?></h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Phân bố trạng thái booking -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Trạng thái Booking</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4 pb-2">
                        <canvas id="statusChart"></canvas>
                    </div>
                    <div class="mt-4 text-center small">
                        <?php foreach ($bookingStatus as $status): ?>
                            <span class="mr-2">
                                <i class="fas fa-circle text-primary"></i> <?= ucfirst($status['status']) ?>: <?= $status['count'] ?>
                            </span>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Top khách hàng VIP -->
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Top Khách hàng VIP</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Khách hàng</th>
                                    <th>Số booking</th>
                                    <th>Tổng chi tiêu</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($topCustomers as $customer): ?>
                                    <tr>
                                        <td>
                                            <strong><?= e($customer['full_name']) ?></strong><br>
                                            <small class="text-muted"><?= e($customer['email']) ?></small>
                                        </td>
                                        <td><span class="badge bg-info"><?= $customer['total_bookings'] ?></span></td>
                                        <td><strong><?= formatCurrency($customer['total_spent']) ?></strong></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Loại phòng phổ biến -->
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Loại phòng phổ biến</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Loại phòng</th>
                                    <th>Số booking</th>
                                    <th>Doanh thu</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($popularRoomTypes as $roomType): ?>
                                    <tr>
                                        <td>
                                            <strong><?= e($roomType['name_Room_Type']) ?></strong><br>
                                            <small class="text-muted"><?= formatCurrency($roomType['price_per_night']) ?>/đêm</small>
                                        </td>
                                        <td><span class="badge bg-success"><?= $roomType['bookings_count'] ?: 0 ?></span></td>
                                        <td><strong><?= formatCurrency($roomType['total_revenue'] ?: 0) ?></strong></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Thống kê chi tiết tháng -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        Chi tiết doanh thu tháng <?= $month ?>/<?= $year ?>
                    </h6>
                </div>
                <div class="card-body">
                    <?php if (empty($monthlyRevenue['daily_revenue'])): ?>
                        <div class="text-center text-muted py-4">
                            <i class="bi bi-graph-down fa-3x mb-3"></i>
                            <p>Chưa có dữ liệu doanh thu cho tháng này</p>
                        </div>
                    <?php else: ?>
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <div class="card bg-primary text-white">
                                    <div class="card-body text-center">
                                        <h5><?= $monthlyRevenue['total_bookings'] ?></h5>
                                        <small>Tổng booking</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-success text-white">
                                    <div class="card-body text-center">
                                        <h5><?= formatCurrency($monthlyRevenue['total_revenue']) ?></h5>
                                        <small>Tổng doanh thu</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-info text-white">
                                    <div class="card-body text-center">
                                        <h5><?= formatCurrency($monthlyRevenue['avg_revenue']) ?></h5>
                                        <small>Doanh thu TB/booking</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-warning text-white">
                                    <div class="card-body text-center">
                                        <h5><?= $occupancyStats['occupied_nights'] ?></h5>
                                        <small>Tổng đêm được đặt</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Ngày</th>
                                        <th>Số booking</th>
                                        <th>Doanh thu</th>
                                        <th>Doanh thu TB</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($monthlyRevenue['daily_revenue'] as $daily): ?>
                                        <tr>
                                            <td><?= date('d/m/Y', strtotime($daily['checkout_date'])) ?></td>
                                            <td><?= $daily['bookings_count'] ?></td>
                                            <td><strong><?= formatCurrency($daily['total_revenue']) ?></strong></td>
                                            <td><?= formatCurrency($daily['avg_revenue']) ?></td>
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

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Biểu đồ doanh thu theo tháng
const revenueCtx = document.getElementById('revenueChart').getContext('2d');
const revenueChart = new Chart(revenueCtx, {
    type: 'line',
    data: {
        labels: [<?php 
            echo implode(',', array_map(function($item) { 
                return '"Tháng ' . $item['month'] . '"'; 
            }, $yearlyRevenue)); 
        ?>],
        datasets: [{
            label: 'Doanh thu (VNĐ)',
            data: [<?php 
                echo implode(',', array_map(function($item) { 
                    return $item['total_revenue']; 
                }, $yearlyRevenue)); 
            ?>],
            borderColor: 'rgb(75, 192, 192)',
            backgroundColor: 'rgba(75, 192, 192, 0.2)',
            tension: 0.1
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return new Intl.NumberFormat('vi-VN').format(value) + ' VNĐ';
                    }
                }
            }
        }
    }
});

// Biểu đồ trạng thái booking
const statusCtx = document.getElementById('statusChart').getContext('2d');
const statusChart = new Chart(statusCtx, {
    type: 'doughnut',
    data: {
        labels: [<?php 
            echo implode(',', array_map(function($item) { 
                return '"' . ucfirst($item['status']) . '"'; 
            }, $bookingStatus)); 
        ?>],
        datasets: [{
            data: [<?php 
                echo implode(',', array_map(function($item) { 
                    return $item['count']; 
                }, $bookingStatus)); 
            ?>],
            backgroundColor: [
                '#4e73df',
                '#1cc88a', 
                '#36b9cc',
                '#f6c23e',
                '#e74a3b'
            ]
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});
</script>

<?php include '../layout/admin_footer.php'; ?>