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
            <div id="emptyState" style="text-align: center; padding: 3rem; color: #6B7280; display: none;">
                <p style="font-size: 1.1rem; margin-bottom: 1rem;">Recycle bin is empty</p>
                <p>No deleted tasks found</p>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', async function() {
    const token = localStorage.getItem('auth_token');
    
    if (!token) {
        window.location.href = '/login';
        return;
    }

    await loadTrashedTasks();
});

async function loadTrashedTasks() {
    const token = localStorage.getItem('auth_token');
    
    document.getElementById('loadingContainer').style.display = 'block';
    document.getElementById('tasksContainer').style.display = 'none';

    try {
        const response = await fetch('/api/tasks/trashed', {
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        });

        if (response.ok) {
            const data = await response.json();
            displayTasks(data.data);
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

    if (tasks.length === 0) {
        tasksList.innerHTML = '';
        emptyState.style.display = 'block';
        return;
    }

    emptyState.style.display = 'none';
    tasksList.innerHTML = tasks.map(task => createTaskCard(task)).join('');

    attachTaskEventListeners();
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

    return `
        <div class="info-row" style="display: block; margin-bottom: 1rem; opacity: 0.8;">
            <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 0.5rem;">
                <div style="flex: 1;">
                    <h3 style="font-size: 1.1rem; font-weight: 600; color: var(--text); margin-bottom: 0.25rem;">
                        ${escapeHtml(task.title)}
                    </h3>
                    ${task.description ? `<p style="color: #6B7280; font-size: 0.95rem; margin-bottom: 0.5rem;">${escapeHtml(task.description)}</p>` : ''}
                    <div style="display: flex; gap: 1rem; font-size: 0.875rem; color: #6B7280;">
                        <span style="color: ${statusColor}; font-weight: 600;">
                            ${task.status.charAt(0).toUpperCase() + task.status.slice(1)}
                        </span>
                        <span>Due: ${dueDate}</span>
                        <span style="color: var(--danger);">Deleted: ${deletedDate}</span>
                    </div>
                </div>
                <div style="display: flex; gap: 0.5rem;">
                    <button class="btn btn-primary" data-action="restore" data-id="${task.id}" style="padding: 0.5rem 1rem; font-size: 0.875rem; width: auto;">Restore</button>
                    <button class="btn btn-danger" data-action="permanent-delete" data-id="${task.id}" style="padding: 0.5rem 1rem; font-size: 0.875rem; width: auto;">Delete Forever</button>
                </div>
            </div>
        </div>
    `;
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
        const response = await fetch(`/api/tasks/${taskId}/restore`, {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        });

        if (response.ok) {
            showAlert('success', 'Task restored successfully');
            await loadTrashedTasks();
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
        const response = await fetch(`/api/tasks/${taskId}/force`, {
            method: 'DELETE',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        });

        if (response.ok) {
            showAlert('success', 'Task permanently deleted');
            await loadTrashedTasks();
        } else {
            showAlert('error', 'Failed to permanently delete task');
        }
    } catch (error) {
        showAlert('error', 'An error occurred');
    }
}

function showAlert(type, message) {
    const alertContainer = document.getElementById('alertContainer');
    alertContainer.className = `alert alert-${type} active`;
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
