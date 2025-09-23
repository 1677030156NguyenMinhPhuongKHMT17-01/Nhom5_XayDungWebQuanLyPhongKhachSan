<?php
require_once 'db_connection.php';
require_once 'guest_functions.php';
require_once 'room_functions.php';

/**
 * Lấy danh sách tất cả booking với thông tin chi tiết
 */
function getAllBookings() {
    $conn = getDbConnection();
    $sql = "SELECT b.*, g.full_name, g.email, g.phone_number, 
                   r.room_number, rt.name_Room_Type, rt.price_per_night
            FROM bookings b 
            LEFT JOIN guests g ON b.guest_id = g.id 
            LEFT JOIN rooms r ON b.room_id = r.id 
            LEFT JOIN roomtypes rt ON r.room_type_id = rt.id 
            ORDER BY b.id DESC";
    $result = mysqli_query($conn, $sql);
    
    $bookings = [];
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $bookings[] = $row;
        }
    }
    
    mysqli_close($conn);
    return $bookings;
}

/**
 * Lấy thông tin booking theo ID
 */
function getBookingById($id) {
    $conn = getDbConnection();
    $sql = "SELECT b.*, g.full_name, g.email, g.phone_number, g.id_card_number,
                   r.room_number, rt.name_Room_Type, rt.price_per_night, rt.capacity
            FROM bookings b 
            LEFT JOIN guests g ON b.guest_id = g.id 
            LEFT JOIN rooms r ON b.room_id = r.id 
            LEFT JOIN roomtypes rt ON r.room_type_id = rt.id 
            WHERE b.id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    
    $result = mysqli_stmt_get_result($stmt);
    $booking = null;
    
    if ($result && mysqli_num_rows($result) > 0) {
        $booking = mysqli_fetch_assoc($result);
    }
    
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    return $booking;
}

/**
 * Tạo mới booking
 */
function createBooking($guest_id, $room_id, $check_in_date, $check_out_date, $total_price, $status) {
    $conn = getDbConnection();
    
    // Kiểm tra phòng có available không
    $checkRoomSql = "SELECT status FROM rooms WHERE id = ?";
    $checkRoomStmt = mysqli_prepare($conn, $checkRoomSql);
    mysqli_stmt_bind_param($checkRoomStmt, "i", $room_id);
    mysqli_stmt_execute($checkRoomStmt);
    $roomResult = mysqli_stmt_get_result($checkRoomStmt);
    
    if (!$roomResult || mysqli_num_rows($roomResult) == 0) {
        mysqli_stmt_close($checkRoomStmt);
        mysqli_close($conn);
        return false; // Phòng không tồn tại
    }
    
    $roomData = mysqli_fetch_assoc($roomResult);
    if ($roomData['status'] !== 'available') {
        mysqli_stmt_close($checkRoomStmt);
        mysqli_close($conn);
        return false; // Phòng không available
    }
    mysqli_stmt_close($checkRoomStmt);
    
    // Kiểm tra guest_id có tồn tại không (nếu là số)
    if (is_numeric($guest_id)) {
        $checkGuestSql = "SELECT id FROM guests WHERE id = ?";
        $checkGuestStmt = mysqli_prepare($conn, $checkGuestSql);
        mysqli_stmt_bind_param($checkGuestStmt, "i", $guest_id);
        mysqli_stmt_execute($checkGuestStmt);
        $guestResult = mysqli_stmt_get_result($checkGuestStmt);
        
        if (!$guestResult || mysqli_num_rows($guestResult) == 0) {
            mysqli_stmt_close($checkGuestStmt);
            mysqli_close($conn);
            return false; // Guest không tồn tại
        }
        mysqli_stmt_close($checkGuestStmt);
    }
    
    // Tính số đêm
    $nights = (strtotime($check_out_date) - strtotime($check_in_date)) / (60 * 60 * 24);
    
    // Kiểm tra xem các cột mới có tồn tại không
    $checkColumns = mysqli_query($conn, "SHOW COLUMNS FROM bookings LIKE 'check_in_date'");
    $hasNewColumns = mysqli_num_rows($checkColumns) > 0;
    
    if ($hasNewColumns) {
        // Tạo booking mới với các cột mới
        $sql = "INSERT INTO bookings (guest_id, room_id, check_in_date, check_out_date, nights, total_price, status) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "sissids", $guest_id, $room_id, $check_in_date, $check_out_date, $nights, $total_price, $status);
    } else {
        // Fallback cho hệ thống cũ không có cột check_in_date, check_out_date, nights
        $sql = "INSERT INTO bookings (guest_id, room_id, total_price, status) VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "iids", $guest_id, $room_id, $total_price, $status);
    }
    
    $result = mysqli_stmt_execute($stmt);
    
    // Nếu booking thành công và status là confirmed, cập nhật trạng thái phòng
    if ($result && $status === 'confirmed') {
        updateRoomStatus($room_id, 'occupied');
    }
    
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    
    return $result;
}

/**
 * Cập nhật thông tin booking
 */
function updateBooking($id, $guest_id, $room_id, $check_in_date, $check_out_date, $total_price, $status) {
    $conn = getDbConnection();
    
    // Lấy thông tin booking cũ để so sánh
    $oldBooking = getBookingById($id);
    if (!$oldBooking) {
        mysqli_close($conn);
        return false;
    }
    
    // Kiểm tra phòng mới có available không (nếu thay đổi phòng)
    if ($room_id != $oldBooking['room_id']) {
        $checkRoomSql = "SELECT status FROM rooms WHERE id = ?";
        $checkRoomStmt = mysqli_prepare($conn, $checkRoomSql);
        mysqli_stmt_bind_param($checkRoomStmt, "i", $room_id);
        mysqli_stmt_execute($checkRoomStmt);
        $roomResult = mysqli_stmt_get_result($checkRoomStmt);
        
        if (!$roomResult || mysqli_num_rows($roomResult) == 0) {
            mysqli_stmt_close($checkRoomStmt);
            mysqli_close($conn);
            return false; // Phòng không tồn tại
        }
        
        $roomData = mysqli_fetch_assoc($roomResult);
        if ($roomData['status'] !== 'available') {
            mysqli_stmt_close($checkRoomStmt);
            mysqli_close($conn);
            return false; // Phòng không available
        }
        mysqli_stmt_close($checkRoomStmt);
    }
    
    // Tính số đêm
    $nights = (strtotime($check_out_date) - strtotime($check_in_date)) / (60 * 60 * 24);
    
    // Kiểm tra xem các cột mới có tồn tại không
    $checkColumns = mysqli_query($conn, "SHOW COLUMNS FROM bookings LIKE 'check_in_date'");
    $hasNewColumns = mysqli_num_rows($checkColumns) > 0;
    
    if ($hasNewColumns) {
        // Cập nhật booking với các cột mới
        $sql = "UPDATE bookings SET guest_id = ?, room_id = ?, check_in_date = ?, check_out_date = ?, nights = ?, total_price = ?, status = ? WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "sissiosi", $guest_id, $room_id, $check_in_date, $check_out_date, $nights, $total_price, $status, $id);
    } else {
        // Fallback cho hệ thống cũ không có cột check_in_date, check_out_date, nights
        $sql = "UPDATE bookings SET guest_id = ?, room_id = ?, total_price = ?, status = ? WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "iidsi", $guest_id, $room_id, $total_price, $status, $id);
    }
    
    $result = mysqli_stmt_execute($stmt);
    
    // Cập nhật trạng thái phòng dựa trên thay đổi
    if ($result) {
        // Nếu thay đổi phòng, cập nhật trạng thái phòng cũ thành available
        if ($room_id != $oldBooking['room_id']) {
            updateRoomStatus($oldBooking['room_id'], 'available');
        }
        
        // Cập nhật trạng thái phòng mới dựa trên status
        if ($status === 'confirmed') {
            updateRoomStatus($room_id, 'occupied');
        } elseif ($status === 'cancelled' || $status === 'completed') {
            updateRoomStatus($room_id, 'available');
        }
    }
    
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    
    return $result;
}

/**
 * Xóa booking
 */
function deleteBooking($id) {
    $conn = getDbConnection();
    
    // Lấy thông tin booking để cập nhật trạng thái phòng
    $booking = getBookingById($id);
    if (!$booking) {
        mysqli_close($conn);
        return false;
    }
    
    $sql = "DELETE FROM bookings WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);
    
    $result = mysqli_stmt_execute($stmt);
    
    // Nếu xóa thành công, cập nhật trạng thái phòng thành available
    if ($result) {
        updateRoomStatus($booking['room_id'], 'available');
    }
    
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    
    return $result;
}

/**
 * Tìm kiếm booking
 */
function searchBookings($keyword) {
    $conn = getDbConnection();
    $keyword = "%$keyword%";
    
    $sql = "SELECT b.*, g.full_name, g.email, g.phone_number, 
                   r.room_number, rt.name_Room_Type, rt.price_per_night
            FROM bookings b 
            LEFT JOIN guests g ON b.guest_id = g.id 
            LEFT JOIN rooms r ON b.room_id = r.id 
            LEFT JOIN roomtypes rt ON r.room_type_id = rt.id 
            WHERE g.full_name LIKE ? OR g.email LIKE ? OR r.room_number LIKE ? OR b.status LIKE ?
            ORDER BY b.id DESC";
    
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ssss", $keyword, $keyword, $keyword, $keyword);
    mysqli_stmt_execute($stmt);
    
    $result = mysqli_stmt_get_result($stmt);
    $bookings = [];
    
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $bookings[] = $row;
        }
    }
    
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    
    return $bookings;
}

/**
 * Lấy booking theo trạng thái
 */
function getBookingsByStatus($status) {
    $conn = getDbConnection();
    $sql = "SELECT b.*, g.full_name, g.email, g.phone_number, 
                   r.room_number, rt.name_Room_Type, rt.price_per_night
            FROM bookings b 
            LEFT JOIN guests g ON b.guest_id = g.id 
            LEFT JOIN rooms r ON b.room_id = r.id 
            LEFT JOIN roomtypes rt ON r.room_type_id = rt.id 
            WHERE b.status = ?
            ORDER BY b.id DESC";
    
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $status);
    mysqli_stmt_execute($stmt);
    
    $result = mysqli_stmt_get_result($stmt);
    $bookings = [];
    
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $bookings[] = $row;
        }
    }
    
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    
    return $bookings;
}

?>
