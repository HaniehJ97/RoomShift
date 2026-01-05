<?php require __DIR__ . '/../Partials/header.php'; ?>

<div class="container py-5" style="max-width: 520px;">
    <div class="card">
        <div class="card-header bg-primary">
            Login
        </div>

        <div class="card-body">

            <form method="post" action="/login">
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input
                        type="email"
                        name="email"
                        class="form-control"
                        required
                    >
                </div>

                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input
                        type="password"
                        name="password"
                        class="form-control"
                        required
                    >
                </div>

                <button type="submit" class="btn btn-primary w-100">
                    Login
                </button>
            </form>

            <p class="mt-3 text-center mb-0">
                Don’t have an account?
                <a href="/register">Register here</a>
            </p>

        </div>
    </div>
</div>

<?php require __DIR__ . '/../Partials/footer.php'; ?>