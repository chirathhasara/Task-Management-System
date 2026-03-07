@extends('layouts.app')

@section('title', 'Create Task')

@section('content')
<div class="container">
    <div class="profile-container" style="max-width: 600px; margin: 2rem auto;">
        <div class="profile-header">
            <h2>Create New Task</h2>
        </div>

        <div id="alertContainer" class="alert"></div>

        <form id="createTaskForm">
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
                    Create Task
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
document.addEventListener('DOMContentLoaded', function() {
    const token = localStorage.getItem('auth_token');
    
    if (!token) {
        window.location.href = '/login';
        return;
    }

    const today = new Date().toISOString().split('T')[0];
    document.getElementById('due_date').setAttribute('min', today);

    const form = document.getElementById('createTaskForm');
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        clearErrors();
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.textContent;
        
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="loading"></span> Creating...';
        
        const formData = {
            title: form.querySelector('[name="title"]').value,
            description: form.querySelector('[name="description"]').value,
            status: form.querySelector('[name="status"]').value,
            due_date: form.querySelector('[name="due_date"]').value || null,
        };

        try {
            const response = await fetch('/api/tasks', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${token}`
                },
                body: JSON.stringify(formData)
            });

            const data = await response.json();

            if (response.ok && data.success) {
                showAlert('success', 'Task created successfully! Redirecting...');
                
                setTimeout(() => {
                    window.location.href = '/tasks';
                }, 1000);
            } else {
                showAlert('error', data.message || 'Failed to create task.');
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
