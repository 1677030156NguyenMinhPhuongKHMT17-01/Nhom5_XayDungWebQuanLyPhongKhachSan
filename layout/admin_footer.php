    </section>

</main><!-- End #main -->

<!-- ======= Footer ======= -->
<footer id="footer" class="footer">
    <div class="copyright">
        &copy; Copyright <strong><span>FITDNU Hotel Management</span></strong>. All Rights Reserved
    </div>
    <div class="credits">
        Designed by <strong>BTL Team - FITDNU</strong>
    </div>
</footer><!-- End Footer -->

<a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

<!-- Theme Toggle Script -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const themeToggle = document.getElementById('theme-toggle');
        if (themeToggle && window.themeManager) {
            // Khởi tạo icon đúng ngay từ đầu
            const currentTheme = localStorage.getItem('btl-theme') || 'light';
            const icon = themeToggle.querySelector('i');
            if (currentTheme === 'dark') {
                icon.className = 'bi bi-sun';
            } else {
                icon.className = 'bi bi-moon';
            }
            
            themeToggle.addEventListener('click', function(e) {
                e.preventDefault();
                window.themeManager.toggleTheme();
            });
            
            // Update icon based on current theme
            window.addEventListener('themeChanged', function(e) {
                const icon = themeToggle.querySelector('i');
                if (e.detail.theme === 'dark') {
                    icon.className = 'bi bi-sun';
                } else {
                    icon.className = 'bi bi-moon';
                }
            });
        }
    });
</script>

<?php include $baseUrl . 'layout/footer.php'; ?>