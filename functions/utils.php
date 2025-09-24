<?php
/**
 * Utility Functions for BTL Hotel Management System
 * Optimized and DRY (Don't Repeat Yourself) implementation
 * Version: 2.0 - Cleaned up duplicates
 */

/**
 * HTML escape function
 */
function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

/**
 * Format currency amount
 */
function formatCurrency($amount) {
    return number_format($amount, 0, ',', '.') . ' VNĐ';
}

/**
 * Format date with default Vietnamese format
 */
function formatDate($date, $format = 'd/m/Y H:i') {
    if (!$date) return '';
    $dateTime = is_string($date) ? new DateTime($date) : $date;
    return $dateTime->format($format);
}

/**
 * Get status badge HTML
 */
function getStatusBadge($status, $type = 'booking') {
    $badges = [
        'booking' => [
            'pending' => ['class' => 'warning', 'icon' => 'bi-clock', 'text' => 'Chờ xác nhận'],
            'confirmed' => ['class' => 'success', 'icon' => 'bi-check-circle', 'text' => 'Đã xác nhận'],
            'cancelled' => ['class' => 'danger', 'icon' => 'bi-x-circle', 'text' => 'Đã hủy'],
            'completed' => ['class' => 'info', 'icon' => 'bi-check-all', 'text' => 'Hoàn thành']
        ],
        'room' => [
            'available' => ['class' => 'success', 'icon' => 'bi-check-circle', 'text' => 'Trống'],
            'occupied' => ['class' => 'danger', 'icon' => 'bi-person-fill', 'text' => 'Đã thuê'],
            'maintenance' => ['class' => 'warning', 'icon' => 'bi-tools', 'text' => 'Bảo trì']
        ]
    ];
    
    $badge = $badges[$type][$status] ?? ['class' => 'secondary', 'icon' => 'bi-question-circle', 'text' => ucfirst($status)];
    
    return sprintf(
        '<span class="badge bg-%s"><i class="%s me-1"></i>%s</span>',
        $badge['class'],
        $badge['icon'],
        $badge['text']
    );
}

/**
 * Generate action buttons for table rows with modern approach
 */
function getActionButtons($id, $editUrl, $deleteAction, $itemName = '') {
    $html = '<div class="btn-group" role="group">';
    
    // Edit button
    $html .= '<a href="' . e($editUrl) . '" class="btn btn-sm btn-outline-primary" title="Chỉnh sửa">';
    $html .= '<i class="bi bi-pencil"></i>';
    $html .= '</a>';
    
    // Delete button with data attributes for modern JS handling
    $html .= '<button type="button" class="btn btn-sm btn-outline-danger delete-btn" ';
    $html .= 'data-id="' . e($id) . '" ';
    $html .= 'data-action="' . e($deleteAction) . '" ';
    $html .= 'data-name="' . e($itemName) . '" ';
    $html .= 'title="Xóa">';
    $html .= '<i class="bi bi-trash"></i>';
    $html .= '</button>';
    
    $html .= '</div>';
    return $html;
}

/**
 * Show alert messages with Bootstrap styling
 */
function showAlert($type, $message, $dismissible = true) {
    $classes = "alert alert-$type";
    if ($dismissible) {
        $classes .= " alert-dismissible fade show";
    }
    
    $html = '<div class="' . $classes . '" role="alert">';
    $html .= e($message);
    
    if ($dismissible) {
        $html .= '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
    }
    
    $html .= '</div>';
    return $html;
}

/**
 * Generate search form with flexible options
 */
function searchForm($searchKeyword = '', $placeholder = 'Tìm kiếm...', $resetUrl = '') {
    ob_start();
    ?>
    <form method="GET" class="mb-4">
        <div class="row">
            <div class="col-md-8">
                <input type="text" name="search" class="form-control" 
                       placeholder="<?= e($placeholder) ?>" 
                       value="<?= e($searchKeyword) ?>">
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-outline-primary me-2">
                    <i class="bi bi-search me-1"></i>Tìm kiếm
                </button>
                <?php if ($searchKeyword && $resetUrl): ?>
                    <a href="<?= e($resetUrl) ?>" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-clockwise me-1"></i>Reset
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </form>
    <?php
    return ob_get_clean();
}

/**
 * Generate empty state message
 */
function emptyState($icon, $title, $description = '', $actionUrl = '', $actionText = '') {
    ob_start();
    ?>
    <div class="text-center py-5">
        <i class="<?= e($icon) ?> text-muted" style="font-size: 4rem;"></i>
        <h5 class="text-muted mt-3"><?= e($title) ?></h5>
        <?php if ($description): ?>
            <p class="text-muted"><?= e($description) ?></p>
        <?php endif; ?>
        <?php if ($actionUrl && $actionText): ?>
            <a href="<?= e($actionUrl) ?>" class="btn btn-primary mt-3">
                <i class="bi bi-plus-circle me-1"></i><?= e($actionText) ?>
            </a>
        <?php endif; ?>
    </div>
    <?php
    return ob_get_clean();
}

/**
 * Generate modern delete confirmation script using data attributes
 */
function deleteConfirmScript() {
    return '<script>
document.addEventListener("DOMContentLoaded", function() {
    const deleteButtons = document.querySelectorAll(".delete-btn");
    
    deleteButtons.forEach(button => {
        button.addEventListener("click", function() {
            const id = this.getAttribute("data-id");
            const action = this.getAttribute("data-action");
            const name = this.getAttribute("data-name");
            
            const message = name ? 
                `Bạn có chắc chắn muốn xóa "${name}"?` : 
                "Bạn có chắc chắn muốn xóa mục này?";
            
            if (confirm(message)) {
                // Sử dụng GET thay vì POST để tương thích với các file handle hiện tại
                window.location.href = action + "?action=delete&id=" + id;
            }
        });
    });
});
</script>';
}

/**
 * Generate pagination with smart page selection
 */
function pagination($currentPage, $totalPages, $baseUrl = '') {
    if ($totalPages <= 1) return '';
    
    ob_start();
    ?>
    <nav aria-label="Page navigation">
        <ul class="pagination justify-content-center">
            <?php if ($currentPage > 1): ?>
                <li class="page-item">
                    <a class="page-link" href="<?= $baseUrl ?>?page=<?= $currentPage - 1 ?>">
                        <i class="bi bi-chevron-left"></i> Previous
                    </a>
                </li>
            <?php endif; ?>
            
            <?php 
            // Smart pagination - show only relevant pages
            $start = max(1, $currentPage - 2);
            $end = min($totalPages, $currentPage + 2);
            
            if ($start > 1): ?>
                <li class="page-item">
                    <a class="page-link" href="<?= $baseUrl ?>?page=1">1</a>
                </li>
                <?php if ($start > 2): ?>
                    <li class="page-item disabled">
                        <span class="page-link">...</span>
                    </li>
                <?php endif; ?>
            <?php endif; ?>
            
            <?php for ($i = $start; $i <= $end; $i++): ?>
                <li class="page-item <?= $i === $currentPage ? 'active' : '' ?>">
                    <a class="page-link" href="<?= $baseUrl ?>?page=<?= $i ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>
            
            <?php if ($end < $totalPages): ?>
                <?php if ($end < $totalPages - 1): ?>
                    <li class="page-item disabled">
                        <span class="page-link">...</span>
                    </li>
                <?php endif; ?>
                <li class="page-item">
                    <a class="page-link" href="<?= $baseUrl ?>?page=<?= $totalPages ?>"><?= $totalPages ?></a>
                </li>
            <?php endif; ?>
            
            <?php if ($currentPage < $totalPages): ?>
                <li class="page-item">
                    <a class="page-link" href="<?= $baseUrl ?>?page=<?= $currentPage + 1 ?>">
                        Next <i class="bi bi-chevron-right"></i>
                    </a>
                </li>
            <?php endif; ?>
        </ul>
    </nav>
    <?php
    return ob_get_clean();
}
?>