<?php require __DIR__ . '/../partials/header.php' ?>

<div class="admin-container">
    <div class="admin-sidebar">
        <h3>Admin Panel</h3>
        <ul>
            <li><a href="/admin">Dashboard</a></li>
            <li><a href="/admin/users" class="active">Manage Users</a></li>
            <li><a href="/admin/rooms">Manage Rooms</a></li>
            <li><a href="/">Back to Site</a></li>
        </ul>
    </div>
    
    <div class="admin-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Manage Users</h2>
            <div>
                <span class="badge bg-dark"><?= count($users) ?> users</span>
            </div>
        </div>
        
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
        
        <!-- Users Table -->
        <div class="card">
            <div class="card-body">
                <?php if (empty($users)): ?>
                    <div class="text-center py-5">
                        <i class="bi bi-people display-1 text-gray mb-3"></i>
                        <h4 class="text-gray">No users found</h4>
                        <p class="text-gray">There are no users in the system yet.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-dark table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($users as $user): ?>
                                <tr>
                                    <td><?= htmlspecialchars($user->id) ?></td>
                                    <td><?= htmlspecialchars($user->name) ?></td>
                                    <td><?= htmlspecialchars($user->email) ?></td>
                                    <td>
                                        <span class="badge bg-<?= 
                                            $user->role === 'admin' ? 'danger' : 'success' 
                                        ?>">
                                            <?= htmlspecialchars(ucfirst($user->role)) ?>
                                        </span>
                                    </td>
                                    <td><?= date('Y-m-d', strtotime($user->created_at)) ?></td>
                                    <td>
                                        <div class="btn-group">
                                            <form method="POST" action="/admin/users/<?= $user->id ?>/role" class="d-inline">
                                                <select name="role" class="form-select form-select-sm" onchange="this.form.submit()">
                                                    <option value="player" <?= $user->role === 'player' ? 'selected' : '' ?>>Player</option>
                                                    <option value="admin" <?= $user->role === 'admin' ? 'selected' : '' ?>>Admin</option>
                                                </select>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Role Legend -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0">Role Legend</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-2">
                        <span class="badge bg-success me-2">Player</span>
                        <span class="text-gray">Can view and play published rooms</span>
                    </div>
                    <div class="col-md-6 mb-2">
                        <span class="badge bg-danger me-2">Admin</span>
                        <span class="text-gray">Full system access - create, edit rooms, manage users</span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Quick Stats -->
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card bg-success bg-opacity-10">
                    <div class="card-body text-center">
                        <h5 class="card-title text-success">Players</h5>
                        <p class="card-text display-6">
                            <?= count(array_filter($users, fn($u) => $u->role === 'player')) ?>
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card bg-danger bg-opacity-10">
                    <div class="card-body text-center">
                        <h5 class="card-title text-danger">Admins</h5>
                        <p class="card-text display-6">
                            <?= count(array_filter($users, fn($u) => $u->role === 'admin')) ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../partials/footer.php' ?>