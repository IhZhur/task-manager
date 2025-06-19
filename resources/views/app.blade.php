<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Task Manager</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
</head>
<body>
    <div id="toast-container" class="position-fixed bottom-0 end-0 p-3" style="z-index: 11;"></div>

    <div class="container py-5">
        <h1 class="mb-4">Task Manager</h1>
        <div class="btn-group mb-3" role="group" id="filter-buttons">
            <button type="button" class="btn btn-outline-secondary active" data-filter="all">Все</button>
            <button type="button" class="btn btn-outline-secondary" data-filter="completed">Выполненные</button>
            <button type="button" class="btn btn-outline-secondary" data-filter="active">Активные</button>
        </div>
        <form id="task-form" class="mb-3">
            <div class="mb-2">
                <input type="text" id="task-title" class="form-control" placeholder="Enter task title">
            </div>
            <div class="mb-2">
                <textarea id="task-desc" class="form-control" placeholder="Enter description (optional)"></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Add Task</button>
            <div id="task-error" class="text-danger mt-2 d-none">Пожалуйста, введите название задачи.</div>
        </form>
        <div id="task-error" class="text-danger mb-3 d-none">Пожалуйста, введите название задачи.</div>

        <ul class="list-group" id="task-list"></ul>
        <nav>
            <ul class="pagination" id="pagination-controls"></ul>
        </nav>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    $(document).ready(function () {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        function showToast(message, type = 'success') {
            const toast = $(`
                <div class="toast align-items-center text-white bg-${type} border-0 mb-2" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="d-flex">
                        <div class="toast-body">${message}</div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                </div>
            `);
            $('#toast-container').append(toast);
            const bsToast = new bootstrap.Toast(toast[0], { delay: 2500 });
            bsToast.show();
            toast.on('hidden.bs.toast', function () {
                toast.remove();
            });
        }

        let currentPage = 1;

        function fetchTasks(page = 1) {
    $.get(`/api/tasks?page=${page}`, function (data) {
        $('#task-list').empty();
        const filter = $('#filter-buttons .active').data('filter');

        data.data.forEach(function (task) {
            if ((filter === 'completed' && !task.completed) || (filter === 'active' && task.completed)) return;

            $('#task-list').append(`
                <li class="list-group-item d-flex justify-content-between align-items-start flex-column flex-md-row" data-id="${task.id}">
                    <div class="flex-grow-1 w-100">
                        <span class="task-title fw-bold ${task.completed ? 'text-decoration-line-through' : ''}" data-completed="${task.completed}">${task.title}</span>
                        <input type="text" class="form-control d-none task-edit-input mb-2" value="${task.title}">

                        <div class="task-desc">${task.description || ''}</div>
                        <textarea class="form-control d-none task-desc-edit-input" rows="2">${task.description || ''}</textarea>
                    </div>
                    <div class="ms-md-3 mt-3 mt-md-0 d-flex">
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

        renderPagination(data);
    });
}
        function renderPagination(data) {
    let pagination = '';

    if (data.last_page <= 1) {
        $('#pagination-controls').html('');
        return;
    }

    if (data.current_page > 1) {
        pagination += `<li class="page-item"><a class="page-link" href="#" data-page="${data.current_page - 1}">Предыдущая</a></li>`;
    }

    for (let i = 1; i <= data.last_page; i++) {
        pagination += `<li class="page-item ${i === data.current_page ? 'active' : ''}">
            <a class="page-link" href="#" data-page="${i}">${i}</a>
        </li>`;
    }

    if (data.current_page < data.last_page) {
        pagination += `<li class="page-item"><a class="page-link" href="#" data-page="${data.current_page + 1}">Следующая</a></li>`;
    }

    $('#pagination-controls').html(pagination);
    }

        $(document).on('click', '#pagination-controls a', function (e) {
            e.preventDefault();
            const page = $(this).data('page');
            fetchTasks(page);
        });

        function updateTask(row, newTitle, completed, newDescription) {
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
                data: { title: newTitle, completed, description: newDescription },
                success: function () {
                    fetchTasks();
                    showToast('Задача обновлена', 'info');
                    }
                });
            return true;
        }

        $('#filter-buttons').on('click', 'button', function () {
            $('#filter-buttons button').removeClass('active');
            $(this).addClass('active');
            fetchTasks();
        });

        $('#task-form').on('submit', function (e) {
            e.preventDefault();
            const title = $('#task-title').val().trim();
            const description = $('#task-desc').val().trim(); // 🔸 Новое поле

            if (!title) {
                $('#task-title').addClass('is-invalid');
                $('#task-error').removeClass('d-none');
                return;
            }

            $('#task-title').removeClass('is-invalid');
            $('#task-error').addClass('d-none');

            $.post('/api/tasks', { title, description }, function () {
                $('#task-title').val('');
                $('#task-desc').val(''); // 🔸 Очищаем описание
                fetchTasks();
                showToast('Задача добавлена');
            });
        });


        $(document).on('click', '.delete-task', function () {
            const id = $(this).data('id');
            $.ajax({
                url: `/api/tasks/${id}`,
                type: 'DELETE',
                success: function () {
                    fetchTasks();
                    showToast('Задача удалена', 'danger');
                }
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
                success: function () {
                    fetchTasks();
                    showToast(completed ? 'Задача завершена' : 'Задача активна', 'info');
                }
            });
        });

        $(document).on('click', '.edit-task', function () {
            const row = $(this).closest('li');
            row.find('.task-title, .task-desc').addClass('d-none');
            row.find('.task-edit-input, .task-desc-edit-input').removeClass('d-none').first().focus();
            row.find('.save-task').removeClass('d-none');
            row.find('.edit-task').addClass('d-none');
        });

        $(document).on('click', '.save-task', function () {
            const row = $(this).closest('li');
            const newTitle = row.find('.task-edit-input').val().trim();
            const newDescription = row.find('.task-desc-edit-input').val().trim();
            const completed = row.find('.task-title').data('completed');

            if (updateTask(row, newTitle, completed, newDescription)) {
                row.find('.task-edit-input').addClass('d-none');
                row.find('.task-title').removeClass('d-none').text(newTitle);
                row.find('.task-desc-edit-input').addClass('d-none');
                row.find('.task-desc').removeClass('d-none').text(newDescription);
                row.find('.save-task').addClass('d-none');
                row.find('.edit-task').removeClass('d-none');
            }
        });


        $(document).on('keydown', '.task-edit-input, .task-desc-edit-input', function (e) {
            const row = $(this).closest('li');

            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();

                const newTitle = row.find('.task-edit-input').val().trim();
                const newDescription = row.find('.task-desc-edit-input').val().trim();
                const completed = row.find('.task-title').data('completed');

                if (updateTask(row, newTitle, completed, newDescription)) {
                    row.find('.task-edit-input, .task-desc-edit-input').addClass('d-none');
                    row.find('.task-title').removeClass('d-none').text(newTitle);
                    row.find('.task-desc').removeClass('d-none').text(newDescription);
                    row.find('.save-task').addClass('d-none');
                    row.find('.edit-task').removeClass('d-none');
                }
            }

            if (e.key === 'Escape') {
                row.find('.task-edit-input, .task-desc-edit-input').addClass('d-none');
                row.find('.task-title, .task-desc').removeClass('d-none');
                row.find('.save-task').addClass('d-none');
                row.find('.edit-task').removeClass('d-none');
            }
        });


        let blurTimeout;

        $(document).on('blur', '.task-edit-input, .task-desc-edit-input', function () {
            const row = $(this).closest('li');

            clearTimeout(blurTimeout);
            blurTimeout = setTimeout(() => {
                const isAnyInputFocused = row.find('.task-edit-input:focus, .task-desc-edit-input:focus').length > 0;
                if (isAnyInputFocused) return;

                const newTitle = row.find('.task-edit-input').val().trim();
                const newDescription = row.find('.task-desc-edit-input').val().trim();
                const completed = row.find('.task-title').data('completed');

                if (updateTask(row, newTitle, completed, newDescription)) {
                    row.find('.task-edit-input, .task-desc-edit-input').addClass('d-none');
                    row.find('.task-title').removeClass('d-none').text(newTitle);
                    row.find('.task-desc').removeClass('d-none').text(newDescription);
                    row.find('.save-task').addClass('d-none');
                    row.find('.edit-task').removeClass('d-none');
                }
            }, 200); // 200 мс — достаточно, чтобы перейти во второе поле
        });


        $('#task-list').sortable({
            update: function () {
                const positions = $('#task-list li').map(function () {
                    return $(this).data('id');
                }).get();

                $.ajax({
                    url: '/api/tasks/sort',
                    type: 'PUT',
                    data: { positions },
                    success: function () {
                        showToast('Порядок задач обновлён');
                    }
                });
            }
        });

        fetchTasks(1); // загрузка при старте
    });
    </script>
</body>
</html>
