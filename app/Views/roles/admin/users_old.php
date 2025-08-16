<?= $this->extend('shared/layouts/admin_simple') ?>

<?= $this->section('title') ?>User Management<?= $this->endSection() ?>

<?= $this->section('header_actions') ?>
<button class="btn btn-primary" id="addUserBtn">
    <i class="fas fa-plus"></i> Add User
</button>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<style>
/* User specific content styles */

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
    background: #e5e7eb;
    font-weight: 600;
    color: #374151;
    border-bottom: 2px solid #d1d5db;
    position: sticky;
    top: 0;
    z-index: 10;
}

.users-table tbody tr {
    transition: all 0.2s ease;
    cursor: pointer;
}

/* Zebra striping - alternating row colors */
.users-table tbody tr:nth-child(even) {
    background: #f8fafc;
}

.users-table tbody tr:nth-child(odd) {
    background: #ffffff;
}

.users-table tbody tr:hover {
    background: #e0f2fe !important;
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.users-table tbody tr:hover .user-avatar-small {
    transform: scale(1.1);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
}

.users-table tbody tr.table-row-selected {
    background: #bbf7d0 !important;
    border-left: 4px solid var(--admin-success);
}

.users-table tbody tr.table-row-selected:hover {
    background: #a7f3d0 !important;
}

.row-number {
    font-weight: 700;
    color: #4b5563;
    font-size: 0.875rem;
    background: #f3f4f6;
    padding: 0.25rem 0.5rem;
    border-radius: 0.375rem;
    display: inline-block;
    min-width: 2rem;
    text-align: center;
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

.pagination .btn {
    border-radius: 0.375rem;
    font-weight: 500;
    transition: all 0.2s ease;
    border: 1px solid #d1d5db;
}

.pagination .btn:hover {
    background-color: #f3f4f6;
    border-color: #9ca3af;
    color: #374151;
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.pagination .btn-primary:hover {
    background-color: #1d4ed8;
    border-color: #1d4ed8;
    color: white;
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
    
    .row-number {
        font-size: 0.75rem;
        padding: 0.125rem 0.375rem;
        min-width: 1.5rem;
    }
    
    .pagination {
        flex-wrap: wrap;
        gap: 0.25rem;
    }
    
    .pagination .btn {
        font-size: 0.875rem;
        padding: 0.375rem 0.75rem;
    }
}
</style>
<?php $this->endSection(); ?>

<?php $this->section('content'); ?>
<!-- User Management Card -->
<div class="admin-card">
    <div class="admin-card-header">
        <h3>
                        <i class="fas fa-users me-2"></i>
                        All Users
                    </h3>
                    <button class="btn btn-primary btn-sm" id="addUserBtn">
                        <i class="fas fa-plus me-1"></i>
                        Add User
                    </button>
                </div>
    <div class="admin-card-body">
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
                                    <th>#</th>
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
                                    <td colspan="6" class="loading-state">
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
                <td colspan="6" class="loading-state">
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
            
            if (!response.ok) {
                this.showError(`HTTP Error: ${response.status} ${response.statusText}`);
                return;
            }
            
            const data = await response.json();
            
            if (data.status === 'success') {
                this.renderUsers(data.data);
                this.renderPagination(data.pagination);
            } else {
                this.showError(data.message || 'Failed to load users');
            }
        } catch (error) {
            console.error('Error loading users:', error);
            this.showError(`Error loading users: ${error.message}`);
        }
    }
    
    renderUsers(users) {
        const tableBody = document.getElementById('usersTableBody');
        
        if (users.length === 0) {
            tableBody.innerHTML = `
                <tr>
                    <td colspan="6" class="loading-state">
                        No users found
                    </td>
                </tr>
            `;
            return;
        }
        
        tableBody.innerHTML = users.map((user, index) => {
            const rowNumber = ((this.currentPage - 1) * this.limit) + index + 1;
            return `
                <tr data-user-id="${user.id}" onclick="userManager.selectRow(this)">
                    <td>
                        <div class="row-number">${rowNumber}</div>
                    </td>
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
                            <button class="btn-action edit" onclick="event.stopPropagation(); userManager.editUser(${user.id})" title="Edit User">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn-action delete" onclick="event.stopPropagation(); userManager.deleteUser(${user.id})" title="Delete User">
                                <i class="fas fa-trash"></i>
                            </button>
                            <button class="btn-action" onclick="event.stopPropagation(); userManager.toggleUserStatus(${user.id})" 
                                    style="background: ${user.is_verified ? '#fef3c7' : '#dcfce7'}; color: ${user.is_verified ? '#92400e' : '#166534'};" 
                                    title="${user.is_verified ? 'Deactivate User' : 'Activate User'}">
                                <i class="fas fa-${user.is_verified ? 'user-slash' : 'user-check'}"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        }).join('');
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

    selectRow(row) {
        // Remove previous selection
        document.querySelectorAll('.users-table tbody tr').forEach(tr => {
            tr.classList.remove('table-row-selected');
        });
        
        // Add selection to clicked row
        row.classList.add('table-row-selected');
        
        // Show which row is selected
        const userId = row.dataset.userId;
        const rowNumber = row.querySelector('.row-number').textContent;
        console.log(`Selected row ${rowNumber} (User ID: ${userId})`);
    }
    
    async editUser(userId) {
        try {
            // Get user data first
            const response = await fetch(`/admin/api/users/${userId}`);
            const data = await response.json();
            
            if (data.status === 'success') {
                this.showEditUserModal(data.data);
            } else {
                alert('Failed to load user data');
            }
        } catch (error) {
            console.error('Error loading user:', error);
            alert('Error loading user data');
        }
    }
    
    async deleteUser(userId) {
        if (confirm('Are you sure you want to delete this user? This action cannot be undone.')) {
            try {
                const response = await fetch(`/admin/api/users/${userId}/delete`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                
                const data = await response.json();
                
                if (data.status === 'success') {
                    alert('User deleted successfully');
                    this.loadUsers(); // Reload the table
                } else {
                    alert(data.message || 'Failed to delete user');
                }
            } catch (error) {
                console.error('Error deleting user:', error);
                alert('Error deleting user');
            }
        }
    }

    async toggleUserStatus(userId) {
        if (confirm('Are you sure you want to change this user\'s status?')) {
            try {
                const response = await fetch(`/admin/api/users/${userId}/toggle-status`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                
                const data = await response.json();
                
                if (data.status === 'success') {
                    alert(`User status changed to ${data.new_status}`);
                    this.loadUsers(); // Reload the table
                } else {
                    alert(data.message || 'Failed to update user status');
                }
            } catch (error) {
                console.error('Error updating user status:', error);
                alert('Error updating user status');
            }
        }
    }
    
    showAddUserModal() {
        const modalHtml = `
            <div class="modal fade" id="addUserModal" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Add New User</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <form id="addUserForm">
                                <div class="mb-3">
                                    <label for="addFirstName" class="form-label">First Name</label>
                                    <input type="text" class="form-control" id="addFirstName" name="first_name" required>
                                </div>
                                <div class="mb-3">
                                    <label for="addLastName" class="form-label">Last Name</label>
                                    <input type="text" class="form-control" id="addLastName" name="last_name" required>
                                </div>
                                <div class="mb-3">
                                    <label for="addEmail" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="addEmail" name="email" required>
                                </div>
                                <div class="mb-3">
                                    <label for="addRole" class="form-label">Role</label>
                                    <select class="form-select" id="addRole" name="role" required>
                                        <option value="">Select Role</option>
                                        <option value="admin">Admin</option>
                                        <option value="presenter">Presenter</option>
                                        <option value="reviewer">Reviewer</option>
                                        <option value="audience">Audience</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="addPassword" class="form-label">Password</label>
                                    <input type="password" class="form-control" id="addPassword" name="password" required>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-primary" onclick="userManager.createUser()">Create User</button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        // Remove existing modal if any
        const existingModal = document.getElementById('addUserModal');
        if (existingModal) {
            existingModal.remove();
        }
        
        // Add modal to body
        document.body.insertAdjacentHTML('beforeend', modalHtml);
        
        // Show modal
        const modal = new bootstrap.Modal(document.getElementById('addUserModal'));
        modal.show();
    }

    showEditUserModal(user) {
        const modalHtml = `
            <div class="modal fade" id="editUserModal" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Edit User</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <form id="editUserForm">
                                <input type="hidden" id="editUserId" value="${user.id}">
                                <div class="mb-3">
                                    <label for="editFirstName" class="form-label">First Name</label>
                                    <input type="text" class="form-control" id="editFirstName" name="first_name" value="${this.escapeHtml(user.first_name || '')}" required>
                                </div>
                                <div class="mb-3">
                                    <label for="editLastName" class="form-label">Last Name</label>
                                    <input type="text" class="form-control" id="editLastName" name="last_name" value="${this.escapeHtml(user.last_name || '')}" required>
                                </div>
                                <div class="mb-3">
                                    <label for="editEmail" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="editEmail" name="email" value="${this.escapeHtml(user.email || '')}" required>
                                </div>
                                <div class="mb-3">
                                    <label for="editRole" class="form-label">Role</label>
                                    <select class="form-select" id="editRole" name="role" required>
                                        <option value="admin" ${user.role === 'admin' ? 'selected' : ''}>Admin</option>
                                        <option value="presenter" ${user.role === 'presenter' ? 'selected' : ''}>Presenter</option>
                                        <option value="reviewer" ${user.role === 'reviewer' ? 'selected' : ''}>Reviewer</option>
                                        <option value="audience" ${user.role === 'audience' ? 'selected' : ''}>Audience</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="editPassword" class="form-label">New Password <small class="text-muted">(leave blank to keep current)</small></label>
                                    <input type="password" class="form-control" id="editPassword" name="password">
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-primary" onclick="userManager.updateUser()">Update User</button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        // Remove existing modal if any
        const existingModal = document.getElementById('editUserModal');
        if (existingModal) {
            existingModal.remove();
        }
        
        // Add modal to body
        document.body.insertAdjacentHTML('beforeend', modalHtml);
        
        // Show modal
        const modal = new bootstrap.Modal(document.getElementById('editUserModal'));
        modal.show();
    }

    async createUser() {
        try {
            const form = document.getElementById('addUserForm');
            const formData = new FormData(form);
            
            const response = await fetch('/admin/api/users', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            
            const data = await response.json();
            
            if (data.status === 'success') {
                alert('User created successfully');
                bootstrap.Modal.getInstance(document.getElementById('addUserModal')).hide();
                this.loadUsers(); // Reload the table
            } else {
                if (data.errors) {
                    const errors = Object.values(data.errors).join('\n');
                    alert('Validation errors:\n' + errors);
                } else {
                    alert(data.message || 'Failed to create user');
                }
            }
        } catch (error) {
            console.error('Error creating user:', error);
            alert('Error creating user');
        }
    }

    async updateUser() {
        try {
            const userId = document.getElementById('editUserId').value;
            const form = document.getElementById('editUserForm');
            const formData = new FormData(form);
            
            const response = await fetch(`/admin/api/users/${userId}/update`, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            
            const data = await response.json();
            
            if (data.status === 'success') {
                alert('User updated successfully');
                bootstrap.Modal.getInstance(document.getElementById('editUserModal')).hide();
                this.loadUsers(); // Reload the table
            } else {
                if (data.errors) {
                    const errors = Object.values(data.errors).join('\n');
                    alert('Validation errors:\n' + errors);
                } else {
                    alert(data.message || 'Failed to update user');
                }
            }
        } catch (error) {
            console.error('Error updating user:', error);
            alert('Error updating user');
        }
    }
    
    showError(message) {
        const tableBody = document.getElementById('usersTableBody');
        tableBody.innerHTML = `
            <tr>
                <td colspan="6" class="loading-state" style="color: var(--admin-danger);">
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