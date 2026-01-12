<?php require __DIR__ . '/../Partials/header.php'; ?>

<section class="hero">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <h1 class="display-4">
                    Build & <span class="text-warning">Play</span> Escape Rooms
                </h1>
                <p class="lead">
                    Design your own digital escape adventures and let others try to solve them.
                </p>

                <div class="d-flex gap-3 mt-4">
                    <?php if (isset($_SESSION['user_id']) && in_array($_SESSION['user_role'], ['creator', 'admin'])): ?>
                        <a href="/creator/rooms" class="btn btn-light btn-lg px-4 py-3 fw-semibold">
                        <i class="bi bi-plus-circle me-2"></i>Create Room
                        </a>
                    <?php endif; ?>

                    <a href="/rooms" class="btn btn-outline-light btn-lg">Browse Rooms</a>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card border-accent">
                    <div class="card-body text-center py-5">
                        <div style="font-size: 64px;">🚪</div>
                        <h3 class="mt-3">Immersive Puzzles</h3>
                        <p class="text-gray">Create challenging experiences</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<main class="bg-light py-5">
    <div class="container">

        <?php if (!empty($_SESSION['error_message'])): ?>
            <div class="alert alert-danger">
                <?= htmlspecialchars($_SESSION['error_message']) ?>
            </div>
            <?php unset($_SESSION['error_message']); ?>
        <?php endif; ?>

        <?php if (!empty($_SESSION['success_message'])): ?>
            <div class="alert alert-success">
                <?= htmlspecialchars($_SESSION['success_message']) ?>
            </div>
            <?php unset($_SESSION['success_message']); ?>
        <?php endif; ?>

        <div class="card">
            <div class="card-header bg-primary d-flex justify-content-between">
                <span>Available Rooms</span>
                <span><?= count($rooms ?? []) ?> room<?= count($rooms ?? []) === 1 ? '' : 's' ?></span>
            </div>

            <div class="card-body">
                <?php if (empty($rooms)): ?>
                    <p class="text-gray">No rooms available.</p>
                <?php else: ?>
                    <div class="list-group">
                        <?php foreach ($rooms as $room): ?>
                            <div class="list-group-item">
                                <h5><?= htmlspecialchars($room->title) ?></h5>
                                <p><?= htmlspecialchars($room->description) ?></p>

                                <?php if (isset($_SESSION['user_id'])): ?>
                                    <a class="btn btn-outline-primary btn-sm"
                                       href="/rooms/<?= (int)$room->id ?>/play">
                                        Play
                                    </a>
                                <?php else: ?>
                                    <a class="btn btn-outline-primary btn-sm" href="/login">
                                        Login to play
                                    </a>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

    </div>
</main>

<?php require __DIR__ . '/../Partials/footer.php'; ?>