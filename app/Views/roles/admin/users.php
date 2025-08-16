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

.role-badge {
    padding: 0.25rem 0.5rem;
    border-radius: 0.25rem;
    font-size: 0.75rem;
    font-weight: 500;
}

.role-badge.admin {
    background: #ddd6fe;
    color: #7c3aed;
}

.role-badge.presenter {
    background: #dbeafe;
    color: #1d4ed8;
}

.role-badge.reviewer {
    background: #d1fae5;
    color: #059669;
}

.role-badge.audience {
    background: #fef3c7;
    color: #d97706;
}

/* User filters */
.user-filters {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
    flex-wrap: wrap;
    gap: 1rem;
}

.search-box {
    flex: 1;
    min-width: 250px;
}

.filter-box {
    display: flex;
    gap: 0.75rem;
    align-items: center;
}

/* Zebra striping for users table */
.admin-table tbody tr:nth-child(even) {
    background: #f8fafc;
}

.admin-table tbody tr:nth-child(odd) {
    background: #ffffff;
}

.admin-table tbody tr:hover {
    background: #e0f2fe !important;
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    transition: all 0.2s ease-in-out;
}

.admin-table tbody tr.table-row-selected {
    background: #bbf7d0 !important;
    border-left: 4px solid var(--admin-success);
}
</style>

<!-- User Management Card -->
<div class="admin-card">
    <div class="admin-card-header">
        <h3>
            <i class="fas fa-users me-2"></i>
            All Users
        </h3>
        <div class="admin-filters">
            <select id="roleFilter" class="admin-select">
                <option value="">All Roles</option>
                <option value="admin">Admin</option>
                <option value="presenter">Presenter</option>
                <option value="reviewer">Reviewer</option>
                <option value="audience">Audience</option>
            </select>
            <select id="statusFilter" class="admin-select">
                <option value="">All Status</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
                <option value="pending">Pending</option>
            </select>
            <input type="text" id="searchUsers" class="admin-input" placeholder="Search users...">
            <button class="btn btn-outline-primary" onclick="applyUserFilters()">Filter</button>
            <button class="btn btn-outline-secondary" onclick="resetUserFilters()">Reset</button>
        </div>
    </div>
    <div class="admin-card-body">
        <div class="admin-table-container">
            <table class="admin-table" id="usersTable">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Role</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Registered</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="usersTableBody">
                    <!-- Users will be loaded here -->
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div id="usersPagination" style="margin-top: 1rem;">
            <!-- Pagination will be loaded here -->
        </div>
    </div>
</div>

<!-- Add/Edit User Modal -->
<div class="modal fade" id="userModal" tabindex="-1" aria-labelledby="userModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="userModalLabel">Add New User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="userForm">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="firstName" class="form-label">First Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="firstName" name="first_name" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="lastName" class="form-label">Last Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="lastName" name="last_name" required>
                        </div>
                        <div class="col-12 mb-3">
                            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="role" class="form-label">Role <span class="text-danger">*</span></label>
                            <select class="form-control" id="role" name="role" required>
                                <option value="">Select Role</option>
                                <option value="admin">Admin</option>
                                <option value="presenter">Presenter</option>
                                <option value="reviewer">Reviewer</option>
                                <option value="audience">Audience</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <div class="col-12 mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="isVerified" name="is_verified">
                                <label class="form-check-label" for="isVerified">
                                    Account Verified
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="userSubmitBtn">Create User</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete User Modal -->
<div class="modal fade" id="deleteUserModal" tabindex="-1" aria-labelledby="deleteUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteUserModalLabel">Delete User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this user? This action cannot be undone.</p>
                <div class="alert alert-warning">
                    <strong>Warning:</strong> Deleting this user will also remove all their registrations and submissions.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteUser">Delete User</button>
            </div>
        </div>
    </div>
</div>

<script>
// User Management JavaScript
let currentUserPage = 1;
let userSearchTimeout;
let isEditingUser = false;
let editingUserId = null;

// Initialize when page loads
document.addEventListener('DOMContentLoaded', function() {
    loadUsers();
    
    // Setup search with debounce
    document.getElementById('searchUsers').addEventListener('input', function() {
        clearTimeout(userSearchTimeout);
        userSearchTimeout = setTimeout(() => {
            currentUserPage = 1;
            loadUsers();
        }, 500);
    });
    
    // Setup add user button
    document.getElementById('addUserBtn').addEventListener('click', function() {
        openAddUserModal();
    });
});

// Load users with filters and pagination
function loadUsers(page = 1) {
    // Use real API data
    const search = document.getElementById('searchUsers').value.trim();
    const roleFilter = document.getElementById('roleFilter').value;
    const statusFilter = document.getElementById('statusFilter').value;
    
    currentUserPage = page;
    
    const params = new URLSearchParams({
        page: page,
        limit: 10,
        search: search,
        role: roleFilter,
        status: statusFilter
    });
    
    fetch(`/api/admin/users?${params.toString()}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Convert API response format to match expected format
                const users = data.data.map(user => ({
                    id: user.id,
                    first_name: user.first_name,
                    last_name: user.last_name,
                    email: user.email,
                    role: user.role,
                    is_verified: user.is_verified === 't' || user.is_verified === true,
                    created_at: user.created_at || user.updated_at
                }));
                
                const pagination = {
                    total_records: data.pagination.total,
                    current_page: data.pagination.page,
                    total_pages: data.pagination.pages,
                    start_record: ((data.pagination.page - 1) * data.pagination.limit) + 1,
                    end_record: Math.min(data.pagination.page * data.pagination.limit, data.pagination.total)
                };
                
                renderUsersTable(users);
                renderUsersPagination(pagination);
            } else {
                showAlert('Error loading users: ' + (data.message || 'API Error'), 'error');
                document.getElementById('usersTableBody').innerHTML = '<tr><td colspan="6" class="text-center text-muted">Failed to load users</td></tr>';
            }
        })
        .catch(error => {
            console.error('Error loading users:', error);
            showAlert('Failed to load users', 'error');
            document.getElementById('usersTableBody').innerHTML = '<tr><td colspan="6" class="text-center text-muted">Failed to load users</td></tr>';
        });
}

// Render users table
function renderUsersTable(users) {
    const tbody = document.getElementById('usersTableBody');
    
    if (users.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted">No users found</td></tr>';
        return;
    }
    
    tbody.innerHTML = users.map(user => `
        <tr>
            <td>
                <div class="user-info">
                    <div class="user-details">
                        <h6>${user.first_name} ${user.last_name}</h6>
                        <p>ID: ${user.id}</p>
                    </div>
                </div>
            </td>
            <td>
                <span class="role-badge ${user.role}">${user.role}</span>
            </td>
            <td>${user.email}</td>
            <td>
                <span class="status-badge ${user.is_verified ? 'active' : 'inactive'}">
                    ${user.is_verified ? 'Active' : 'Inactive'}
                </span>
            </td>
            <td>${formatDate(user.created_at)}</td>
            <td>
                <div class="action-buttons">
                    <button class="btn-action btn-edit" onclick="editUser(${user.id})" title="Edit User">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn-action btn-toggle ${user.is_verified ? '' : 'inactive'}" 
                            onclick="toggleUserStatus(${user.id}, ${user.is_verified})" 
                            title="${user.is_verified ? 'Deactivate' : 'Activate'} User">
                        <i class="fas fa-${user.is_verified ? 'pause' : 'play'}"></i>
                    </button>
                    <button class="btn-action btn-delete" onclick="deleteUser(${user.id})" title="Delete User">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </td>
        </tr>
    `).join('');
}

// Render users pagination (simplified)
function renderUsersPagination(pagination) {
    const container = document.getElementById('usersPagination');
    
    if (pagination.total_pages <= 1) {
        container.innerHTML = '';
        return;
    }
    
    let paginationHTML = `
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div style="color: #6b7280; font-size: 0.875rem;">
                Showing ${pagination.start_record} to ${pagination.end_record} of ${pagination.total_records} users
            </div>
            <div style="display: flex; gap: 0.25rem;">
    `;
    
    // Previous button
    if (pagination.current_page > 1) {
        paginationHTML += `<button class="btn btn-outline-primary" onclick="loadUsers(${pagination.current_page - 1})">Previous</button>`;
    }
    
    // Next button
    if (pagination.current_page < pagination.total_pages) {
        paginationHTML += `<button class="btn btn-outline-primary" onclick="loadUsers(${pagination.current_page + 1})">Next</button>`;
    }
    
    paginationHTML += `</div></div>`;
    container.innerHTML = paginationHTML;
}

// Filter functions
function applyUserFilters() {
    currentUserPage = 1;
    loadUsers();
}

function resetUserFilters() {
    document.getElementById('searchUsers').value = '';
    document.getElementById('roleFilter').value = '';
    document.getElementById('statusFilter').value = '';
    currentUserPage = 1;
    loadUsers();
}

// Modal functions
function openAddUserModal() {
    isEditingUser = false;
    editingUserId = null;
    document.getElementById('userModalLabel').textContent = 'Add New User';
    document.getElementById('userSubmitBtn').textContent = 'Create User';
    document.getElementById('userForm').reset();
    new bootstrap.Modal(document.getElementById('userModal')).show();
}

function editUser(userId) {
    isEditingUser = true;
    editingUserId = userId;
    document.getElementById('userModalLabel').textContent = 'Edit User';
    document.getElementById('userSubmitBtn').textContent = 'Update User';
    
    // Fetch user data and populate form
    fetch(`/api/admin/users/${userId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                populateUserForm(data.data);
                new bootstrap.Modal(document.getElementById('userModal')).show();
            } else {
                showAlert('Error loading user data: ' + data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error loading user:', error);
            showAlert('Failed to load user data', 'error');
        });
}

function populateUserForm(user) {
    document.getElementById('firstName').value = user.first_name || '';
    document.getElementById('lastName').value = user.last_name || '';
    document.getElementById('email').value = user.email || '';
    document.getElementById('role').value = user.role || '';
    document.getElementById('isVerified').checked = user.is_verified || false;
    // Don't populate password for editing
    document.getElementById('password').required = false;
}

// Form submission
document.getElementById('userForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const userData = {};
    
    formData.forEach((value, key) => {
        if (key === 'is_verified') {
            userData[key] = document.querySelector(`[name="${key}"]`).checked;
        } else {
            userData[key] = value;
        }
    });
    
    const url = isEditingUser ? `/api/admin/users/${editingUserId}/update` : '/api/admin/users';
    
    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(userData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert(isEditingUser ? 'User updated successfully!' : 'User created successfully!', 'success');
            bootstrap.Modal.getInstance(document.getElementById('userModal')).hide();
            loadUsers(currentUserPage);
        } else {
            console.error('User save error:', data);
            let errorMsg = 'Error: ' + data.message;
            
            // Add debug information if available
            if (data.errors) {
                console.error('Validation errors:', data.errors);
                errorMsg += '\nValidation errors: ' + JSON.stringify(data.errors);
            }
            if (data.debug_db_error) {
                console.error('Database error:', data.debug_db_error);
                errorMsg += '\nDB Error: ' + JSON.stringify(data.debug_db_error);
            }
            if (data.debug_input) {
                console.error('Input data:', data.debug_input);
            }
            if (data.debug_update_data) {
                console.error('Update data:', data.debug_update_data);
            }
            
            showAlert(errorMsg, 'error');
        }
    })
    .catch(error => {
        console.error('Error saving user:', error);
        showAlert('Failed to save user', 'error');
    });
});

// Delete user
function deleteUser(userId) {
    new bootstrap.Modal(document.getElementById('deleteUserModal')).show();
    
    document.getElementById('confirmDeleteUser').onclick = function() {
        fetch(`/api/admin/users/${userId}/delete`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('User deleted successfully!', 'success');
                bootstrap.Modal.getInstance(document.getElementById('deleteUserModal')).hide();
                loadUsers(currentUserPage);
            } else {
                showAlert('Error deleting user: ' + data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error deleting user:', error);
            showAlert('Failed to delete user', 'error');
        });
    };
}

// Toggle user status
function toggleUserStatus(userId, currentStatus) {
    fetch(`/api/admin/users/${userId}/toggle-status`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert(`User ${currentStatus ? 'deactivated' : 'activated'} successfully!`, 'success');
            loadUsers(currentUserPage);
        } else {
            showAlert('Error updating user status: ' + data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error updating user status:', error);
        showAlert('Failed to update user status', 'error');
    });
}

// Utility functions
function formatDate(dateString) {
    if (!dateString) return 'Not set';
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
}

function showAlert(message, type = 'info') {
    // Create alert element
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show`;
    alertDiv.style.position = 'fixed';
    alertDiv.style.top = '20px';
    alertDiv.style.right = '20px';
    alertDiv.style.zIndex = '9999';
    alertDiv.style.minWidth = '300px';
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(alertDiv);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 5000);
}
</script>

<?= $this->endSection() ?>