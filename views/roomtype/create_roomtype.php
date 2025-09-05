<?php
require_once '../../functions/roomtype_functions.php';
require_once '../menu.php';
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm loại phòng mới</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../../css/dark-mode.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">
                            <i class="fas fa-bed"></i> Thêm loại phòng mới
                        </h4>
                    </div>
                    <div class="card-body">
                        <!-- Hiển thị thông báo lỗi -->
                        <?php if (isset($_SESSION['error'])): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <?= $_SESSION['error'] ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                            <?php unset($_SESSION['error']); ?>
                        <?php endif; ?>

                        <form action="../../handle/roomtype_process.php" method="POST">
                            <input type="hidden" name="action" value="create">
                            
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label for="name_Room_Type" class="form-label">Tên loại phòng <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="name_Room_Type" name="name_Room_Type" 
                                               placeholder="VD: Phòng Deluxe, Phòng Suite..."
                                               value="<?= isset($_POST['name_Room_Type']) ? htmlspecialchars($_POST['name_Room_Type']) : '' ?>" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="price_per_night" class="form-label">Giá mỗi đêm (VNĐ) <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" id="price_per_night" name="price_per_night" 
                                               placeholder="500000" min="0" step="1000"
                                               value="<?= isset($_POST['price_per_night']) ? $_POST['price_per_night'] : '' ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="capacity" class="form-label">Sức chứa (người) <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" id="capacity" name="capacity" 
                                               placeholder="2" min="1" max="10"
                                               value="<?= isset($_POST['capacity']) ? $_POST['capacity'] : '' ?>" required>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="../roomtype.php" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Quay lại
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Lưu loại phòng
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://kit.fontawesome.com/your-fontawesome-kit.js"></script>
    <script src="../../js/dark-mode.js"></script>
</body>

</html>
