<?php require __DIR__ . '/../partials/header.php' ?>

<div class="admin-container">
    <div class="admin-sidebar">
        <h3>Admin Panel</h3>
        <ul>
            <li><a href="/admin">Dashboard</a></li>
            <li><a href="/admin/users">Manage Users</a></li>
            <li><a href="/admin/rooms">Manage Rooms</a></li>
            <li><a href="/admin/rooms/create" class="active">Create Room</a></li>
            <li><a href="/">Back to Site</a></li>
        </ul>
    </div>
    
    <div class="admin-content">
        <h2>Create New Room</h2>
        
        <!-- Display error/success messages -->
        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <?= htmlspecialchars($_SESSION['error_message']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['error_message']); ?>
        <?php endif; ?>
        
        <!-- Create Room Form -->
        <div class="card">
            <div class="card-body">
                <form method="POST" action="/admin/rooms">
                    <div class="row mb-3">
                        <div class="col-md-8">
                            <label for="title" class="form-label">Room Title</label>
                            <input type="text" class="form-control" id="title" name="title" 
                                   value="<?= htmlspecialchars($_SESSION['form_data']['title'] ?? '') ?>" 
                                   required placeholder="Enter room title">
                        </div>
                        <div class="col-md-4">
                            <label for="difficulty" class="form-label">Difficulty</label>
                            <select class="form-select" id="difficulty" name="difficulty">
                                <option value="easy" <?= ($_SESSION['form_data']['difficulty'] ?? 'easy') === 'easy' ? 'selected' : '' ?>>Easy</option>
                                <option value="medium" <?= ($_SESSION['form_data']['difficulty'] ?? '') === 'medium' ? 'selected' : '' ?>>Medium</option>
                                <option value="hard" <?= ($_SESSION['form_data']['difficulty'] ?? '') === 'hard' ? 'selected' : '' ?>>Hard</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" 
                                  rows="3" required placeholder="Describe your room..."><?= htmlspecialchars($_SESSION['form_data']['description'] ?? '') ?></textarea>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="estimated_time" class="form-label">Estimated Time (minutes)</label>
                            <input type="number" class="form-control" id="estimated_time" name="estimated_time" 
                                   value="<?= $_SESSION['form_data']['estimated_time'] ?? 30 ?>" min="1" max="180">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Publication Status</label>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_published" 
                                       name="is_published" value="1" <?= isset($_SESSION['form_data']['is_published']) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="is_published">
                                    Publish this room immediately
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Level Config Editor -->
                    <div class="mb-4">
                        <label class="form-label">Level Configuration</label>
                        <div class="card bg-dark">
                            <div class="card-body">
                                <div id="levelGrid"></div>
                                <input type="hidden" id="levelConfigJson" name="config_json" value='{}'>
                                
                                <div class="mt-3">
                                    <div class="btn-group mb-2">
                                        <button type="button" class="btn btn-outline-primary active" data-tool="wall">
                                            <i class="bi bi-square"></i> Wall
                                        </button>
                                        <button type="button" class="btn btn-outline-primary" data-tool="bomb">
                                            <i class="bi bi-exclamation-triangle"></i> Bomb
                                        </button>
                                        <button type="button" class="btn btn-outline-primary" data-tool="key">
                                            <i class="bi bi-key"></i> Key
                                        </button>
                                        <button type="button" class="btn btn-outline-primary" data-tool="door">
                                            <i class="bi bi-door-open"></i> Door
                                        </button>
                                        <button type="button" class="btn btn-outline-primary" data-tool="erase">
                                            <i class="bi bi-eraser"></i> Erase
                                        </button>
                                    </div>
                                    <button type="button" class="btn btn-outline-danger btn-sm" id="btnClearLevel">
                                        <i class="bi bi-trash"></i> Clear All
                                    </button>
                                </div>
                                
                                <!-- Grid Dimensions -->
                                <div class="row mt-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Grid Width</label>
                                        <input type="number" class="form-control" id="gridWidth" 
                                               value="12" min="8" max="30">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Grid Height</label>
                                        <input type="number" class="form-control" id="gridHeight" 
                                               value="12" min="8" max="30">
                                    </div>
                                </div>
                                
                                <input type="hidden" name="grid_width" id="gridWidthInput" value="12">
                                <input type="hidden" name="grid_height" id="gridHeightInput" value="12">
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <div>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-plus-circle me-2"></i>Create Room
                            </button>
                            <a href="/admin/rooms" class="btn btn-outline-secondary ms-2">Cancel</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Instructions -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0">Level Editor Instructions</h5>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item bg-transparent">
                        <span class="badge bg-secondary me-2">Wall</span> Click to place/remove walls (gray)
                    </li>
                    <li class="list-group-item bg-transparent">
                        <span class="badge bg-danger me-2">Bomb</span> Click to place/remove bombs (red)
                    </li>
                    <li class="list-group-item bg-transparent">
                        <span class="badge bg-success me-2">Key</span> Click to place the key (green)
                    </li>
                    <li class="list-group-item bg-transparent">
                        <span class="badge bg-warning me-2">Door</span> Click to place the exit door (yellow)
                    </li>
                    <li class="badge bg-dark me-2">Erase</span> Click to remove any object
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
window.levelEditorData = {
    gridWidth: 12,
    gridHeight: 12,
    config: {
        walls: [],
        bombs: [],
        key: { x: 0, y: 0 },
        door: { x: 11, y: 11 }
    }
};
</script>

<script src="/assets/js/level-editor.js"></script>

<?php 
// Clear form data after showing
unset($_SESSION['form_data']);

require __DIR__ . '/../partials/footer.php' ?>