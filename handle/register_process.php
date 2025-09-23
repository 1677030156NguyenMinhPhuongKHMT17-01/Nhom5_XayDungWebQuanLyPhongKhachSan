<?php
session_start();
require_once '../functions/db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    handleRegister();
}

function handleRegister() {
    $conn = getDbConnection();
    
    $fullname = trim($_POST['fullname'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Validate input
    if (empty($fullname) || empty($email) || empty($username) || empty($password) || empty($confirm_password)) {
        $_SESSION['error'] = 'Vui lòng điền đầy đủ tất cả các trường!';
        header('Location: ../register.php');
        exit();
    }

    // Check if passwords match
    if ($password !== $confirm_password) {
        $_SESSION['error'] = 'Mật khẩu xác nhận không khớp!';
        header('Location: ../register.php');
        exit();
    }

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = 'Định dạng email không hợp lệ!';
        header('Location: ../register.php');
        exit();
    }

    // Check if username already exists
    $checkUserSql = "SELECT id FROM users WHERE username = ? LIMIT 1";
    $checkStmt = mysqli_prepare($conn, $checkUserSql);
    if (!$checkStmt) {
        $_SESSION['error'] = 'Lỗi hệ thống: ' . mysqli_error($conn);
        header('Location: ../register.php');
        exit();
    }

    mysqli_stmt_bind_param($checkStmt, "s", $username);
    mysqli_stmt_execute($checkStmt);
    $result = mysqli_stmt_get_result($checkStmt);

    if (mysqli_num_rows($result) > 0) {
        mysqli_stmt_close($checkStmt);
        $_SESSION['error'] = 'Username đã tồn tại! Vui lòng chọn username khác.';
        header('Location: ../register.php');
        exit();
    }
    mysqli_stmt_close($checkStmt);

    // Check if email already exists
    $checkEmailSql = "SELECT id FROM users WHERE email = ? LIMIT 1";
    $checkEmailStmt = mysqli_prepare($conn, $checkEmailSql);
    if (!$checkEmailStmt) {
        $_SESSION['error'] = 'Lỗi hệ thống: ' . mysqli_error($conn);
        header('Location: ../register.php');
        exit();
    }

    mysqli_stmt_bind_param($checkEmailStmt, "s", $email);
    mysqli_stmt_execute($checkEmailStmt);
    $emailResult = mysqli_stmt_get_result($checkEmailStmt);

    if (mysqli_num_rows($emailResult) > 0) {
        mysqli_stmt_close($checkEmailStmt);
        $_SESSION['error'] = 'Email đã được sử dụng! Vui lòng sử dụng email khác.';
        header('Location: ../register.php');
        exit();
    }
    mysqli_stmt_close($checkEmailStmt);

    // Hash password (for security - although the original system doesn't use this)
    // For compatibility with existing system, we'll store plain text password
    // In production, you should use: $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
    // Insert new user
    $insertSql = "INSERT INTO users (username, password, email, fullname, role, created_at) VALUES (?, ?, ?, ?, 'user', NOW())";
    $insertStmt = mysqli_prepare($conn, $insertSql);
    
    if (!$insertStmt) {
        $_SESSION['error'] = 'Lỗi hệ thống: ' . mysqli_error($conn);
        header('Location: ../register.php');
        exit();
    }

    mysqli_stmt_bind_param($insertStmt, "ssss", $username, $password, $email, $fullname);
    
    if (mysqli_stmt_execute($insertStmt)) {
        mysqli_stmt_close($insertStmt);
        mysqli_close($conn);
        
        $_SESSION['success'] = 'Đăng ký thành công! Bạn có thể đăng nhập ngay bây giờ.';
        header('Location: ../index.php');
        exit();
    } else {
        mysqli_stmt_close($insertStmt);
        $_SESSION['error'] = 'Có lỗi xảy ra khi tạo tài khoản: ' . mysqli_error($conn);
        header('Location: ../register.php');
        exit();
    }
}
?>