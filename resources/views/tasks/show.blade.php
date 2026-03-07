@extends('layouts.app')

@section('title', 'View Task')

@section('content')
<div class="container">
    <div class="profile-container" style="max-width: 700px; margin: 2rem auto;">
        <div class="profile-header" style="display: flex; justify-content: space-between; align-items: center;">
            <h2>Task Details</h2>
            <a href="/tasks" class="btn btn-secondary" style="width: auto;">Back to Tasks</a>
        </div>

        <div id="alertContainer" class="alert"></div>

        <div id="loadingContainer" style="text-align: center; padding: 3rem;">
            <div class="loading" style="margin: 0 auto;"></div>
            <p style="margin-top: 1rem; color: #6B7280;">Loading task...</p>
        </div>

        <div id="taskDetails" style="display: none;">
            <div style="margin-bottom: 2rem;">
                <div class="info-row">
                    <div style="display: flex; justify-content: space-between; align-items: start;">
                        <div style="flex: 1;">
                            <h3 style="font-size: 1.5rem; font-weight: 700; color: var(--text); margin-bottom: 0.5rem;" id="taskTitle"></h3>
                            <div style="display: flex; gap: 0.5rem; align-items: center; margin-bottom: 1rem;">
                                <span id="statusBadge" style="padding: 0.25rem 0.75rem; border-radius: 6px; font-size: 0.875rem; font-weight: 600;"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="profile-info">
                <div class="info-row">
                    <span class="info-label">Description</span>
                    <span class="info-value" id="taskDescription" style="white-space: pre-wrap;"></span>
                </div>

                <div class="info-row">
                    <span class="info-label">Status</span>
                    <span class="info-value" id="taskStatus"></span>
                </div>

                <div class="info-row">
                    <span class="info-label">Due Date</span>
                    <span class="info-value" id="taskDueDate"></span>
                </div>

                <div class="info-row">
                    <span class="info-label">Created</span>
                    <span class="info-value" id="taskCreated"></span>
                </div>

                <div class="info-row">
                    <span class="info-label">Last Updated</span>
                    <span class="info-value" id="taskUpdated"></span>
                </div>
            </div>

            <div style="margin-top: 2rem; display: flex; gap: 1rem; flex-wrap: wrap;">
                <button id="toggleStatusBtn" class="btn btn-primary" style="flex: 1; min-width: 150px;"></button>
                <a id="editTaskBtn" class="btn btn-secondary" style="flex: 1; min-width: 150px; text-align: center; text-decoration: none;">Edit Task</a>
                <button id="deleteTaskBtn" class="btn btn-danger" style="flex: 1; min-width: 150px;">Delete Task</button>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
let currentTask = null;

document.addEventListener('DOMContentLoaded', async function() {
    const token = localStorage.getItem('auth_token');
    
    if (!token) {
        window.location.href = '/login';
        return;
    }

    const taskId = window.location.pathname.split('/')[2];
    await loadTask(taskId);
});

async function loadTask(taskId) {
    const token = localStorage.getItem('auth_token');

    try {
        const response = await fetch(`/api/tasks/${taskId}`, {
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        });

        if (response.ok) {
            const data = await response.json();
            currentTask = data.data;
            displayTask(currentTask);

            document.getElementById('loadingContainer').style.display = 'none';
            document.getElementById('taskDetails').style.display = 'block';

            setupEventListeners(taskId);
        } else {
            showAlert('error', 'Task not found');
            setTimeout(() => {
                window.location.href = '/tasks';
            }, 2000);
        }
    } catch (error) {
        showAlert('error', 'Failed to load task');
        setTimeout(() => {
            window.location.href = '/tasks';
        }, 2000);
    }
}

function displayTask(task) {
    document.getElementById('taskTitle').textContent = task.title;
    
    document.getElementById('taskDescription').textContent = task.description || 'No description provided';
    
    const statusBadge = document.getElementById('statusBadge');
    const statusText = task.status.charAt(0).toUpperCase() + task.status.slice(1);
    statusBadge.textContent = statusText;
    
    if (task.status === 'completed') {
        statusBadge.style.background = '#DCFCE7';
        statusBadge.style.color = '#166534';
    } else {
        statusBadge.style.background = '#DBEAFE';
        statusBadge.style.color = '#1E40AF';
    }
    
    document.getElementById('taskStatus').textContent = statusText;
    
    if (task.due_date) {
        const dueDate = new Date(task.due_date);
        const formattedDate = dueDate.toLocaleDateString('en-US', {
            month: 'long',
            day: 'numeric',
            year: 'numeric'
        });
        
        const isOverdue = dueDate < new Date() && task.status === 'pending';
        const dueDateElement = document.getElementById('taskDueDate');
        dueDateElement.textContent = formattedDate;
        
        if (isOverdue) {
            dueDateElement.style.color = 'var(--danger)';
            dueDateElement.style.fontWeight = '600';
            dueDateElement.textContent += ' (Overdue)';
        }
    } else {
        document.getElementById('taskDueDate').textContent = 'No due date set';
    }
    
    const createdDate = new Date(task.created_at);
    document.getElementById('taskCreated').textContent = createdDate.toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
    
    const updatedDate = new Date(task.updated_at);
    document.getElementById('taskUpdated').textContent = updatedDate.toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
    
    const toggleBtn = document.getElementById('toggleStatusBtn');
    if (task.status === 'pending') {
        toggleBtn.textContent = 'Mark as Completed';
        toggleBtn.className = 'btn btn-primary';
        toggleBtn.style.background = 'var(--success)';
    } else {
        toggleBtn.textContent = 'Mark as Pending';
        toggleBtn.className = 'btn btn-secondary';
    }
    
    document.getElementById('editTaskBtn').href = `/tasks/${task.id}/edit`;
}

function setupEventListeners(taskId) {
    document.getElementById('toggleStatusBtn').addEventListener('click', async function() {
        const action = currentTask.status === 'pending' ? 'complete' : 'pending';
        await updateTaskStatus(taskId, action);
    });

    document.getElementById('deleteTaskBtn').addEventListener('click', async function() {
        await deleteTask(taskId);
    });
}

async function updateTaskStatus(taskId, action) {
    const token = localStorage.getItem('auth_token');
    const btn = document.getElementById('toggleStatusBtn');
    const originalText = btn.textContent;
    
    btn.disabled = true;
    btn.innerHTML = '<span class="loading"></span> Updating...';
    
    try {
        const response = await fetch(`/api/tasks/${taskId}/${action}`, {
            method: 'PATCH',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        });

        if (response.ok) {
            showAlert('success', 'Task status updated successfully');
            await loadTask(taskId);
        } else {
            showAlert('error', 'Failed to update task status');
            btn.disabled = false;
            btn.textContent = originalText;
        }
    } catch (error) {
        showAlert('error', 'An error occurred');
        btn.disabled = false;
        btn.textContent = originalText;
    }
}

async function deleteTask(taskId) {
    if (!confirm('Are you sure you want to delete this task? This action cannot be undone.')) {
        return;
    }

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
            showAlert('success', 'Task deleted successfully. Redirecting...');
            setTimeout(() => {
                window.location.href = '/tasks';
            }, 1500);
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
    
    if (type === 'success') {
        setTimeout(() => {
            alertContainer.classList.remove('active');
        }, 5000);
    }
}
</script>
@endsection
@endsection
