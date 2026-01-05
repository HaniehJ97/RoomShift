<?php
$pageTitle = isset($room) && $room ? $room->title . ' - Play' : 'Play Room';
require __DIR__ . '/../Partials/header.php';
?>

<div class="container py-5">

    <?php if (!isset($room) || !$room): ?>
        <div class="card">
            <div class="card-header bg-primary">Room</div>
            <div class="card-body">
                <p class="text-gray mb-3">Room not found.</p>
                <a href="/rooms" class="btn btn-outline-primary">Back to Rooms</a>
            </div>
        </div>
    <?php else: ?>

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header bg-primary d-flex justify-content-between align-items-center">
                        <span><?= htmlspecialchars($room->title) ?></span>
                        <span class="badge bg-dark">
                            <?= htmlspecialchars($room->difficulty ?? 'medium') ?>
                        </span>
                    </div>

                    <div class="card-body">
                        <p class="room-description mb-4">
                            <?= nl2br(htmlspecialchars($room->description)) ?>
                        </p>

                        <div class="room-meta mb-4">
                            <span class="me-3">
                                <strong>Estimated time:</strong> <?= (int)($room->estimated_time ?? 30) ?> min
                            </span>
                        </div>

                        <div class="d-flex flex-wrap gap-2">
                            <a href="/rooms" class="btn btn-outline-primary">
                                Back to Rooms
                            </a>

                            <button class="btn btn-primary" type="button" disabled>
                                Start Game (coming soon)
                            </button>
                        </div>

                        <div class="mt-4 text-gray">
                            <small>
                                This page will later show the game steps/puzzles. For now it shows room details.
                            </small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card border-accent">
                    <div class="card-body text-center py-4">
                        <i class="bi bi-door-closed display-4 text-accent mb-2"></i>
                        <h5 class="mb-2">Ready?</h5>
                        <p class="text-gray mb-0">Make sure you have time before you start.</p>
                    </div>
                </div>
            </div>
        </div>

    <?php endif; ?>

</div>

<?php require __DIR__ . '/../Partials/footer.php'; ?>