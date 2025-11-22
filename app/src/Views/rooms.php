<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>RoomShift – Rooms</title>
</head>
<body>
    <h1>RoomShift – Escape Rooms</h1>

    <section>
        <h2>Create a new room</h2>
        <form action="/rooms" method="post">
            <div>
                <label for="title">Title</label><br>
                <input type="text" id="title" name="title" required>
            </div>

            <div style="margin-top: 8px;">
                <label for="description">Description</label><br>
                <textarea id="description" name="description" rows="4" cols="40" required></textarea>
            </div>

            <div style="margin-top: 8px;">
                <button type="submit">Create room</button>
            </div>
        </form>
    </section>

    <hr>

    <section>
        <h2>Available rooms</h2>

        <?php if (empty($rooms)): ?>
            <p>No rooms yet. Create the first one!</p>
        <?php else: ?>
            <ul>
                <?php foreach ($rooms as $room): ?>
                    <li>
                        <h3><?= htmlspecialchars($room['title'], ENT_QUOTES, 'UTF-8') ?></h3>
                        <p><?= nl2br(htmlspecialchars($room['description'], ENT_QUOTES, 'UTF-8')) ?></p>
                        <small>
                            Created at:
                            <?= htmlspecialchars($room['created_at'], ENT_QUOTES, 'UTF-8') ?>
                        </small>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </section>
</body>
</html>