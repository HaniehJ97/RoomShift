# Docker template for PHP projects
This repository provides a starting template for PHP application development.

It contains:
* NGINX webserver
* PHP FastCGI Process Manager with PDO MySQL support
* MariaDB (GPL MySQL fork)
* PHPMyAdmin
* Composer
* Composer package [nikic/fast-route](https://github.com/nikic/FastRoute) for routing

## Setup

1. Install Docker Desktop on Windows or Mac, or Docker Engine on Linux.
1. Clone the project

## Usage

In a terminal, from the cloned project folder, run:
```bash
docker compose up
```

### Composer Autoload

This template is configured to use Composer for PSR-4 autoloading:

- Namespace `App\\` is mapped to `app/src/`.

To install dependencies and generate the autoloader, run:

```bash
docker compose run --rm php composer install
```

If you add new classes or change namespaces, regenerate the autoloader:

```bash
docker compose run --rm php composer dump-autoload
```

Example usage is wired in `app/public/index.php` and a sample class exists at `app/src/hello.php`.

### NGINX

NGINX will now serve files in the app/public folder.

Go to [http://localhost/hello.php](http://localhost/hello.php). You should see a hello world message.

### PHPMyAdmin

PHPMyAdmin provides basic database administration. It is accessible at [localhost:8080](localhost:8080).

Credentials are defined in `docker-compose.yml`. They are: developer/secret123


### Stopping the docker container

If you want to stop the containers, press Ctrl+C. 

Or run:
```bash
docker compose down
```

# RoomShift - Digital Escape Room Platform

## Course Information
- **Course:** Web Development 1 (PHP)
- **Student:** Hanieh Jafari
- **Student ID:** 732370
- **Email:** 732370@student.inholland.nl

## Quick Start
1. Extract the submitted ZIP file
2. Run: `docker-compose up`
3. Access: http://localhost
4. Login with:
   - Admin: `admin@roomshift.com` / `password123`
   - Or register a new account as a user(player)

## Project Overview
RoomShift lets users create and play digital escape rooms. Players can browse and play rooms, while Admins can create, edit, and manage rooms using a visual editor, and can manage users.

## Database Setup
A database export (`developmentdb.sql`) is included in the root folder. The database is automatically imported when Docker starts. You can also import it manually via phpMyAdmin at http://localhost:8080.

## Docker Setup
This project uses the same Docker setup from class:

**Credentials:**
- PHPMyAdmin: `developer` / `secret123`
- Root database: `root` / `secret123`


## MVC Architecture & Patterns
I followed the MVC pattern with these enhancements:

### Repository Pattern
- **Location:** `app/src/Repositories/`
- **Why:** Abstracts database operations, makes testing easier
- **Example:** `RoomRepository.php` handles all room database queries

### Service Layer with Interfaces
- **Location:** `app/src/Services/`
- **Why:** Separates business logic from controllers
- **Example:** `IRoomService.php` (interface) and `RoomService.php` (implementation)

### Dependency Injection
- **Location:** `app/public/index.php`
- **Why:** Makes classes testable and decoupled
- **Example:** Controllers receive services via constructor

### ViewModels
- **Location:** `app/src/ViewModels/`
- **Why:** Type-safe data for views
- **Example:** `RoomsViewModel.php` passes room data to views

## Security Features
1. **Password Hashing:** Uses PHP's `password_hash()` with bcrypt
2. **CSRF Protection:** Tokens on all forms (see `header.php` meta tag)
3. **Prepared Statements:** PDO used everywhere in repositories
4. **Session Security:** HTTP-only cookies, session regeneration on login

## AJAX Implementation
- **File:** `public/assets/js/rooms.js`
- **Feature:** Room details load in modal without page refresh
- **Endpoint:** `ApiController.php` handles `/api/rooms/{id}` requests

## Game Mechanics
The game (`public/assets/js/game.js`) uses:
- Grid-based movement
- Collision detection
- Visual feedback for game events
- Keyboard and button controls
- Web Audio API Implementation ("Game Effects: For the Web Audio API sound effects and particle animations in .js files I received assistance from online resources and my sister (Front-end Deveoloper).)

## WCAG Compliance Efforts
From Lecture 4 requirements, I implemented:

1. **Semantic HTML:** Used `<nav>`, `<main>`, proper headings
2. **Form Labels:** All inputs have `<label>` with `for` attributes
3. **Color Contrast:** Bootstrap's default colors meet AA standards
4. **Keyboard Navigation:** Game fully playable with keyboard
5. **Responsive Design:** Works on mobile, tablet, desktop

**Areas for improvement:** Could add skip-to-content links and more ARIA labels.

## GDPR Considerations
From Lecture 4 requirements:

1. **Minimal Data:** Only collect email, name, and password
2. **Secure Storage:** Passwords hashed, sessions encrypted
3. **Transparency:** Clear what data is collected (registration form)
4. **Session Cleanup:** Automatic expiry, no permanent tracking

**Note:** No analytics, cookies only for session management.

## Special Notes for Grading

### Code to Review
1. **MVC Structure:** Check `app/src/` folder organization
2. **Repository Pattern:** `RoomRepository.php` and `UserRepository.php`
3. **Service Interfaces:** All services have interfaces
4. **Game Logic:** `game.js` - pure JavaScript, no libraries
5. **AJAX:** `ApiController.php` and `rooms.js`

### Framework Enhancements
1. **Base Repository:** `app/src/Framework/Repository.php` - shared PDO connection
2. **Flash Messages:** Session-based success/error messages
3. **Route Parameters:** FastRoute with typed parameters
4. **View Partials:** Reusable header/footer in `views/partials/`

### Database Notes
- Export included: `developmentdb.sql`
- Has test data: 3 rooms, 2 users (admin + test player)
- JSON column used for `level_config` in rooms table

## File Structure

roomshift/
├── app/ # Application code
│ ├── src/ # PHP classes (MVC)
│ └── views/ # Templates
├── public/ # Web root
│ ├── assets/ # CSS, JS, images
│ └── index.php # Entry point
├── docker-compose.yml # Docker setup
├── roomshift_database.sql # Database export
└── README.md # This file


---

*This project was developed individually for Web Development 1. All code is my own work but I got help for javascript*