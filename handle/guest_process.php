<?php
session_start();
require_once '../functions/guest_functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' && !isset($_GET['action'])) {
    header('Location: ../views/guest.php');
    exit();
}

$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch ($action) {
    case 'create':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $full_name = trim($_POST['full_name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $phone_number = trim($_POST['phone_number'] ?? '');
            $id_card_number = trim($_POST['id_card_number'] ?? '');

            // Validate dữ liệu
            if (empty($full_name) || empty($email) || empty($phone_number) || empty($id_card_number)) {
                $_SESSION['error'] = 'Vui lòng điền đầy đủ thông tin!';
                header('Location: ../views/guest/create_guest.php');
                exit();
            }

            // Validate email format
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $_SESSION['error'] = 'Định dạng email không hợp lệ!';
                header('Location: ../views/guest/create_guest.php');
                exit();
            }

            // Tạo khách hàng mới
            $result = createGuest($full_name, $email, $phone_number, $id_card_number);

            if ($result) {
                $_SESSION['success'] = 'Thêm khách hàng thành công!';
                header('Location: ../views/guest.php');
            } else {
                $_SESSION['error'] = 'Không thể thêm khách hàng. Email, số điện thoại hoặc CMND có thể đã tồn tại!';
                header('Location: ../views/guest/create_guest.php');
            }
        }
        break;

    case 'update':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? 0;
            $full_name = trim($_POST['full_name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $phone_number = trim($_POST['phone_number'] ?? '');
            $id_card_number = trim($_POST['id_card_number'] ?? '');

            // Validate dữ liệu
            if (empty($id) || empty($full_name) || empty($email) || empty($phone_number) || empty($id_card_number)) {
                $_SESSION['error'] = 'Vui lòng điền đầy đủ thông tin!';
                header("Location: ../views/guest/edit_guest.php?id=$id");
                exit();
            }

            // Validate email format
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $_SESSION['error'] = 'Định dạng email không hợp lệ!';
                header("Location: ../views/guest/edit_guest.php?id=$id");
                exit();
            }

            // Cập nhật khách hàng
            $result = updateGuest($id, $full_name, $email, $phone_number, $id_card_number);

            if ($result) {
                $_SESSION['success'] = 'Cập nhật thông tin khách hàng thành công!';
                header('Location: ../views/guest.php');
            } else {
                $_SESSION['error'] = 'Không thể cập nhật khách hàng. Email, số điện thoại hoặc CMND có thể đã tồn tại!';
                header("Location: ../views/guest/edit_guest.php?id=$id");
            }
        }
        break;

    case 'delete':
        $id = $_GET['id'] ?? 0;

        if (empty($id)) {
            $_SESSION['error'] = 'Không tìm thấy khách hàng cần xóa!';
            header('Location: ../views/guest.php');
            exit();
        }

        $result = deleteGuest($id);

        if ($result) {
            $_SESSION['success'] = 'Xóa khách hàng thành công!';
        } else {
            $_SESSION['error'] = 'Không thể xóa khách hàng. Có thể khách hàng đang có đặt phòng!';
        }

        header('Location: ../views/guest.php');
        break;

    default:
        $_SESSION['error'] = 'Hành động không hợp lệ!';
        header('Location: ../views/guest.php');
        break;
}

exit();
?>
