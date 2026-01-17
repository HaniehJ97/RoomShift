<?php require __DIR__ . '/../partials/header.php' ?>

<div class="admin-container">
    <div class="admin-sidebar">
        <h3>Admin Panel</h3>
        <ul>
            <li><a href="/admin">Dashboard</a></li>
            <li><a href="/admin/users">Manage Users</a></li>
            <li><a href="/admin/rooms" class="active">Manage Rooms</a></li>
            <li><a href="/admin/rooms/create">Create Room</a></li>
            <li><a href="/">Back to Site</a></li>
        </ul>
    </div>
    
    <div class="admin-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Manage Rooms</h2>
            <div>
                <span class="badge bg-dark"><?= count($rooms) ?> rooms</span>
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
        
        <!-- Rooms Table -->
        <div class="card">
            <div class="card-body">
                <?php if (empty($rooms)): ?>
                    <div class="text-center py-5">
                        <i class="bi bi-door-open display-1 text-muted mb-3"></i>
                        <h4 class="text-muted">No rooms found</h4>
                        <p class="text-muted">There are no rooms in the system yet.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-dark table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Title</th>
                                    <th>Creator</th>
                                    <th>Difficulty</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($rooms as $room): ?>
                                <tr>
                                    <td><?= htmlspecialchars($room->id) ?></td>
                                    <td>
                                        <a href="/rooms/<?= $room->id ?>/play" class="text-decoration-none">
                                            <?= htmlspecialchars($room->title) ?>
                                        </a>
                                    </td>
                                    <td>User <?= htmlspecialchars($room->creator_id) ?></td>
                                    <td>
                                        <span class="badge bg-<?= 
                                            $room->difficulty === 'hard' ? 'danger' : 
                                            ($room->difficulty === 'medium' ? 'warning' : 'success') 
                                        ?>">
                                            <?= htmlspecialchars($room->difficulty) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($room->is_published): ?>
                                            <span class="badge bg-success">Published</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Draft</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= date('Y-m-d', strtotime($room->created_at)) ?></td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="/rooms/<?= $room->id ?>/play" 
                                               class="btn btn-sm btn-outline-primary" 
                                               title="Play">
                                                <i class="bi bi-play"></i>
                                            </a>
                                            <a href="/admin/rooms/<?= $room->id ?>/edit" 
                                               class="btn btn-sm btn-outline-warning" 
                                               title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <form method="POST" action="/admin/rooms/<?= $room->id ?>/publish" 
                                                  class="d-inline" onsubmit="return confirm('Change publication status?')">
                                                <button type="submit" class="btn btn-sm btn-<?= 
                                                    $room->is_published ? 'danger' : 'success'
                                                ?>">
                                                    <i class="bi bi-<?= 
                                                        $room->is_published ? 'x-circle' : 'check-circle'
                                                    ?>"></i>
                                                    <?= $room->is_published ? 'Unpublish' : 'Publish' ?>
                                                </button>
                                                <input type="hidden" name="publish" value="<?= 
                                                    $room->is_published ? '0' : '1'
                                                ?>">
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
        
        <!-- Quick Stats -->
        <div class="row mt-4">
            <div class="col-md-4">
                <div class="card bg-success bg-opacity-10">
                    <div class="card-body text-center">
                        <h5 class="card-title text-success">Published</h5>
                        <p class="card-text display-6">
                            <?= count(array_filter($rooms, fn($r) => $r->is_published)) ?>
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-warning bg-opacity-10">
                    <div class="card-body text-center">
                        <h5 class="card-title text-warning">Drafts</h5>
                        <p class="card-text display-6">
                            <?= count(array_filter($rooms, fn($r) => !$r->is_published)) ?>
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-info bg-opacity-10">
                    <div class="card-body text-center">
                        <h5 class="card-title text-info">Easy Rooms</h5>
                        <p class="card-text display-6">
                            <?= count(array_filter($rooms, fn($r) => $r->difficulty === 'easy')) ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../partials/footer.php' ?>