<?php
$pageTitle = 'My Rooms';
require __DIR__ . '/../Partials/header.php';
?>

<div class="container py-5">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">My Rooms</h2>
        <a href="/" class="btn btn-outline-primary">Back to Home</a>
    </div>

    <?php if (empty($rooms)): ?>
        <div class="card">
            <div class="card-body">
               <p class="mb-0 text-white opacity-75"> You haven’t created any rooms yet.</p>
            </div>
        </div>
    <?php else: ?>

        <?php foreach ($rooms as $room): ?>
            <div class="card mb-3">
                <div class="card-body">

                    <h5 class="card-title">
                        <?= htmlspecialchars($room->title) ?>
                    </h5>

                    <p class="card-text text-muted">
                        <?= htmlspecialchars($room->description) ?>
                    </p>

                    <div class="d-flex flex-wrap gap-2">

                        <a class="btn btn-outline-secondary btn-sm"
                           href="/creator/rooms/<?= (int)$room->id ?>/edit">
                            Edit Room
                        </a>

                        <!-- ⭐ THIS IS THE IMPORTANT PART -->
                        <a class="btn btn-outline-primary btn-sm"
                           href="/creator/rooms/<?= (int)$room->id ?>/level">
                            Edit Level
                        </a>

                        <form method="post"
                              action="/creator/rooms/<?= (int)$room->id ?>/delete"
                              onsubmit="return confirm('Delete this room?');">
                            <button class="btn btn-outline-danger btn-sm">
                                Delete
                            </button>
                        </form>

                    </div>

                </div>
            </div>
        <?php endforeach; ?>

    <?php endif; ?>

</div>

<?php require __DIR__ . '/../Partials/footer.php'; ?>