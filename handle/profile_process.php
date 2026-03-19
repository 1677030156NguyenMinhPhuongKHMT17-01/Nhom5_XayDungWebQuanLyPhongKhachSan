<?php
/**
 * Profile Process Handler
 * Xử lý các thao tác liên quan đến hồ sơ cá nhân
 */

session_start();
require_once '../functions/auth.php';
require_once '../functions/profile_functions.php';

// Kiểm tra đăng nhập
if (!isLoggedIn()) {
    $_SESSION['error'] = 'Bạn cần đăng nhập để thực hiện thao tác này!';
    header('Location: ../index.php');
    exit();
}

// Lấy thông tin user hiện tại
$currentUser = getCurrentUser();

// Kiểm tra method POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../views/profile.php');
    exit();
}

// Lấy action
$action = $_POST['action'] ?? '';

switch ($action) {
    case 'update_profile':
        // Lấy dữ liệu từ form
        $full_name = trim($_POST['full_name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $bio = trim($_POST['bio'] ?? '');

        // Validate email nếu có
        if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = 'Email không hợp lệ!';
            header('Location: ../views/profile.php');
            exit();
        }

        // Kiểm tra email đã tồn tại chưa
        if (!empty($email) && isEmailTaken($email, $currentUser['id'])) {
            $_SESSION['error'] = 'Email này đã được sử dụng bởi tài khoản khác!';
            header('Location: ../views/profile.php');
            exit();
        }

        // Validate số điện thoại nếu có
        if (!empty($phone) && !preg_match('/^[0-9]{10,11}$/', $phone)) {
            $_SESSION['error'] = 'Số điện thoại không hợp lệ! (10-11 chữ số)';
            header('Location: ../views/profile.php');
            exit();
        }

        // Chuẩn bị dữ liệu cập nhật
        $data = [
            'full_name' => $full_name,
            'email' => $email,
            'phone' => $phone,
            'bio' => $bio
        ];

        // Cập nhật profile
        if (updateUserProfile($currentUser['id'], $data)) {
            $_SESSION['success'] = 'Cập nhật hồ sơ thành công!';
        } else {
            $_SESSION['error'] = 'Không thể cập nhật hồ sơ. Vui lòng thử lại!';
        }

        header('Location: ../views/profile.php');
        exit();

    case 'change_password':
        // Lấy dữ liệu từ form
        $old_password = $_POST['old_password'] ?? '';
        $new_password = $_POST['new_password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';

        // Validate
        if (empty($old_password) || empty($new_password) || empty($confirm_password)) {
            $_SESSION['error'] = 'Vui lòng điền đầy đủ thông tin!';
            header('Location: ../views/profile.php');
            exit();
        }

        if (strlen($new_password) < 6) {
            $_SESSION['error'] = 'Mật khẩu mới phải có ít nhất 6 ký tự!';
            header('Location: ../views/profile.php');
            exit();
        }

        if ($new_password !== $confirm_password) {
            $_SESSION['error'] = 'Mật khẩu mới và xác nhận mật khẩu không khớp!';
            header('Location: ../views/profile.php');
            exit();
        }

        // Đổi mật khẩu
        $result = changePassword($currentUser['id'], $old_password, $new_password);

        if ($result['success']) {
            $_SESSION['success'] = $result['message'];
        } else {
            $_SESSION['error'] = $result['message'];
        }

        header('Location: ../views/profile.php');
        exit();

    default:
        $_SESSION['error'] = 'Hành động không hợp lệ!';
        header('Location: ../views/profile.php');
        exit();
}
?>
