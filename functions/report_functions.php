<?php
require_once 'db_connection.php';

/**
 * Thống kê doanh thu theo tháng
 */
function getRevenueByMonth($year = null, $month = null) {
    $conn = getDbConnection();
    $currentYear = $year ?? date('Y');
    $currentMonth = $month ?? date('m');
    
    // Check if actual_check_out column exists
    $checkColumn = mysqli_query($conn, "SHOW COLUMNS FROM bookings LIKE 'actual_check_out'");
    $hasColumn = mysqli_num_rows($checkColumn) > 0;
    
    if ($hasColumn) {
        $sql = "SELECT 
                    DATE(actual_check_out) as checkout_date,
                    COUNT(*) as bookings_count,
                    SUM(total_price) as total_revenue,
                    AVG(total_price) as avg_revenue
                FROM bookings 
                WHERE YEAR(actual_check_out) = ? 
                AND MONTH(actual_check_out) = ?
                AND status = 'checked_out'
                GROUP BY DATE(actual_check_out)
                ORDER BY checkout_date ASC";
    } else {
        // Fallback cho hệ thống cũ không có cột actual_check_out
        // Sử dụng logic đơn giản chỉ dựa trên status
        $sql = "SELECT 
                    id as checkout_date,
                    COUNT(*) as bookings_count,
                    SUM(total_price) as total_revenue,
                    AVG(total_price) as avg_revenue
                FROM bookings 
                WHERE status = 'checked_out'
                GROUP BY id
                ORDER BY id ASC";
    }
    
    $stmt = mysqli_prepare($conn, $sql);
    if ($hasColumn) {
        mysqli_stmt_bind_param($stmt, "ii", $currentYear, $currentMonth);
    }
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    $revenue = [];
    $totalRevenue = 0;
    $totalBookings = 0;
    
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $revenue[] = $row;
            $totalRevenue += $row['total_revenue'];
            $totalBookings += $row['bookings_count'];
        }
    }
    
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    
    return [
        'daily_revenue' => $revenue,
        'total_revenue' => $totalRevenue,
        'total_bookings' => $totalBookings,
        'avg_revenue' => $totalBookings > 0 ? $totalRevenue / $totalBookings : 0
    ];
}

/**
 * Thống kê doanh thu theo năm
 */
function getRevenueByYear($year = null) {
    $conn = getDbConnection();
    $currentYear = $year ?? date('Y');
    
    // Check if actual_check_out column exists
    $checkColumn = mysqli_query($conn, "SHOW COLUMNS FROM bookings LIKE 'actual_check_out'");
    $hasColumn = mysqli_num_rows($checkColumn) > 0;
    
    if ($hasColumn) {
        $sql = "SELECT 
                    MONTH(actual_check_out) as month,
                    MONTHNAME(actual_check_out) as month_name,
                    COUNT(*) as bookings_count,
                    SUM(total_price) as total_revenue
                FROM bookings 
                WHERE YEAR(actual_check_out) = ?
                AND status = 'checked_out'
                GROUP BY MONTH(actual_check_out)
                ORDER BY month ASC";
    } else {
        // Fallback cho hệ thống cũ không có cột actual_check_out
        // Sử dụng logic đơn giản chỉ dựa trên status
        $sql = "SELECT 
                    1 as month,
                    'All Time' as month_name,
                    COUNT(*) as bookings_count,
                    SUM(total_price) as total_revenue
                FROM bookings 
                WHERE status = 'checked_out'
                ORDER BY month ASC";
    }
    
    $stmt = mysqli_prepare($conn, $sql);
    if ($hasColumn) {
        mysqli_stmt_bind_param($stmt, "i", $currentYear);
    }
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    $monthlyRevenue = [];
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $monthlyRevenue[] = $row;
        }
    }
    
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    return $monthlyRevenue;
}

/**
 * Thống kê tỷ lệ lấp đầy theo tháng
 */
function getOccupancyRateByMonth($year = null, $month = null) {
    $conn = getDbConnection();
    $currentYear = $year ?? date('Y');
    $currentMonth = $month ?? date('m');
    
    // Tổng số phòng
    $totalRoomsSql = "SELECT COUNT(*) as total FROM rooms";
    $totalResult = mysqli_query($conn, $totalRoomsSql);
    $totalRooms = mysqli_fetch_assoc($totalResult)['total'];
    
    // Số ngày trong tháng
    $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $currentMonth, $currentYear);
    $totalRoomNights = $totalRooms * $daysInMonth;
    
    // Số phòng đã được đặt
    // Check if check_in_date and nights columns exist
    $checkColumn1 = mysqli_query($conn, "SHOW COLUMNS FROM bookings LIKE 'check_in_date'");
    $checkColumn2 = mysqli_query($conn, "SHOW COLUMNS FROM bookings LIKE 'nights'");
    $hasDateColumns = mysqli_num_rows($checkColumn1) > 0 && mysqli_num_rows($checkColumn2) > 0;
    
    if ($hasDateColumns) {
        $occupiedSql = "SELECT SUM(nights) as occupied_nights
                        FROM bookings 
                        WHERE YEAR(check_in_date) = ? 
                        AND MONTH(check_in_date) = ?
                        AND status IN ('confirmed', 'checked_in', 'checked_out')";
    } else {
        // Fallback cho hệ thống cũ không có cột check_in_date và nights
        // Đếm booking thay vì tính nights
        $occupiedSql = "SELECT COUNT(*) as occupied_nights
                        FROM bookings 
                        WHERE status IN ('confirmed', 'checked_in', 'checked_out')";
    }
    
    $occupiedStmt = mysqli_prepare($conn, $occupiedSql);
    if ($hasDateColumns) {
        mysqli_stmt_bind_param($occupiedStmt, "ii", $currentYear, $currentMonth);
    }
    mysqli_stmt_execute($occupiedStmt);
    $occupiedResult = mysqli_stmt_get_result($occupiedStmt);
    $occupiedNights = mysqli_fetch_assoc($occupiedResult)['occupied_nights'] ?? 0;
    
    mysqli_stmt_close($occupiedStmt);
    mysqli_close($conn);
    
    $occupancyRate = $totalRoomNights > 0 ? ($occupiedNights / $totalRoomNights) * 100 : 0;
    
    return [
        'total_rooms' => $totalRooms,
        'days_in_month' => $daysInMonth,
        'total_room_nights' => $totalRoomNights,
        'occupied_nights' => $occupiedNights,
        'occupancy_rate' => round($occupancyRate, 2)
    ];
}

/**
 * Top khách hàng VIP
 */
function getTopCustomers($limit = 10) {
    $conn = getDbConnection();
    
    // Check if actual_check_out column exists
    $checkColumn = mysqli_query($conn, "SHOW COLUMNS FROM bookings LIKE 'actual_check_out'");
    $hasColumn = mysqli_num_rows($checkColumn) > 0;
    
    if ($hasColumn) {
        $sql = "SELECT 
                    g.id,
                    g.full_name,
                    g.email,
                    g.phone_number,
                    COUNT(b.id) as total_bookings,
                    SUM(b.total_price) as total_spent,
                    AVG(b.total_price) as avg_spent,
                    MAX(b.actual_check_out) as last_visit
                FROM guests g
                LEFT JOIN bookings b ON g.id = b.guest_id
                WHERE b.status = 'checked_out'
                GROUP BY g.id
                ORDER BY total_spent DESC
                LIMIT ?";
    } else {
        // Fallback cho hệ thống cũ không có cột actual_check_out
        $sql = "SELECT 
                    g.id,
                    g.full_name,
                    g.email,
                    g.phone_number,
                    COUNT(b.id) as total_bookings,
                    SUM(b.total_price) as total_spent,
                    AVG(b.total_price) as avg_spent,
                    'N/A' as last_visit
                FROM guests g
                LEFT JOIN bookings b ON g.id = b.guest_id
                WHERE b.status = 'checked_out'
                GROUP BY g.id
                ORDER BY total_spent DESC
                LIMIT ?";
    }
    
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $limit);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    $customers = [];
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $customers[] = $row;
        }
    }
    
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    return $customers;
}

/**
 * Thống kê loại phòng phổ biến
 */
function getPopularRoomTypes() {
    $conn = getDbConnection();
    
    // Check if nights column exists
    $checkColumn = mysqli_query($conn, "SHOW COLUMNS FROM bookings LIKE 'nights'");
    $hasColumn = mysqli_num_rows($checkColumn) > 0;
    
    if ($hasColumn) {
        $sql = "SELECT 
                    rt.id,
                    rt.name_Room_Type,
                    rt.price_per_night,
                    COUNT(b.id) as bookings_count,
                    SUM(b.total_price) as total_revenue,
                    AVG(b.nights) as avg_nights
                FROM roomtypes rt
                LEFT JOIN rooms r ON rt.id = r.room_type_id
                LEFT JOIN bookings b ON r.id = b.room_id AND b.status = 'checked_out'
                GROUP BY rt.id
                ORDER BY bookings_count DESC";
    } else {
        // Fallback for systems without nights column
        $sql = "SELECT 
                    rt.id,
                    rt.name_Room_Type,
                    rt.price_per_night,
                    COUNT(b.id) as bookings_count,
                    SUM(b.total_price) as total_revenue,
                    1 as avg_nights
                FROM roomtypes rt
                LEFT JOIN rooms r ON rt.id = r.room_type_id
                LEFT JOIN bookings b ON r.id = b.room_id AND b.status = 'checked_out'
                GROUP BY rt.id
                ORDER BY bookings_count DESC";
    }
    
    $result = mysqli_query($conn, $sql);
    
    $roomTypes = [];
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $roomTypes[] = $row;
        }
    }
    
    mysqli_close($conn);
    return $roomTypes;
}

/**
 * Thống kê booking theo trạng thái
 */
function getBookingsByStatus() {
    $conn = getDbConnection();
    
    $sql = "SELECT 
                status,
                COUNT(*) as count,
                SUM(total_price) as total_value
            FROM bookings 
            GROUP BY status
            ORDER BY count DESC";
    
    $result = mysqli_query($conn, $sql);
    
    $statusStats = [];
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $statusStats[] = $row;
        }
    }
    
    mysqli_close($conn);
    return $statusStats;
}

/**
 * Thống kê tổng quát
 */
function getGeneralStats() {
    $conn = getDbConnection();
    
    // Check if actual_check_out column exists
    $checkColumn = mysqli_query($conn, "SHOW COLUMNS FROM bookings LIKE 'actual_check_out'");
    $hasColumn = mysqli_num_rows($checkColumn) > 0;
    
    if ($hasColumn) {
        // Doanh thu hôm nay
        $todayRevenueSql = "SELECT COALESCE(SUM(total_price), 0) as today_revenue 
                            FROM bookings 
                            WHERE DATE(actual_check_out) = CURDATE() 
                            AND status = 'checked_out'";
        
        // Doanh thu tháng này
        $monthRevenueSql = "SELECT COALESCE(SUM(total_price), 0) as month_revenue 
                            FROM bookings 
                            WHERE YEAR(actual_check_out) = YEAR(CURDATE()) 
                            AND MONTH(actual_check_out) = MONTH(CURDATE())
                            AND status = 'checked_out'";
    } else {
        // Fallback cho hệ thống cũ không có cột actual_check_out
        // Sử dụng logic đơn giản chỉ dựa trên status
        $todayRevenueSql = "SELECT COALESCE(SUM(total_price), 0) as today_revenue 
                            FROM bookings 
                            WHERE status = 'checked_out'";
        
        $monthRevenueSql = "SELECT COALESCE(SUM(total_price), 0) as month_revenue 
                            FROM bookings 
                            WHERE status = 'checked_out'";
    }
    
    $todayResult = mysqli_query($conn, $todayRevenueSql);
    $todayRevenue = mysqli_fetch_assoc($todayResult)['today_revenue'];
    
    $monthResult = mysqli_query($conn, $monthRevenueSql);
    $monthRevenue = mysqli_fetch_assoc($monthResult)['month_revenue'];
    
    // Booking hôm nay
    // Kiểm tra xem có cột thời gian nào khả dụng không
    $checkCreatedAt = mysqli_query($conn, "SHOW COLUMNS FROM bookings LIKE 'created_at'");
    $hasCreatedAt = mysqli_num_rows($checkCreatedAt) > 0;
    
    if ($hasCreatedAt) {
        $todayBookingsSql = "SELECT COUNT(*) as today_bookings 
                             FROM bookings 
                             WHERE DATE(created_at) = CURDATE()";
    } else {
        // Fallback: đếm tất cả booking
        $todayBookingsSql = "SELECT COUNT(*) as today_bookings 
                             FROM bookings";
    }
    $todayBookingsResult = mysqli_query($conn, $todayBookingsSql);
    $todayBookings = mysqli_fetch_assoc($todayBookingsResult)['today_bookings'];
    
    // Phòng đang được sử dụng
    $occupiedRoomsSql = "SELECT COUNT(*) as occupied_rooms 
                         FROM rooms 
                         WHERE status = 'occupied'";
    $occupiedResult = mysqli_query($conn, $occupiedRoomsSql);
    $occupiedRooms = mysqli_fetch_assoc($occupiedResult)['occupied_rooms'];
    
    mysqli_close($conn);
    
    return [
        'today_revenue' => $todayRevenue,
        'month_revenue' => $monthRevenue,
        'today_bookings' => $todayBookings,
        'occupied_rooms' => $occupiedRooms
    ];
}
?>