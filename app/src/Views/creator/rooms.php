<?php
$pageTitle = 'My Rooms';
require __DIR__ . '/../Partials/header.php';
?>

<div class="container py-5">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">My Rooms</h2>
        <a href="/" class="btn btn-outline-warning">Back to Home</a>
    </div>

    <!-- CREATE ROOM-->
    <div class="card border-warning mb-5">
        <div class="card-header text-warning fw-bold">
            Create New Escape Room
        </div>

        <div class="card-body">
            <form method="post" action="/creator/rooms">

                <div class="row mb-3">
                    <div class="col-md-8">
                        <label class="form-label">Title</label>
                        <input class="form-control" name="title" required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Difficulty</label>
                        <select class="form-select" name="difficulty">
                            <option value="easy">Easy</option>
                            <option value="medium" selected>Medium</option>
                            <option value="hard">Hard</option>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea class="form-control" name="description" rows="3" required></textarea>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Estimated time (minutes)</label>
                        <input type="number" class="form-control" name="estimated_time" value="30">
                    </div>

                    <div class="col-md-6 d-flex align-items-end">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_published" value="1">
                            <label class="form-check-label">Publish now</label>
                        </div>
                    </div>
                </div>

                <!-- LEVEL EDITOR (GRID)-->
                <?php
                // Create default level config for the create form
                $defaultLevelConfig = [
                    'grid_width' => 12,
                    'grid_height' => 12,
                    'walls' => [],
                    'bombs' => [],
                    'key' => ['x' => 0, 'y' => 0],
                    'door' => ['x' => 11, 'y' => 11]
                ];
                
                // Set variables that levelEditor.php expects
                $levelConfig = $defaultLevelConfig;
                $gridWidth = $levelConfig['grid_width'];
                $gridHeight = $levelConfig['grid_height'];
                $configJson = json_encode($levelConfig);
                
                require __DIR__ . '/../Partials/levelEditor.php';
                ?>

                <button class="btn btn-warning w-100 mt-4">
                    Create Room
                </button>

            </form>
        </div>
    </div>

    <!-- YOUR ROOMS -->
    <h4 class="mb-3">Your Rooms</h4>

    <?php if (empty($rooms)): ?>
        <div class="card">
            <div class="card-body text-muted">
                You haven't created any rooms yet.
            </div>
        </div>
    <?php else: ?>

        <?php foreach ($rooms as $room): ?>
            <div class="card mb-3">
                <div class="card-body">

                    <h5><?= htmlspecialchars($room->title) ?></h5>
                    <p class="text-muted"><?= htmlspecialchars($room->description) ?></p>

                    <small class="text-muted">
                        Difficulty: <?= htmlspecialchars($room->difficulty) ?> |
                        Time: <?= (int)$room->estimated_time ?> min |
                        <?= $room->is_published ? 'Published' : 'Draft' ?>
                    </small>

                    <div class="mt-3 d-flex gap-2">
                        <a class="btn btn-outline-warning btn-sm"
                           href="/creator/rooms/<?= (int)$room->id ?>/edit">
                            Edit
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