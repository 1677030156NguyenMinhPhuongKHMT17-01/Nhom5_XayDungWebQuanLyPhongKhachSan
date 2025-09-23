<?php
/**
 * Database Configuration Template
 * Copy this file to db_connection.php and update with your database credentials
 */

function getDbConnection() {
    // Cấu hình database
    $servername = "localhost";      
    $username = "root";             
    $password = "nope1234!";        // Database password
    $dbname = "ql_phongks";        
    $port = 3306;                   

    // Tạo kết nối
    $conn = mysqli_connect($servername, $username, $password, $dbname, $port);

    // Kiểm tra kết nối
    if (!$conn) {
        die("Kết nối database thất bại: " . mysqli_connect_error());
    }
    
    // Thiết lập charset cho kết nối (quan trọng để hiển thị tiếng Việt đúng)
    mysqli_set_charset($conn, "utf8");
    
    return $conn;
}

/**
 * HƯỚNG DẪN CẤU HÌNH:
 * 
 * 1. Copy file này thành 'db_connection.php'
 * 2. Cập nhật thông tin database của bạn:
 *    - $username: tên đăng nhập MySQL
 *    - $password: mật khẩu MySQL
 *    - $dbname: tên database (mặc định: ql_phongks)
 * 
 * 3. Tạo database bằng cách import file SQL có sẵn
 * 4. Chạy script database_update.sql để có đầy đủ tính năng
 */
?>