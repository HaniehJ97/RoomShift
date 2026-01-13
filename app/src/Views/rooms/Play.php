<?php
$pageTitle = isset($room) && $room ? htmlspecialchars($room->title) . ' - Play' : 'Play Room';
require __DIR__ . '/../Partials/header.php';

// Get level configuration from room
if (isset($room) && $room && isset($room->level_config)) {
    $levelConfig = $room->level_config;
    $gridWidth = (int)($levelConfig['grid_width'] ?? 12);
    $gridHeight = (int)($levelConfig['grid_height'] ?? 12);
    $difficulty = $room->difficulty ?? 'easy';
    $configJson = json_encode($levelConfig);
} else {
    $gridWidth = 12;
    $gridHeight = 12;
    $difficulty = 'easy';
    $configJson = '{"walls":[],"bombs":[],"key":{"x":0,"y":0},"door":{"x":11,"y":11}}';
}
?>
<div class="container py-4">
    <?php if (!isset($room) || !$room): ?>
        <div class="card">
            <div class="card-body">
                <p class="text-white opacity-75 mb-3">Room not found.</p>
                <a href="/rooms" class="btn btn-outline-primary">Back</a>
            </div>
        </div>
    <?php else: ?>

        <div class="row g-4">
            <!-- Game Grid (Left) -->
            <div class="col-lg-7 col-xl-8">
                <div class="card h-100 border-end border-accent">
                    <div class="card-header bg-primary d-flex justify-content-between align-items-center">
                        <span><?= htmlspecialchars($room->title) ?></span>
                        <span class="badge bg-dark"><?= htmlspecialchars($difficulty) ?></span>
                    </div>

                    <div class="card-body d-flex flex-column p-4">
                        <!-- Game Status -->
                        <div class="text-center mb-3">
                            <div id="statusText" class="fs-4 fw-bold text-warning mb-2" 
                                 style="min-height: 36px;">
                                Press Start to play.
                            </div>
                            <div class="text-white opacity-75">
                                Score: <span id="score" class="fs-4 fw-bold text-success">0</span>
                            </div>
                        </div>

                        <!-- The Game Grid -->
                        <div class="d-flex justify-content-center align-items-center flex-grow-1">
                            <div id="gameGrid" 
                                 style="display:grid; gap:6px; justify-content:center; align-items:center;">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Controls & Info (Right) -->
            <div class="col-lg-5 col-xl-4">
                <div class="h-100 d-flex flex-column">
                    <!-- Game Controls Panel -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <h5 class="text-white mb-3">
                                <i class="bi bi-joystick me-2"></i>Game Controls
                            </h5>
                            
                            <!-- Start/Reset Buttons -->
                            <div class="d-flex justify-content-center gap-3 mb-4">
                                <button class="btn btn-success px-4 py-2 flex-grow-1" id="btnStart">
                                    <i class="bi bi-play-fill me-2"></i>Start
                                </button>
                                <button class="btn btn-outline-secondary px-4 py-2 flex-grow-1" id="btnReset">
                                    <i class="bi bi-arrow-clockwise me-2"></i>Reset
                                </button>
                            </div>

                            <!-- Directional Controls -->
                            <div class="mb-4">
                                <h6 class="text-warning text-center mb-3">Move Player</h6>
                                <div class="direction-grid">
                                    <!-- Up Button -->
                                    <div class="d-flex justify-content-center mb-2">
                                        <button class="btn btn-outline-primary px-4 py-2" id="btnUp" 
                                                style="width: 120px;">
                                            <i class="bi bi-arrow-up me-2"></i>Up
                                        </button>
                                    </div>
                                    
                                    <!-- Left/Right Buttons -->
                                    <div class="d-flex justify-content-center gap-3 mb-2">
                                        <button class="btn btn-outline-primary px-4 py-2" id="btnLeft" 
                                                style="width: 120px;">
                                            <i class="bi bi-arrow-left me-2"></i>Left
                                        </button>
                                        <button class="btn btn-outline-primary px-4 py-2" id="btnRight" 
                                                style="width: 120px;">
                                            <i class="bi bi-arrow-right me-2"></i>Right
                                        </button>
                                    </div>
                                    
                                    <!-- Down Button -->
                                    <div class="d-flex justify-content-center">
                                        <button class="btn btn-outline-primary px-4 py-2" id="btnDown" 
                                                style="width: 120px;">
                                            <i class="bi bi-arrow-down me-2"></i>Down
                                        </button>
                                    </div>
                                </div>
                                
                                <!-- Keyboard Instructions -->
                                <div class="text-center mt-3 pt-3 border-top border-secondary">
                                    <small class="text-white-50 d-block mb-2">
                                        <i class="bi bi-keyboard me-1"></i>Keyboard Shortcuts
                                    </small>
                                    <div class="d-flex flex-wrap justify-content-center gap-2">
                                        <span class="badge bg-dark">W / ↑</span>
                                        <span class="badge bg-dark">A / ←</span>
                                        <span class="badge bg-dark">S / ↓</span>
                                        <span class="badge bg-dark">D / →</span>
                                        <span class="badge bg-dark">R</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Game Stats -->
                            <div>
                                <h6 class="text-warning mb-3">Game Stats</h6>
                                <div class="row g-2">
                                    <div class="col-6">
                                        <div class="bg-dark p-3 rounded text-center">
                                            <div class="text-white-50 small mb-1">Moves</div>
                                            <div id="moveCount" class="text-white fs-4 fw-bold">0</div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="bg-dark p-3 rounded text-center">
                                            <div class="text-white-50 small mb-1">Time</div>
                                            <div id="gameTime" class="text-white fs-4 fw-bold">0s</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Room Info Panel -->
                    <div class="card border-accent flex-grow-1">
                        <div class="card-body">
                            <h5 class="text-white mb-3">
                                <i class="bi bi-info-circle me-2"></i>Room Info
                            </h5>
                            <p class="text-white opacity-75 mb-3" style="line-height: 1.6;">
                                <?= nl2br(htmlspecialchars($room->description ?? '')) ?>
                            </p>
                            <div class="d-flex justify-content-between">
                                <div>
                                    <i class="bi bi-clock text-warning me-1"></i>
                                    <span class="text-white opacity-75">
                                        <?= (int)($room->estimated_time ?? 30) ?> min
                                    </span>
                                </div>
                                <div>
                                    <i class="bi bi-person text-info me-1"></i>
                                    <span class="text-white opacity-75">
                                        Creator: <?= htmlspecialchars($creatorName ?? 'User ' . $room->creator_id) ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- JavaScript Data -->
        <script>
        window.roomLevelData = {
            gridWidth: <?= $gridWidth ?>,
            gridHeight: <?= $gridHeight ?>,
            config: <?= $configJson ?>,
            hideSecrets: true
        };
        </script>
        
        
        <script>
        // Add these functions for move counter and timer
        let moveCount = 0;
        let gameTime = 0;
        let gameTimer = null;
        
        function updateMoveCount() {
            moveCount++;
            const moveCountEl = document.getElementById('moveCount');
            if (moveCountEl) moveCountEl.textContent = moveCount;
        }
        
        function startGameTimer() {
            if (gameTimer) clearInterval(gameTimer);
            gameTime = 0;
            gameTimer = setInterval(() => {
                gameTime++;
                const gameTimeEl = document.getElementById('gameTime');
                if (gameTimeEl) gameTimeEl.textContent = gameTime + 's';
            }, 1000);
        }
        
        function stopGameTimer() {
            if (gameTimer) {
                clearInterval(gameTimer);
                gameTimer = null;
            }
        }
        
        function resetGameStats() {
            moveCount = 0;
            gameTime = 0;
            const moveCountEl = document.getElementById('moveCount');
            const gameTimeEl = document.getElementById('gameTime');
            if (moveCountEl) moveCountEl.textContent = '0';
            if (gameTimeEl) gameTimeEl.textContent = '0s';
            stopGameTimer();
        }
        </script>
        
        <script src="/assets/js/game.js"></script>

    <?php endif; ?>
</div>

<?php require __DIR__ . '/../Partials/footer.php'; ?>