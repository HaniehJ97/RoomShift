<?php

namespace App\Controllers;

use App\Config;
use PDO;

class RoomController
{
    // GET /rooms  → show list of rooms + create form
    public function index(array $vars = []): void
    {
        // 1) Connect to DB (same style as in lecture)
        $connection = new PDO(
            Config::getDsn(),
            Config::DB_USER,
            Config::DB_PASSWORD
        );
        $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // 2) Fetch all rooms
        $query = 'SELECT id, title, description, created_at
                  FROM rooms
                  ORDER BY created_at DESC';

        $statement = $connection->query($query);
        $rooms = $statement->fetchAll(PDO::FETCH_ASSOC);

        // 3) Show view
        require __DIR__ . '/../Views/rooms.php';
    }

    // POST /rooms  → handle form submit and insert new room
    public function store(array $vars = []): void
    {
        // Very basic validation (only what you saw so far)
        $title       = isset($_POST['title']) ? trim($_POST['title']) : '';
        $description = isset($_POST['description']) ? trim($_POST['description']) : '';

        if ($title === '' || $description === '') {
            // In a later week you can do nicer error messages
            echo 'Title and description are required.';
            return;
        }

        // 1) Connect to DB
        $connection = new PDO(
            Config::getDsn(),
            Config::DB_USER,
            Config::DB_PASSWORD
        );
        $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // 2) Insert the new room
        $insertQuery = 'INSERT INTO rooms (title, description)
                        VALUES (:title, :description)';

        $statement = $connection->prepare($insertQuery);
        $statement->bindValue(':title', $title, PDO::PARAM_STR);
        $statement->bindValue(':description', $description, PDO::PARAM_STR);
        $statement->execute();

        // 3) Re-fetch all rooms and show updated page
        $query = 'SELECT id, title, description, created_at
                  FROM rooms
                  ORDER BY created_at DESC';

        $statement = $connection->query($query);
        $rooms = $statement->fetchAll(PDO::FETCH_ASSOC);

        require __DIR__ . '/../Views/rooms.php';
    }
}