<?php
// Auto-chargement PSR-4 : App\Model\User → model/App/Model/User.php
// Les contrôleurs (App\Controller\...) vivent dans controller/.

spl_autoload_register(function (string $class): void {
    $relativePath = str_replace('\\', '/', $class) . '.php';
    foreach (['model', 'controller'] as $folder) {
        $file = RACINE_PATH . '/' . $folder . '/' . $relativePath;
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});
