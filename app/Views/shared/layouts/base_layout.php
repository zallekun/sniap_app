<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $this->renderSection('title') ?> - SNIA Conference</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Base CSS - Always loaded -->
    <link rel="stylesheet" href="<?= base_url('css/app.css') ?>">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Additional head content -->
    <?= $this->renderSection('head') ?>
    
    <!-- CSS Loader -->
    <script src="<?= base_url('js/css-loader.js') ?>"></script>
    
    <!-- Bootstrap JS (for components) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>

<!-- Body classes for CSS loading -->
<?php 
$bodyClasses = [];

// Add role class
if (isset($userRole)) {
    $bodyClasses[] = $userRole . '-dashboard';
} elseif (isset($user) && !empty($user['role'])) {
    $bodyClasses[] = $user['role'] . '-dashboard';
}

// Add page-specific class based on current route
$router = service('router');
$currentRoute = $router->getMatchedRoute();

if ($currentRoute) {
    $routeName = $currentRoute[0] ?? '';
    
    if (strpos($routeName, 'login') !== false || strpos($routeName, 'register') !== false) {
        $bodyClasses[] = 'auth-page';
    } elseif (strpos($routeName, 'event') !== false || strpos($routeName, 'schedule') !== false) {
        $bodyClasses[] = 'events-page';
    } elseif (strpos($routeName, 'registration') !== false) {
        $bodyClasses[] = 'registrations-page';
    } elseif (strpos($routeName, 'payment') !== false) {
        $bodyClasses[] = 'payments-page';
    } elseif (strpos($routeName, 'certificate') !== false) {
        $bodyClasses[] = 'certificates-page';
    } else {
        $bodyClasses[] = 'dashboard-page';
    }
}

$bodyClassStr = implode(' ', array_filter($bodyClasses));
?>

<body class="<?= $bodyClassStr ?>">
    <!-- Main content -->
    <?= $this->renderSection('content') ?>
    
    <!-- Global Scripts -->
    <script>
        // Global configuration
        window.SNIA_CONFIG = {
            baseUrl: '<?= base_url() ?>',
            csrfToken: '<?= csrf_hash() ?>',
            userRole: '<?= $userRole ?? ($user['role'] ?? null) ?>',
            userId: <?= isset($user) ? $user['id'] : 'null' ?>
        };
    </script>
    
    <!-- Additional scripts -->
    <?= $this->renderSection('scripts') ?>
</body>
</html>