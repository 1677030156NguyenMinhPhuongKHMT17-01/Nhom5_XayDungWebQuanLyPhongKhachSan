<?php
require_once '../functions/auth.php';
require_once '../functions/profile_functions.php';

// Kiểm tra đăng nhập
checkLogin('../index.php');
$currentUser = getCurrentUser();

// Thiết lập thông tin trang
$pageTitle = 'Hồ sơ của tôi';
$baseUrl = '../';

// Lấy thông tin profile đầy đủ
$profile = getUserProfile($currentUser['id']);
$stats = getUserStats($currentUser['id']);

// Nếu không có profile, sử dụng thông tin từ session
if (!$profile) {
    $profile = [
        'id' => $currentUser['id'],
        'username' => $currentUser['username'],
        'role' => $currentUser['role'],
        'email' => '',
        'full_name' => '',
        'phone' => '',
        'bio' => '',
        'avatar' => '',
        'created_at' => date('Y-m-d H:i:s')
    ];
}

// Include layout header
include '../layout/admin_header.php';
?>

<!-- Profile Content -->
<div class="row">
    <div class="col-12">
        <div class="pagetitle">
            <h1><i class="bi bi-person-circle text-primary"></i> Hồ sơ của tôi</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="dashboard.php">Trang chủ</a></li>
                    <li class="breadcrumb-item active">Hồ sơ</li>
                </ol>
            </nav>
        </div>
    </div>
</div>

<!-- Alert Messages -->
<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-1"></i>
        <?= htmlspecialchars($_SESSION['success']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle me-1"></i>
        <?= htmlspecialchars($_SESSION['error']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>

<div class="row">
    <!-- Profile Info Card -->
    <div class="col-xl-4">
        <div class="card">
            <div class="card-body profile-card pt-4 d-flex flex-column align-items-center">
                <img src="<?= !empty($profile['avatar']) ? $baseUrl . $profile['avatar'] : $baseUrl . 'images/fitdnu_logo.png' ?>"
                     alt="Profile" class="rounded-circle" style="width: 120px; height: 120px; object-fit: cover;">
                <h2 class="mt-3"><?= htmlspecialchars($profile['full_name'] ?: $profile['username']) ?></h2>
                <h3><?= htmlspecialchars($profile['role']) ?></h3>
                <div class="social-links mt-2">
                    <a href="#" class="twitter"><i class="bi bi-twitter"></i></a>
                    <a href="#" class="facebook"><i class="bi bi-facebook"></i></a>
                    <a href="#" class="instagram"><i class="bi bi-instagram"></i></a>
                    <a href="#" class="linkedin"><i class="bi bi-linkedin"></i></a>
                </div>
            </div>
        </div>

        <!-- Account Stats Card -->
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Thống kê tài khoản</h5>
                <div class="row">
                    <div class="col-12 mb-3">
                        <div class="d-flex align-items-center">
                            <div class="card-icon rounded-circle d-flex align-items-center justify-content-center"
                                 style="width: 50px; height: 50px; background-color: #e6f3ff;">
                                <i class="bi bi-calendar-check text-primary" style="font-size: 24px;"></i>
                            </div>
                            <div class="ps-3">
                                <h6 class="mb-0">Ngày tham gia</h6>
                                <span class="text-muted small"><?= $stats['account_age_days'] ?> ngày trước</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="d-flex align-items-center">
                            <div class="card-icon rounded-circle d-flex align-items-center justify-content-center"
                                 style="width: 50px; height: 50px; background-color: #fff3e6;">
                                <i class="bi bi-shield-check text-warning" style="font-size: 24px;"></i>
                            </div>
                            <div class="ps-3">
                                <h6 class="mb-0">Trạng thái</h6>
                                <span class="badge bg-success">Hoạt động</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Profile Details and Edit Forms -->
    <div class="col-xl-8">
        <div class="card">
            <div class="card-body pt-3">
                <!-- Tabs -->
                <ul class="nav nav-tabs nav-tabs-bordered" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#profile-overview"
                                type="button" role="tab">Tổng quan</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#profile-edit"
                                type="button" role="tab">Chỉnh sửa hồ sơ</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#profile-change-password"
                                type="button" role="tab">Đổi mật khẩu</button>
                    </li>
                </ul>

                <div class="tab-content pt-3">
                    <!-- Profile Overview Tab -->
                    <div class="tab-pane fade show active profile-overview" id="profile-overview" role="tabpanel">
                        <h5 class="card-title">Giới thiệu</h5>
                        <p class="small fst-italic">
                            <?= !empty($profile['bio']) ? nl2br(htmlspecialchars($profile['bio'])) : 'Chưa có thông tin giới thiệu.' ?>
                        </p>

                        <h5 class="card-title">Chi tiết hồ sơ</h5>
                        <div class="row mb-2">
                            <div class="col-lg-3 col-md-4 label fw-bold">Tên đăng nhập</div>
                            <div class="col-lg-9 col-md-8"><?= htmlspecialchars($profile['username']) ?></div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-lg-3 col-md-4 label fw-bold">Họ và tên</div>
                            <div class="col-lg-9 col-md-8"><?= htmlspecialchars($profile['full_name'] ?: 'Chưa cập nhật') ?></div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-lg-3 col-md-4 label fw-bold">Vai trò</div>
                            <div class="col-lg-9 col-md-8">
                                <span class="badge bg-primary"><?= htmlspecialchars($profile['role']) ?></span>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-lg-3 col-md-4 label fw-bold">Email</div>
                            <div class="col-lg-9 col-md-8"><?= htmlspecialchars($profile['email'] ?: 'Chưa cập nhật') ?></div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-lg-3 col-md-4 label fw-bold">Số điện thoại</div>
                            <div class="col-lg-9 col-md-8"><?= htmlspecialchars($profile['phone'] ?: 'Chưa cập nhật') ?></div>
                        </div>
                    </div>

                    <!-- Edit Profile Tab -->
                    <div class="tab-pane fade profile-edit" id="profile-edit" role="tabpanel">
                        <h5 class="card-title">Chỉnh sửa hồ sơ</h5>
                        <form action="<?= $baseUrl ?>handle/profile_process.php" method="POST">
                            <input type="hidden" name="action" value="update_profile">

                            <div class="row mb-3">
                                <label for="username" class="col-md-4 col-lg-3 col-form-label">Tên đăng nhập</label>
                                <div class="col-md-8 col-lg-9">
                                    <input type="text" class="form-control" id="username"
                                           value="<?= htmlspecialchars($profile['username']) ?>" disabled>
                                    <small class="text-muted">Tên đăng nhập không thể thay đổi</small>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="full_name" class="col-md-4 col-lg-3 col-form-label">Họ và tên</label>
                                <div class="col-md-8 col-lg-9">
                                    <input type="text" class="form-control" id="full_name" name="full_name"
                                           value="<?= htmlspecialchars($profile['full_name']) ?>"
                                           placeholder="Nhập họ và tên đầy đủ">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="email" class="col-md-4 col-lg-3 col-form-label">Email</label>
                                <div class="col-md-8 col-lg-9">
                                    <input type="email" class="form-control" id="email" name="email"
                                           value="<?= htmlspecialchars($profile['email']) ?>"
                                           placeholder="example@email.com">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="phone" class="col-md-4 col-lg-3 col-form-label">Số điện thoại</label>
                                <div class="col-md-8 col-lg-9">
                                    <input type="tel" class="form-control" id="phone" name="phone"
                                           value="<?= htmlspecialchars($profile['phone']) ?>"
                                           placeholder="0123456789">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="bio" class="col-md-4 col-lg-3 col-form-label">Giới thiệu</label>
                                <div class="col-md-8 col-lg-9">
                                    <textarea class="form-control" id="bio" name="bio" rows="5"
                                              placeholder="Giới thiệu về bản thân..."><?= htmlspecialchars($profile['bio']) ?></textarea>
                                    <small class="text-muted">Chia sẻ một chút về bản thân bạn, công việc và sở thích</small>
                                </div>
                            </div>

                            <div class="text-center">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-save"></i> Lưu thay đổi
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Change Password Tab -->
                    <div class="tab-pane fade" id="profile-change-password" role="tabpanel">
                        <h5 class="card-title">Đổi mật khẩu</h5>
                        <form action="<?= $baseUrl ?>handle/profile_process.php" method="POST">
                            <input type="hidden" name="action" value="change_password">

                            <div class="row mb-3">
                                <label for="old_password" class="col-md-4 col-lg-3 col-form-label">Mật khẩu hiện tại</label>
                                <div class="col-md-8 col-lg-9">
                                    <input type="password" class="form-control" id="old_password"
                                           name="old_password" required>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="new_password" class="col-md-4 col-lg-3 col-form-label">Mật khẩu mới</label>
                                <div class="col-md-8 col-lg-9">
                                    <input type="password" class="form-control" id="new_password"
                                           name="new_password" required minlength="6">
                                    <small class="text-muted">Mật khẩu phải có ít nhất 6 ký tự</small>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="confirm_password" class="col-md-4 col-lg-3 col-form-label">Xác nhận mật khẩu mới</label>
                                <div class="col-md-8 col-lg-9">
                                    <input type="password" class="form-control" id="confirm_password"
                                           name="confirm_password" required minlength="6">
                                </div>
                            </div>

                            <div class="text-center">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-key"></i> Đổi mật khẩu
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../layout/admin_footer.php'; ?>
