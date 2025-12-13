<?php require __DIR__ . '/../partials/header.php' ?>

<div class="container py-5">
    <div class="row g-4">
        <!-- Left: Create Room Form -->
        <div class="col-lg-6">
            <div class="card shadow border-0" id="create-room">
                <div class="card-header bg-primary text-white">
                    <h2 class="h4 mb-0"><i class="bi bi-plus-circle me-2"></i>Create New Escape Room</h2>
                </div>
                <div class="card-body">
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <?= htmlspecialchars($error) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <form action="/creator/rooms" method="post" id="roomForm" class="needs-validation" novalidate>
                        <div class="mb-3">
                            <label for="title" class="form-label">Room Title *</label>
                            <input type="text" class="form-control form-control-lg" 
                                   id="title" name="title" 
                                   value="<?= isset($_POST['title']) ? htmlspecialchars($_POST['title']) : '' ?>"
                                   placeholder="Enter room title" required>
                            <div class="invalid-feedback">Please enter a room title (at least 3 characters).</div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description *</label>
                            <textarea class="form-control" id="description" name="description" 
                                      rows="5" placeholder="Describe your escape room story..." required><?= 
                                isset($_POST['description']) ? htmlspecialchars($_POST['description']) : '' 
                            ?></textarea>
                            <div class="invalid-feedback">Please enter a description (at least 10 characters).</div>
                        </div>

                        <!-- NEW FIELDS -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="difficulty" class="form-label">Difficulty</label>
                                <select class="form-select" id="difficulty" name="difficulty">
                                    <option value="easy">Easy</option>
                                    <option value="medium" selected>Medium</option>
                                    <option value="hard">Hard</option>
                                </select>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="estimated_time" class="form-label">Estimated Time (minutes)</label>
                                <input type="number" class="form-control" 
                                       id="estimated_time" name="estimated_time" 
                                       min="5" max="180" value="30">
                            </div>
                        </div>

                        <!-- Hidden field for creator_id -->
                        <input type="hidden" name="creator_id" value="<?= $_SESSION['user_id'] ?? 1 ?>">
                        
                        <!-- Publish option (only for creators/admins) -->
                        <?php if (isset($_SESSION['user_role']) && in_array($_SESSION['user_role'], ['creator', 'admin'])): ?>
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="is_published" name="is_published" value="1">
                            <label class="form-check-label" for="is_published">Publish immediately</label>
                            <div class="form-text">Published rooms are visible to all players.</div>
                        </div>
                        <?php endif; ?>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-plus-circle me-2"></i>Create Room
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Right: Rooms List -->
        <div class="col-lg-6" id="browse-rooms">
            <div class="card shadow border-0">
                <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                    <h2 class="h4 mb-0"><i class="bi bi-door-open me-2"></i>Available Rooms</h2>
                    <span class="badge bg-light text-dark fs-6" id="roomCount">
                        <?= isset($publishedRooms) ? count($publishedRooms) : 0 ?> room<?= isset($publishedRooms) && count($publishedRooms) !== 1 ? 's' : '' ?>
                    </span>
                </div>
                <div class="card-body">
                    <?php 
                    // This should show PUBLISHED rooms for public, or ALL rooms for creators
                    $roomsToShow = $rooms ?? $publishedRooms ?? [];
                    ?>
                    
                    <?php if (empty($roomsToShow)): ?>
                        <div class="text-center py-5">
                            <i class="bi bi-door-closed display-1 text-gray mb-3"></i>
                            <h3 class="h5 text-gray">No rooms yet</h3>
                            <p class="text-gray">Be the first to create an escape room!</p>
                        </div>
                    <?php else: ?>
                        <div class="list-group list-group-flush">
                            <?php foreach ($roomsToShow as $room): ?>
                            <div class="list-group-item border-0 py-3">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <h5 class="mb-0 fw-bold"><?= htmlspecialchars($room->title) ?></h5>
                                        <!-- Show difficulty badge -->
                                        <span class="badge bg-<?= 
                                            $room->difficulty === 'easy' ? 'success' : 
                                            ($room->difficulty === 'medium' ? 'warning' : 'danger')
                                        ?> me-2">
                                            <?= ucfirst($room->difficulty) ?>
                                        </span>
                                        <!-- Show publish status -->
                                        <?php if (isset($room->is_published)): ?>
                                            <span class="badge bg-<?= $room->is_published ? 'info' : 'secondary' ?>">
                                                <?= $room->is_published ? 'Published' : 'Draft' ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                    <button class="btn btn-sm btn-outline-primary view-room-btn" 
                                            data-id="<?= $room->id ?>">
                                        <i class="bi bi-eye"></i> View
                                    </button>
                                </div>
                                <p class="room-description mb-2"><?= htmlspecialchars($room->description) ?></p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-gray">
                                        <i class="bi bi-clock me-1"></i>
                                        <?= $room->estimated_time ?? 30 ?> min •
                                        <i class="bi bi-calendar ms-2 me-1"></i>
                                        <?= date('M d, Y', strtotime($room->created_at)) ?>
                                    </small>
                                    <span class="badge bg-info">Escape Room</span>
                                </div>
                            </div>
                            <hr class="my-1">
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../partials/footer.php' ?>