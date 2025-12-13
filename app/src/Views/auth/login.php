<?php require __DIR__ . '/../partials/header.php' ?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow border-0">
                <div class="card-header bg-primary text-white text-center py-3">
                    <h3 class="mb-0"><i class="bi bi-box-arrow-in-right me-2"></i>Login to RoomShift</h3>
                </div>
                <div class="card-body p-4">
                    <form action="/login" method="post" class="needs-validation" novalidate>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?= isset($_SESSION['form_data']['email']) ? htmlspecialchars($_SESSION['form_data']['email']) : '' ?>"
                                       placeholder="you@example.com" required>
                                <?php unset($_SESSION['form_data']['email']); ?>
                                <div class="invalid-feedback">Please enter a valid email address.</div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                <input type="password" class="form-control" id="password" name="password" 
                                       placeholder="Enter your password" required>
                                <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                    <i class="bi bi-eye"></i>
                                </button>
                                <div class="invalid-feedback">Please enter your password.</div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <input type="checkbox" class="form-check-input" id="remember" name="remember">
                            <label class="form-check-label" for="remember">Remember me</label>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-box-arrow-in-right me-2"></i>Login
                            </button>
                            <a href="/register" class="btn btn-outline-secondary">
                                Don't have an account? Register
                            </a>
                        </div>
                        
                        <div class="text-center mt-3">
                            <a href="/forgot-password" class="text-decoration-none">Forgot your password?</a>
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
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        icon.className = 'bi bi-eye-slash';
    } else {
        passwordInput.type = 'password';
        icon.className = 'bi bi-eye';
    }
});

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