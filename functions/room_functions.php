<?php
require_once 'db_connection.php';
require_once 'roomtype_functions.php';

/**
 * Lấy danh sách tất cả phòng với thông tin loại phòng
 */
function getAllRooms() {
    $conn = getDbConnection();
    $sql = "SELECT r.*, rt.name_Room_Type, rt.price_per_night, rt.capacity 
            FROM rooms r 
            LEFT JOIN roomtypes rt ON r.room_type_id = rt.id 
            ORDER BY r.room_number ASC";
    $result = mysqli_query($conn, $sql);
    
    $rooms = [];
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $rooms[] = $row;
        }
    }
    
    mysqli_close($conn);
    return $rooms;
}

/**
 * Lấy thông tin phòng theo ID
 */
function getRoomById($id) {
    $conn = getDbConnection();
    $sql = "SELECT r.*, rt.name_Room_Type, rt.price_per_night, rt.capacity 
            FROM rooms r 
            LEFT JOIN roomtypes rt ON r.room_type_id = rt.id 
            WHERE r.id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    
    $result = mysqli_stmt_get_result($stmt);
    $room = null;
    
    if ($result && mysqli_num_rows($result) > 0) {
        $room = mysqli_fetch_assoc($result);
    }
    
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    return $room;
}

/**
 * Tạo mới phòng
 */
function createRoom($room_number, $room_type_id, $status) {
    $conn = getDbConnection();
    
    // Kiểm tra số phòng đã tồn tại chưa
    $checkSql = "SELECT id FROM rooms WHERE room_number = ?";
    $checkStmt = mysqli_prepare($conn, $checkSql);
    mysqli_stmt_bind_param($checkStmt, "s", $room_number);
    mysqli_stmt_execute($checkStmt);
    $checkResult = mysqli_stmt_get_result($checkStmt);
    
    if (mysqli_num_rows($checkResult) > 0) {
        mysqli_stmt_close($checkStmt);
        mysqli_close($conn);
        return false; // Số phòng đã tồn tại
    }
    mysqli_stmt_close($checkStmt);
    
    // Thêm phòng mới
    $sql = "INSERT INTO rooms (room_number, room_type_id, status) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "sis", $room_number, $room_type_id, $status);
    
    $result = mysqli_stmt_execute($stmt);
    
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    
    return $result;
}

/**
 * Cập nhật thông tin phòng
 */
function updateRoom($id, $room_number, $room_type_id, $status) {
    $conn = getDbConnection();
    
    // Kiểm tra số phòng đã tồn tại với phòng khác chưa
    $checkSql = "SELECT id FROM rooms WHERE room_number = ? AND id != ?";
    $checkStmt = mysqli_prepare($conn, $checkSql);
    mysqli_stmt_bind_param($checkStmt, "si", $room_number, $id);
    mysqli_stmt_execute($checkStmt);
    $checkResult = mysqli_stmt_get_result($checkStmt);
    
    if (mysqli_num_rows($checkResult) > 0) {
        mysqli_stmt_close($checkStmt);
        mysqli_close($conn);
        return false; // Số phòng đã tồn tại
    }
    mysqli_stmt_close($checkStmt);
    
    $sql = "UPDATE rooms SET room_number = ?, room_type_id = ?, status = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "sisi", $room_number, $room_type_id, $status, $id);
    
    $result = mysqli_stmt_execute($stmt);
    
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    
    return $result;
}

/**
 * Xóa phòng
 */
function deleteRoom($id) {
    $conn = getDbConnection();
    
    // Kiểm tra xem phòng có booking nào không
    $checkSql = "SELECT id FROM bookings WHERE room_id = ?";
    $checkStmt = mysqli_prepare($conn, $checkSql);
    mysqli_stmt_bind_param($checkStmt, "i", $id);
    mysqli_stmt_execute($checkStmt);
    $checkResult = mysqli_stmt_get_result($checkStmt);
    
    if (mysqli_num_rows($checkResult) > 0) {
        mysqli_stmt_close($checkStmt);
        mysqli_close($conn);
        return false; // Không thể xóa vì có booking
    }
    mysqli_stmt_close($checkStmt);
    
    $sql = "DELETE FROM rooms WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);
    
    $result = mysqli_stmt_execute($stmt);
    
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    
    return $result;
}

/**
 * Tìm kiếm phòng
 */
function searchRooms($keyword) {
    $conn = getDbConnection();
    $keyword = "%$keyword%";
    
    $sql = "SELECT r.*, rt.name_Room_Type, rt.price_per_night, rt.capacity 
            FROM rooms r 
            LEFT JOIN roomtypes rt ON r.room_type_id = rt.id 
            WHERE r.room_number LIKE ? OR rt.name_Room_Type LIKE ? OR r.status LIKE ? 
            ORDER BY r.room_number ASC";
    
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "sss", $keyword, $keyword, $keyword);
    mysqli_stmt_execute($stmt);
    
    $result = mysqli_stmt_get_result($stmt);
    $rooms = [];
    
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $rooms[] = $row;
        }
    }
    
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    
    return $rooms;
}

/**
 * Lấy danh sách phòng trống
 */
function getAvailableRooms() {
    $conn = getDbConnection();
    $sql = "SELECT r.*, rt.name_Room_Type, rt.price_per_night, rt.capacity 
            FROM rooms r 
            LEFT JOIN roomtypes rt ON r.room_type_id = rt.id 
            WHERE r.status = 'available' 
            ORDER BY r.room_number ASC";
    $result = mysqli_query($conn, $sql);
    
    $rooms = [];
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $rooms[] = $row;
        }
    }
    
    mysqli_close($conn);
    return $rooms;
}

/**
 * Cập nhật trạng thái phòng
 */
function updateRoomStatus($id, $status) {
    $conn = getDbConnection();
    
    $sql = "UPDATE rooms SET status = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "si", $status, $id);
    
    $result = mysqli_stmt_execute($stmt);
    
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    
    return $result;
}

?>
