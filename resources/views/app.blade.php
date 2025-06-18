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
        fetchTasks(); // обновим список с учётом фильтра
    });

    fetchTasks(); // первая загрузка задач

function fetchTasks() {
    $.get('/api/tasks', function(tasks) {
        $('#task-list').empty();
        const filter = $('#filter-buttons .active').data('filter');

        tasks.forEach(function(task) {
            if (
                (filter === 'completed' && !task.completed) ||
                (filter === 'active' && task.completed)
            ) return;

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
            row.find('.save-task').removeClass('d-none');
            row.find('.edit-task').addClass('d-none');
        });

    // 💾 Сохранение по Enter
    
        $(document).on('click', '.save-task', function () {
            let row = $(this).closest('li');
            let id = row.data('id');
            let newTitle = row.find('.task-edit-input').val().trim();
            let completed = row.find('.task-title').data('completed');

            if (!newTitle) return;

            $.ajax({
                url: `/api/tasks/${id}`,
                type: 'PUT',
                data: { title: newTitle, completed },
                success: fetchTasks
            });
        });
});

</script>
</body>
</html>
