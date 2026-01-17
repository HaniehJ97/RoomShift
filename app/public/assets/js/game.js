(function () {
  const data = window.roomLevelData || {};

  const gridEl = document.getElementById("gameGrid");
  const scoreEl = document.getElementById("score");
  const statusEl = document.getElementById("statusText");

  const btnStart = document.getElementById("btnStart");
  const btnReset = document.getElementById("btnReset");

  const btnUp = document.getElementById("btnUp");
  const btnDown = document.getElementById("btnDown");
  const btnLeft = document.getElementById("btnLeft");
  const btnRight = document.getElementById("btnRight");

  const w = parseInt(data.gridWidth, 10) || 10;
  const h = parseInt(data.gridHeight, 10) || 10;

  // true => player mode (hide bombs/key/door/walls)
  const hideSecrets = !!data.hideSecrets;

  let score = 0;
  let direction = { x: 0, y: 0 };
  let player = { x: 1, y: 1 };
  let hasKey = false;
  let isFinished = false;

  // fog of war visited cells
  let visited = {};

  // Add game styles
  addGameStyles();

  function toKey(x, y) {
    return x + "," + y;
  }

  function listToMap(list) {
    const map = {};
    if (!Array.isArray(list)) return map;

    for (let i = 0; i < list.length; i++) {
      const x = parseInt(list[i].x, 10);
      const y = parseInt(list[i].y, 10);
      if (!isNaN(x) && !isNaN(y)) {
        map[toKey(x, y)] = true;
      }
    }
    return map;
  }

  const cfg = data.config || {};
  const walls = listToMap(cfg.walls || []);
  const bombs = listToMap(cfg.bombs || []);

  const keyPos = cfg.key
    ? { x: parseInt(cfg.key.x, 10), y: parseInt(cfg.key.y, 10) }
    : null;

  const doorPos = cfg.door
    ? { x: parseInt(cfg.door.x, 10), y: parseInt(cfg.door.y, 10) }
    : null;

  let cells = [];

  function indexOfCell(x, y) {
    return y * w + x;
  }

  // UI UPDATE FUNCTIONS
  function setStatus(text) {
    if (statusEl) {
      statusEl.textContent = text;
      // Add animation to status text
      statusEl.style.animation = 'textPulse 0.5s';
      setTimeout(() => {
        statusEl.style.animation = '';
      }, 500);
    }
  }

  // Update score display with animation
  function setScore(val) {
    score = val;
    if (scoreEl) {
      scoreEl.textContent = String(score);
      // Animate score change
      scoreEl.style.transform = 'scale(1.3)';
      scoreEl.style.color = '#4CAF50';
      setTimeout(() => {
        scoreEl.style.transform = 'scale(1)';
        setTimeout(() => {
          scoreEl.style.color = '';
        }, 300);
      }, 300);
    }
  }

  // GRID AND CELL FUNCTIONS
  function buildGrid() {
    if (!gridEl) return;

    gridEl.style.gridTemplateColumns = `repeat(${w}, 34px)`;
    gridEl.innerHTML = "";
    cells = [];

    for (let y = 0; y < h; y++) {
      for (let x = 0; x < w; x++) {
        const cell = document.createElement("div");
        cell.dataset.x = x;
        cell.dataset.y = y;

        cell.style.width = "34px";
        cell.style.height = "34px";
        cell.style.borderRadius = "10px";
        cell.style.border = "2px solid rgba(255,255,255,0.15)";
        cell.style.background = "rgba(255,255,255,0.05)";
        cell.style.boxShadow = "inset 0 0 0 1px rgba(0,0,0,0.25)";
        cell.style.transition = "all 0.2s";

        gridEl.appendChild(cell);
        cells.push(cell);
      }
    }
  }

  // Reset all cells to default appearance
  function resetCells() {
    for (let i = 0; i < cells.length; i++) {
      cells[i].style.background = "rgba(255,255,255,0.05)";
      cells[i].style.boxShadow = "inset 0 0 0 1px rgba(0,0,0,0.25)";
      cells[i].style.border = "2px solid rgba(255,255,255,0.15)";
      cells[i].style.transform = "";
    }
  }

  // Paint a specific cell with given background and border style
  function paintCell(x, y, bg, strong) {
    const idx = indexOfCell(x, y);
    const cell = cells[idx];
    if (!cell) return;
    cell.style.background = bg;
    if (strong) {
      cell.style.boxShadow = "0 0 0 3px rgba(0,0,0,0.5) inset";
      cell.style.border = "2px solid rgba(255,255,255,0.8)";
    }
  }

  // Draw visited cells with fog of war effect
  function drawVisited() {
    if (!hideSecrets) return;

    for (const k in visited) {
      const parts = k.split(",");
      const x = parseInt(parts[0], 10);
      const y = parseInt(parts[1], 10);
      paintCell(x, y, "rgba(255,255,255,0.12)", false);
    }
  }

  // Draw walls, bombs, key, and door if not in player mode
  function drawSecretsIfAllowed() {
    if (hideSecrets) return;

    for (const k in walls) {
      const parts = k.split(",");
      paintCell(parseInt(parts[0], 10), parseInt(parts[1], 10), "rgba(255,255,255,0.25)", false);
    }

    for (const k in bombs) {
      const parts = k.split(",");
      paintCell(parseInt(parts[0], 10), parseInt(parts[1], 10), "rgba(220, 53, 69, 0.8)", false);
    }

    if (keyPos && !hasKey) {
      paintCell(keyPos.x, keyPos.y, "rgba(25, 135, 84, 0.8)", false);
    }

    if (doorPos) {
      paintCell(doorPos.x, doorPos.y, "rgba(255, 193, 7, 0.8)", false);
    }
  }

  function drawPlayer() {
    paintCell(player.x, player.y, "rgba(255,255,255,0.95)", true);
  }

  function draw() {
    resetCells();
    drawVisited();
    drawSecretsIfAllowed();
    drawPlayer();
  }

  // ANIMATION FUNCTIONS
  function animateWallHit(x, y) {
    const idx = indexOfCell(x, y);
    const cell = cells[idx];
    if (!cell) return;
    
    const originalBg = cell.style.background;
    const originalBorder = cell.style.border;
    
    // Wall hit animation
    cell.style.animation = 'wallBump 0.3s';
    cell.style.border = '2px solid #ff9800';
    cell.style.boxShadow = '0 0 15px rgba(255, 152, 0, 0.7)';
    
    setTimeout(() => {
      cell.style.animation = '';
      cell.style.border = originalBorder;
      cell.style.boxShadow = '';
    }, 300);
  }

  function animateBombExplosion(x, y) {
    const idx = indexOfCell(x, y);
    const cell = cells[idx];
    if (!cell) return;
    
    // Create explosion effect
    createExplosionEffect(x, y);
    
    // Animate the bomb cell
    cell.style.animation = 'bombExplode 0.5s';
    cell.style.background = 'radial-gradient(circle, #ff0000, #ff3300, #ff6600)';
    cell.style.boxShadow = '0 0 25px #ff0000';
    cell.style.zIndex = '100';
    
    // Add explosion emoji temporarily
    const originalContent = cell.innerHTML;
    cell.innerHTML = '💥';
    cell.style.display = 'flex';
    cell.style.alignItems = 'center';
    cell.style.justifyContent = 'center';
    cell.style.fontSize = '20px';
    
    setTimeout(() => {
      cell.style.animation = '';
      cell.style.zIndex = '';
      cell.innerHTML = originalContent;
      cell.style.fontSize = '';
    }, 500);
  }

  function animateKeyPickup(x, y) {
    const idx = indexOfCell(x, y);
    const cell = cells[idx];
    if (!cell) return;
    
    // Key pickup animation
    cell.style.animation = 'keyGlow 1s';
    cell.style.boxShadow = '0 0 20px rgba(25, 135, 84, 0.8)';
    
    // Create floating key effect
    const keyEmoji = document.createElement('div');
    keyEmoji.textContent = '🔑';
    keyEmoji.style.position = 'absolute';
    keyEmoji.style.fontSize = '24px';
    keyEmoji.style.zIndex = '1000';
    keyEmoji.style.left = cell.offsetLeft + 'px';
    keyEmoji.style.top = cell.offsetTop + 'px';
    keyEmoji.style.animation = 'floatUp 1s forwards';
    document.querySelector('.container').appendChild(keyEmoji);
    
    setTimeout(() => {
      if (keyEmoji.parentNode) {
        keyEmoji.parentNode.removeChild(keyEmoji);
      }
    }, 1000);
  }

  // Door reached animation
  function animateDoorReached(x, y) {
    const idx = indexOfCell(x, y);
    const cell = cells[idx];
    if (!cell) return;
    
    // Door success animation
    cell.style.animation = 'doorOpen 1s';
    cell.style.boxShadow = '0 0 30px rgba(255, 193, 7, 0.9)';
    
    // Create victory particles
    createVictoryParticles(x, y);
  }

  function animateDoorLocked(x, y) {
    const idx = indexOfCell(x, y);
    const cell = cells[idx];
    if (!cell) return;
    
    // Door locked animation
    cell.style.animation = 'doorLocked 0.5s';
    cell.style.boxShadow = '0 0 15px rgba(255, 0, 0, 0.7)';
  }

  function createExplosionEffect(centerX, centerY) {
    // Screen flash
    const overlay = document.createElement('div');
    overlay.style.position = 'fixed';
    overlay.style.top = '0';
    overlay.style.left = '0';
    overlay.style.width = '100%';
    overlay.style.height = '100%';
    overlay.style.backgroundColor = 'rgba(255, 0, 0, 0.3)';
    overlay.style.zIndex = '9998';
    overlay.style.pointerEvents = 'none';
    overlay.style.animation = 'flashRed 0.5s';
    document.body.appendChild(overlay);
    
    setTimeout(() => {
      if (overlay.parentNode) overlay.parentNode.removeChild(overlay);
    }, 500);
    
    // Grid shake
    if (gridEl) {
      gridEl.style.animation = 'shake 0.5s';
      setTimeout(() => {
        gridEl.style.animation = '';
      }, 500);
    }
  }

  function createVictoryParticles(centerX, centerY) {
    const colors = ['#4CAF50', '#8BC34A', '#CDDC39', '#FFEB3B', '#FFC107'];
    const emojis = ['🎉', '🎊', '🏆', '⭐', '✨'];
    
    for (let i = 0; i < 15; i++) {
      setTimeout(() => {
        const particle = document.createElement('div');
        particle.textContent = emojis[Math.floor(Math.random() * emojis.length)];
        particle.style.position = 'absolute';
        particle.style.fontSize = Math.random() * 20 + 15 + 'px';
        particle.style.zIndex = '1000';
        
        const gridRect = gridEl.getBoundingClientRect();
        const cellSize = 34;
        const gap = 6;
        
        particle.style.left = (gridRect.left + centerX * (cellSize + gap)) + 'px';
        particle.style.top = (gridRect.top + centerY * (cellSize + gap)) + 'px';
        
        document.querySelector('.container').appendChild(particle);
        
        // Animate particle
        const angle = Math.random() * Math.PI * 2;
        const distance = 50 + Math.random() * 100;
        const duration = 1000 + Math.random() * 500;
        
        const startTime = Date.now();
        
        function animateParticle() {
          const elapsed = Date.now() - startTime;
          const progress = elapsed / duration;
          
          if (progress >= 1) {
            if (particle.parentNode) particle.parentNode.removeChild(particle);
            return;
          }
          
          const currentDistance = distance * progress;
          const x = Math.cos(angle) * currentDistance;
          const y = Math.sin(angle) * currentDistance;
          
          particle.style.transform = `translate(${x}px, ${y}px)`;
          particle.style.opacity = (1 - progress).toString();
          
          requestAnimationFrame(animateParticle);
        }
        
        requestAnimationFrame(animateParticle);
      }, i * 50);
    }
  }

  function win() {
    isFinished = true;
    
    // Victory screen flash
    const victoryOverlay = document.createElement('div');
    victoryOverlay.style.position = 'fixed';
    victoryOverlay.style.top = '0';
    victoryOverlay.style.left = '0';
    victoryOverlay.style.width = '100%';
    victoryOverlay.style.height = '100%';
    victoryOverlay.style.background = 'linear-gradient(45deg, rgba(76, 175, 80, 0.3), rgba(139, 195, 74, 0.3))';
    victoryOverlay.style.zIndex = '9997';
    victoryOverlay.style.pointerEvents = 'none';
    victoryOverlay.style.animation = 'victoryFlash 2s';
    document.body.appendChild(victoryOverlay);
    
    setTimeout(() => {
      if (victoryOverlay.parentNode) victoryOverlay.parentNode.removeChild(victoryOverlay);
    }, 2000);
    
    setStatus("🎉 VICTORY! You escaped the room! 🎉");
    playVictorySound();
  }

  function lose(msg) {
    isFinished = true;
    
    // Different effects based on failure type
    if (msg.includes('Boom') || msg.includes('bomb')) {
      setStatus("💥 KABOOM! You triggered a bomb! Game Over! 💥");
      playExplosionSound();
    } else if (msg.includes('wall')) {
      setStatus("🚧 Ouch! You hit a solid wall! 🚧");
    } else if (msg.includes('boundary')) {
      setStatus("🌌 You wandered off the map! 🌌");
    } else {
      setStatus(msg);
    }
  }

  function playExplosionSound() {
    // Simple explosion sound using Web Audio
    try {
      const audioContext = new (window.AudioContext || window.webkitAudioContext)();
      const oscillator = audioContext.createOscillator();
      const gainNode = audioContext.createGain();
      
      oscillator.connect(gainNode);
      gainNode.connect(audioContext.destination);
      
      oscillator.frequency.setValueAtTime(150, audioContext.currentTime);
      oscillator.frequency.exponentialRampToValueAtTime(50, audioContext.currentTime + 0.3);
      
      gainNode.gain.setValueAtTime(0.3, audioContext.currentTime);
      gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.3);
      
      oscillator.start();
      oscillator.stop(audioContext.currentTime + 0.3);
    } catch (e) {
      // Audio not supported
    }
  }

  function playVictorySound() {
    try {
      const audioContext = new (window.AudioContext || window.webkitAudioContext)();
      
      // Play a victory melody
      const notes = [523.25, 659.25, 783.99, 1046.50]; // C5, E5, G5, C6
      let time = audioContext.currentTime;
      
      notes.forEach((freq, i) => {
        const oscillator = audioContext.createOscillator();
        const gainNode = audioContext.createGain();
        
        oscillator.connect(gainNode);
        gainNode.connect(audioContext.destination);
        
        oscillator.frequency.setValueAtTime(freq, time);
        gainNode.gain.setValueAtTime(0.2, time);
        gainNode.gain.exponentialRampToValueAtTime(0.01, time + 0.3);
        
        oscillator.start(time);
        oscillator.stop(time + 0.3);
        
        time += 0.15;
      });
    } catch (e) {
      // Audio not supported
    }
  }

  function markVisited(x, y) {
    visited[toKey(x, y)] = true;
  }

  function resetGame() {
    setScore(0);
    hasKey = false;
    isFinished = false;

    visited = {};

    player = { x: 1, y: 1 };
    if (walls[toKey(player.x, player.y)] || bombs[toKey(player.x, player.y)]) {
      player = { x: 0, y: 0 };
    }

    markVisited(player.x, player.y);

    direction = { x: 0, y: 0 };

    if (!keyPos || !doorPos) {
      setStatus("⚠️ Level incomplete! Place key and door in editor.");
    } else {
      setStatus("🚀 Ready! Find the key, then reach the door!");
    }

    draw();
  }

  function step() {
    if (isFinished) return;

    if (direction.x === 0 && direction.y === 0) {
      setStatus("🎮 Choose a direction to move!");
      return;
    }

    const next = { x: player.x + direction.x, y: player.y + direction.y };

    // Boundary check
    if (next.x < 0 || next.x >= w || next.y < 0 || next.y >= h) {
      animateWallHit(next.x < 0 ? 0 : (next.x >= w ? w-1 : next.x), 
                    next.y < 0 ? 0 : (next.y >= h ? h-1 : next.y));
      lose("🌌 You hit the boundary!");
      return;
    }

    const k = toKey(next.x, next.y);

    // Wall collision
    if (walls[k]) {
      animateWallHit(next.x, next.y);
      setStatus("🚧 Thud! That's a solid wall!");
      return;
    } 

    // Bomb collision
    if (bombs[k]) {
      markVisited(next.x, next.y);
      draw();
      animateBombExplosion(next.x, next.y);
      setTimeout(() => {
        lose("💣 BOOM! You stepped on a bomb!");
      }, 300);
      return;
    }

    // Move player
    player = next;
    markVisited(player.x, player.y);

    // Key pickup
    if (keyPos && !hasKey && player.x === keyPos.x && player.y === keyPos.y) {
      hasKey = true;
      setScore(score + 100);
      animateKeyPickup(player.x, player.y);
      setStatus("🔑 KEY FOUND! Now find the door!");
    }

    // Door reached
    if (doorPos && player.x === doorPos.x && player.y === doorPos.y) {
      if (hasKey) {
        animateDoorReached(player.x, player.y);
        setTimeout(() => {
          draw();
          win();
        }, 500);
        return;
      } else {
        animateDoorLocked(player.x, player.y);
        setStatus("🔒 The door is locked! Find the key first!");
      }
    }

    draw();
  }

  function startGame() {
    if (isFinished) return;

    if (!keyPos || !doorPos) {
      setStatus("⚠️ Level incomplete! Place key and door in editor.");
      return;
    }

    setStatus("🎮 Use buttons or arrow keys to move!");
    
    // Animate start
    if (gridEl) {
      gridEl.style.animation = 'pulseStart 1s';
      setTimeout(() => {
        gridEl.style.animation = '';
      }, 1000);
    }
  }

  function setDirection(dx, dy) {
    if (isFinished) return;
    direction = { x: dx, y: dy };
    step();
  }

  // Add CSS animations
  function addGameStyles() {
    const style = document.createElement('style');
    style.textContent = `
      @keyframes wallBump {
        0% { transform: scale(1); }
        50% { transform: scale(1.1); background-color: rgba(255, 152, 0, 0.5); }
        100% { transform: scale(1); }
      }
      
      @keyframes bombExplode {
        0% { transform: scale(1); }
        50% { transform: scale(1.5); }
        100% { transform: scale(1); }
      }
      
      @keyframes keyGlow {
        0% { box-shadow: 0 0 5px rgba(25, 135, 84, 0.5); }
        50% { box-shadow: 0 0 25px rgba(25, 135, 84, 0.9); }
        100% { box-shadow: 0 0 5px rgba(25, 135, 84, 0.5); }
      }
      
      @keyframes doorOpen {
        0% { transform: rotateY(0); }
        50% { transform: rotateY(90deg); }
        100% { transform: rotateY(0); }
      }
      
      @keyframes doorLocked {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-5px); }
        75% { transform: translateX(5px); }
      }
      
      @keyframes flashRed {
        0%, 100% { background-color: rgba(255, 0, 0, 0); }
        50% { background-color: rgba(255, 0, 0, 0.4); }
      }
      
      @keyframes victoryFlash {
        0% { background: rgba(76, 175, 80, 0); }
        50% { background: rgba(76, 175, 80, 0.4); }
        100% { background: rgba(76, 175, 80, 0); }
      }
      
      @keyframes shake {
        0%, 100% { transform: translateX(0); }
        10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
        20%, 40%, 60%, 80% { transform: translateX(5px); }
      }
      
      @keyframes textPulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.05); }
      }
      
      @keyframes pulseStart {
        0%, 100% { box-shadow: 0 0 0 0 rgba(0, 123, 255, 0); }
        50% { box-shadow: 0 0 0 10px rgba(0, 123, 255, 0.3); }
      }
      
      @keyframes floatUp {
        0% { transform: translateY(0) scale(1); opacity: 1; }
        100% { transform: translateY(-100px) scale(0.5); opacity: 0; }
      }
      
      .shake {
        animation: shake 0.5s;
      }
    `;
    document.head.appendChild(style);
  }

  // Event listeners
  if (btnUp) btnUp.addEventListener("click", function () { setDirection(0, -1); });
  if (btnDown) btnDown.addEventListener("click", function () { setDirection(0, 1); });
  if (btnLeft) btnLeft.addEventListener("click", function () { setDirection(-1, 0); });
  if (btnRight) btnRight.addEventListener("click", function () { setDirection(1, 0); });

  if (btnStart) btnStart.addEventListener("click", startGame);
  if (btnReset) btnReset.addEventListener("click", resetGame);

  // Keyboard controls
  document.addEventListener("keydown", function (e) {
    if (e.key === "ArrowUp" || e.key === "w" || e.key === "W") setDirection(0, -1);
    if (e.key === "ArrowDown" || e.key === "s" || e.key === "S") setDirection(0, 1);
    if (e.key === "ArrowLeft" || e.key === "a" || e.key === "A") setDirection(-1, 0);
    if (e.key === "ArrowRight" || e.key === "d" || e.key === "D") setDirection(1, 0);
  });

  // Initialize game
  buildGrid();
  resetGame();
})();