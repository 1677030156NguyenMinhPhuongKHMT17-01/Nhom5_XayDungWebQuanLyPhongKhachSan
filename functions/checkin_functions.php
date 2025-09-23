<?php
require_once 'db_connection.php';

/**
 * Thực hiện check-in cho booking
 */
function checkInBooking($booking_id) {
    $conn = getDbConnection();
    
    // Kiểm tra booking tồn tại và có thể check-in
    $checkSql = "SELECT b.*, r.room_number, r.status as room_status 
                 FROM bookings b 
                 LEFT JOIN rooms r ON b.room_id = r.id 
                 WHERE b.id = ? AND b.status IN ('confirmed', 'pending')";
    $checkStmt = mysqli_prepare($conn, $checkSql);
    mysqli_stmt_bind_param($checkStmt, "i", $booking_id);
    mysqli_stmt_execute($checkStmt);
    $result = mysqli_stmt_get_result($checkStmt);
    
    if (!$result || mysqli_num_rows($result) == 0) {
        mysqli_stmt_close($checkStmt);
        mysqli_close($conn);
        return ['success' => false, 'message' => 'Booking không tồn tại hoặc không thể check-in!'];
    }
    
    $booking = mysqli_fetch_assoc($result);
    mysqli_stmt_close($checkStmt);
    
    // Kiểm tra phòng có available không
    if ($booking['room_status'] !== 'available') {
        mysqli_close($conn);
        return ['success' => false, 'message' => 'Phòng không sẵn sàng để check-in!'];
    }
    
    $now = date('Y-m-d H:i:s');
    
    // Bắt đầu transaction
    mysqli_begin_transaction($conn);
    
    try {
        // 1. Cập nhật booking status và actual_check_in
        // Kiểm tra xem cột actual_check_in có tồn tại không
        $checkColumn = mysqli_query($conn, "SHOW COLUMNS FROM bookings LIKE 'actual_check_in'");
        $hasColumn = mysqli_num_rows($checkColumn) > 0;
        
        if ($hasColumn) {
            $updateBookingSql = "UPDATE bookings SET status = 'checked_in', actual_check_in = ? WHERE id = ?";
            $updateBookingStmt = mysqli_prepare($conn, $updateBookingSql);
            mysqli_stmt_bind_param($updateBookingStmt, "si", $now, $booking_id);
        } else {
            // Fallback cho hệ thống cũ không có cột actual_check_in
            $updateBookingSql = "UPDATE bookings SET status = 'checked_in' WHERE id = ?";
            $updateBookingStmt = mysqli_prepare($conn, $updateBookingSql);
            mysqli_stmt_bind_param($updateBookingStmt, "i", $booking_id);
        }
        
        if (!mysqli_stmt_execute($updateBookingStmt)) {
            throw new Exception('Không thể cập nhật trạng thái booking!');
        }
        mysqli_stmt_close($updateBookingStmt);
        
        // 2. Cập nhật trạng thái phòng thành occupied
        $updateRoomSql = "UPDATE rooms SET status = 'occupied' WHERE id = ?";
        $updateRoomStmt = mysqli_prepare($conn, $updateRoomSql);
        mysqli_stmt_bind_param($updateRoomStmt, "i", $booking['room_id']);
        
        if (!mysqli_stmt_execute($updateRoomStmt)) {
            throw new Exception('Không thể cập nhật trạng thái phòng!');
        }
        mysqli_stmt_close($updateRoomStmt);
        
        // Commit transaction
        mysqli_commit($conn);
        mysqli_close($conn);
        
        return [
            'success' => true, 
            'message' => "Check-in thành công cho phòng {$booking['room_number']}!",
            'check_in_time' => $now
        ];
        
    } catch (Exception $e) {
        mysqli_rollback($conn);
        mysqli_close($conn);
        return ['success' => false, 'message' => $e->getMessage()];
    }
}

/**
 * Thực hiện check-out cho booking
 */
function checkOutBooking($booking_id, $additional_charges = 0) {
    $conn = getDbConnection();
    
    // Kiểm tra booking có thể check-out
    $checkSql = "SELECT b.*, r.room_number 
                 FROM bookings b 
                 LEFT JOIN rooms r ON b.room_id = r.id 
                 WHERE b.id = ? AND b.status = 'checked_in'";
    $checkStmt = mysqli_prepare($conn, $checkSql);
    mysqli_stmt_bind_param($checkStmt, "i", $booking_id);
    mysqli_stmt_execute($checkStmt);
    $result = mysqli_stmt_get_result($checkStmt);
    
    if (!$result || mysqli_num_rows($result) == 0) {
        mysqli_stmt_close($checkStmt);
        mysqli_close($conn);
        return ['success' => false, 'message' => 'Booking không tồn tại hoặc chưa check-in!'];
    }
    
    $booking = mysqli_fetch_assoc($result);
    mysqli_stmt_close($checkStmt);
    
    $now = date('Y-m-d H:i:s');
    
    // Bắt đầu transaction
    mysqli_begin_transaction($conn);
    
    try {
        // 1. Cập nhật booking status và actual_check_out
        // Kiểm tra xem cột actual_check_out có tồn tại không
        $checkColumn = mysqli_query($conn, "SHOW COLUMNS FROM bookings LIKE 'actual_check_out'");
        $hasColumn = mysqli_num_rows($checkColumn) > 0;
        
        if ($hasColumn) {
            $updateBookingSql = "UPDATE bookings SET status = 'checked_out', actual_check_out = ? WHERE id = ?";
            $updateBookingStmt = mysqli_prepare($conn, $updateBookingSql);
            mysqli_stmt_bind_param($updateBookingStmt, "si", $now, $booking_id);
        } else {
            // Fallback cho hệ thống cũ không có cột actual_check_out
            $updateBookingSql = "UPDATE bookings SET status = 'checked_out' WHERE id = ?";
            $updateBookingStmt = mysqli_prepare($conn, $updateBookingSql);
            mysqli_stmt_bind_param($updateBookingStmt, "i", $booking_id);
        }
        
        if (!mysqli_stmt_execute($updateBookingStmt)) {
            throw new Exception('Không thể cập nhật trạng thái booking!');
        }
        mysqli_stmt_close($updateBookingStmt);
        
        // 2. Thêm phí phát sinh (nếu có)
        if ($additional_charges > 0) {
            $serviceSql = "INSERT INTO booking_services (booking_id, service_id, quantity, unit_price, service_date, notes) 
                          VALUES (?, 1, 1, ?, CURDATE(), 'Phí phát sinh khi check-out')";
            $serviceStmt = mysqli_prepare($conn, $serviceSql);
            mysqli_stmt_bind_param($serviceStmt, "id", $booking_id, $additional_charges);
            mysqli_stmt_execute($serviceStmt);
            mysqli_stmt_close($serviceStmt);
        }
        
        // 3. Cập nhật trạng thái phòng thành available
        $updateRoomSql = "UPDATE rooms SET status = 'available' WHERE id = ?";
        $updateRoomStmt = mysqli_prepare($conn, $updateRoomSql);
        mysqli_stmt_bind_param($updateRoomStmt, "i", $booking['room_id']);
        
        if (!mysqli_stmt_execute($updateRoomStmt)) {
            throw new Exception('Không thể cập nhật trạng thái phòng!');
        }
        mysqli_stmt_close($updateRoomStmt);
        
        // Commit transaction
        mysqli_commit($conn);
        mysqli_close($conn);
        
        return [
            'success' => true, 
            'message' => "Check-out thành công cho phòng {$booking['room_number']}!",
            'check_out_time' => $now
        ];
        
    } catch (Exception $e) {
        mysqli_rollback($conn);
        mysqli_close($conn);
        return ['success' => false, 'message' => $e->getMessage()];
    }
}

/**
 * Lấy danh sách booking cần check-in hôm nay
 */
function getTodayCheckIns() {
    $conn = getDbConnection();
    $today = date('Y-m-d');
    
    // Kiểm tra xem cột check_in_date có tồn tại không
    $checkColumn = mysqli_query($conn, "SHOW COLUMNS FROM bookings LIKE 'check_in_date'");
    
    if (mysqli_num_rows($checkColumn) > 0) {
        // Nếu có cột check_in_date, sử dụng logic mới
        $sql = "SELECT b.*, g.full_name, g.phone_number, r.room_number, rt.name_Room_Type
                FROM bookings b
                LEFT JOIN guests g ON b.guest_id = g.id
                LEFT JOIN rooms r ON b.room_id = r.id
                LEFT JOIN roomtypes rt ON r.room_type_id = rt.id
                WHERE b.check_in_date = ? AND b.status IN ('confirmed', 'pending')
                ORDER BY b.check_in_date ASC";
        
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $today);
    } else {
        // Nếu chưa có cột, sử dụng logic cũ (lấy tất cả booking confirmed)
        $sql = "SELECT b.*, g.full_name, g.phone_number, r.room_number, rt.name_Room_Type
                FROM bookings b
                LEFT JOIN guests g ON b.guest_id = g.id
                LEFT JOIN rooms r ON b.room_id = r.id
                LEFT JOIN roomtypes rt ON r.room_type_id = rt.id
                WHERE b.status IN ('confirmed', 'pending')
                ORDER BY b.id ASC";
        
        $stmt = mysqli_prepare($conn, $sql);
    }
    
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    $checkIns = [];
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $checkIns[] = $row;
        }
    }
    
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    return $checkIns;
}

/**
 * Lấy danh sách booking cần check-out hôm nay
 */
function getTodayCheckOuts() {
    $conn = getDbConnection();
    $today = date('Y-m-d');
    
    // Kiểm tra xem cột check_out_date có tồn tại không
    $checkColumn = mysqli_query($conn, "SHOW COLUMNS FROM bookings LIKE 'check_out_date'");
    
    if (mysqli_num_rows($checkColumn) > 0) {
        // Nếu có cột check_out_date, sử dụng logic mới
        $sql = "SELECT b.*, g.full_name, g.phone_number, r.room_number, rt.name_Room_Type
                FROM bookings b
                LEFT JOIN guests g ON b.guest_id = g.id
                LEFT JOIN rooms r ON b.room_id = r.id
                LEFT JOIN roomtypes rt ON r.room_type_id = rt.id
                WHERE b.check_out_date = ? AND b.status = 'checked_in'
                ORDER BY b.check_out_date ASC";
        
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $today);
    } else {
        // Nếu chưa có cột, sử dụng logic cũ (lấy tất cả booking checked_in)
        $sql = "SELECT b.*, g.full_name, g.phone_number, r.room_number, rt.name_Room_Type
                FROM bookings b
                LEFT JOIN guests g ON b.guest_id = g.id
                LEFT JOIN rooms r ON b.room_id = r.id
                LEFT JOIN roomtypes rt ON r.room_type_id = rt.id
                WHERE b.status = 'checked_in'
                ORDER BY b.id ASC";
        
        $stmt = mysqli_prepare($conn, $sql);
    }
    
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    $checkOuts = [];
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $checkOuts[] = $row;
        }
    }
    
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    return $checkOuts;
}

/**
 * Lấy thống kê occupancy rate
 */
function getOccupancyRate($date = null) {
    $conn = getDbConnection();
    $target_date = $date ?? date('Y-m-d');
    
    // Tổng số phòng
    $totalRoomsSql = "SELECT COUNT(*) as total FROM rooms";
    $totalResult = mysqli_query($conn, $totalRoomsSql);
    $totalRooms = mysqli_fetch_assoc($totalResult)['total'];
    
    // Số phòng đã được đặt
    // Kiểm tra xem cột check_in_date và check_out_date có tồn tại không
    $checkColumn = mysqli_query($conn, "SHOW COLUMNS FROM bookings LIKE 'check_in_date'");
    $hasColumns = mysqli_num_rows($checkColumn) > 0;
    
    if ($hasColumns) {
        $occupiedSql = "SELECT COUNT(*) as occupied 
                        FROM bookings b 
                        WHERE ? BETWEEN b.check_in_date AND b.check_out_date 
                        AND b.status IN ('confirmed', 'checked_in')";
    } else {
        // Fallback cho hệ thống cũ không có cột check_in_date, check_out_date
        $occupiedSql = "SELECT COUNT(*) as occupied 
                        FROM bookings b 
                        WHERE b.status IN ('confirmed', 'checked_in')";
    }
    
    $occupiedStmt = mysqli_prepare($conn, $occupiedSql);
    if ($hasColumns) {
        mysqli_stmt_bind_param($occupiedStmt, "s", $target_date);
    }
    mysqli_stmt_execute($occupiedStmt);
    $occupiedResult = mysqli_stmt_get_result($occupiedStmt);
    $occupiedRooms = mysqli_fetch_assoc($occupiedResult)['occupied'];
    
    mysqli_stmt_close($occupiedStmt);
    mysqli_close($conn);
    
    $occupancyRate = $totalRooms > 0 ? ($occupiedRooms / $totalRooms) * 100 : 0;
    
    return [
        'total_rooms' => $totalRooms,
        'occupied_rooms' => $occupiedRooms,
        'available_rooms' => $totalRooms - $occupiedRooms,
        'occupancy_rate' => round($occupancyRate, 2)
    ];
}
?>