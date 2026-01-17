<?php require __DIR__ . '/../Partials/header.php'; ?>

<section class="hero">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <h1 class="display-4">
                    Build & <span class="text-warning">Play</span> Escape Rooms
                </h1>
                <p class="lead text-light">
                    Find the key and skip the room. be careful with the bombs!
                </p>

                <div class="d-flex gap-3 mt-4">
                    <?php if (isset($_SESSION['user_id']) && $_SESSION['user_role'] === 'admin'): ?>
                        <!-- Only admins can create rooms -->
                        <a href="/admin/rooms/create" class="btn btn-light btn-lg px-4 py-3 fw-semibold">
                            <i class="bi bi-plus-circle me-2"></i>Create Room
                        </a>
                    <?php endif; ?>

                    <a href="/rooms" class="btn btn-outline-light btn-lg">Browse Rooms</a>
                    
                    <?php if (isset($_SESSION['user_id']) && $_SESSION['user_role'] === 'admin'): ?>
                        <a href="/admin" class="btn btn-outline-warning btn-lg">
                            <i class="bi bi-speedometer2 me-2"></i>Admin Panel
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card border-accent">
                    <div class="card-body text-center py-5">
                        <div style="font-size: 64px;">🚪</div>
                        <h3 class="mt-3 text-light">Immersive Puzzles</h3>
                        <p class="text-gray">Create challenging experiences</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<main class="bg-light py-5">
    <div class="container">

        <?php if (!empty($_SESSION['error_message'])): ?>
            <div class="alert alert-danger">
                <?= htmlspecialchars($_SESSION['error_message']) ?>
            </div>
            <?php unset($_SESSION['error_message']); ?>
        <?php endif; ?>

        <?php if (!empty($_SESSION['success_message'])): ?>
            <div class="alert alert-success">
                <?= htmlspecialchars($_SESSION['success_message']) ?>
            </div>
            <?php unset($_SESSION['success_message']); ?>
        <?php endif; ?>

        <div class="card">
            <div class="card-header bg-primary d-flex justify-content-between">
                <span class="text-light">Available Rooms</span>
                <span class="text-light"><?= count($rooms ?? []) ?> room<?= count($rooms ?? []) === 1 ? '' : 's' ?></span>
            </div>

            <div class="card-body">
                <?php if (empty($rooms)): ?>
                    <p class="text-gray">No rooms available.</p>
                <?php else: ?>
                    <div class="list-group">
                        <?php foreach ($rooms as $room): ?>
                            <div class="list-group-item">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h5 class="text-light mb-1"><?= htmlspecialchars($room->title) ?></h5>
                                        
                                        <!-- Room info badges -->
                                        <div class="mb-2">
                                            <span class="badge bg-<?= 
                                                $room->difficulty === 'hard' ? 'danger' : 
                                                ($room->difficulty === 'medium' ? 'warning' : 'success') 
                                            ?> me-2">
                                                <?= htmlspecialchars(ucfirst($room->difficulty)) ?>
                                            </span>
                                            
                                            <span class="badge bg-info me-2">
                                                <i class="bi bi-clock me-1"></i><?= $room->estimated_time ?> min
                                            </span>
                                            
                                            <?php if (!$room->is_published): ?>
                                                <span class="badge bg-secondary">Draft</span>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <p class="text-light opacity-90 mb-3">
                                            <?= htmlspecialchars($room->description) ?>
                                        </p>
                                    </div>
                                    
                                    <!-- Admin actions for unpublished rooms -->
                                    <?php if (isset($_SESSION['user_id']) && $_SESSION['user_role'] === 'admin' && !$room->is_published): ?>
                                        <div class="dropdown">
                                            <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" 
                                                    data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="bi bi-gear"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-dark">
                                                <li><a class="dropdown-item" href="/admin/rooms/<?= $room->id ?>/edit">
                                                    <i class="bi bi-pencil me-2"></i>Edit
                                                </a></li>
                                                <li><a class="dropdown-item" href="/rooms/<?= $room->id ?>/play">
                                                    <i class="bi bi-play me-2"></i>Test
                                                </a></li>
                                            </ul>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-gray">
                                        <i class="bi bi-calendar me-1"></i>
                                        <?= date('M d, Y', strtotime($room->created_at)) ?>
                                    </small>
                                    
                                    <?php if (isset($_SESSION['user_id'])): ?>
                                        <?php if ($room->is_published || $_SESSION['user_role'] === 'admin'): ?>
                                            <a class="btn btn-outline-primary btn-sm"
                                               href="/rooms/<?= (int)$room->id ?>/play">
                                                <i class="bi bi-play me-1"></i>Play
                                            </a>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Coming Soon</span>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <a class="btn btn-outline-primary btn-sm" href="/login">
                                            <i class="bi bi-box-arrow-in-right me-1"></i>Login to play
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                
                <!-- Quick Stats -->
                <?php if (!empty($rooms) && isset($_SESSION['user_id']) && $_SESSION['user_role'] === 'admin'): ?>
                    <?php 
                    $published = count(array_filter($rooms, fn($r) => $r->is_published));
                    $drafts = count(array_filter($rooms, fn($r) => !$r->is_published));
                    ?>
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="card bg-success bg-opacity-10">
                                <div class="card-body text-center py-3">
                                    <h5 class="card-title text-success">Published</h5>
                                    <p class="card-text display-6"><?= $published ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-warning bg-opacity-10">
                                <div class="card-body text-center py-3">
                                    <h5 class="card-title text-warning">Drafts</h5>
                                    <p class="card-text display-6"><?= $drafts ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Call to Action -->
        <?php if (!isset($_SESSION['user_id'])): ?>
            <div class="card mt-4 border-warning">
                <div class="card-body text-center">
                    <h4 class="text-warning mb-3">Ready to Play?</h4>
                    <p class="text-light mb-4">Join now to access all published escape rooms!</p>
                    <div class="d-flex justify-content-center gap-3">
                        <a href="/register" class="btn btn-primary px-4">
                            <i class="bi bi-person-plus me-2"></i>Sign Up
                        </a>
                        <a href="/login" class="btn btn-outline-primary px-4">
                            <i class="bi bi-box-arrow-in-right me-2"></i>Login
                        </a>
                    </div>
                </div>
            </div>
        <?php endif; ?>

    </div>
</main>

<?php require __DIR__ . '/../Partials/footer.php'; ?>