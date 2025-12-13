<?php require __DIR__ . '/../partials/header.php' ?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-6">
            <div class="card shadow border-0">
                <div class="card-header bg-success text-white text-center py-3">
                    <h3 class="mb-0"><i class="bi bi-person-plus me-2"></i>Create Your RoomShift Account</h3>
                </div>
                <div class="card-body p-4">
                    <form action="/register" method="post" class="needs-validation" novalidate>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Full Name</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-person"></i></span>
                                    <input type="text" class="form-control" id="name" name="name" 
                                           value="<?= isset($_SESSION['form_data']['name']) ? htmlspecialchars($_SESSION['form_data']['name']) : '' ?>"
                                           placeholder="John Doe" required>
                                    <?php unset($_SESSION['form_data']['name']); ?>
                                    <div class="invalid-feedback">Please enter your name.</div>
                                </div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                    <input type="email" class="form-control" id="email" name="email" 
                                           value="<?= isset($_SESSION['form_data']['email']) ? htmlspecialchars($_SESSION['form_data']['email']) : '' ?>"
                                           placeholder="you@example.com" required>
                                    <?php unset($_SESSION['form_data']['email']); ?>
                                    <div class="invalid-feedback">Please enter a valid email.</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label">Password</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                    <input type="password" class="form-control" id="password" name="password" 
                                           placeholder="Minimum 6 characters" required minlength="6">
                                    <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    <div class="invalid-feedback">Password must be at least 6 characters.</div>
                                </div>
                                <div class="form-text" id="passwordStrength"></div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="confirm_password" class="form-label">Confirm Password</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                                    <input type="password" class="form-control" id="confirm_password" 
                                           name="confirm_password" placeholder="Confirm your password" required>
                                    <button class="btn btn-outline-secondary" type="button" id="toggleConfirmPassword">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    <div class="invalid-feedback">Passwords must match.</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="terms" required>
                                <label class="form-check-label" for="terms">
                                    I agree to the <a href="/terms" class="text-decoration-none">Terms of Service</a> 
                                    and <a href="/privacy" class="text-decoration-none">Privacy Policy</a>
                                </label>
                                <div class="invalid-feedback">You must agree to the terms.</div>
                            </div>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="bi bi-person-plus me-2"></i>Create Account
                            </button>
                            <a href="/login" class="btn btn-outline-secondary">
                                Already have an account? Login
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Toggle password visibility
document.getElementById('togglePassword').addEventListener('click', function() {
    const passwordInput = document.getElementById('password');
    const icon = this.querySelector('i');
    togglePasswordVisibility(passwordInput, icon);
});

document.getElementById('toggleConfirmPassword').addEventListener('click', function() {
    const confirmInput = document.getElementById('confirm_password');
    const icon = this.querySelector('i');
    togglePasswordVisibility(confirmInput, icon);
});

function togglePasswordVisibility(input, icon) {
    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'bi bi-eye-slash';
    } else {
        input.type = 'password';
        icon.className = 'bi bi-eye';
    }
}

// Password strength indicator
document.getElementById('password').addEventListener('input', function() {
    const password = this.value;
    const strengthText = document.getElementById('passwordStrength');
    
    if (password.length === 0) {
        strengthText.textContent = '';
        return;
    }
    
    let strength = 0;
    if (password.length >= 8) strength++;
    if (/[A-Z]/.test(password)) strength++;
    if (/[0-9]/.test(password)) strength++;
    if (/[^A-Za-z0-9]/.test(password)) strength++;
    
    const messages = ['Very Weak', 'Weak', 'Fair', 'Good', 'Strong'];
    const colors = ['danger', 'danger', 'warning', 'info', 'success'];
    
    strengthText.textContent = `Strength: ${messages[strength]}`;
    strengthText.className = `form-text text-${colors[strength]}`;
});

// Password confirmation validation
const passwordField = document.getElementById('password');
const confirmField = document.getElementById('confirm_password');

function validatePasswordMatch() {
    if (passwordField.value !== confirmField.value) {
        confirmField.setCustomValidity('Passwords do not match');
    } else {
        confirmField.setCustomValidity('');
    }
}

passwordField.addEventListener('input', validatePasswordMatch);
confirmField.addEventListener('input', validatePasswordMatch);

// Form validation
(() => {
    'use strict'
    const form = document.querySelector('.needs-validation');
    form.addEventListener('submit', event => {
        if (!form.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
        }
        form.classList.add('was-validated');
    }, false);
})();
</script>

<?php require __DIR__ . '/../partials/footer.php' ?>