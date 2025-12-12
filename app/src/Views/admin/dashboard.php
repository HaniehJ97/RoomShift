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
        
        <div class="admin-stats">
            <div class="stat-card">
                <h3>Total Users</h3>
                <p>--</p>
            </div>
            <div class="stat-card">
                <h3>Total Rooms</h3>
                <p>--</p>
            </div>
            <div class="stat-card">
                <h3>Active Games</h3>
                <p>--</p>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../partials/footer.php' ?>