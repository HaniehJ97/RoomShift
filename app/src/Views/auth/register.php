<?php require __DIR__ . '/../Partials/header.php'; ?>

<?php
$formData = $_SESSION['form_data'] ?? [];
unset($_SESSION['form_data']);
?>

<div class="container py-5" style="max-width: 520px;">
    <div class="card">
        <div class="card-header bg-primary">Register</div>
        <div class="card-body">

            <form method="post" action="/register">
                <div class="mb-3">
                    <label class="form-label">Name</label>
                    <input class="form-control" type="text" name="name"
                           value="<?= htmlspecialchars($formData['name'] ?? '') ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input class="form-control" type="email" name="email"
                           value="<?= htmlspecialchars($formData['email'] ?? '') ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input class="form-control" type="password" name="password" required>
                </div>

                <button class="btn btn-primary w-100" type="submit">
                    Create account
                </button>
            </form>

            <p class="mt-3 mb-0 text-center">
                Already have an account? <a href="/login">Login</a>
            </p>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../Partials/footer.php'; ?>