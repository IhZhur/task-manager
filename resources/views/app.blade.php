<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Task Manager</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container py-5">
    <h1 class="mb-4">Task Manager</h1>

    <form id="task-form" class="mb-3 d-flex">
        <input type="text" id="task-title" class="form-control me-2" placeholder="Enter task title">
        <button type="submit" class="btn btn-primary">Add Task</button>
    </form>

    <ul class="list-group" id="task-list"></ul>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
$(document).ready(function () {
    fetchTasks();

    // Получить все задачи
    function fetchTasks() {
        $.get('/api/tasks', function(tasks) {
            $('#task-list').empty();
            tasks.forEach(function(task) {
                $('#task-list').append(`
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span class="${task.completed ? 'text-decoration-line-through' : ''}">${task.title}</span>
                        <div>
                            <button class="btn btn-sm btn-success me-1 toggle-complete" data-id="${task.id}" data-completed="${task.completed}">
                                ✓
                            </button>
                            <button class="btn btn-sm btn-danger delete-task" data-id="${task.id}">✕</button>
                        </div>
                    </li>
                `);
            });
        });
    }

    // Добавить задачу
    $('#task-form').on('submit', function (e) {
        e.preventDefault();
        let title = $('#task-title').val();
        if (!title.trim()) return;

        $.ajax({
            url: '/api/tasks',
            type: 'POST',
            data: { title },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function () {
                $('#task-title').val('');
                fetchTasks();
            }
        });
    });

    // Удалить задачу
    $(document).on('click', '.delete-task', function () {
        let id = $(this).data('id');

        $.ajax({
            url: `/api/tasks/${id}`,
            type: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: fetchTasks
        });
    });

    // Переключить статус выполнения
    $(document).on('click', '.toggle-complete', function () {
        let id = $(this).data('id');
        let completed = $(this).data('completed') ? 0 : 1;

        $.ajax({
            url: `/api/tasks/${id}`,
            type: 'PUT',
            data: { completed, title: 'updated' }, // placeholder title
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: fetchTasks
        });
    });
});
</script>
</body>
</html>
