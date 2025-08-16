<header class="admin-header">
    <h1 class="header-title"><?= $page_title ?? 'Admin Panel' ?></h1>
    <div class="header-actions">
        <?= $this->renderSection('header_actions') ?>
        <div class="user-menu">
            <div class="user-avatar">
                <?= strtoupper(substr($user['first_name'] ?? 'A', 0, 1)) ?>
            </div>
            <div>
                <div style="font-weight: 600; color: #1f2937;">
                    <?= esc($user['first_name'] ?? '') ?> <?= esc($user['last_name'] ?? '') ?>
                </div>
                <div style="font-size: 0.875rem; color: #6b7280;">
                    Administrator
                </div>
            </div>
        </div>
    </div>
</header>