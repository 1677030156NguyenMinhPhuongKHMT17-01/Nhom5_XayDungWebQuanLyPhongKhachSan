<?php
session_start();
require_once '../functions/room_functions.php';
require_once '../functions/roomtype_functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' && !isset($_GET['action'])) {
    header('Location: ../views/room.php');
    exit();
}

$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch ($action) {
    case 'create':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $room_number = trim($_POST['room_number'] ?? '');
            $room_type_id = $_POST['room_type_id'] ?? 0;
            $status = $_POST['status'] ?? 'available';

            // Validate dữ liệu
            if (empty($room_number) || empty($room_type_id)) {
                $_SESSION['error'] = 'Vui lòng điền đầy đủ thông tin!';
                header('Location: ../views/room/create_room.php');
                exit();
            }

            // Tạo phòng mới
            $result = createRoom($room_number, $room_type_id, $status);

            if ($result) {
                $_SESSION['success'] = 'Thêm phòng thành công!';
                header('Location: ../views/room.php');
            } else {
                $_SESSION['error'] = 'Không thể thêm phòng. Số phòng có thể đã tồn tại!';
                header('Location: ../views/room/create_room.php');
            }
        }
        break;

    case 'update':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? 0;
            $room_number = trim($_POST['room_number'] ?? '');
            $room_type_id = $_POST['room_type_id'] ?? 0;
            $status = $_POST['status'] ?? 'available';

            // Validate dữ liệu
            if (empty($id) || empty($room_number) || empty($room_type_id)) {
                $_SESSION['error'] = 'Vui lòng điền đầy đủ thông tin!';
                header("Location: ../views/room/edit_room.php?id=$id");
                exit();
            }

            // Cập nhật phòng
            $result = updateRoom($id, $room_number, $room_type_id, $status);

            if ($result) {
                $_SESSION['success'] = 'Cập nhật phòng thành công!';
                header('Location: ../views/room.php');
            } else {
                $_SESSION['error'] = 'Không thể cập nhật phòng. Số phòng có thể đã tồn tại!';
                header("Location: ../views/room/edit_room.php?id=$id");
            }
        }
        break;

    case 'delete':
        $id = $_GET['id'] ?? 0;

        if (empty($id)) {
            $_SESSION['error'] = 'Không tìm thấy phòng cần xóa!';
            header('Location: ../views/room.php');
            exit();
        }

        $result = deleteRoom($id);

        if ($result) {
            $_SESSION['success'] = 'Xóa phòng thành công!';
        } else {
            $_SESSION['error'] = 'Không thể xóa phòng. Có thể phòng đang có booking!';
        }

        header('Location: ../views/room.php');
        break;

    default:
        $_SESSION['error'] = 'Hành động không hợp lệ!';
        header('Location: ../views/room.php');
        break;
}

exit();
?>
