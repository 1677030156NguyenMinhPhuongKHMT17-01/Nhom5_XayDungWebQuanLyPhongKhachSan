<?php
session_start();
require_once '../functions/roomtype_functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' && !isset($_GET['action'])) {
    header('Location: ../views/roomtype.php');
    exit();
}

/**
 * Xử lý upload ảnh
 */
function uploadImage($file) {
    if (empty($file['name'])) {
        return null; // Không có file upload
    }
    
    $uploadDir = '../images/rooms/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    // Validate file
    $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
    $maxSize = 2 * 1024 * 1024; // 2MB
    
    if (!in_array($file['type'], $allowedTypes)) {
        throw new Exception('Chỉ chấp nhận file ảnh (JPG, PNG, GIF)!');
    }
    
    if ($file['size'] > $maxSize) {
        throw new Exception('Kích thước file quá lớn (tối đa 2MB)!');
    }
    
    // Tạo tên file unique
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $fileName = 'room_' . time() . '_' . uniqid() . '.' . $extension;
    $filePath = $uploadDir . $fileName;
    
    if (move_uploaded_file($file['tmp_name'], $filePath)) {
        return $fileName;
    } else {
        throw new Exception('Không thể upload file!');
    }
}

$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch ($action) {
    case 'create':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name_Room_Type = trim($_POST['name_Room_Type'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $price_per_night = $_POST['price_per_night'] ?? 0;
            $capacity = $_POST['capacity'] ?? 0;

            // Validate dữ liệu
            if (empty($name_Room_Type) || $price_per_night <= 0 || $capacity <= 0) {
                $_SESSION['error'] = 'Vui lòng điền đầy đủ thông tin hợp lệ!';
                header('Location: ../views/roomtype/create_roomtype.php');
                exit();
            }

            try {
                // Xử lý upload ảnh
                $imageName = null;
                if (!empty($_FILES['image']['name'])) {
                    $imageName = uploadImage($_FILES['image']);
                }

                // Tạo loại phòng mới
                $result = createRoomType($name_Room_Type, $description, $imageName, $price_per_night, $capacity);

                if ($result) {
                    $_SESSION['success'] = 'Thêm loại phòng thành công!';
                    header('Location: ../views/roomtype.php');
                } else {
                    $_SESSION['error'] = 'Không thể thêm loại phòng. Tên loại phòng có thể đã tồn tại!';
                    header('Location: ../views/roomtype/create_roomtype.php');
                }
            } catch (Exception $e) {
                $_SESSION['error'] = $e->getMessage();
                header('Location: ../views/roomtype/create_roomtype.php');
            }
        }
        break;

    case 'update':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? 0;
            $name_Room_Type = trim($_POST['name_Room_Type'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $price_per_night = $_POST['price_per_night'] ?? 0;
            $capacity = $_POST['capacity'] ?? 0;

            // Validate dữ liệu
            if (empty($id) || empty($name_Room_Type) || $price_per_night <= 0 || $capacity <= 0) {
                $_SESSION['error'] = 'Vui lòng điền đầy đủ thông tin hợp lệ!';
                header("Location: ../views/roomtype/edit_roomtype.php?id=$id");
                exit();
            }

            try {
                // Lấy thông tin hiện tại để giữ lại ảnh cũ nếu không upload ảnh mới
                $currentRoomType = getRoomTypeById($id);
                $imageName = $currentRoomType['image'] ?? null;
                
                // Xử lý upload ảnh mới (nếu có)
                if (!empty($_FILES['image']['name'])) {
                    // Xóa ảnh cũ nếu có
                    if (!empty($currentRoomType['image'])) {
                        $oldImagePath = '../images/rooms/' . $currentRoomType['image'];
                        if (file_exists($oldImagePath)) {
                            unlink($oldImagePath);
                        }
                    }
                    $imageName = uploadImage($_FILES['image']);
                }

                // Cập nhật loại phòng
                $result = updateRoomType($id, $name_Room_Type, $description, $imageName, $price_per_night, $capacity);

                if ($result) {
                    $_SESSION['success'] = 'Cập nhật loại phòng thành công!';
                    header('Location: ../views/roomtype.php');
                } else {
                    $_SESSION['error'] = 'Không thể cập nhật loại phòng. Tên loại phòng có thể đã tồn tại!';
                    header("Location: ../views/roomtype/edit_roomtype.php?id=$id");
                }
            } catch (Exception $e) {
                $_SESSION['error'] = $e->getMessage();
                header("Location: ../views/roomtype/edit_roomtype.php?id=$id");
            }
        }
        break;

    case 'delete':
        $id = $_GET['id'] ?? 0;
        
        if (empty($id)) {
            $_SESSION['error'] = 'ID loại phòng không hợp lệ!';
            header('Location: ../views/roomtype.php');
            exit();
        }

        // Lấy thông tin để xóa ảnh
        $roomType = getRoomTypeById($id);
        
        $result = deleteRoomType($id);
        
        if ($result) {
            // Xóa ảnh nếu có
            if (!empty($roomType['image'])) {
                $imagePath = '../images/rooms/' . $roomType['image'];
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }
            $_SESSION['success'] = 'Xóa loại phòng thành công!';
        } else {
            $_SESSION['error'] = 'Không thể xóa loại phòng. Có thể đang được sử dụng!';
        }
        
        header('Location: ../views/roomtype.php');
        break;

    default:
        header('Location: ../views/roomtype.php');
        break;
}
?>