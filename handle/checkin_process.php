<?php
session_start();
require_once '../functions/checkin_functions.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit();
}

$action = $_POST['action'] ?? '';
$booking_id = (int)($_POST['booking_id'] ?? 0);

if (empty($action) || empty($booking_id)) {
    echo json_encode(['success' => false, 'message' => 'Thiếu thông tin cần thiết!']);
    exit();
}

switch ($action) {
    case 'checkin':
        $result = checkInBooking($booking_id);
        echo json_encode($result);
        break;
        
    case 'checkout':
        $additional_charges = (float)($_POST['additional_charges'] ?? 0);
        $result = checkOutBooking($booking_id, $additional_charges);
        echo json_encode($result);
        break;
        
    default:
        echo json_encode(['success' => false, 'message' => 'Hành động không hợp lệ!']);
        break;
}
?>