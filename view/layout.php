<?php
use App\Core\View;

/** @var string $title @var string $description @var string $page @var string $content */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= View::e($title) ?></title>
    <meta name="description" content="<?= View::e($description) ?>">
    <meta name="csrf-token" content="<?= View::e($csrfToken) ?>">
    <link rel="icon" type="image/png" href="<?= View::asset('img/favicon.png') ?>">
    <link rel="apple-touch-icon" href="<?= View::asset('img/apple-touch-icon.png') ?>">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,500;0,600;0,700;1,600&family=Josefin+Sans:wght@300;400;600&display=swap">
    <link rel="stylesheet" href="<?= View::asset('css/style.css') ?>">
</head>
<body class="d-flex flex-column min-vh-100">
<a class="visually-hidden-focusable skip-link" href="#contenu">Aller au contenu</a>
<?php require RACINE_PATH . '/view/partials/header.php'; ?>

<main id="contenu" class="flex-grow-1">
<?= $content ?>
</main>

<?php require RACINE_PATH . '/view/partials/footer.php'; ?>
<?php require RACINE_PATH . '/view/partials/auth-modals.php'; ?>
<?php require RACINE_PATH . '/view/partials/legal-modal.php'; ?>

<div class="toast-container position-fixed bottom-0 end-0 p-3" id="toasts" aria-live="polite" aria-atomic="true"></div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script type="module" src="<?= View::asset('js/auth.js') ?>"></script>
<?php foreach ($scripts as $script): ?>
<script type="module" src="<?= View::asset('js/' . $script) ?>"></script>
<?php endforeach; ?>
</body>
</html>
