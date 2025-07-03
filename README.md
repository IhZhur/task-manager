# Task Manager

A modern single-page task manager built with Laravel 12, PHP 8.2+, jQuery, and Bootstrap 5. Features AJAX CRUD, drag-and-drop reordering, inline editing, and pagination. No SPA frameworks—just Laravel API, Blade, and classic jQuery.

---

## Features

* Create, update, delete tasks via AJAX
* Task description field
* Inline editing for title and description
* Bootstrap 5 responsive interface
* Filter: All / Completed / Active
* Pagination (`?page=1`), server-side via Laravel paginate
* Drag-and-drop sorting (jQuery UI)
* Toast notifications (Bootstrap)
* CSRF protection for all AJAX requests

---

## Requirements

* PHP 8.2+
* Composer
* Node.js & npm
* MySQL (or SQLite)

---

## Getting Started

1. **Clone the repository:**

   ```bash
   git clone https://github.com/IhZhur/task-manager.git
   cd task-manager
   ```
2. **Install PHP dependencies:**

   ```bash
   composer install
   ```
3. **Install frontend dependencies:**

   ```bash
   npm install
   ```
4. **Copy env and generate key:**

   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
5. **Configure database in ********`.env`********:**
   (Example for MySQL)

   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=task_manager
   DB_USERNAME=root
   DB_PASSWORD=
   ```

   (Example for SQLite)

   ```env
   DB_CONNECTION=sqlite
   DB_DATABASE=/absolute/path/to/database.sqlite
   ```
6. **Run migrations:**

   ```bash
   php artisan migrate
   ```
7. **Build assets:**

   ```bash
   npm run build       # or npm run dev for watch mode
   ```
8. **Start the server:**

   ```bash
   php artisan serve
   ```

   Visit [http://localhost:8000](http://localhost:8000)

---

## API Overview

* `GET    /api/tasks`        — List tasks (with pagination)
* `POST   /api/tasks`        — Create a new task
* `GET    /api/tasks/{id}`   — Get task by ID
* `PUT    /api/tasks/{id}`   — Update task
* `DELETE /api/tasks/{id}`   — Delete task
* `PUT    /api/tasks/sort`   — Save new task order (drag-and-drop)

All API endpoints return JSON. CSRF tokens are required for mutations.

---

## Project Structure

* `app/Http/Controllers/TaskController.php` — All task logic (CRUD, sorting)
* `routes/api.php` — API routes
* `resources/views/app.blade.php` — Main interface (Blade)
* `public/` — Entry point and assets
* `config/` — Laravel config
* `database/` — Migrations

---

## Testing

Add your own tests to `tests/` if needed. Basic test skeleton provided.

---

## Author

IhZhur

---

##
