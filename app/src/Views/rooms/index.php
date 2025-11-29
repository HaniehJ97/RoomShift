<?php

use App\ViewModels\RoomsViewModel;

/** @var RoomsViewModel $vm */
/** @var string $error */

?>

<?php require __DIR__ . '/../partials/header.php' ?>

<div class="grid">
    <!-- Left: Create room -->
    <section class="card" id="create-room">
        <h2 class="card-title">Create a new room</h2>

        <?php if (isset($error)): ?>
            <div class="error-message" style="color: red; padding: 10px; border: 1px solid red; margin-bottom: 15px;">
                <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

        <form action="/rooms" method="post">
            <div class="form-group">
                <label for="title">Title</label>
                <input type="text" id="title" name="title" required 
                       value="<?= isset($_POST['title']) ? htmlspecialchars($_POST['title'], ENT_QUOTES, 'UTF-8') : '' ?>">
            </div>

            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" rows="4" required><?= 
                    isset($_POST['description']) ? htmlspecialchars($_POST['description'], ENT_QUOTES, 'UTF-8') : '' 
                ?></textarea>
            </div>

            <button type="submit">Create room</button>
        </form>
    </section>

    <!-- Right: Rooms list -->
    <section class="card">
        <h2 class="card-title">Available rooms</h2>

        <?php if (empty($vm->rooms)): ?>
            <p class="rooms-empty">No rooms yet. Create the first one!</p>
        <?php else: ?>
            <ul class="rooms-list">
                <?php foreach ($vm->rooms as $room): ?>
                    <li class="rooms-item">
                        <div class="room-title">
                            <?= htmlspecialchars($room->title, ENT_QUOTES, 'UTF-8') ?>
                        </div>
                        <p class="room-description">
                            <?= nl2br(htmlspecialchars($room->description, ENT_QUOTES, 'UTF-8')) ?>
                        </p>
                        <div class="room-meta">
                            Created at: <?= htmlspecialchars($room->created_at, ENT_QUOTES, 'UTF-8') ?>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </section>
</div>

<?php require __DIR__ . '/../partials/footer.php' ?>