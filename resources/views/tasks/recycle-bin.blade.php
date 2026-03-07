@extends('layouts.app')

@section('title', 'Recycle Bin')

@section('content')
<div class="container">
    <div id="alertContainer" class="alert"></div>

    <div class="profile-container">
        <div class="profile-header" style="display: flex; justify-content: space-between; align-items: center;">
            <h2>Recycle Bin</h2>
            <a href="/tasks" class="btn btn-secondary" style="width: auto;">Back to Tasks</a>
        </div>

        <div style="background: #FEF3C7; border: 1px solid #FCD34D; border-radius: 8px; padding: 1rem; margin-bottom: 1.5rem; color: #92400E;">
            <strong>Note:</strong> Deleted tasks are stored here temporarily. You can restore them or permanently delete them.
        </div>

        <div id="loadingContainer" style="text-align: center; padding: 3rem;">
            <div class="loading" style="margin: 0 auto;"></div>
            <p style="margin-top: 1rem; color: #6B7280;">Loading deleted tasks...</p>
        </div>

        <div id="tasksContainer" style="display: none;">
            <div id="tasksList"></div>
            <div id="paginationContainer" style="margin-top: 2rem;"></div>
            <div id="emptyState" style="text-align: center; padding: 3rem; color: #6B7280; display: none;">
                <p style="font-size: 1.1rem; margin-bottom: 1rem;">Recycle bin is empty</p>
                <p>No deleted tasks found</p>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
let currentPage = 1;
let paginationData = null;

document.addEventListener('DOMContentLoaded', async function() {
    const token = localStorage.getItem('auth_token');
    
    if (!token) {
        window.location.href = '/login';
        return;
    }

    await loadTrashedTasks(currentPage);
});

async function loadTrashedTasks(page) {
    const token = localStorage.getItem('auth_token');
    
    document.getElementById('loadingContainer').style.display = 'block';
    document.getElementById('tasksContainer').style.display = 'none';

    try {
        const response = await fetch(`/api/tasks/trashed?page=` + page, {
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
            throw new Error('Failed to load deleted tasks');
        }
    } catch (error) {
        showAlert('error', 'Failed to load deleted tasks. Please try again.');
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
    html += '<div style="color: #6B7280; font-size: 0.875rem;">Showing ' + from + ' to ' + to + ' of ' + total + ' deleted tasks</div>';
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
    await loadTrashedTasks(currentPage);
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function createTaskCard(task) {
    const dueDate = task.due_date ? new Date(task.due_date).toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric'
    }) : 'No due date';

    const deletedDate = task.deleted_at ? new Date(task.deleted_at).toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    }) : '';

    const statusColor = task.status === 'completed' ? 'var(--success)' : 'var(--primary)';

    let html = '<div class="info-row" style="display: block; margin-bottom: 1rem; opacity: 0.8;">';
    html += '<div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 0.5rem;">';
    html += '<div style="flex: 1;">';
    html += '<h3 style="font-size: 1.1rem; font-weight: 600; color: var(--text); margin-bottom: 0.25rem;">' + escapeHtml(task.title) + '</h3>';
    
    if (task.description) {
        html += '<p style="color: #6B7280; font-size: 0.95rem; margin-bottom: 0.5rem;">' + escapeHtml(task.description) + '</p>';
    }
    
    html += '<div style="display: flex; gap: 1rem; font-size: 0.875rem; color: #6B7280;">';
    html += '<span style="color: ' + statusColor + '; font-weight: 600;">' + task.status.charAt(0).toUpperCase() + task.status.slice(1) + '</span>';
    html += '<span>Due: ' + dueDate + '</span>';
    html += '<span style="color: var(--danger);">Deleted: ' + deletedDate + '</span>';
    html += '</div></div>';
    
    html += '<div style="display: flex; gap: 0.5rem;">';
    html += '<button class="btn btn-primary" data-action="restore" data-id="' + task.id + '" style="padding: 0.5rem 1rem; font-size: 0.875rem; width: auto;">Restore</button>';
    html += '<button class="btn btn-danger" data-action="permanent-delete" data-id="' + task.id + '" style="padding: 0.5rem 1rem; font-size: 0.875rem; width: auto;">Delete Forever</button>';
    html += '</div></div></div>';
    
    return html;
}

function attachTaskEventListeners() {
    document.querySelectorAll('[data-action="restore"]').forEach(btn => {
        btn.addEventListener('click', () => restoreTask(btn.dataset.id));
    });

    document.querySelectorAll('[data-action="permanent-delete"]').forEach(btn => {
        btn.addEventListener('click', () => permanentlyDeleteTask(btn.dataset.id));
    });
}

async function restoreTask(taskId) {
    const token = localStorage.getItem('auth_token');
    
    try {
        const response = await fetch(`/api/tasks/` + taskId + `/restore`, {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ` + token,
                'Accept': 'application/json'
            }
        });

        if (response.ok) {
            showAlert('success', 'Task restored successfully');
            await loadTrashedTasks(currentPage);
        } else {
            showAlert('error', 'Failed to restore task');
        }
    } catch (error) {
        showAlert('error', 'An error occurred');
    }
}

async function permanentlyDeleteTask(taskId) {
    if (!confirm('Are you sure you want to permanently delete this task? This action cannot be undone!')) return;

    const token = localStorage.getItem('auth_token');
    
    try {
        const response = await fetch(`/api/tasks/` + taskId + `/force`, {
            method: 'DELETE',
            headers: {
                'Authorization': `Bearer ` + token,
                'Accept': 'application/json'
            }
        });

        if (response.ok) {
            showAlert('success', 'Task permanently deleted');
            await loadTrashedTasks(currentPage);
        } else {
            showAlert('error', 'Failed to permanently delete task');
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
