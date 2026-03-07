@extends('layouts.app')

@section('title', 'Tasks')

@section('content')
<div class="container">
    <div id="alertContainer" class="alert"></div>

    <div class="profile-container">
        <div class="profile-header" style="display: flex; justify-content: space-between; align-items: center;">
            <h2>My Tasks</h2>
            <div style="display: flex; gap: 0.5rem;">
                <a href="/tasks/recycle-bin" class="btn btn-secondary" style="width: auto;">Recycle Bin</a>
                <a href="/tasks/create" class="btn btn-primary" style="width: auto;">Create New Task</a>
            </div>
        </div>

        <div style="margin-bottom: 1.5rem; display: flex; gap: 1rem;">
            <button class="btn btn-secondary" data-filter="all">All Tasks</button>
            <button class="btn btn-secondary" data-filter="pending">Pending</button>
            <button class="btn btn-secondary" data-filter="completed">Completed</button>
        </div>

        <div id="loadingContainer" style="text-align: center; padding: 3rem;">
            <div class="loading" style="margin: 0 auto;"></div>
            <p style="margin-top: 1rem; color: #6B7280;">Loading tasks...</p>
        </div>

        <div id="tasksContainer" style="display: none;">
            <div id="tasksList"></div>
            <div id="paginationContainer" style="margin-top: 2rem;"></div>
            <div id="emptyState" style="text-align: center; padding: 3rem; color: #6B7280; display: none;">
                <p style="font-size: 1.1rem; margin-bottom: 1rem;">No tasks found</p>
                <p>Create your first task to get started</p>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
let currentFilter = 'all';
let currentPage = 1;
let paginationData = null;

document.addEventListener('DOMContentLoaded', async function() {
    const token = localStorage.getItem('auth_token');
    
    if (!token) {
        window.location.href = '/login';
        return;
    }

    await loadTasks(currentFilter, currentPage);

    const filterButtons = document.querySelectorAll('[data-filter]');
    filterButtons.forEach(button => {
        button.addEventListener('click', async function() {
            filterButtons.forEach(btn => btn.classList.remove('btn-primary'));
            filterButtons.forEach(btn => btn.classList.add('btn-secondary'));
            this.classList.remove('btn-secondary');
            this.classList.add('btn-primary');
            
            currentFilter = this.dataset.filter;
            currentPage = 1;
            await loadTasks(currentFilter, currentPage);
        });
    });
});

async function loadTasks(filter, page) {
    const token = localStorage.getItem('auth_token');
    
    document.getElementById('loadingContainer').style.display = 'block';
    document.getElementById('tasksContainer').style.display = 'none';

    try {
        let url = `/api/tasks?page=` + page;
        if (filter !== 'all') {
            url += `&status=` + filter;
        }

        const response = await fetch(url, {
            headers: {
                'Authorization': `Bearer ` + token,
                'Accept': 'application/json'
            }
        });

        if (response.ok) {
            const data = await response.json();
            paginationData = data.pagination;
            displayTasks(data.data);
            displayPagination(paginationData);
            document.getElementById('loadingContainer').style.display = 'none';
            document.getElementById('tasksContainer').style.display = 'block';
        } else {
            throw new Error('Failed to load tasks');
        }
    } catch (error) {
        showAlert('error', 'Failed to load tasks. Please try again.');
        document.getElementById('loadingContainer').style.display = 'none';
    }
}

function displayTasks(tasks) {
    const tasksList = document.getElementById('tasksList');
    const emptyState = document.getElementById('emptyState');
    const paginationContainer = document.getElementById('paginationContainer');

    if (tasks.length === 0) {
        tasksList.innerHTML = '';
        paginationContainer.innerHTML = '';
        emptyState.style.display = 'block';
        return;
    }

    emptyState.style.display = 'none';
    tasksList.innerHTML = tasks.map(task => createTaskCard(task)).join('');

    attachTaskEventListeners();
}

function displayPagination(pagination) {
    const container = document.getElementById('paginationContainer');
    
    if (!pagination || pagination.last_page <= 1) {
        container.innerHTML = '';
        return;
    }

    const currentPage = pagination.current_page;
    const lastPage = pagination.last_page;
    const from = pagination.from || 0;
    const to = pagination.to || 0;
    const total = pagination.total;

    let html = '<div style="display: flex; justify-content: space-between; align-items: center; padding: 1rem; background: var(--background); border-radius: 8px;">';
    html += '<div style="color: #6B7280; font-size: 0.875rem;">Showing ' + from + ' to ' + to + ' of ' + total + ' tasks</div>';
    html += '<div style="display: flex; gap: 0.5rem;">';

    if (currentPage > 1) {
        html += '<button class="btn btn-secondary" onclick="changePage(' + (currentPage - 1) + ')" style="padding: 0.5rem 1rem; width: auto;">Previous</button>';
    }

    const maxVisiblePages = 5;
    let startPage = Math.max(1, currentPage - Math.floor(maxVisiblePages / 2));
    let endPage = Math.min(lastPage, startPage + maxVisiblePages - 1);

    if (endPage - startPage < maxVisiblePages - 1) {
        startPage = Math.max(1, endPage - maxVisiblePages + 1);
    }

    if (startPage > 1) {
        html += '<button class="btn btn-secondary" onclick="changePage(1)" style="padding: 0.5rem 1rem; width: auto;">1</button>';
        if (startPage > 2) {
            html += '<span style="padding: 0.5rem; color: #6B7280;">...</span>';
        }
    }

    for (let i = startPage; i <= endPage; i++) {
        const btnClass = i === currentPage ? 'btn btn-primary' : 'btn btn-secondary';
        html += '<button class="' + btnClass + '" onclick="changePage(' + i + ')" style="padding: 0.5rem 1rem; width: auto;">' + i + '</button>';
    }

    if (endPage < lastPage) {
        if (endPage < lastPage - 1) {
            html += '<span style="padding: 0.5rem; color: #6B7280;">...</span>';
        }
        html += '<button class="btn btn-secondary" onclick="changePage(' + lastPage + ')" style="padding: 0.5rem 1rem; width: auto;">' + lastPage + '</button>';
    }

    if (currentPage < lastPage) {
        html += '<button class="btn btn-secondary" onclick="changePage(' + (currentPage + 1) + ')" style="padding: 0.5rem 1rem; width: auto;">Next</button>';
    }

    html += '</div></div>';

    container.innerHTML = html;
}

async function changePage(page) {
    currentPage = page;
    await loadTasks(currentFilter, currentPage);
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function createTaskCard(task) {
    const dueDate = task.due_date ? new Date(task.due_date).toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric'
    }) : 'No due date';

    const statusColor = task.status === 'completed' ? 'var(--success)' : 'var(--primary)';
    const isOverdue = task.due_date && new Date(task.due_date) < new Date() && task.status === 'pending';

    let html = '<div class="info-row" style="display: block; margin-bottom: 1rem;">';
    html += '<div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 0.5rem;">';
    html += '<div style="flex: 1;">';
    html += '<h3 style="font-size: 1.1rem; font-weight: 600; color: var(--text); margin-bottom: 0.25rem;">' + escapeHtml(task.title) + '</h3>';
    
    if (task.description) {
        html += '<p style="color: #6B7280; font-size: 0.95rem; margin-bottom: 0.5rem;">' + escapeHtml(task.description) + '</p>';
    }
    
    html += '<div style="display: flex; gap: 1rem; font-size: 0.875rem; color: #6B7280;">';
    html += '<span style="color: ' + statusColor + '; font-weight: 600;">' + task.status.charAt(0).toUpperCase() + task.status.slice(1) + '</span>';
    html += '<span ' + (isOverdue ? 'style="color: var(--danger); font-weight: 600;"' : '') + '>' + dueDate + '</span>';
    html += '</div></div>';
    
    html += '<div style="display: flex; gap: 0.5rem;">';
    
    if (task.status === 'pending') {
        html += '<button class="btn btn-secondary" data-action="complete" data-id="' + task.id + '" style="padding: 0.5rem 1rem; font-size: 0.875rem; width: auto;">Complete</button>';
    } else {
        html += '<button class="btn btn-secondary" data-action="pending" data-id="' + task.id + '" style="padding: 0.5rem 1rem; font-size: 0.875rem; width: auto;">Reopen</button>';
    }
    
    html += '<a href="/tasks/' + task.id + '" class="btn btn-primary" style="padding: 0.5rem 1rem; font-size: 0.875rem; width: auto; text-decoration: none;">View</a>';
    html += '<a href="/tasks/' + task.id + '/edit" class="btn btn-secondary" style="padding: 0.5rem 1rem; font-size: 0.875rem; width: auto; text-decoration: none;">Edit</a>';
    html += '<button class="btn btn-danger" data-action="delete" data-id="' + task.id + '" style="padding: 0.5rem 1rem; font-size: 0.875rem; width: auto;">Delete</button>';
    html += '</div></div></div>';
    
    return html;
}

function attachTaskEventListeners() {
    document.querySelectorAll('[data-action="complete"]').forEach(btn => {
        btn.addEventListener('click', () => updateTaskStatus(btn.dataset.id, 'complete'));
    });

    document.querySelectorAll('[data-action="pending"]').forEach(btn => {
        btn.addEventListener('click', () => updateTaskStatus(btn.dataset.id, 'pending'));
    });

    document.querySelectorAll('[data-action="delete"]').forEach(btn => {
        btn.addEventListener('click', () => deleteTask(btn.dataset.id));
    });
}

async function updateTaskStatus(taskId, action) {
    const token = localStorage.getItem('auth_token');
    
    try {
        const response = await fetch(`/api/tasks/` + taskId + `/` + action, {
            method: 'PATCH',
            headers: {
                'Authorization': `Bearer ` + token,
                'Accept': 'application/json'
            }
        });

        if (response.ok) {
            showAlert('success', 'Task updated successfully');
            await loadTasks(currentFilter, currentPage);
        } else {
            showAlert('error', 'Failed to update task');
        }
    } catch (error) {
        showAlert('error', 'An error occurred');
    }
}

async function deleteTask(taskId) {
    if (!confirm('Are you sure you want to delete this task?')) return;

    const token = localStorage.getItem('auth_token');
    
    try {
        const response = await fetch(`/api/tasks/` + taskId, {
            method: 'DELETE',
            headers: {
                'Authorization': `Bearer ` + token,
                'Accept': 'application/json'
            }
        });

        if (response.ok) {
            showAlert('success', 'Task deleted successfully');
            await loadTasks(currentFilter, currentPage);
        } else {
            showAlert('error', 'Failed to delete task');
        }
    } catch (error) {
        showAlert('error', 'An error occurred');
    }
}

function showAlert(type, message) {
    const alertContainer = document.getElementById('alertContainer');
    alertContainer.className = `alert alert-` + type + ` active`;
    alertContainer.textContent = message;
    
    setTimeout(() => {
        alertContainer.classList.remove('active');
    }, 5000);
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
</script>
@endsection
@endsection
