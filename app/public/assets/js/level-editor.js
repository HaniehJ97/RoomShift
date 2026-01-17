(function () {
    // Check if data is available
    if (!window.levelEditorData) {
        console.error('levelEditorData not found!');
        return;
    }

    const data = window.levelEditorData;
    const gridEl = document.getElementById('levelGrid');
    const configInput = document.getElementById('levelConfigJson');
    
    if (!gridEl || !configInput) {
        console.error('Required elements not found!');
        return;
    }

    let W = data.gridWidth || 12;
    let H = data.gridHeight || 12;
    const cfg = data.config || { 
        walls: [], 
        bombs: [], 
        key: { x: 0, y: 0 }, 
        door: { x: W-1, y: H-1 } 
    };

    let tool = 'wall';
    
    // Initialize from config
    let walls = {};
    let bombs = {};
    let key = cfg.key || { x: 0, y: 0 };
    let door = cfg.door || { x: W-1, y: H-1 };
    
    // Convert arrays to maps
    (cfg.walls || []).forEach(w => { 
        if (w && w.x !== undefined && w.y !== undefined) {
            walls[`${w.x},${w.y}`] = true; 
        }
    });
    
    (cfg.bombs || []).forEach(b => { 
        if (b && b.x !== undefined && b.y !== undefined) {
            bombs[`${b.x},${b.y}`] = true; 
        }
    });

    function posKey(x, y) {
        return x + ',' + y;
    }

    function mapToList(map) {
        const out = [];
        for (const k in map) {
            const p = k.split(',');
            out.push({ 
                x: parseInt(p[0], 10), 
                y: parseInt(p[1], 10) 
            });
        }
        return out;
    }

    //writes the full level config JSON into a hidden input (plus width/height).
    function updateConfig() {
        const config = {
            walls: mapToList(walls),
            bombs: mapToList(bombs),
            key: { x: key.x, y: key.y },
            door: { x: door.x, y: door.y },
            grid_width: W,
            grid_height: H
        };
        configInput.value = JSON.stringify(config);
        
        // Also update hidden width/height inputs
        const widthInput = document.getElementById('gridWidthInput');
        const heightInput = document.getElementById('gridHeightInput');
        if (widthInput) widthInput.value = W;
        if (heightInput) heightInput.value = H;
    }

    // Draws the grid based on current W and H and then colors cells based on state.
    function drawGrid() {
        gridEl.innerHTML = '';
        gridEl.style.gridTemplateColumns = `repeat(${W}, 34px)`;
        
        for (let y = 0; y < H; y++) {
            for (let x = 0; x < W; x++) {
                const cell = document.createElement('div');
                cell.dataset.x = x;
                cell.dataset.y = y;
                cell.style.width = '34px';
                cell.style.height = '34px';
                cell.style.borderRadius = '6px';
                cell.style.border = '1px solid rgba(255,255,255,0.2)';
                cell.style.cursor = 'pointer';
                cell.style.transition = 'all 0.2s';
                
                cell.addEventListener('click', function(e) {
                    handleCellClick(x, y);
                });
                
                cell.addEventListener('mouseenter', function() {
                    cell.style.borderColor = 'rgba(255,255,255,0.5)';
                });
                
                cell.addEventListener('mouseleave', function() {
                    cell.style.borderColor = 'rgba(255,255,255,0.2)';
                });
                
                gridEl.appendChild(cell);
            }
        }
        
        updateColors();
    }

    // Updates cell colors based on current state of walls, bombs, key, and door.
    function updateColors() {
        const cells = gridEl.children;
        
        // Reset all cells
        for (let i = 0; i < cells.length; i++) {
            const cell = cells[i];
            const x = parseInt(cell.dataset.x);
            const y = parseInt(cell.dataset.y);
            
            // Default empty cell
            cell.style.background = 'rgba(255,255,255,0.05)';
        }
        
        // Draw walls (light gray)
        for (const pos in walls) {
            const [x, y] = pos.split(',').map(Number);
            const idx = y * W + x;
            if (cells[idx]) {
                cells[idx].style.background = 'rgba(255,255,255,0.3)';
            }
        }
        
        // Draw bombs (red)
        for (const pos in bombs) {
            const [x, y] = pos.split(',').map(Number);
            const idx = y * W + x;
            if (cells[idx]) {
                cells[idx].style.background = 'rgba(220,53,69,0.7)';
            }
        }
        
        // Draw key (green)
        const keyIdx = key.y * W + key.x;
        if (cells[keyIdx]) {
            cells[keyIdx].style.background = 'rgba(25,135,84,0.7)';
        }
        
        // Draw door (yellow)
        const doorIdx = door.y * W + door.x;
        if (cells[doorIdx]) {
            cells[doorIdx].style.background = 'rgba(255,193,7,0.7)';
        }
    }

    // Cell click handler based on current tool
    function handleCellClick(x, y) {
        const pos = posKey(x, y);
        
        if (tool === 'wall') {
            // Toggle wall
            if (walls[pos]) {
                delete walls[pos];
            } else {
                walls[pos] = true;
                delete bombs[pos];
            }
        } else if (tool === 'bomb') {
            // Toggle bomb
            if (bombs[pos]) {
                delete bombs[pos];
            } else {
                bombs[pos] = true;
                delete walls[pos];
            }
        } else if (tool === 'key') {
            // Move key here
            key = { x: x, y: y };
            delete walls[pos];
            delete bombs[pos];
        } else if (tool === 'door') {
            // Move door here
            door = { x: x, y: y };
            delete walls[pos];
            delete bombs[pos];
        } else if (tool === 'erase') {
            // Erase everything at this position
            delete walls[pos];
            delete bombs[pos];
        }
        
        updateColors();
        updateConfig();
    }

    // Grid dimension change handler
    function updateGridDimensions() {
        const gridWidthInput = document.getElementById('gridWidth');
        const gridHeightInput = document.getElementById('gridHeight');
        const gridWidthHidden = document.getElementById('gridWidthInput');
        const gridHeightHidden = document.getElementById('gridHeightInput');
        
        if (!gridWidthInput || !gridHeightInput) return;
        
        let newWidth = parseInt(gridWidthInput.value) || 12;
        let newHeight = parseInt(gridHeightInput.value) || 12;
        
        // Validate bounds
        newWidth = Math.max(8, Math.min(30, newWidth));
        newHeight = Math.max(8, Math.min(30, newHeight));
        
        // Update inputs with validated values
        gridWidthInput.value = newWidth;
        gridHeightInput.value = newHeight;
        
        // Only update if dimensions actually changed
        if (newWidth !== W || newHeight !== H) {
            // Update global variables
            const oldW = W;
            const oldH = H;
            W = newWidth;
            H = newHeight;
            
            // Reset key and door positions if needed
            if (key.x >= W) key.x = 0;
            if (key.y >= H) key.y = 0;
            if (door.x >= W) door.x = W - 1;
            if (door.y >= H) door.y = H - 1;
            
            // Clear walls and bombs outside new bounds
            const newWalls = {};
            const newBombs = {};
            
            for (const pos in walls) {
                const [x, y] = pos.split(',').map(Number);
                if (x < W && y < H) {
                    newWalls[pos] = true;
                }
            }
            
            for (const pos in bombs) {
                const [x, y] = pos.split(',').map(Number);
                if (x < W && y < H) {
                    newBombs[pos] = true;
                }
            }
            
            walls = newWalls;
            bombs = newBombs;
            
            // Update hidden inputs
            if (gridWidthHidden) gridWidthHidden.value = W;
            if (gridHeightHidden) gridHeightHidden.value = H;
            
            // Redraw
            drawGrid();
            updateConfig();
            
            console.log(`Grid resized from ${oldW}x${oldH} to ${W}x${H}`);
        }
    }

    // Set up tool buttons
    document.querySelectorAll('button[data-tool]').forEach(btn => {
        btn.addEventListener('click', function() {
            // Update active state
            document.querySelectorAll('button[data-tool]').forEach(b => {
                b.classList.remove('active');
            });
            this.classList.add('active');
            
            // Set tool
            tool = this.dataset.tool;
        });
    });

    // Clear button
    const clearBtn = document.getElementById('btnClearLevel');
    if (clearBtn) {
        clearBtn.addEventListener('click', function() {
            if (confirm('Clear the entire grid?')) {
                walls = {};
                bombs = {};
                key = { x: 0, y: 0 };
                door = { x: W-1, y: H-1 };
                updateColors();
                updateConfig();
            }
        });
    }

    // Initialize
    drawGrid();
    updateConfig();
    
    // Add event listeners for grid dimension changes
    const gridWidthInput = document.getElementById('gridWidth');
    const gridHeightInput = document.getElementById('gridHeight');
    
    if (gridWidthInput && gridHeightInput) {
        // Create and add Apply button
        const applyBtn = document.createElement('button');
        applyBtn.type = 'button';
        applyBtn.id = 'btnApplyGridSize';
        applyBtn.className = 'btn btn-outline-info btn-sm ms-2';
        applyBtn.innerHTML = '<i class="bi bi-check-lg"></i> Apply';
        applyBtn.disabled = true; // Initially disabled
        
        // Insert the Apply button after the height input
        const heightCol = gridHeightInput.closest('.col-md-6');
        if (heightCol) {
            const buttonWrapper = document.createElement('div');
            buttonWrapper.className = 'mt-2';
            buttonWrapper.appendChild(applyBtn);
            heightCol.appendChild(buttonWrapper);
        }
        
        // Track original values to know when something changed
        let originalWidth = W;
        let originalHeight = H;
        
        // Function to check if dimensions changed
        function checkForChanges() {
            const currentWidth = parseInt(gridWidthInput.value) || 12;
            const currentHeight = parseInt(gridHeightInput.value) || 12;
            const validatedWidth = Math.max(8, Math.min(30, currentWidth));
            const validatedHeight = Math.max(8, Math.min(30, currentHeight));
            
            // Enable Apply button only if values changed
            applyBtn.disabled = (validatedWidth === originalWidth && validatedHeight === originalHeight);
        }
        
        // Listen for changes in inputs
        gridWidthInput.addEventListener('input', checkForChanges);
        gridHeightInput.addEventListener('input', checkForChanges);
        
        // Also apply on Enter key press
        gridWidthInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                updateGridDimensions();
                originalWidth = W;
                originalHeight = H;
                applyBtn.disabled = true;
            }
        });
        
        gridHeightInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                updateGridDimensions();
                originalWidth = W;
                originalHeight = H;
                applyBtn.disabled = true;
            }
        });
        
        // Apply on button click
        applyBtn.addEventListener('click', function() {
            updateGridDimensions();
            originalWidth = W;
            originalHeight = H;
            this.disabled = true;
        });
        
        // Also apply when input loses focus (optional)
        gridWidthInput.addEventListener('blur', function() {
            // Only apply if changed and button is enabled
            if (!applyBtn.disabled) {
                updateGridDimensions();
                originalWidth = W;
                originalHeight = H;
                applyBtn.disabled = true;
            }
        });
        
        gridHeightInput.addEventListener('blur', function() {
            if (!applyBtn.disabled) {
                updateGridDimensions();
                originalWidth = W;
                originalHeight = H;
                applyBtn.disabled = true;
            }
        });
        
        // Add warning message about data loss
        const warningDiv = document.createElement('div');
        warningDiv.className = 'alert alert-warning alert-sm mt-2';
        warningDiv.innerHTML = '<small><i class="bi bi-exclamation-triangle me-1"></i> Changing grid size will remove objects outside the new boundaries</small>';
        warningDiv.style.display = 'none';
        
        // Insert warning
        const gridDimensionsContainer = document.querySelector('.row.mt-3');
        if (gridDimensionsContainer) {
            const warningCol = document.createElement('div');
            warningCol.className = 'col-md-12';
            warningCol.appendChild(warningDiv);
            gridDimensionsContainer.parentNode.insertBefore(warningCol, gridDimensionsContainer.nextSibling);
        }
        
        // Show warning when inputs change
        function showWarning() {
            warningDiv.style.display = 'block';
        }
        
        gridWidthInput.addEventListener('input', showWarning);
        gridHeightInput.addEventListener('input', showWarning);
        
        // Hide warning after applying
        applyBtn.addEventListener('click', function() {
            warningDiv.style.display = 'none';
        });
    }
})();