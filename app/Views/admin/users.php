<?php
$this->extend('layouts/main');
$this->section('title');
echo $title ?? 'User Management - Admin Panel';
$this->endSection();

$this->section('additional_css');
?>
<link rel="stylesheet" href="/css/colors.css">
<style>
/* Admin User Management Specific Styles */
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
}

.sidebar-subtitle {
    font-size: 0.875rem;
    color: #9ca3af;
    margin-top: 0.25rem;
}

.sidebar-nav {
    padding: 1rem 0;
}

.nav-section {
    margin-bottom: 2rem;
}

.nav-section-title {
    padding: 0.5rem 1.5rem;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: #9ca3af;
    margin-bottom: 0.5rem;
}

.nav-item {
    margin-bottom: 0.25rem;
}

.nav-link {
    display: flex;
    align-items: center;
    padding: 0.75rem 1.5rem;
    color: var(--sidebar-text);
    text-decoration: none;
    transition: all 0.2s ease;
    position: relative;
}

.nav-link:hover {
    background: rgba(59, 130, 246, 0.1);
    color: white;
}

.nav-link.active {
    background: rgba(59, 130, 246, 0.2);
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
}

.admin-header {
    background: white;
    height: var(--header-height);
    padding: 0 2rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-bottom: 1px solid #e5e7eb;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.header-title {
    font-size: 1.5rem;
    font-weight: 600;
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
    background: var(--admin-primary);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
}

.admin-content {
    padding: 2rem;
}

/* Content Cards */
.content-card {
    background: white;
    border-radius: 0.75rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    border: 1px solid #e5e7eb;
    margin-bottom: 1.5rem;
}

.card-header {
    padding: 1.5rem;
    border-bottom: 1px solid #e5e7eb;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.card-title {
    font-size: 1.125rem;
    font-weight: 600;
    color: #1f2937;
    margin: 0;
}

.card-body {
    padding: 1.5rem;
}

/* User Management Specific Styles */
.user-filters {
    display: flex;
    gap: 1rem;
    margin-bottom: 1.5rem;
    flex-wrap: wrap;
}

.filter-group {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.filter-group label {
    font-weight: 500;
    color: #374151;
    margin-bottom: 0;
}

.search-box {
    flex: 1;
    min-width: 300px;
}

.users-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 1rem;
}

.users-table th,
.users-table td {
    padding: 1rem;
    text-align: left;
    border-bottom: 1px solid #e5e7eb;
}

.users-table th {
    background: #f9fafb;
    font-weight: 600;
    color: #374151;
}

.users-table tbody tr:hover {
    background: #f9fafb;
}

.user-avatar-small {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: var(--admin-info);
    color: white;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 0.875rem;
    font-weight: 600;
    margin-right: 0.75rem;
}

.user-info {
    display: flex;
    align-items: center;
}

.user-details h6 {
    margin: 0;
    font-size: 0.875rem;
    font-weight: 600;
    color: #1f2937;
}

.user-details p {
    margin: 0;
    font-size: 0.75rem;
    color: #6b7280;
}

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

.status-badge.pending {
    background: #fef3c7;
    color: #92400e;
}

.role-badge {
    padding: 0.25rem 0.5rem;
    border-radius: 0.25rem;
    font-size: 0.75rem;
    font-weight: 500;
}

.role-badge.admin {
    background: #ddd6fe;
    color: #5b21b6;
}

.role-badge.audience {
    background: #dbeafe;
    color: #1e40af;
}

.role-badge.presenter {
    background: #d1fae5;
    color: #065f46;
}

.role-badge.reviewer {
    background: #fed7aa;
    color: #9a3412;
}

.action-buttons {
    display: flex;
    gap: 0.5rem;
}

.btn-action {
    padding: 0.25rem 0.5rem;
    border: none;
    border-radius: 0.25rem;
    font-size: 0.75rem;
    cursor: pointer;
    transition: all 0.2s ease;
}

.btn-action.edit {
    background: #fef3c7;
    color: #92400e;
}

.btn-action.edit:hover {
    background: #fcd34d;
}

.btn-action.delete {
    background: #fee2e2;
    color: #991b1b;
}

.btn-action.delete:hover {
    background: #fca5a5;
}

.pagination {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 0.5rem;
    margin-top: 1.5rem;
}

.loading-state {
    text-align: center;
    padding: 2rem;
    color: #6b7280;
}

.spinner {
    width: 20px;
    height: 20px;
    border: 2px solid #e5e7eb;
    border-top: 2px solid var(--admin-primary);
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin: 0 auto 1rem;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Responsive Design */
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
    
    .user-filters {
        flex-direction: column;
    }
    
    .search-box {
        min-width: auto;
    }
    
    .users-table {
        font-size: 0.875rem;
    }
    
    .users-table th,
    .users-table td {
        padding: 0.75rem 0.5rem;
    }
}
</style>
<?php $this->endSection(); ?>

<?php $this->section('content'); ?>
<div class="admin-layout">
    <!-- Sidebar -->
    <aside class="admin-sidebar">
        <div class="sidebar-header">
            <a href="/admin/dashboard" class="sidebar-logo">SNIA Admin</a>
            <div class="sidebar-subtitle">Conference Management</div>
        </div>
        
        <nav class="sidebar-nav">
            <div class="nav-section">
                <div class="nav-section-title">Dashboard</div>
                <div class="nav-item">
                    <a href="/admin/dashboard" class="nav-link">
                        <i class="fas fa-tachometer-alt"></i>
                        Overview
                    </a>
                </div>
            </div>
            
            <div class="nav-section">
                <div class="nav-section-title">Management</div>
                <div class="nav-item">
                    <a href="/admin/users" class="nav-link active">
                        <i class="fas fa-users"></i>
                        User Management
                    </a>
                </div>
                <div class="nav-item">
                    <a href="/admin/events" class="nav-link">
                        <i class="fas fa-calendar"></i>
                        Event Management
                    </a>
                </div>
                <div class="nav-item">
                    <a href="/admin/registrations" class="nav-link">
                        <i class="fas fa-user-check"></i>
                        Registrations
                    </a>
                </div>
                <div class="nav-item">
                    <a href="/admin/abstracts" class="nav-link">
                        <i class="fas fa-file-alt"></i>
                        Abstract & Reviews
                    </a>
                </div>
            </div>
            
            <div class="nav-section">
                <div class="nav-section-title">Reports & Analytics</div>
                <div class="nav-item">
                    <a href="/admin/analytics" class="nav-link">
                        <i class="fas fa-chart-bar"></i>
                        Analytics
                    </a>
                </div>
            </div>
            
            <div class="nav-section">
                <div class="nav-section-title">System</div>
                <div class="nav-item">
                    <a href="/admin/settings" class="nav-link">
                        <i class="fas fa-cog"></i>
                        System Settings
                    </a>
                </div>
                <div class="nav-item">
                    <a href="/dashboard" class="nav-link">
                        <i class="fas fa-arrow-left"></i>
                        Back to Dashboard
                    </a>
                </div>
            </div>
        </nav>
    </aside>
    
    <!-- Main Content -->
    <main class="admin-main">
        <!-- Header -->
        <header class="admin-header">
            <h1 class="header-title">User Management</h1>
            <div class="header-actions">
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
        
        <!-- Content -->
        <div class="admin-content">
            <!-- User Management Card -->
            <div class="content-card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-users me-2"></i>
                        All Users
                    </h3>
                    <button class="btn btn-primary btn-sm" id="addUserBtn">
                        <i class="fas fa-plus me-1"></i>
                        Add User
                    </button>
                </div>
                <div class="card-body">
                    <!-- Filters -->
                    <div class="user-filters">
                        <div class="search-box">
                            <div class="input-group">
                                <input type="text" class="form-control" id="searchUsers" placeholder="Search users by name or email...">
                                <button class="btn btn-outline-secondary" type="button" id="searchBtn">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                        
                        <div class="filter-group">
                            <label for="roleFilter">Role:</label>
                            <select class="form-select" id="roleFilter">
                                <option value="">All Roles</option>
                                <option value="admin">Admin</option>
                                <option value="presenter">Presenter</option>
                                <option value="reviewer">Reviewer</option>
                                <option value="audience">Audience</option>
                            </select>
                        </div>
                        
                        <div class="filter-group">
                            <label for="statusFilter">Status:</label>
                            <select class="form-select" id="statusFilter">
                                <option value="">All Status</option>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                                <option value="pending">Pending</option>
                            </select>
                        </div>
                        
                        <button class="btn btn-secondary btn-sm" id="refreshUsers">
                            <i class="fas fa-refresh"></i>
                            Refresh
                        </button>
                    </div>
                    
                    <!-- Users Table -->
                    <div class="table-responsive">
                        <table class="users-table" id="usersTable">
                            <thead>
                                <tr>
                                    <th>User</th>
                                    <th>Role</th>
                                    <th>Status</th>
                                    <th>Registered</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="usersTableBody">
                                <!-- Loading state -->
                                <tr>
                                    <td colspan="5" class="loading-state">
                                        <div class="spinner"></div>
                                        Loading users...
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <nav class="pagination" id="usersPagination">
                        <!-- Pagination will be generated by JavaScript -->
                    </nav>
                </div>
            </div>
        </div>
    </main>
</div>
<?php $this->endSection(); ?>

<?php $this->section('additional_js'); ?>
<script>
// User Management JavaScript
class UserManager {
    constructor() {
        this.currentPage = 1;
        this.limit = 10;
        this.search = '';
        this.roleFilter = '';
        this.statusFilter = '';
        
        this.init();
    }
    
    init() {
        this.bindEvents();
        this.loadUsers();
    }
    
    bindEvents() {
        // Search functionality
        const searchInput = document.getElementById('searchUsers');
        const searchBtn = document.getElementById('searchBtn');
        
        let searchTimeout;
        searchInput.addEventListener('input', (e) => {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                this.search = e.target.value;
                this.currentPage = 1;
                this.loadUsers();
            }, 500);
        });
        
        searchBtn.addEventListener('click', () => {
            this.search = searchInput.value;
            this.currentPage = 1;
            this.loadUsers();
        });
        
        // Filter events
        document.getElementById('roleFilter').addEventListener('change', (e) => {
            this.roleFilter = e.target.value;
            this.currentPage = 1;
            this.loadUsers();
        });
        
        document.getElementById('statusFilter').addEventListener('change', (e) => {
            this.statusFilter = e.target.value;
            this.currentPage = 1;
            this.loadUsers();
        });
        
        // Refresh button
        document.getElementById('refreshUsers').addEventListener('click', () => {
            this.loadUsers();
        });
        
        // Add user button
        document.getElementById('addUserBtn').addEventListener('click', () => {
            this.showAddUserModal();
        });
    }
    
    async loadUsers() {
        const tableBody = document.getElementById('usersTableBody');
        
        // Show loading state
        tableBody.innerHTML = `
            <tr>
                <td colspan="5" class="loading-state">
                    <div class="spinner"></div>
                    Loading users...
                </td>
            </tr>
        `;
        
        try {
            const params = new URLSearchParams({
                page: this.currentPage,
                limit: this.limit,
                search: this.search,
                role: this.roleFilter,
                status: this.statusFilter
            });
            
            const response = await fetch(`/admin/api/users?${params}`);
            const data = await response.json();
            
            if (data.status === 'success') {
                this.renderUsers(data.data);
                this.renderPagination(data.pagination);
            } else {
                this.showError('Failed to load users');
            }
        } catch (error) {
            console.error('Error loading users:', error);
            this.showError('Error loading users');
        }
    }
    
    renderUsers(users) {
        const tableBody = document.getElementById('usersTableBody');
        
        if (users.length === 0) {
            tableBody.innerHTML = `
                <tr>
                    <td colspan="5" class="loading-state">
                        No users found
                    </td>
                </tr>
            `;
            return;
        }
        
        tableBody.innerHTML = users.map(user => `
            <tr>
                <td>
                    <div class="user-info">
                        <div class="user-avatar-small">
                            ${(user.first_name || 'U').charAt(0).toUpperCase()}
                        </div>
                        <div class="user-details">
                            <h6>${this.escapeHtml(user.first_name || '')} ${this.escapeHtml(user.last_name || '')}</h6>
                            <p>${this.escapeHtml(user.email || '')}</p>
                        </div>
                    </div>
                </td>
                <td>
                    <span class="role-badge ${user.role || 'audience'}">
                        ${this.capitalizeFirst(user.role || 'audience')}
                    </span>
                </td>
                <td>
                    <span class="status-badge ${user.is_verified ? 'active' : 'pending'}">
                        ${user.is_verified ? 'Active' : 'Pending'}
                    </span>
                </td>
                <td>
                    ${user.created_at ? new Date(user.created_at).toLocaleDateString() : '-'}
                </td>
                <td>
                    <div class="action-buttons">
                        <button class="btn-action edit" onclick="userManager.editUser(${user.id})">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn-action delete" onclick="userManager.deleteUser(${user.id})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `).join('');
    }
    
    renderPagination(pagination) {
        const paginationContainer = document.getElementById('usersPagination');
        
        if (!pagination || pagination.pages <= 1) {
            paginationContainer.innerHTML = '';
            return;
        }
        
        let paginationHTML = '';
        
        // Previous button
        if (pagination.page > 1) {
            paginationHTML += `
                <button class="btn btn-outline-secondary btn-sm" onclick="userManager.goToPage(${pagination.page - 1})">
                    <i class="fas fa-chevron-left"></i>
                </button>
            `;
        }
        
        // Page numbers
        for (let i = Math.max(1, pagination.page - 2); i <= Math.min(pagination.pages, pagination.page + 2); i++) {
            paginationHTML += `
                <button class="btn ${i === pagination.page ? 'btn-primary' : 'btn-outline-secondary'} btn-sm" 
                        onclick="userManager.goToPage(${i})">
                    ${i}
                </button>
            `;
        }
        
        // Next button
        if (pagination.page < pagination.pages) {
            paginationHTML += `
                <button class="btn btn-outline-secondary btn-sm" onclick="userManager.goToPage(${pagination.page + 1})">
                    <i class="fas fa-chevron-right"></i>
                </button>
            `;
        }
        
        // Info
        paginationHTML += `
            <span style="margin-left: 1rem; color: #6b7280; font-size: 0.875rem;">
                Showing ${((pagination.page - 1) * pagination.limit) + 1} to ${Math.min(pagination.page * pagination.limit, pagination.total)} of ${pagination.total} users
            </span>
        `;
        
        paginationContainer.innerHTML = paginationHTML;
    }
    
    goToPage(page) {
        this.currentPage = page;
        this.loadUsers();
    }
    
    editUser(userId) {
        // TODO: Implement edit user functionality
        alert(`Edit user ${userId} - Feature coming soon!`);
    }
    
    deleteUser(userId) {
        if (confirm('Are you sure you want to delete this user?')) {
            // TODO: Implement delete user functionality
            alert(`Delete user ${userId} - Feature coming soon!`);
        }
    }
    
    showAddUserModal() {
        // TODO: Implement add user modal
        alert('Add user feature coming soon!');
    }
    
    showError(message) {
        const tableBody = document.getElementById('usersTableBody');
        tableBody.innerHTML = `
            <tr>
                <td colspan="5" class="loading-state" style="color: var(--admin-danger);">
                    <i class="fas fa-exclamation-triangle"></i>
                    ${message}
                </td>
            </tr>
        `;
    }
    
    escapeHtml(text) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text ? text.replace(/[&<>"']/g, m => map[m]) : '';
    }
    
    capitalizeFirst(str) {
        return str ? str.charAt(0).toUpperCase() + str.slice(1) : '';
    }
}

// Initialize user manager when page loads
let userManager;
document.addEventListener('DOMContentLoaded', function() {
    userManager = new UserManager();
});
</script>
<?php $this->endSection(); ?>