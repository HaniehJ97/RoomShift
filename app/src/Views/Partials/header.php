<?php

?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RoomShift – Escape Rooms</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <header class="topbar">
        <div class="logo">RoomShift</div>
        <nav class="nav-links">
            <a href="/">Home</a>
            <a href="/rooms">Rooms</a>
            <a href="#">Play</a>
            
            <?php if (isset($_SESSION['user_id'])): ?>
                <!-- Logged in user -->
                <div class="auth-status">
                    <div class="user-info">
                        <span>Welcome, <?= htmlspecialchars($_SESSION['user_name'] ?? 'User', ENT_QUOTES, 'UTF-8') ?></span>
                        <?php if (isset($_SESSION['user_role'])): ?>
                            <span class="user-role-badge <?= $_SESSION['user_role'] === 'admin' ? 'admin-badge' : '' ?>">
                                <?= htmlspecialchars(ucfirst($_SESSION['user_role']), ENT_QUOTES, 'UTF-8') ?>
                            </span>
                        <?php endif; ?>
                    </div>
                    
                    <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                        <a href="/admin" class="btn btn-sm btn-outline-light">Admin Panel</a>
                    <?php endif; ?>
                    
                    <a href="/logout" class="btn btn-sm btn-outline-light">Logout</a>
                </div>
            <?php else: ?>
                <!-- Not logged in -->
                <div class="auth-status">
                    <a href="/login" class="btn btn-sm btn-outline-light">Login</a>
                    <a href="/register" class="btn btn-sm btn-primary">Register</a>
                </div>
            <?php endif; ?>
        </nav>
    </header>

    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert-container">
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($_SESSION['error_message'], ENT_QUOTES, 'UTF-8') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                <?php unset($_SESSION['error_message']); ?>
            </div>
        </div>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert-container">
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($_SESSION['success_message'], ENT_QUOTES, 'UTF-8') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                <?php unset($_SESSION['success_message']); ?>
            </div>
        </div>
    <?php endif; ?>

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