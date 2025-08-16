<style>
/* Admin Common Styles */
:root {
    --sidebar-width: 280px;
    --header-height: 70px;
    --admin-primary: #1e40af;
    --admin-secondary: #3b82f6;
    --admin-success: #10b981;
    --admin-warning: #f59e0b;
    --admin-danger: #ef4444;
    --admin-info: #06b6d4;
    --sidebar-bg: #1f2937;
    --sidebar-text: #d1d5db;
    --sidebar-active: #3b82f6;
}

/* Layout Structure */
.admin-layout {
    display: flex;
    min-height: 100vh;
    background: #f8fafc;
}

/* Sidebar */
.admin-sidebar {
    width: var(--sidebar-width);
    background: var(--sidebar-bg);
    color: var(--sidebar-text);
    position: fixed;
    top: 0;
    left: 0;
    height: 100vh;
    overflow-y: auto;
    z-index: 1000;
    transition: transform 0.3s ease;
}

.sidebar-header {
    padding: 1.5rem;
    border-bottom: 1px solid #374151;
    text-align: center;
}

.sidebar-logo {
    font-size: 1.5rem;
    font-weight: 700;
    color: white;
    text-decoration: none;
    display: block;
    margin-bottom: 0.5rem;
}

.sidebar-subtitle {
    font-size: 0.875rem;
    color: #9ca3af;
}

/* Navigation */
.sidebar-nav {
    padding: 1rem 0;
}

.nav-section {
    margin-bottom: 1.5rem;
}

.nav-section-title {
    padding: 0 1.5rem 0.5rem;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: #9ca3af;
}

.nav-item {
    padding: 0 1rem;
}

.nav-link {
    display: flex;
    align-items: center;
    padding: 0.75rem 0.5rem;
    color: var(--sidebar-text);
    text-decoration: none;
    border-radius: 0.375rem;
    transition: all 0.2s ease;
    margin-bottom: 0.25rem;
}

.nav-link:hover {
    background: rgba(59, 130, 246, 0.1);
    color: white;
}

.nav-link.active {
    background: var(--sidebar-active);
    color: white;
    border-right: 3px solid var(--sidebar-active);
}

.nav-link i {
    width: 20px;
    margin-right: 0.75rem;
    font-size: 1rem;
}

/* Main Content Area */
.admin-main {
    flex: 1;
    margin-left: var(--sidebar-width);
    min-height: 100vh;
    display: flex;
    flex-direction: column;
}

.admin-header {
    background: white;
    height: var(--header-height);
    padding: 0 2rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-bottom: 1px solid #e5e7eb;
    position: sticky;
    top: 0;
    z-index: 100;
}

.header-title {
    font-size: 1.875rem;
    font-weight: 700;
    color: #1f2937;
    margin: 0;
}

.header-actions {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.user-menu {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.user-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: var(--admin-info);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 0.875rem;
}

/* Content */
.admin-content {
    flex: 1;
    padding: 2rem;
}

/* Cards */
.admin-card {
    background: white;
    border-radius: 0.75rem;
    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
    border: 1px solid #e5e7eb;
    margin-bottom: 1.5rem;
    overflow: hidden;
}

.admin-card-header {
    padding: 1.5rem;
    border-bottom: 1px solid #e5e7eb;
    background: #f9fafb;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.admin-card-body {
    padding: 1.5rem;
}

.admin-card h3 {
    margin: 0;
    font-size: 1.125rem;
    font-weight: 600;
    color: #1f2937;
}

/* Filters */
.admin-filters {
    display: flex;
    gap: 1rem;
    align-items: center;
}

.admin-select, .admin-input {
    border: 1px solid #d1d5db;
    border-radius: 0.5rem;
    padding: 0.5rem 0.75rem;
    font-size: 0.875rem;
    background: white;
    transition: all 0.2s;
}

.admin-select:focus, .admin-input:focus {
    outline: none;
    border-color: var(--admin-primary);
    box-shadow: 0 0 0 3px rgba(30, 64, 175, 0.1);
}

/* Tables */
.admin-table-container {
    overflow-x: auto;
    border-radius: 0.5rem;
    border: 1px solid #e5e7eb;
}

.admin-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.875rem;
}

.admin-table th {
    background: #f9fafb;
    padding: 0.75rem;
    text-align: left;
    font-weight: 600;
    color: #374151;
    border-bottom: 1px solid #e5e7eb;
}

.admin-table td {
    padding: 0.75rem;
    border-bottom: 1px solid #f3f4f6;
    color: #374151;
}

.admin-table tbody tr:hover {
    background: #f9fafb;
}

/* Buttons */
.btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    border-radius: 0.5rem;
    font-size: 0.875rem;
    font-weight: 500;
    text-decoration: none;
    border: none;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-primary {
    background: var(--admin-primary);
    color: white;
}

.btn-primary:hover {
    background: #1e3a8a;
}

.btn-secondary {
    background: #6b7280;
    color: white;
}

.btn-outline-primary {
    background: transparent;
    color: var(--admin-primary);
    border: 1px solid var(--admin-primary);
}

.btn-outline-secondary {
    background: transparent;
    color: #6b7280;
    border: 1px solid #6b7280;
}

/* Status badges */
.status-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 1rem;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
}

.status-badge.active {
    background: #dcfce7;
    color: #166534;
}

.status-badge.inactive {
    background: #fee2e2;
    color: #991b1b;
}

/* Format badges */
.format-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 1rem;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
}

.format-online {
    background: #dbeafe;
    color: #1d4ed8;
}

.format-offline {
    background: #dcfce7;
    color: #166534;
}

.format-hybrid {
    background: #fef3c7;
    color: #92400e;
}

/* Action buttons */
.action-buttons {
    display: flex;
    gap: 0.25rem;
}

.btn-action {
    padding: 0.25rem 0.5rem;
    border-radius: 0.25rem;
    border: none;
    cursor: pointer;
    font-size: 0.75rem;
    color: white;
    transition: all 0.2s;
}

.btn-edit {
    background: var(--admin-info);
}

.btn-edit:hover {
    background: #0891b2;
}

.btn-delete {
    background: var(--admin-danger);
}

.btn-delete:hover {
    background: #dc2626;
}

.btn-toggle {
    background: var(--admin-success);
}

.btn-toggle:hover {
    background: #059669;
}

.btn-toggle.inactive {
    background: #6b7280;
}

/* Responsive */
@media (max-width: 768px) {
    .admin-sidebar {
        transform: translateX(-100%);
    }
    
    .admin-main {
        margin-left: 0;
    }
    
    .admin-header {
        padding: 0 1rem;
    }
    
    .admin-content {
        padding: 1rem;
    }
    
    .admin-filters {
        flex-direction: column;
        align-items: stretch;
    }
    
    .user-menu {
        gap: 0.5rem;
    }
    
    .user-avatar {
        width: 32px;
        height: 32px;
        font-size: 0.75rem;
    }
}
</style>