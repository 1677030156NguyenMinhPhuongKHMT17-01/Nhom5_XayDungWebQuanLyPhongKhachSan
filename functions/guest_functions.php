<?php
require_once 'db_connection.php';

/**
 * Lấy danh sách tất cả khách hàng
 */
function getAllGuests() {
    $conn = getDbConnection();
    $sql = "SELECT * FROM guests ORDER BY full_name ASC";
    $result = mysqli_query($conn, $sql);
    
    $guests = [];
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $guests[] = $row;
        }
    }
    
    mysqli_close($conn);
    return $guests;
}

/**
 * Lấy thông tin khách hàng theo ID
 */
function getGuestById($id) {
    $conn = getDbConnection();
    $sql = "SELECT * FROM guests WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    
    $result = mysqli_stmt_get_result($stmt);
    $guest = null;
    
    if ($result && mysqli_num_rows($result) > 0) {
        $guest = mysqli_fetch_assoc($result);
    }
    
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    return $guest;
}

/**
 * Tạo mới khách hàng
 */
function createGuest($full_name, $email, $phone_number, $id_card_number) {
    $conn = getDbConnection();
    
    // Kiểm tra email đã tồn tại chưa
    $checkSql = "SELECT id FROM guests WHERE email = ? OR phone_number = ? OR id_card_number = ?";
    $checkStmt = mysqli_prepare($conn, $checkSql);
    mysqli_stmt_bind_param($checkStmt, "sss", $email, $phone_number, $id_card_number);
    mysqli_stmt_execute($checkStmt);
    $checkResult = mysqli_stmt_get_result($checkStmt);
    
    if (mysqli_num_rows($checkResult) > 0) {
        mysqli_stmt_close($checkStmt);
        mysqli_close($conn);
        return false; // Email, số điện thoại hoặc CMND đã tồn tại
    }
    mysqli_stmt_close($checkStmt);
    
    // Thêm khách hàng mới
    $sql = "INSERT INTO guests (full_name, email, phone_number, id_card_number) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ssss", $full_name, $email, $phone_number, $id_card_number);
    
    $result = mysqli_stmt_execute($stmt);
    
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    
    return $result;
}

/**
 * Cập nhật thông tin khách hàng
 */
function updateGuest($id, $full_name, $email, $phone_number, $id_card_number) {
    $conn = getDbConnection();
    
    // Kiểm tra email, số điện thoại, CMND đã tồn tại với khách hàng khác chưa
    $checkSql = "SELECT id FROM guests WHERE (email = ? OR phone_number = ? OR id_card_number = ?) AND id != ?";
    $checkStmt = mysqli_prepare($conn, $checkSql);
    mysqli_stmt_bind_param($checkStmt, "sssi", $email, $phone_number, $id_card_number, $id);
    mysqli_stmt_execute($checkStmt);
    $checkResult = mysqli_stmt_get_result($checkStmt);
    
    if (mysqli_num_rows($checkResult) > 0) {
        mysqli_stmt_close($checkStmt);
        mysqli_close($conn);
        return false; // Email, số điện thoại hoặc CMND đã tồn tại
    }
    mysqli_stmt_close($checkStmt);
    
    $sql = "UPDATE guests SET full_name = ?, email = ?, phone_number = ?, id_card_number = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ssssi", $full_name, $email, $phone_number, $id_card_number, $id);
    
    $result = mysqli_stmt_execute($stmt);
    
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    
    return $result;
}

/**
 * Xóa khách hàng
 */
function deleteGuest($id) {
    $conn = getDbConnection();
    
    // Kiểm tra xem khách hàng có booking nào không
    $checkSql = "SELECT id FROM bookings WHERE guest_id = ?";
    $checkStmt = mysqli_prepare($conn, $checkSql);
    mysqli_stmt_bind_param($checkStmt, "s", $id);
    mysqli_stmt_execute($checkStmt);
    $checkResult = mysqli_stmt_get_result($checkStmt);
    
    if (mysqli_num_rows($checkResult) > 0) {
        mysqli_stmt_close($checkStmt);
        mysqli_close($conn);
        return false; // Không thể xóa vì có booking
    }
    mysqli_stmt_close($checkStmt);
    
    $sql = "DELETE FROM guests WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);
    
    $result = mysqli_stmt_execute($stmt);
    
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    
    return $result;
}

/**
 * Tìm kiếm khách hàng
 */
function searchGuests($keyword) {
    $conn = getDbConnection();
    $keyword = "%$keyword%";
    
    $sql = "SELECT * FROM guests WHERE 
            full_name LIKE ? OR 
            email LIKE ? OR 
            phone_number LIKE ? OR 
            id_card_number LIKE ? 
            ORDER BY full_name ASC";
    
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ssss", $keyword, $keyword, $keyword, $keyword);
    mysqli_stmt_execute($stmt);
    
    $result = mysqli_stmt_get_result($stmt);
    $guests = [];
    
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $guests[] = $row;
        }
    }
    
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    
    return $guests;
}

?>
