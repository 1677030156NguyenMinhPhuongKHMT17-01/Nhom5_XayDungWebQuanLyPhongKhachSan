    <!-- Vendor JS Files -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Template Main JS File -->
    <script src="<?= isset($baseUrl) ? $baseUrl : '../' ?>js/admin-main.js"></script>
    <!-- Dark Mode JS -->
    <script src="<?= isset($baseUrl) ? $baseUrl : '../' ?>js/dark-mode.js"></script>

    <?php if (isset($additionalJS)): ?>
        <?php foreach ($additionalJS as $js): ?>
            <script src="<?= $js ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>

    <?php if (isset($inlineJS)): ?>
        <script>
            <?= $inlineJS ?>
        </script>
    <?php endif; ?>

</body>
</html>