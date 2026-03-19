<?php
/**
 * Profile Functions
 * Quản lý thông tin hồ sơ cá nhân người dùng
 */

require_once 'db_connection.php';

/**
 * Lấy thông tin profile đầy đủ của user
 * @param int $userId
 * @return array|null
 */
function getUserProfile($userId) {
    $conn = getDbConnection();
    $sql = "SELECT u.id, u.username, u.role, u.email, u.full_name, u.phone, u.bio, u.avatar, u.created_at
            FROM users u
            WHERE u.id = ?";
    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        mysqli_close($conn);
        return null;
    }

    mysqli_stmt_bind_param($stmt, "i", $userId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $profile = null;
    if ($result && mysqli_num_rows($result) > 0) {
        $profile = mysqli_fetch_assoc($result);
    }

    mysqli_stmt_close($stmt);
    mysqli_close($conn);

    return $profile;
}

/**
 * Cập nhật thông tin profile của user
 * @param int $userId
 * @param array $data (full_name, email, phone, bio)
 * @return bool
 */
function updateUserProfile($userId, $data) {
    $conn = getDbConnection();

    $sql = "UPDATE users SET
            full_name = ?,
            email = ?,
            phone = ?,
            bio = ?
            WHERE id = ?";

    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        mysqli_close($conn);
        return false;
    }

    mysqli_stmt_bind_param($stmt, "ssssi",
        $data['full_name'],
        $data['email'],
        $data['phone'],
        $data['bio'],
        $userId
    );

    $result = mysqli_stmt_execute($stmt);

    mysqli_stmt_close($stmt);
    mysqli_close($conn);

    return $result;
}

/**
 * Đổi mật khẩu user
 * @param int $userId
 * @param string $oldPassword
 * @param string $newPassword
 * @return array ['success' => bool, 'message' => string]
 */
function changePassword($userId, $oldPassword, $newPassword) {
    $conn = getDbConnection();

    // Kiểm tra mật khẩu cũ
    $sql = "SELECT password FROM users WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        mysqli_close($conn);
        return ['success' => false, 'message' => 'Lỗi hệ thống'];
    }

    mysqli_stmt_bind_param($stmt, "i", $userId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);

        // Kiểm tra mật khẩu cũ
        if ($oldPassword !== $user['password']) {
            mysqli_close($conn);
            return ['success' => false, 'message' => 'Mật khẩu cũ không đúng'];
        }

        // Cập nhật mật khẩu mới
        $sql = "UPDATE users SET password = ? WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);

        if (!$stmt) {
            mysqli_close($conn);
            return ['success' => false, 'message' => 'Lỗi hệ thống'];
        }

        mysqli_stmt_bind_param($stmt, "si", $newPassword, $userId);
        $result = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        mysqli_close($conn);

        if ($result) {
            return ['success' => true, 'message' => 'Đổi mật khẩu thành công'];
        } else {
            return ['success' => false, 'message' => 'Không thể cập nhật mật khẩu'];
        }
    }

    if ($stmt) mysqli_stmt_close($stmt);
    mysqli_close($conn);
    return ['success' => false, 'message' => 'Không tìm thấy người dùng'];
}

/**
 * Lấy thống kê hoạt động của user
 * @param int $userId
 * @return array
 */
function getUserStats($userId) {
    $conn = getDbConnection();

    // Đếm số lượng bookings đã tạo (nếu có tracking)
    // Hiện tại return stats cơ bản
    $stats = [
        'total_logins' => 0,
        'last_login' => null,
        'account_age_days' => 0
    ];

    $sql = "SELECT created_at FROM users WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $userId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($result && mysqli_num_rows($result) > 0) {
            $user = mysqli_fetch_assoc($result);
            if ($user['created_at']) {
                $createdDate = new DateTime($user['created_at']);
                $now = new DateTime();
                $stats['account_age_days'] = $now->diff($createdDate)->days;
            }
        }
        mysqli_stmt_close($stmt);
    }

    mysqli_close($conn);
    return $stats;
}

/**
 * Kiểm tra email đã tồn tại chưa (cho user khác)
 * @param string $email
 * @param int $excludeUserId
 * @return bool
 */
function isEmailTaken($email, $excludeUserId = null) {
    if (empty($email)) return false;

    $conn = getDbConnection();
    $sql = "SELECT id FROM users WHERE email = ?";

    if ($excludeUserId) {
        $sql .= " AND id != ?";
    }

    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        mysqli_close($conn);
        return false;
    }

    if ($excludeUserId) {
        mysqli_stmt_bind_param($stmt, "si", $email, $excludeUserId);
    } else {
        mysqli_stmt_bind_param($stmt, "s", $email);
    }

    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $exists = ($result && mysqli_num_rows($result) > 0);

    mysqli_stmt_close($stmt);
    mysqli_close($conn);

    return $exists;
}

?>
