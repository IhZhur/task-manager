# 🗂 Task Manager (Laravel + jQuery)


## English Version

**Task Manager** is a modern single-page task management application built using **Laravel** and **jQuery**. It demonstrates AJAX-based CRUD operations, pagination, inline editing, drag-and-drop reordering, and toast notifications.

### ✅ Features

- Add, edit, delete tasks via AJAX
- Description field for each task
- Inline editing of titles and descriptions
- Bootstrap 5 interface with responsive layout
- Filtering: All / Completed / Active
- Pagination via `?page=1` and `paginate()`
- Drag-and-drop sorting (jQuery UI)
- Toast notifications using Bootstrap
- CSRF protection for all requests

### 🚀 Tech Stack

- **Backend**: Laravel 10, MySQL
- **Frontend**: jQuery, Bootstrap 5, Blade, jQuery UI
- **API**: RESTful JSON endpoints (`routes/api.php`)

### 📥 Installation

```bash
git clone https://github.com/IhZhur/task-manager.git
cd task-manager
composer install
npm install
cp .env.example .env
php artisan key:generate
# Setup DB credentials in .env
php artisan migrate
php artisan serve
```

### 📁 Project Structure

| Path                                        | Description                      |
|---------------------------------------------|----------------------------------|
| `resources/views/app.blade.php`             | Main HTML + JS frontend template |
| `app/Http/Controllers/TaskController.php`   | REST API logic for tasks         |
| `routes/api.php`                            | Laravel API routes               |
| `app/Models/Task.php`                       | Eloquent model for tasks table   |

### 🚧 Roadmap

- [ ] Tags and categorization
- [ ] Subtasks and task hierarchy
- [ ] User authentication
- [ ] Dark mode support
- [ ] Search & sort by date
- [ ] Export to CSV/PDF


### 📝 License

Licensed under the MIT License. See `LICENSE` for details.

---

## Русская версия

**Task Manager** — это современное одностраничное приложение на **Laravel** и **jQuery** для управления задачами. Оно реализует операции CRUD через AJAX, пагинацию, редактирование на лету, drag-and-drop сортировку и всплывающие уведомления.

### ✅ Возможности

- Добавление, редактирование и удаление задач через AJAX
- Поле описания для каждой задачи
- Инлайн-редактирование заголовков и описаний
- Интерфейс на Bootstrap 5 с адаптивной версткой
- Фильтрация: Все / Выполненные / Активные
- Пагинация через `?page=1` и `paginate()`
- Сортировка drag-and-drop (jQuery UI)
- Уведомления Toast через Bootstrap
- CSRF-защита для всех запросов

### 🚀 Стек технологий

- **Backend**: Laravel 10, MySQL
- **Frontend**: jQuery, Bootstrap 5, Blade, jQuery UI
- **API**: RESTful JSON API (`routes/api.php`)

### 📥 Установка

```bash
git clone https://github.com/IhZhur/task-manager.git
cd task-manager
composer install
npm install
cp .env.example .env
php artisan key:generate
# Укажите данные подключения к БД в .env
php artisan migrate
php artisan serve
```

### 📁 Структура проекта

| Path                                        | Description                      |
|---------------------------------------------|----------------------------------|
| `resources/views/app.blade.php`             | Основной HTML + JS               |
| `app/Http/Controllers/TaskController.php`   | API-контроллер                   |
| `routes/api.php`                            | API-маршруты                     |
| `app/Models/Task.php`                       | Eloquent для таблицы задач       |

### 🚧 Дорожная карта


- [ ] Категории и теги
- [ ] Подзадачи и иерархия
- [ ] Аутентификация пользователей
- [ ] Поддержка тёмной темы
- [ ] Поиск и сортировка по дате
- [ ] Экспорт задач в CSV/PDF

### 📝 Лицензия

Проект распространяется по лицензии MIT. См. файл `LICENSE`.

---

**Author:** [IhZhur](https://github.com/IhZhur)  
**Repository:** [task-manager](https://github.com/IhZhur/task-manager)
