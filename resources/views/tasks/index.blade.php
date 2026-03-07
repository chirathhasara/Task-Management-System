@extends('layouts.app')

@section('title', 'Tasks')

@section('content')
<div class="container">
    <div id="alertContainer" class="alert"></div>

    <div class="profile-container">
        <div class="profile-header" style="display: flex; justify-content: space-between; align-items: center;">
            <h2>My Tasks</h2>
            <a href="/tasks/create" class="btn btn-primary" style="width: auto;">Create New Task</a>
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
let allTasks = [];

document.addEventListener('DOMContentLoaded', async function() {
    const token = localStorage.getItem('auth_token');
    
    if (!token) {
        window.location.href = '/login';
        return;
    }

    await loadTasks();

    const filterButtons = document.querySelectorAll('[data-filter]');
    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            filterButtons.forEach(btn => btn.classList.remove('btn-primary'));
            filterButtons.forEach(btn => btn.classList.add('btn-secondary'));
            this.classList.remove('btn-secondary');
            this.classList.add('btn-primary');
            
            currentFilter = this.dataset.filter;
            displayTasks();
        });
    });
});

async function loadTasks() {
    const token = localStorage.getItem('auth_token');

    try {
        const response = await fetch('/api/tasks', {
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        });

        if (response.ok) {
            const data = await response.json();
            allTasks = data.data;
            document.getElementById('loadingContainer').style.display = 'none';
            document.getElementById('tasksContainer').style.display = 'block';
            displayTasks();
        } else {
            throw new Error('Failed to load tasks');
        }
    } catch (error) {
        showAlert('error', 'Failed to load tasks. Please try again.');
        document.getElementById('loadingContainer').style.display = 'none';
    }
}

function displayTasks() {
    const tasksList = document.getElementById('tasksList');
    const emptyState = document.getElementById('emptyState');
    
    let filteredTasks = allTasks;
    if (currentFilter !== 'all') {
        filteredTasks = allTasks.filter(task => task.status === currentFilter);
    }

    if (filteredTasks.length === 0) {
        tasksList.innerHTML = '';
        emptyState.style.display = 'block';
        return;
    }

    emptyState.style.display = 'none';
    tasksList.innerHTML = filteredTasks.map(task => createTaskCard(task)).join('');

    attachTaskEventListeners();
}

function createTaskCard(task) {
    const dueDate = task.due_date ? new Date(task.due_date).toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric'
    }) : 'No due date';

    const statusColor = task.status === 'completed' ? 'var(--success)' : 'var(--primary)';
    const isOverdue = task.due_date && new Date(task.due_date) < new Date() && task.status === 'pending';

    return `
        <div class="info-row" style="display: block; margin-bottom: 1rem;">
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
                        <span ${isOverdue ? 'style="color: var(--danger); font-weight: 600;"' : ''}>
                            ${dueDate}
                        </span>
                    </div>
                </div>
                <div style="display: flex; gap: 0.5rem;">
                    ${task.status === 'pending' ? 
                        `<button class="btn btn-secondary" data-action="complete" data-id="${task.id}" style="padding: 0.5rem 1rem; font-size: 0.875rem; width: auto;">Complete</button>` :
                        `<button class="btn btn-secondary" data-action="pending" data-id="${task.id}" style="padding: 0.5rem 1rem; font-size: 0.875rem; width: auto;">Reopen</button>`
                    }
                    <a href="/tasks/${task.id}" class="btn btn-primary" style="padding: 0.5rem 1rem; font-size: 0.875rem; width: auto; text-decoration: none;">View</a>
                    <a href="/tasks/${task.id}/edit" class="btn btn-secondary" style="padding: 0.5rem 1rem; font-size: 0.875rem; width: auto; text-decoration: none;">Edit</a>
                    <button class="btn btn-danger" data-action="delete" data-id="${task.id}" style="padding: 0.5rem 1rem; font-size: 0.875rem; width: auto;">Delete</button>
                </div>
            </div>
        </div>
    `;
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
        const response = await fetch(`/api/tasks/${taskId}/${action}`, {
            method: 'PATCH',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        });

        if (response.ok) {
            showAlert('success', 'Task updated successfully');
            await loadTasks();
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
        const response = await fetch(`/api/tasks/${taskId}`, {
            method: 'DELETE',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        });

        if (response.ok) {
            showAlert('success', 'Task deleted successfully');
            await loadTasks();
        } else {
            showAlert('error', 'Failed to delete task');
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
