@extends('layouts.app')

@section('title', 'Edit Task')

@section('content')
<div class="container">
    <div class="profile-container" style="max-width: 600px; margin: 2rem auto;">
        <div class="profile-header">
            <h2>Edit Task</h2>
        </div>

        <div id="alertContainer" class="alert"></div>

        <div id="loadingContainer" style="text-align: center; padding: 3rem;">
            <div class="loading" style="margin: 0 auto;"></div>
            <p style="margin-top: 1rem; color: #6B7280;">Loading task...</p>
        </div>

        <form id="editTaskForm" style="display: none;">
            <input type="hidden" id="taskId" name="taskId">

            <div class="form-group">
                <label for="title" class="form-label">Task Title</label>
                <input 
                    type="text" 
                    id="title" 
                    name="title" 
                    class="form-input" 
                    placeholder="Enter task title"
                    required
                >
            </div>

            <div class="form-group">
                <label for="description" class="form-label">Description</label>
                <textarea 
                    id="description" 
                    name="description" 
                    class="form-input" 
                    placeholder="Enter task description (optional)"
                    rows="4"
                    style="resize: vertical;"
                ></textarea>
            </div>

            <div class="form-group">
                <label for="due_date" class="form-label">Due Date</label>
                <input 
                    type="date" 
                    id="due_date" 
                    name="due_date" 
                    class="form-input"
                >
            </div>

            <div class="form-group">
                <label for="status" class="form-label">Status</label>
                <select id="status" name="status" class="form-input">
                    <option value="pending">Pending</option>
                    <option value="completed">Completed</option>
                </select>
            </div>

            <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                <button type="submit" class="btn btn-primary" style="flex: 1;">
                    Update Task
                </button>
                <a href="/tasks" class="btn btn-secondary" style="flex: 1; text-align: center; text-decoration: none;">
                    Cancel
                </a>
            </div>
        </form>
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

    const taskId = window.location.pathname.split('/')[2];
    await loadTask(taskId);

    const form = document.getElementById('editTaskForm');
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        clearErrors();
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.textContent;
        
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="loading"></span> Updating...';
        
        const formData = {
            title: form.querySelector('[name="title"]').value,
            description: form.querySelector('[name="description"]').value,
            status: form.querySelector('[name="status"]').value,
            due_date: form.querySelector('[name="due_date"]').value || null,
        };

        try {
            const response = await fetch(`/api/tasks/${taskId}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${token}`
                },
                body: JSON.stringify(formData)
            });

            const data = await response.json();

            if (response.ok && data.success) {
                showAlert('success', 'Task updated successfully! Redirecting...');
                
                setTimeout(() => {
                    window.location.href = '/tasks';
                }, 1000);
            } else {
                showAlert('error', data.message || 'Failed to update task.');
                if (data.errors) {
                    displayErrors(data.errors);
                }
            }
        } catch (error) {
            showAlert('error', 'An error occurred. Please try again.');
        } finally {
            submitBtn.disabled = false;
            submitBtn.textContent = originalText;
        }
    });
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
            const task = data.data;

            document.getElementById('taskId').value = task.id;
            document.getElementById('title').value = task.title;
            document.getElementById('description').value = task.description || '';
            document.getElementById('status').value = task.status;
            
            if (task.due_date) {
                document.getElementById('due_date').value = task.due_date;
            }

            document.getElementById('loadingContainer').style.display = 'none';
            document.getElementById('editTaskForm').style.display = 'block';
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

function displayErrors(errors) {
    Object.keys(errors).forEach(field => {
        const input = document.querySelector(`[name="${field}"]`);
        if (input) {
            input.classList.add('error');
            
            const errorDiv = document.createElement('div');
            errorDiv.className = 'error-message active';
            errorDiv.textContent = errors[field][0];
            
            input.parentNode.appendChild(errorDiv);
        }
    });
}

function clearErrors() {
    document.querySelectorAll('.error-message').forEach(el => el.remove());
    document.querySelectorAll('.form-input.error').forEach(el => el.classList.remove('error'));
    
    const alertContainer = document.getElementById('alertContainer');
    if (alertContainer) {
        alertContainer.classList.remove('active');
    }
}
</script>
@endsection
@endsection
