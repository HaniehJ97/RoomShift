<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>RoomShift – Escape Rooms</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>

    <header class="topbar">
        <div class="logo">RoomShift</div>
        <nav class="nav-links">
            <a href="/">Home</a>
            <a href="/rooms">Rooms</a>
            <a href="#">Play</a>
            <a href="#">Login</a>
        </nav>
    </header>

    <section class="hero">
        <h1 class="hero-title">
            Build &amp; <span class="hero-highlight">Play</span> Escape Rooms
        </h1>
        <p class="hero-subtitle">
            Design your own digital escape adventures and let others try to solve them.
            Create a room, add puzzles, and share your world.
        </p>
        <a href="#create-room" class="hero-cta">Create your first room</a>
    </section>

    <main class="main">
        <div class="grid">

            <!-- Left: Create room -->
            <section class="card" id="create-room">
                <h2 class="card-title">Create a new room</h2>

                <form action="/rooms" method="post">
                    <div class="form-group">
                        <label for="title">Title</label>
                        <input type="text" id="title" name="title" required>
                    </div>

                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea id="description" name="description" rows="4" required></textarea>
                    </div>

                    <button type="submit">Create room</button>
                </form>
            </section>

            <!-- Right: Rooms list -->
            <section class="card">
                <h2 class="card-title">Available rooms</h2>

                <?php if (empty($rooms)): ?>
                    <p class="rooms-empty">No rooms yet. Create the first one!</p>
                <?php else: ?>
                    <ul class="rooms-list">
                        <?php foreach ($rooms as $room): ?>
                            <li class="rooms-item">
                                <div class="room-title">
                                    <?= htmlspecialchars($room['title'], ENT_QUOTES, 'UTF-8') ?>
                                </div>
                                <p class="room-description">
                                    <?= nl2br(htmlspecialchars($room['description'], ENT_QUOTES, 'UTF-8')) ?>
                                </p>
                                <div class="room-meta">
                                    Created at:
                                    <?= htmlspecialchars($room['created_at'], ENT_QUOTES, 'UTF-8') ?>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </section>

        </div>
    </main>

</body>
</html>