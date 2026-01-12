<?php
$pageTitle = 'Edit Room';
require __DIR__ . '/../Partials/header.php';

// Ensure we have the level config data
if (isset($room) && $room && isset($room->level_config)) {
    $levelConfig = $room->level_config;
    $gridWidth = (int)($levelConfig['grid_width'] ?? 12);
    $gridHeight = (int)($levelConfig['grid_height'] ?? 12);
    $configJson = json_encode($levelConfig);
} else {
    // Fallback defaults (shouldn't happen, but just in case)
    $gridWidth = 12;
    $gridHeight = 12;
    $configJson = '{"walls":[],"bombs":[],"key":{"x":0,"y":0},"door":{"x":11,"y":11}}';
}
?>

<div class="container py-5">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Edit Room</h2>
        <a href="/creator/rooms" class="btn btn-outline-warning">Back to My Rooms</a>
    </div>

    <div class="card border-warning">
        <div class="card-body">

            <form method="post" action="/creator/rooms/<?= (int)$room->id ?>/edit">

                <div class="row mb-3">
                    <div class="col-md-8">
                        <label class="form-label">Title</label>
                        <input class="form-control" name="title"
                               value="<?= htmlspecialchars($room->title) ?>" required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Difficulty</label>
                        <select class="form-select" name="difficulty">
                            <option value="easy" <?= $room->difficulty === 'easy' ? 'selected' : '' ?>>Easy</option>
                            <option value="medium" <?= $room->difficulty === 'medium' ? 'selected' : '' ?>>Medium</option>
                            <option value="hard" <?= $room->difficulty === 'hard' ? 'selected' : '' ?>>Hard</option>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea class="form-control" name="description" rows="3" required><?= htmlspecialchars($room->description) ?></textarea>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Estimated time (minutes)</label>
                        <input type="number" class="form-control" name="estimated_time"
                               value="<?= (int)$room->estimated_time ?>">
                    </div>

                    <div class="col-md-6 d-flex align-items-end">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox"
                                   name="is_published" value="1"
                                   <?= $room->is_published ? 'checked' : '' ?>>
                            <label class="form-check-label">Published</label>
                        </div>
                    </div>
                </div>

                <!-- LEVEL EDITOR  -->
                <?php
                // Pass the variables to levelEditor.php
                // Some levelEditor.php files might expect $level instead of separate variables
                // Let's set both to be safe
                $level = [
                    'grid_width' => $gridWidth,
                    'grid_height' => $gridHeight,
                    'config_json' => $configJson
                ];
                
                require __DIR__ . '/../Partials/levelEditor.php';
                ?>

                <button class="btn btn-warning w-100 mt-4">
                    Save Changes
                </button>

            </form>

        </div>
    </div>

</div>

<?php require __DIR__ . '/../Partials/footer.php'; ?>