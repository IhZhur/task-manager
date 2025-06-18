<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Task Manager</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>
<div class="container py-5">
    <h1 class="mb-4">Task Manager</h1>
    <div class="btn-group mb-3" role="group" id="filter-buttons">
        <button type="button" class="btn btn-outline-secondary active" data-filter="all">Все</button>
        <button type="button" class="btn btn-outline-secondary" data-filter="completed">Выполненные</button>
        <button type="button" class="btn btn-outline-secondary" data-filter="active">Активные</button>
    </div>
    <form id="task-form" class="mb-3 d-flex">
        <input type="text" id="task-title" class="form-control me-2" placeholder="Enter task title">
        <button type="submit" class="btn btn-primary">Add Task</button>
    </form>
    <div id="task-error" class="text-danger mb-3 d-none">Пожалуйста, введите название задачи.</div>

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

    $('#filter-buttons').on('click', 'button', function () {
        $('#filter-buttons button').removeClass('active');
        $(this).addClass('active');
        fetchTasks();
    });

    fetchTasks();

    function fetchTasks() {
        $.get('/api/tasks', function(tasks) {
            $('#task-list').empty();
            const filter = $('#filter-buttons .active').data('filter');

            tasks.forEach(function(task) {
                if ((filter === 'completed' && !task.completed) ||
                    (filter === 'active' && task.completed)) return;

                $('#task-list').append(`
                    <li class="list-group-item d-flex justify-content-between align-items-center" data-id="${task.id}">
                        <div class="flex-grow-1">
                            <span class="task-title ${task.completed ? 'text-decoration-line-through' : ''}" data-completed="${task.completed}">${task.title}</span>
                            <input type="text" class="form-control d-none task-edit-input" value="${task.title}">
                        </div>
                        <div class="ms-3 d-flex">
                            <button class="btn btn-sm btn-primary d-none save-task me-1">
                                <i class="bi bi-save"></i>
                            </button>
                            <button class="btn btn-sm btn-secondary edit-task me-1">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button class="btn btn-sm btn-success me-1 toggle-complete" data-id="${task.id}" data-completed="${task.completed}">
                                <i class="bi bi-check2"></i>
                            </button>
                            <button class="btn btn-sm btn-danger delete-task" data-id="${task.id}">
                                <i class="bi bi-x"></i>
                            </button>
                        </div>
                    </li>
                `);
            });
        });
    }

    function updateTask(row, newTitle, completed) {
        const id = row.data('id');
        if (!newTitle) {
            row.find('.task-edit-input').addClass('is-invalid');
            return false;
        } else {
            row.find('.task-edit-input').removeClass('is-invalid');
        }

        $.ajax({
            url: `/api/tasks/${id}`,
            type: 'PUT',
            data: { title: newTitle, completed },
            success: fetchTasks
        });

        return true;
    }

    $('#task-form').on('submit', function (e) {
        e.preventDefault();
        const title = $('#task-title').val().trim();
        if (!title) {
            $('#task-title').addClass('is-invalid');
            return;
        }
        $('#task-title').removeClass('is-invalid');

        $.post('/api/tasks', { title }, function () {
            $('#task-title').val('');
            fetchTasks();
        });
    });

    $(document).on('click', '.delete-task', function () {
        const id = $(this).data('id');

        $.ajax({
            url: `/api/tasks/${id}`,
            type: 'DELETE',
            success: fetchTasks
        });
    });

    $(document).on('click', '.toggle-complete', function () {
        const id = $(this).data('id');
        const row = $(this).closest('li');
        const title = row.find('.task-title').text().trim();
        const completed = $(this).data('completed') ? 0 : 1;

        $.ajax({
            url: `/api/tasks/${id}`,
            type: 'PUT',
            data: { title, completed },
            success: fetchTasks
        });
    });

    $(document).on('click', '.edit-task', function () {
        const row = $(this).closest('li');
        row.find('.task-title').addClass('d-none');
        row.find('.task-edit-input').removeClass('d-none').focus();
        row.find('.save-task').removeClass('d-none');
        row.find('.edit-task').addClass('d-none');
    });

    $(document).on('click', '.save-task', function () {
        const row = $(this).closest('li');
        const newTitle = row.find('.task-edit-input').val().trim();
        const completed = row.find('.task-title').data('completed');

        if (updateTask(row, newTitle, completed)) {
            row.find('.task-edit-input').addClass('d-none');
            row.find('.task-title').removeClass('d-none');
            row.find('.save-task').addClass('d-none');
            row.find('.edit-task').removeClass('d-none');
        }
    });

    $(document).on('keydown', '.task-edit-input', function (e) {
        const row = $(this).closest('li');
        if (e.key === 'Enter') {
            e.preventDefault();
            const newTitle = $(this).val().trim();
            const completed = row.find('.task-title').data('completed');

            if (updateTask(row, newTitle, completed)) {
                row.find('.task-edit-input').addClass('d-none');
                row.find('.task-title').removeClass('d-none');
                row.find('.save-task').addClass('d-none');
                row.find('.edit-task').removeClass('d-none');
            }
        }
        if (e.key === 'Escape') {
            row.find('.task-edit-input').addClass('d-none');
            row.find('.task-title').removeClass('d-none');
            row.find('.save-task').addClass('d-none');
            row.find('.edit-task').removeClass('d-none');
        }
    });

    $(document).on('blur', '.task-edit-input', function () {
        const row = $(this).closest('li');
        const newTitle = $(this).val().trim();
        const completed = row.find('.task-title').data('completed');

        if (updateTask(row, newTitle, completed)) {
            row.find('.task-edit-input').addClass('d-none');
            row.find('.task-title').removeClass('d-none');
            row.find('.save-task').addClass('d-none');
            row.find('.edit-task').removeClass('d-none');
        }
    });

});
</script>
</body>
</html>
