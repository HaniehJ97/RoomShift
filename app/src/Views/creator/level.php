<?php
$pageTitle = 'Level Editor';
require __DIR__ . '/../Partials/header.php';

$level = $level ?? null;
$gridW = $level['grid_width'] ?? 12;
$gridH = $level['grid_height'] ?? 12;
$difficulty = $level['difficulty'] ?? 'easy';
$configJson = $level['config_json'] ?? '{"walls":[],"bombs":[],"foods":[]}';
?>

<div class="container py-4">
    <div class="card">
        <div class="card-header bg-primary d-flex justify-content-between align-items-center">
            <span>Level Editor</span>
            <a class="btn btn-outline-light btn-sm" href="/creator/rooms">Back</a>
        </div>

        <div class="card-body">
            <form method="post" action="/creator/rooms/<?= (int)$roomId ?>/level" id="levelForm">
                <div class="row g-3 mb-3">
                    <div class="col-md-3">
                        <label class="form-label">Grid width</label>
                        <input class="form-control" type="number" min="8" max="30" name="grid_width" id="gridW" value="<?= (int)$gridW ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Grid height</label>
                        <input class="form-control" type="number" min="8" max="30" name="grid_height" id="gridH" value="<?= (int)$gridH ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Difficulty</label>
                        <select class="form-select" name="difficulty" id="difficulty">
                            <option value="easy"   <?= $difficulty === 'easy' ? 'selected' : '' ?>>Easy</option>
                            <option value="medium" <?= $difficulty === 'medium' ? 'selected' : '' ?>>Medium</option>
                            <option value="hard"   <?= $difficulty === 'hard' ? 'selected' : '' ?>>Hard</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="button" class="btn btn-outline-primary w-100" id="rebuildBtn">Rebuild Grid</button>
                    </div>
                </div>

                <div class="mb-3 d-flex flex-wrap gap-2">
                    <button type="button" class="btn btn-outline-primary tool-btn active" data-tool="wall">Wall</button>
                    <button type="button" class="btn btn-outline-danger tool-btn" data-tool="bomb">Bomb</button>
                    <button type="button" class="btn btn-outline-success tool-btn" data-tool="food">Food</button>
                    <button type="button" class="btn btn-outline-secondary tool-btn" data-tool="erase">Erase</button>
                </div>

                <input type="hidden" name="config_json" id="configJsonInput" value="<?= htmlspecialchars($configJson) ?>">

                <div class="mb-3">
                    <div id="grid" style="display:grid; gap:6px;"></div>
                    <small class="text-gray d-block mt-2">
                        Click cells to place items. One item per cell.
                    </small>
                </div>

                <button class="btn btn-primary w-100" type="submit">Save Level</button>
            </form>
        </div>
    </div>
</div>

<script>
(function () {
    const gridEl = document.getElementById('grid');
    const gridWEl = document.getElementById('gridW');
    const gridHEl = document.getElementById('gridH');
    const rebuildBtn = document.getElementById('rebuildBtn');
    const configInput = document.getElementById('configJsonInput');

    let tool = 'wall';

    function parseConfig() {
        try {
            return JSON.parse(configInput.value || '{"walls":[],"bombs":[],"foods":[]}');
        } catch (e) {
            return { walls: [], bombs: [], foods: [] };
        }
    }

    function toKey(x, y) {
        return x + ',' + y;
    }

    function fromListToSet(list) {
        const set = {};
        for (let i = 0; i < list.length; i++) {
            set[toKey(list[i].x, list[i].y)] = true;
        }
        return set;
    }

    function setCell(config, x, y, type) {
        // remove from all first
        config.walls = config.walls.filter(p => !(p.x === x && p.y === y));
        config.bombs = config.bombs.filter(p => !(p.x === x && p.y === y));
        config.foods = config.foods.filter(p => !(p.x === x && p.y === y));

        if (type === 'wall') config.walls.push({x, y});
        if (type === 'bomb') config.bombs.push({x, y});
        if (type === 'food') config.foods.push({x, y});
    }

    function drawGrid() {
        const w = parseInt(gridWEl.value, 10);
        const h = parseInt(gridHEl.value, 10);

        const config = parseConfig();

        const wallsSet = fromListToSet(config.walls);
        const bombsSet = fromListToSet(config.bombs);
        const foodsSet = fromListToSet(config.foods);

        gridEl.style.gridTemplateColumns = `repeat(${w}, 34px)`;
        gridEl.innerHTML = '';

        for (let y = 0; y < h; y++) {
            for (let x = 0; x < w; x++) {
                const cell = document.createElement('div');
                cell.dataset.x = x;
                cell.dataset.y = y;

                cell.style.width = '34px';
                cell.style.height = '34px';
                cell.style.borderRadius = '10px';
                cell.style.cursor = 'pointer';
                cell.style.border = '1px solid rgba(255,255,255,0.08)';

                const key = toKey(x, y);

                if (wallsSet[key]) cell.style.background = 'rgba(255,255,255,0.18)';
                else if (bombsSet[key]) cell.style.background = 'rgba(220, 53, 69, 0.55)';
                else if (foodsSet[key]) cell.style.background = 'rgba(25, 135, 84, 0.55)';
                else cell.style.background = 'rgba(255,255,255,0.06)';

                cell.addEventListener('click', function () {
                    const cfg = parseConfig();
                    const cx = parseInt(cell.dataset.x, 10);
                    const cy = parseInt(cell.dataset.y, 10);

                    if (tool === 'erase') {
                        setCell(cfg, cx, cy, '');
                    } else {
                        setCell(cfg, cx, cy, tool);
                    }

                    configInput.value = JSON.stringify(cfg);
                    drawGrid();
                });

                gridEl.appendChild(cell);
            }
        }
    }

    // tool buttons
    const toolBtns = document.querySelectorAll('.tool-btn');
    toolBtns.forEach(btn => {
        btn.addEventListener('click', function () {
            toolBtns.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            tool = btn.dataset.tool;
        });
    });

    rebuildBtn.addEventListener('click', function () {
        // reset config to empty when rebuilding
        configInput.value = '{"walls":[],"bombs":[],"foods":[]}';
        drawGrid();
    });

    drawGrid();
})();
</script>

<?php require __DIR__ . '/../Partials/footer.php'; ?>