<?php require __DIR__ . '/../partials/header.php' ?>

<div class="admin-container">
    <div class="admin-sidebar">
        <h3>Admin Panel</h3>
        <ul>
            <li><a href="/admin">Dashboard</a></li>
            <li><a href="/admin/users">Manage Users</a></li>
            <li><a href="/admin/rooms">Manage Rooms</a></li>
            <li><a href="/">Back to Site</a></li>
        </ul>
    </div>
    
    <div class="admin-content">
        <h2>Admin Dashboard</h2>
        <p>Welcome, <?= htmlspecialchars($_SESSION['user_name'] ?? 'Admin', ENT_QUOTES, 'UTF-8') ?>!</p>
        
        <!-- Display error/success messages -->
        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <?= htmlspecialchars($_SESSION['error_message']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['error_message']); ?>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <?= htmlspecialchars($_SESSION['success_message']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['success_message']); ?>
        <?php endif; ?>
        
        <!-- Stats Cards -->
        <div class="admin-stats">
            <div class="stat-card">
                <h3>Total Users</h3>
                <p><?= $stats['user_count'] ?? 0 ?></p>
            </div>
            <div class="stat-card">
                <h3>Total Rooms</h3>
                <p><?= $stats['room_count'] ?? 0 ?></p>
            </div>
            <div class="stat-card">
                <h3>Published Rooms</h3>
                <p><?= $stats['published_rooms'] ?? 0 ?></p>
            </div>
            <div class="stat-card">
                <h3>Active Games</h3>
                <p><?= $stats['active_games'] ?? 0 ?></p>
            </div>
        </div>
        
        <!-- Quick Actions -->
        <div class="card mt-4">
            <div class="card-header">
                <h4 class="mb-0">Quick Actions</h4>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <a href="/admin/users" class="btn btn-outline-primary w-100">
                            <i class="bi bi-people me-2"></i>Manage Users
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="/admin/rooms" class="btn btn-outline-primary w-100">
                            <i class="bi bi-door-open me-2"></i>Manage Rooms
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="/creator/rooms/create" class="btn btn-outline-success w-100">
                            <i class="bi bi-plus-circle me-2"></i>Create Room
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="/" class="btn btn-outline-secondary w-100">
                            <i class="bi bi-house me-2"></i>Back to Site
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Recent Activity -->
        <div class="card mt-4">
            <div class="card-header">
                <h4 class="mb-0">Recent Activity</h4>
            </div>
            <div class="card-body">
                <p class="text-muted">No recent activity to display.</p>
                <!-- You can add recent rooms, users, etc. here -->
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../partials/footer.php' ?>