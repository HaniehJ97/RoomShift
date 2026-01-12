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

    const W = data.gridWidth || 12;
    const H = data.gridHeight || 12;
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
})();