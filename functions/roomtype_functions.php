<?php
require_once 'db_connection.php';

/**
 * Lấy danh sách tất cả loại phòng
 */
function getAllRoomTypes() {
    $conn = getDbConnection();
    $sql = "SELECT * FROM roomtypes ORDER BY name_Room_Type ASC";
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
 * Lấy thông tin loại phòng theo ID
 */
function getRoomTypeById($id) {
    $conn = getDbConnection();
    $sql = "SELECT * FROM roomtypes WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    
    $result = mysqli_stmt_get_result($stmt);
    $roomType = null;
    
    if ($result && mysqli_num_rows($result) > 0) {
        $roomType = mysqli_fetch_assoc($result);
    }
    
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    return $roomType;
}

/**
 * Tạo mới loại phòng
 */
function createRoomType($name_Room_Type, $price_per_night, $capacity) {
    $conn = getDbConnection();
    
    // Kiểm tra tên loại phòng đã tồn tại chưa
    $checkSql = "SELECT id FROM roomtypes WHERE name_Room_Type = ?";
    $checkStmt = mysqli_prepare($conn, $checkSql);
    mysqli_stmt_bind_param($checkStmt, "s", $name_Room_Type);
    mysqli_stmt_execute($checkStmt);
    $checkResult = mysqli_stmt_get_result($checkStmt);
    
    if (mysqli_num_rows($checkResult) > 0) {
        mysqli_stmt_close($checkStmt);
        mysqli_close($conn);
        return false; // Tên loại phòng đã tồn tại
    }
    mysqli_stmt_close($checkStmt);
    
    // Thêm loại phòng mới
    $sql = "INSERT INTO roomtypes (name_Room_Type, price_per_night, capacity) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "sdi", $name_Room_Type, $price_per_night, $capacity);
    
    $result = mysqli_stmt_execute($stmt);
    
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    
    return $result;
}

/**
 * Cập nhật thông tin loại phòng
 */
function updateRoomType($id, $name_Room_Type, $price_per_night, $capacity) {
    $conn = getDbConnection();
    
    // Kiểm tra tên loại phòng đã tồn tại với loại phòng khác chưa
    $checkSql = "SELECT id FROM roomtypes WHERE name_Room_Type = ? AND id != ?";
    $checkStmt = mysqli_prepare($conn, $checkSql);
    mysqli_stmt_bind_param($checkStmt, "si", $name_Room_Type, $id);
    mysqli_stmt_execute($checkStmt);
    $checkResult = mysqli_stmt_get_result($checkStmt);
    
    if (mysqli_num_rows($checkResult) > 0) {
        mysqli_stmt_close($checkStmt);
        mysqli_close($conn);
        return false; // Tên loại phòng đã tồn tại
    }
    mysqli_stmt_close($checkStmt);
    
    $sql = "UPDATE roomtypes SET name_Room_Type = ?, price_per_night = ?, capacity = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "sdii", $name_Room_Type, $price_per_night, $capacity, $id);
    
    $result = mysqli_stmt_execute($stmt);
    
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    
    return $result;
}

/**
 * Xóa loại phòng
 */
function deleteRoomType($id) {
    $conn = getDbConnection();
    
    // Kiểm tra xem loại phòng có được sử dụng trong rooms không
    $checkSql = "SELECT id FROM rooms WHERE room_type_id = ?";
    $checkStmt = mysqli_prepare($conn, $checkSql);
    mysqli_stmt_bind_param($checkStmt, "i", $id);
    mysqli_stmt_execute($checkStmt);
    $checkResult = mysqli_stmt_get_result($checkStmt);
    
    if (mysqli_num_rows($checkResult) > 0) {
        mysqli_stmt_close($checkStmt);
        mysqli_close($conn);
        return false; // Không thể xóa vì có phòng đang sử dụng
    }
    mysqli_stmt_close($checkStmt);
    
    $sql = "DELETE FROM roomtypes WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);
    
    $result = mysqli_stmt_execute($stmt);
    
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    
    return $result;
}

/**
 * Tìm kiếm loại phòng
 */
function searchRoomTypes($keyword) {
    $conn = getDbConnection();
    $keyword = "%$keyword%";
    
    $sql = "SELECT * FROM roomtypes WHERE name_Room_Type LIKE ? ORDER BY name_Room_Type ASC";
    
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $keyword);
    mysqli_stmt_execute($stmt);
    
    $result = mysqli_stmt_get_result($stmt);
    $roomTypes = [];
    
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $roomTypes[] = $row;
        }
    }
    
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    
    return $roomTypes;
}

?>
