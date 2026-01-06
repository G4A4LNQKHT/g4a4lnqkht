/* ============================================
   MAIN.JS - JAVASCRIPT CHÍNH
   ============================================ */

// Global config (set from config.js.php)
// const BASE_URL is already defined in HTML
if (typeof BASE_URL === 'undefined') {
    console.error('BASE_URL not defined. Include config.js.php first.');
}

// ============================================
// FETCH WRAPPER
// ============================================

function apiCall(endpoint, method = 'GET', data = null) {
    const options = {
        method: method,
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    };
    
    if (data) {
        options.body = JSON.stringify(data);
    }
    
    return fetch(BASE_URL + endpoint, options)
        .then(response => response.json())
        .catch(error => {
            console.error('Error:', error);
            return { status: 'error', message: 'Network error' };
        });
}

// ============================================
// NOTIFICATIONS
// ============================================

function showNotification(message, type = 'info', duration = 3000) {
    const alertClass = {
        'success': 'alert-success',
        'error': 'alert-danger',
        'warning': 'alert-warning',
        'info': 'alert-info'
    };
    
    const alert = document.createElement('div');
    alert.className = `alert ${alertClass[type] || 'alert-info'} alert-dismissible fade show`;
    alert.innerHTML = `
        ${getIcon(type)} ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    const container = document.querySelector('main') || document.body;
    container.insertBefore(alert, container.firstChild);
    
    if (duration > 0) {
        setTimeout(() => alert.remove(), duration);
    }
    
    return alert;
}

function getIcon(type) {
    const icons = {
        'success': '<i class="fas fa-check-circle"></i>',
        'error': '<i class="fas fa-exclamation-circle"></i>',
        'warning': '<i class="fas fa-exclamation-triangle"></i>',
        'info': '<i class="fas fa-info-circle"></i>'
    };
    return icons[type] || icons['info'];
}

// Shortcuts
function showSuccess(message, duration = 3000) {
    return showNotification(message, 'success', duration);
}

function showError(message, duration = 3000) {
    return showNotification(message, 'error', duration);
}

function showWarning(message, duration = 3000) {
    return showNotification(message, 'warning', duration);
}

function showInfo(message, duration = 3000) {
    return showNotification(message, 'info', duration);
}

// ============================================
// FORM HANDLING
// ============================================

function validateEmail(email) {
    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return regex.test(email);
}

function validateForm(formElement) {
    let isValid = true;
    
    formElement.querySelectorAll('[required]').forEach(field => {
        if (!field.value.trim()) {
            field.classList.add('is-invalid');
            isValid = false;
        } else {
            field.classList.remove('is-invalid');
        }
    });
    
    return isValid;
}

// ============================================
// TABLE ACTIONS
// ============================================

function confirmDelete(message = 'Bạn chắc chắn muốn xóa?') {
    return confirm(message);
}

function deleteRow(url, rowElement, successMessage = 'Đã xóa thành công') {
    if (!confirmDelete()) return;
    
    fetch(url, { method: 'DELETE' })
        .then(r => r.json())
        .then(d => {
            if (d.status === 'success') {
                rowElement.style.transition = 'opacity 0.3s';
                rowElement.style.opacity = '0';
                setTimeout(() => rowElement.remove(), 300);
                showSuccess(successMessage);
            } else {
                showError(d.message || 'Lỗi xóa');
            }
        });
}

// ============================================
// LOADING STATE
// ============================================

function setLoading(button, isLoading = true) {
    if (isLoading) {
        button.disabled = true;
        button.dataset.originalText = button.innerHTML;
        button.innerHTML = `<span class="spinner-border spinner-border-sm me-2"></span>Đang xử lý...`;
    } else {
        button.disabled = false;
        button.innerHTML = button.dataset.originalText || 'Submit';
    }
}

// ============================================
// UTILITIES
// ============================================

function formatDate(dateString, format = 'dd/mm/yyyy') {
    const date = new Date(dateString);
    const day = String(date.getDate()).padStart(2, '0');
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const year = date.getFullYear();
    const hours = String(date.getHours()).padStart(2, '0');
    const minutes = String(date.getMinutes()).padStart(2, '0');
    
    return format
        .replace('dd', day)
        .replace('mm', month)
        .replace('yyyy', year)
        .replace('hh', hours)
        .replace('ii', minutes);
}

function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        showSuccess('Đã sao chép!', 2000);
    });
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// ============================================
// DOM READY
// ============================================

document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    const tooltips = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    tooltips.forEach(el => new bootstrap.Tooltip(el));
    
    // Initialize popovers
    const popovers = document.querySelectorAll('[data-bs-toggle="popover"]');
    popovers.forEach(el => new bootstrap.Popover(el));
    
    // Remove invalid state on input
    document.querySelectorAll('.form-control, .form-select').forEach(field => {
        field.addEventListener('input', function() {
            this.classList.remove('is-invalid');
        });
    });
});

// ============================================
// EXPORT FUNCTIONS
// ============================================

function exportTableToCSV(tableId, filename = 'export.csv') {
    const table = document.getElementById(tableId);
    let csv = [];
    
    table.querySelectorAll('tr').forEach(row => {
        let cells = [];
        row.querySelectorAll('td, th').forEach(cell => {
            cells.push('"' + cell.textContent.trim().replace(/"/g, '""') + '"');
        });
        csv.push(cells.join(','));
    });
    
    const csvContent = csv.join('\n');
    downloadFile(csvContent, filename, 'text/csv');
}

function downloadFile(content, filename, mimeType = 'text/plain') {
    const blob = new Blob([content], { type: mimeType });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = filename;
    document.body.appendChild(a);
    a.click();
    window.URL.revokeObjectURL(url);
    document.body.removeChild(a);
}

// ============================================
// PRINT
// ============================================

function printPage(elementId = null) {
    if (elementId) {
        const element = document.getElementById(elementId);
        const printWindow = window.open('', '', 'height=600,width=800');
        printWindow.document.write('<html><head><title>Print</title></head><body>');
        printWindow.document.write(element.innerHTML);
        printWindow.document.write('</body></html>');
        printWindow.document.close();
        printWindow.print();
    } else {
        window.print();
    }
}
