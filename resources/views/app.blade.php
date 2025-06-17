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

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    fetchTasks();

    function fetchTasks() {
        $.get('/api/tasks', function(tasks) {
            $('#task-list').empty();
            tasks.forEach(function(task) {
                $('#task-list').append(`
                    <li class="list-group-item d-flex justify-content-between align-items-center" data-id="${task.id}">
                        <div class="flex-grow-1">
                            <span class="task-title ${task.completed ? 'text-decoration-line-through' : ''}" data-completed="${task.completed}">${task.title}</span>
                            <input type="text" class="form-control d-none task-edit-input" value="${task.title}">
                        </div>
                        <div class="ms-3">
                            <button class="btn btn-sm btn-secondary edit-task me-1">✎</button>
                            <button class="btn btn-sm btn-success me-1 toggle-complete" data-id="${task.id}" data-completed="${task.completed}">✓</button>
                            <button class="btn btn-sm btn-danger delete-task" data-id="${task.id}">✕</button>
                        </div>
                    </li>
                `);
            });
        });
    }

    $('#task-form').on('submit', function (e) {
        e.preventDefault();
        let title = $('#task-title').val();
        if (!title.trim()) return;

        $.post('/api/tasks', { title }, function () {
            $('#task-title').val('');
            fetchTasks();
        });
    });

    $(document).on('click', '.delete-task', function () {
        let id = $(this).data('id');

        $.ajax({
            url: `/api/tasks/${id}`,
            type: 'DELETE',
            success: fetchTasks
        });
    });

    $(document).on('click', '.toggle-complete', function () {
        let id = $(this).data('id');
        let row = $(this).closest('li');
        let title = row.find('.task-title').text().trim();
        let completed = $(this).data('completed') ? 0 : 1;

        $.ajax({
            url: `/api/tasks/${id}`,
            type: 'PUT',
            data: { title, completed },
            success: fetchTasks
        });
    });

    // ✏️ Показать поле для редактирования
    $(document).on('click', '.edit-task', function () {
        let row = $(this).closest('li');
        row.find('.task-title').addClass('d-none');
        row.find('.task-edit-input').removeClass('d-none').focus();
    });

    // 💾 Сохранение по Enter
    $(document).on('keydown', '.task-edit-input', function (e) {
        if (e.key === 'Enter') {
            let row = $(this).closest('li');
            let id = row.data('id');
            let newTitle = $(this).val().trim();
            let completed = row.find('.task-title').data('completed');

            if (!newTitle) return;

            $.ajax({
                url: `/api/tasks/${id}`,
                type: 'PUT',
                data: { title: newTitle, completed },
                success: fetchTasks
            });
        }
    });

});
</script>
</body>
</html>
