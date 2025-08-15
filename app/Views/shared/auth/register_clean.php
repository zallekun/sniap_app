<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - SNIA Conference</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Clean Auth CSS -->
    <link rel="stylesheet" href="<?= base_url('css/auth.css') ?>">
</head>
<body class="auth-clean">
    <div class="auth-wrapper">
        <!-- Left Side - Form -->
        <div class="auth-form-section">
            <div class="auth-form-container">
                <!-- Logo -->
                <div class="brand-logo">
                    <i class="fas fa-graduation-cap"></i>
                    <span>SNIA</span>
                </div>
                
                <!-- Form -->
                <div class="auth-form">
                    <h1 class="auth-title">Create Account</h1>
                    <p class="auth-subtitle">Join the SNIA Conference community</p>
                    
                    <!-- Alert Container -->
                    <div id="alertContainer"></div>
                    
                    <form id="registerForm" method="POST" action="<?= site_url('auth/register') ?>">
                        <?= csrf_field() ?>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="first_name">First Name</label>
                                    <input type="text" id="first_name" name="first_name" class="form-control" 
                                           placeholder="Enter first name" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="last_name">Last Name</label>
                                    <input type="text" id="last_name" name="last_name" class="form-control" 
                                           placeholder="Enter last name" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Email Address</label>
                            <input type="email" id="email" name="email" class="form-control" 
                                   placeholder="Enter your email" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="phone_number">Phone Number</label>
                            <input type="tel" id="phone_number" name="phone_number" class="form-control" 
                                   placeholder="Enter phone number" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="institution">Institution/Organization</label>
                            <input type="text" id="institution" name="institution" class="form-control" 
                                   placeholder="Enter your institution" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="role">Account Type</label>
                            <select id="role" name="role" class="form-control" required>
                                <option value="">Select your role</option>
                                <option value="audience">Participant/Audience</option>
                                <option value="presenter">Presenter</option>
                                <option value="reviewer">Reviewer</option>
                            </select>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="password">Password</label>
                                    <input type="password" id="password" name="password" class="form-control" 
                                           placeholder="Create password" required minlength="6">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="confirm_password">Confirm Password</label>
                                    <input type="password" id="confirm_password" name="confirm_password" class="form-control" 
                                           placeholder="Confirm password" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-options">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="terms" name="terms" required>
                                <label class="form-check-label" for="terms">
                                    I agree to the <a href="#" class="text-decoration-none">Terms of Service</a> and <a href="#" class="text-decoration-none">Privacy Policy</a>
                                </label>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn-primary btn-login">
                            <span class="btn-text">Create Account</span>
                            <i class="fas fa-spinner fa-spin btn-loading d-none"></i>
                        </button>
                    </form>
                    
                    <div class="auth-footer">
                        <p>Already have an account? 
                            <a href="<?= site_url('auth/login') ?>">Sign in</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Right Side - Visual -->
        <div class="auth-visual-section">
            <div class="visual-content">
                <div class="visual-icon">
                    <i class="fas fa-user-plus"></i>
                </div>
                <h2>Join Our Community</h2>
                <p>Connect with researchers, academics, and industry professionals</p>
                <ul class="feature-list">
                    <li><i class="fas fa-check"></i> Access to exclusive conferences</li>
                    <li><i class="fas fa-check"></i> Submit research papers</li>
                    <li><i class="fas fa-check"></i> Review academic work</li>
                    <li><i class="fas fa-check"></i> Network with peers</li>
                    <li><i class="fas fa-check"></i> Get digital certificates</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
    // Phone number formatting
    document.getElementById('phone_number').addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        if (value.startsWith('0')) {
            value = '62' + value.slice(1);
        } else if (!value.startsWith('62')) {
            value = '62' + value;
        }
        if (value.length > 13) {
            value = value.slice(0, 13);
        }
        e.target.value = '+' + value;
    });
    
    // Password confirmation validation
    document.getElementById('confirm_password').addEventListener('input', function(e) {
        const password = document.getElementById('password').value;
        const confirmPassword = e.target.value;
        
        if (password !== confirmPassword) {
            e.target.setCustomValidity('Passwords do not match');
        } else {
            e.target.setCustomValidity('');
        }
    });
    
    // Form submission
    document.getElementById('registerForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const form = this;
        const submitBtn = form.querySelector('.btn-login');
        const btnText = submitBtn.querySelector('.btn-text');
        const btnLoading = submitBtn.querySelector('.btn-loading');
        const alertContainer = document.getElementById('alertContainer');
        
        // Show loading state
        submitBtn.disabled = true;
        btnText.classList.add('d-none');
        btnLoading.classList.remove('d-none');
        
        // Clear previous alerts
        alertContainer.innerHTML = '';
        
        const formData = new FormData(form);
        
        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                // Show success message
                alertContainer.innerHTML = `
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle me-2"></i>
                        ${data.message}
                    </div>
                `;
                
                // Redirect after short delay
                setTimeout(() => {
                    window.location.href = data.redirect || '/auth/login';
                }, 2000);
            } else {
                // Show error message
                let errorMessage = data.message;
                if (data.errors) {
                    errorMessage += '<ul class="mt-2 mb-0">';
                    for (const error of Object.values(data.errors)) {
                        errorMessage += `<li>${error}</li>`;
                    }
                    errorMessage += '</ul>';
                }
                
                alertContainer.innerHTML = `
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        ${errorMessage}
                    </div>
                `;
                
                // Reset button
                submitBtn.disabled = false;
                btnText.classList.remove('d-none');
                btnLoading.classList.add('d-none');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alertContainer.innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    An error occurred. Please try again.
                </div>
            `;
            
            // Reset button
            submitBtn.disabled = false;
            btnText.classList.remove('d-none');
            btnLoading.classList.add('d-none');
        });
    });
    </script>
</body>
</html>