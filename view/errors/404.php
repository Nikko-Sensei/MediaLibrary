<?php require BASE_PATH . '/view/layout/header.php'; ?>

<section class="container error-page">
    <h1>Page not found</h1>
    <p><?= htmlspecialchars($errorMessage ?? 'The page you are looking for does not exist.') ?></p>
    <a href="<?= BASE_URL ?>/Public/index.php">Back to home</a>
</section>

<?php require BASE_PATH . '/view/layout/footer.php'; ?>
