(function () {
  const data = window.roomLevelData;
  const gridEl = document.getElementById("gameGrid");

  const scoreEl = document.getElementById("score");
  const statusEl = document.getElementById("statusText");

  const btnStart = document.getElementById("btnStart");
  const btnReset = document.getElementById("btnReset");

  const btnUp = document.getElementById("btnUp");
  const btnDown = document.getElementById("btnDown");
  const btnLeft = document.getElementById("btnLeft");
  const btnRight = document.getElementById("btnRight");

  const w = data.gridWidth;
  const h = data.gridHeight;

  let timer = null;
  let score = 0;
  let direction = { x: 1, y: 0 }; // start moving right
  let snake = [{ x: 2, y: 2 }, { x: 1, y: 2 }];
  let food = null;

  function speedFromDifficulty(diff) {
    if (diff === "hard") return 120;
    if (diff === "medium") return 200;
    return 320;
  }

  function toKey(x, y) {
    return x + "," + y;
  }

  function listToMap(list) {
    const map = {};
    for (let i = 0; i < list.length; i++) {
      map[toKey(list[i].x, list[i].y)] = true;
    }
    return map;
  }

  const walls = listToMap((data.config && data.config.walls) ? data.config.walls : []);
  const bombs = listToMap((data.config && data.config.bombs) ? data.config.bombs : []);
  const fixedFoods = (data.config && data.config.foods) ? data.config.foods : [];

  let cells = [];

  function buildGrid() {
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
        cell.style.border = "1px solid rgba(255,255,255,0.08)";
        cell.style.background = "rgba(255,255,255,0.06)";

        gridEl.appendChild(cell);
        cells.push(cell);
      }
    }
  }

  function indexOfCell(x, y) {
    return y * w + x;
  }

  function draw() {
    for (let i = 0; i < cells.length; i++) {
      cells[i].style.background = "rgba(255,255,255,0.06)";
    }

    // walls
    for (const key in walls) {
      const parts = key.split(",");
      const x = parseInt(parts[0], 10);
      const y = parseInt(parts[1], 10);
      const cell = cells[indexOfCell(x, y)];
      if (cell) cell.style.background = "rgba(255,255,255,0.18)";
    }

    // bombs
    for (const key in bombs) {
      const parts = key.split(",");
      const x = parseInt(parts[0], 10);
      const y = parseInt(parts[1], 10);
      const cell = cells[indexOfCell(x, y)];
      if (cell) cell.style.background = "rgba(220, 53, 69, 0.55)";
    }

    // food
    if (food) {
      const cell = cells[indexOfCell(food.x, food.y)];
      if (cell) cell.style.background = "rgba(25, 135, 84, 0.55)";
    }

    // snake
    for (let i = 0; i < snake.length; i++) {
      const s = snake[i];
      const cell = cells[indexOfCell(s.x, s.y)];
      if (!cell) continue;

      if (i === 0) cell.style.background = "rgba(255,255,255,0.75)";
      else cell.style.background = "rgba(255,255,255,0.40)";
    }
  }

  function setStatus(text) {
    statusEl.textContent = text;
  }

  function setScore(val) {
    score = val;
    scoreEl.textContent = String(score);
  }

  function isOnSnake(x, y) {
    for (let i = 0; i < snake.length; i++) {
      if (snake[i].x === x && snake[i].y === y) return true;
    }
    return false;
  }

  function randomEmptyCell() {
    // if creator placed foods, use them first
    if (fixedFoods.length > 0) {
      for (let i = 0; i < fixedFoods.length; i++) {
        const fx = fixedFoods[i].x;
        const fy = fixedFoods[i].y;
        if (!walls[toKey(fx, fy)] && !bombs[toKey(fx, fy)] && !isOnSnake(fx, fy)) {
          return { x: fx, y: fy };
        }
      }
    }

    // fallback random
    let tries = 0;
    while (tries < 500) {
      const x = Math.floor(Math.random() * w);
      const y = Math.floor(Math.random() * h);
      const key = toKey(x, y);

      if (walls[key]) { tries++; continue; }
      if (bombs[key]) { tries++; continue; }
      if (isOnSnake(x, y)) { tries++; continue; }

      return { x, y };
    }
    return { x: 0, y: 0 };
  }

  function resetGame() {
    stopGame();
    setScore(0);
    direction = { x: 1, y: 0 };
    snake = [{ x: 2, y: 2 }, { x: 1, y: 2 }];
    food = randomEmptyCell();
    setStatus("Press Start to play.");
    draw();
  }

  function stopGame() {
    if (timer) {
      clearInterval(timer);
      timer = null;
    }
  }

  function gameOver(message) {
    stopGame();
    setStatus(message);
  }

  function move() {
    const head = snake[0];
    const next = { x: head.x + direction.x, y: head.y + direction.y };

    // hit border
    if (next.x < 0 || next.x >= w || next.y < 0 || next.y >= h) {
      gameOver("Game over: you hit the wall.");
      return;
    }

    const key = toKey(next.x, next.y);

    // hit placed wall
    if (walls[key]) {
      gameOver("Game over: you hit a wall.");
      return;
    }

    // hit bomb
    if (bombs[key]) {
      gameOver("Game over: boom!");
      return;
    }

    // hit yourself
    if (isOnSnake(next.x, next.y)) {
      gameOver("Game over: you hit yourself.");
      return;
    }

    // move head
    snake.unshift(next);

    // eat
    if (food && next.x === food.x && next.y === food.y) {
      setScore(score + 1);
      food = randomEmptyCell();
    } else {
      // remove tail
      snake.pop();
    }

    draw();
  }

  function startGame() {
    if (timer) return;
    setStatus("Playing...");
    const speed = speedFromDifficulty(data.difficulty);
    timer = setInterval(move, speed);
  }

  function setDirection(dx, dy) {
    // prevent reverse move
    if (snake.length > 1) {
      if (direction.x === -dx && direction.y === -dy) return;
    }
    direction = { x: dx, y: dy };
  }

  btnUp.addEventListener("click", function () { setDirection(0, -1); });
  btnDown.addEventListener("click", function () { setDirection(0, 1); });
  btnLeft.addEventListener("click", function () { setDirection(-1, 0); });
  btnRight.addEventListener("click", function () { setDirection(1, 0); });

  btnStart.addEventListener("click", startGame);
  btnReset.addEventListener("click", resetGame);

  // optional keyboard support (doesn't replace buttons)
  document.addEventListener("keydown", function (e) {
    if (e.key === "ArrowUp") setDirection(0, -1);
    if (e.key === "ArrowDown") setDirection(0, 1);
    if (e.key === "ArrowLeft") setDirection(-1, 0);
    if (e.key === "ArrowRight") setDirection(1, 0);
  });

  buildGrid();
  resetGame();
})();