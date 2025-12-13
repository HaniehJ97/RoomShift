<?php
/** @var App\ViewModels\RoomsViewModel $vm */
/** @var string $error */
?>

<?php require __DIR__ . '/../partials/header.php' ?>

<div class="container py-5">
    <!-- Two Column Layout with Bootstrap Grid -->
    <div class="row g-4">
        <!-- Left: Create Room Form -->
        <div class="col-lg-6">
            <div class="card shadow border-0" id="create-room">
                <div class="card-header bg-primary text-white">
                    <h2 class="h4 mb-0"><i class="bi bi-plus-circle me-2"></i>Create New Room</h2>
                </div>
                <div class="card-body">
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <?= htmlspecialchars($error) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <form action="/rooms" method="post" id="roomForm" class="needs-validation" novalidate>
                        <div class="mb-3">
                            <label for="title" class="form-label">Room Title</label>
                            <input type="text" class="form-control form-control-lg" 
                                   id="title" name="title" 
                                   value="<?= isset($_POST['title']) ? htmlspecialchars($_POST['title']) : '' ?>"
                                   placeholder="Enter room title" required>
                            <div class="invalid-feedback">Please enter a room title (at least 3 characters).</div>
                            <div class="form-text">Make it catchy and descriptive!</div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" 
                                      rows="5" placeholder="Describe your escape room..." required><?= 
                                isset($_POST['description']) ? htmlspecialchars($_POST['description']) : '' 
                            ?></textarea>
                            <div class="invalid-feedback">Please enter a description (at least 10 characters).</div>
                            <div class="form-text">Describe the story, puzzles, and challenges.</div>
                        </div>

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
                        <?= count($vm->rooms) ?> room<?= count($vm->rooms) !== 1 ? 's' : '' ?>
                    </span>
                </div>
                <div class="card-body">
                    <?php if (empty($vm->rooms)): ?>
                        <div class="text-center py-5">
                            <i class="bi bi-door-closed display-1 text-muted mb-3"></i>
                            <h3 class="h5 text-muted">No rooms yet</h3>
                            <p class="text-muted">Be the first to create an escape room!</p>
                        </div>
                    <?php else: ?>
                        <div class="list-group list-group-flush">
                            <?php foreach ($vm->rooms as $room): ?>
                            <div class="list-group-item border-0 py-3">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h5 class="mb-0 fw-bold"><?= htmlspecialchars($room->title) ?></h5>
                                    <button class="btn btn-sm btn-outline-primary view-room-btn" 
                                            data-id="<?= $room->id ?>">
                                        <i class="bi bi-eye"></i> View
                                    </button>
                                </div>
                                <p class="room-description mb-2"><?= htmlspecialchars($room->description) ?></p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-gray">
                                        <i class="bi bi-calendar me-1"></i>
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

    <!-- Room Details Modal (Bootstrap Component) -->
    <div class="modal fade" id="roomModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="roomModalTitle">Room Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="roomModalBody">
                    <!-- Content loaded via JavaScript -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <a href="#" class="btn btn-primary" id="playRoomBtn">Play This Room</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../partials/footer.php' ?>

<!-- JavaScript for Interactivity -->
<script src="/assets/js/rooms.js"></script>
<script>
// Bootstrap Form Validation
(() => {
    'use strict'
    const forms = document.querySelectorAll('.needs-validation')
    Array.from(forms).forEach(form => {
        form.addEventListener('submit', event => {
            if (!form.checkValidity()) {
                event.preventDefault()
                event.stopPropagation()
            }
            form.classList.add('was-validated')
        }, false)
    })
})()
</script>