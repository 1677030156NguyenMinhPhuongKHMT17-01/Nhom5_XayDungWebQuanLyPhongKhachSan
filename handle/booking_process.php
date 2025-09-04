<?php
session_start();
require_once '../functions/booking_functions.php';
require_once '../functions/guest_functions.php';
require_once '../functions/room_functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' && !isset($_GET['action'])) {
    header('Location: ../views/booking.php');
    exit();
}

$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch ($action) {
    case 'create':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $guest_id = $_POST['guest_id'] ?? '';
            $room_id = $_POST['room_id'] ?? 0;
            $total_price = $_POST['total_price'] ?? 0;
            $status = $_POST['status'] ?? 'pending';

            // Validate dữ liệu
            if (empty($guest_id) || empty($room_id) || $total_price <= 0) {
                $_SESSION['error'] = 'Vui lòng điền đầy đủ thông tin hợp lệ!';
                header('Location: ../views/booking/create_booking.php');
                exit();
            }

            // Tạo booking mới
            $result = createBooking($guest_id, $room_id, $total_price, $status);

            if ($result) {
                $_SESSION['success'] = 'Tạo đặt phòng thành công!';
                header('Location: ../views/booking.php');
            } else {
                $_SESSION['error'] = 'Không thể tạo đặt phòng. Phòng có thể không còn trống hoặc khách hàng không tồn tại!';
                header('Location: ../views/booking/create_booking.php');
            }
        }
        break;

    case 'update':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? 0;
            $guest_id = $_POST['guest_id'] ?? '';
            $room_id = $_POST['room_id'] ?? 0;
            $total_price = $_POST['total_price'] ?? 0;
            $status = $_POST['status'] ?? 'pending';

            // Validate dữ liệu
            if (empty($id) || empty($guest_id) || empty($room_id) || $total_price <= 0) {
                $_SESSION['error'] = 'Vui lòng điền đầy đủ thông tin hợp lệ!';
                header("Location: ../views/booking/edit_booking.php?id=$id");
                exit();
            }

            // Cập nhật booking
            $result = updateBooking($id, $guest_id, $room_id, $total_price, $status);

            if ($result) {
                $_SESSION['success'] = 'Cập nhật đặt phòng thành công!';
                header('Location: ../views/booking.php');
            } else {
                $_SESSION['error'] = 'Không thể cập nhật đặt phòng. Phòng có thể không còn trống!';
                header("Location: ../views/booking/edit_booking.php?id=$id");
            }
        }
        break;

    case 'delete':
        $id = $_GET['id'] ?? 0;

        if (empty($id)) {
            $_SESSION['error'] = 'Không tìm thấy đặt phòng cần xóa!';
            header('Location: ../views/booking.php');
            exit();
        }

        $result = deleteBooking($id);

        if ($result) {
            $_SESSION['success'] = 'Xóa đặt phòng thành công!';
        } else {
            $_SESSION['error'] = 'Không thể xóa đặt phòng!';
        }

        header('Location: ../views/booking.php');
        break;

    default:
        $_SESSION['error'] = 'Hành động không hợp lệ!';
        header('Location: ../views/booking.php');
        break;
}

exit();
?>
