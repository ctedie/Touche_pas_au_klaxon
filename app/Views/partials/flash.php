<?php if (isset($_SESSION['flash_success']) && is_string($_SESSION['flash_success'])): ?>
    <div class="alert alert-success" role="alert">
        <?= htmlspecialchars($_SESSION['flash_success']) ?>
    </div>
    <?php unset($_SESSION['flash_success']); ?>
<?php endif; ?>