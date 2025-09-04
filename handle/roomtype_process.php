<?php
session_start();
require_once '../functions/roomtype_functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' && !isset($_GET['action'])) {
    header('Location: ../views/roomtype.php');
    exit();
}

$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch ($action) {
    case 'create':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name_Room_Type = trim($_POST['name_Room_Type'] ?? '');
            $price_per_night = $_POST['price_per_night'] ?? 0;
            $capacity = $_POST['capacity'] ?? 0;

            // Validate dữ liệu
            if (empty($name_Room_Type) || $price_per_night <= 0 || $capacity <= 0) {
                $_SESSION['error'] = 'Vui lòng điền đầy đủ thông tin hợp lệ!';
                header('Location: ../views/roomtype/create_roomtype.php');
                exit();
            }

            // Tạo loại phòng mới
            $result = createRoomType($name_Room_Type, $price_per_night, $capacity);

            if ($result) {
                $_SESSION['success'] = 'Thêm loại phòng thành công!';
                header('Location: ../views/roomtype.php');
            } else {
                $_SESSION['error'] = 'Không thể thêm loại phòng. Tên loại phòng có thể đã tồn tại!';
                header('Location: ../views/roomtype/create_roomtype.php');
            }
        }
        break;

    case 'update':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? 0;
            $name_Room_Type = trim($_POST['name_Room_Type'] ?? '');
            $price_per_night = $_POST['price_per_night'] ?? 0;
            $capacity = $_POST['capacity'] ?? 0;

            // Validate dữ liệu
            if (empty($id) || empty($name_Room_Type) || $price_per_night <= 0 || $capacity <= 0) {
                $_SESSION['error'] = 'Vui lòng điền đầy đủ thông tin hợp lệ!';
                header("Location: ../views/roomtype/edit_roomtype.php?id=$id");
                exit();
            }

            // Cập nhật loại phòng
            $result = updateRoomType($id, $name_Room_Type, $price_per_night, $capacity);

            if ($result) {
                $_SESSION['success'] = 'Cập nhật loại phòng thành công!';
                header('Location: ../views/roomtype.php');
            } else {
                $_SESSION['error'] = 'Không thể cập nhật loại phòng. Tên loại phòng có thể đã tồn tại!';
                header("Location: ../views/roomtype/edit_roomtype.php?id=$id");
            }
        }
        break;

    case 'delete':
        $id = $_GET['id'] ?? 0;

        if (empty($id)) {
            $_SESSION['error'] = 'Không tìm thấy loại phòng cần xóa!';
            header('Location: ../views/roomtype.php');
            exit();
        }

        $result = deleteRoomType($id);

        if ($result) {
            $_SESSION['success'] = 'Xóa loại phòng thành công!';
        } else {
            $_SESSION['error'] = 'Không thể xóa loại phòng. Có thể loại phòng đang được sử dụng!';
        }

        header('Location: ../views/roomtype.php');
        break;

    default:
        $_SESSION['error'] = 'Hành động không hợp lệ!';
        header('Location: ../views/roomtype.php');
        break;
}

exit();
?>
