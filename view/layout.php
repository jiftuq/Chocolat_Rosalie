<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Abhaya+Libre:wght@400;500;600;700&family=Abyssinica+SIL&family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;0,700;1,300;1,700&family=Josefin+Sans:wght@300;400;500;600;700&display=swap">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="<?= e($bodyClass) ?>">
<?php require ROOT . '/view/partials/header.php'; ?>
<main>
<?= $content ?>
</main>
<?php if (!$floatingFooter) {
    require ROOT . '/view/partials/footer.php';
} ?>
</body>
</html>
