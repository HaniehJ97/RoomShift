<?php
$pageTitle = $room->title . ' - Play';
require __DIR__ . '/../Partials/header.php';
$levelConfig = $room->level_config;
?>

<script>
window.roomLevelData = {
    gridWidth: <?= $levelConfig['grid_width'] ?>,
    gridHeight: <?= $levelConfig['grid_height'] ?>,
    config: <?= json_encode($levelConfig) ?>,
    hideSecrets: true
};
</script>

<!-- $gridW = (int)($levelConfig['grid_width'] ?? 12);
$gridH = (int)($levelConfig['grid_height'] ?? 12);
$difficulty = $levelConfig['difficulty'] ?? 'easy';
$configJson = json_encode($levelConfig);?> -->

<div class="container py-5">
    <?php if (!isset($room) || !$room): ?>
        <div class="card">
            <div class="card-body">
                <p class="text-white opacity-75 mb-3">Room not found.</p>
                <a href="/rooms" class="btn btn-outline-primary">Back</a>
            </div>
        </div>
    <?php else: ?>

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header bg-primary d-flex justify-content-between align-items-center">
                        <span><?= htmlspecialchars($room->title) ?></span>
                        <span class="badge bg-dark"><?= htmlspecialchars($difficulty) ?></span>
                    </div>

                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="text-white opacity-75">
                                Score: <span id="score">0</span>
                            </div>

                            <div class="d-flex gap-2">
                                <button class="btn btn-outline-light btn-sm" id="btnStart">Start</button>
                                <button class="btn btn-outline-light btn-sm" id="btnReset">Reset</button>
                            </div>
                        </div>

                        <div id="gameGrid" style="display:grid; gap:6px; justify-content:start;"></div>

                        <div class="mt-4 d-flex justify-content-center">
                            <div class="d-grid gap-2" style="width: 220px;">
                                <button class="btn btn-outline-primary" id="btnUp">Up</button>
                                <div class="d-flex gap-2">
                                    <button class="btn btn-outline-primary w-50" id="btnLeft">Left</button>
                                    <button class="btn btn-outline-primary w-50" id="btnRight">Right</button>
                                </div>
                                <button class="btn btn-outline-primary" id="btnDown">Down</button>
                            </div>
                        </div>

                        <div class="mt-3 text-center">
                            <small class="text-white opacity-50" id="statusText">Press Start to play.</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card border-accent">
                    <div class="card-body">
                        <h5 class="text-white mb-2">Room Info</h5>
                        <p class="text-white opacity-75 mb-3">
                            <?= nl2br(htmlspecialchars($room->description)) ?>
                        </p>
                        <p class="text-white opacity-75 mb-0">
                            Estimated time: <?= (int)($room->estimated_time ?? 30) ?> min
                        </p>
                    </div>
                </div>
            </div>
        </div>

            <script>
            window.roomLevelData = <?= json_encode($levelData) ?>;
            window.roomLevelData.hideSecrets = true;
             </script>   
        <script src="/assets/js/snake.js"></script>

    <?php endif; ?>
</div>

<?php require __DIR__ . '/../Partials/footer.php'; ?>