<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $this->renderSection('title') ?> - Admin Panel</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Admin Common Styles -->
    <?= $this->include('shared/partials/admin_styles') ?>
    
    <?= $this->renderSection('head') ?>
</head>
<body class="admin-layout">
    <!-- Sidebar -->
    <?= $this->include('shared/partials/admin_sidebar') ?>
    
    <!-- Main Content -->
    <main class="admin-main">
        <!-- Header -->
        <?= $this->include('shared/partials/admin_header', [
            'page_title' => $this->renderSection('title'),
            'user' => $user ?? []
        ]) ?>
        
        <!-- Content -->
        <div class="admin-content">
            <?= $this->renderSection('content') ?>
        </div>
    </main>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <?= $this->renderSection('additional_js') ?>
</body>
</html>