function updateStatus(applicationId, selectElement) {
    const status = selectElement.value;
    const indicator = document.getElementById('status-indicator-' + applicationId);
    
    indicator.textContent = 'Saving...';
    indicator.style.color = '#6c757d';
    indicator.style.opacity = '1';
    
    const formData = new FormData();
    formData.append('application_id', applicationId);
    formData.append('status', status);
    
    fetch('../controllers/ApplicationController.php?action=update_status', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            indicator.textContent = 'Saved!';
            indicator.style.color = '#28a745';
            setTimeout(() => {
                indicator.style.opacity = '0';
            }, 2000);
        } else {
            indicator.textContent = 'Error!';
            indicator.style.color = '#dc3545';
            alert('Error updating status: ' + data.message);
        }
    })
    .catch(err => {
        indicator.textContent = 'Error!';
        indicator.style.color = '#dc3545';
        console.error('AJAX Error:', err);
    });
}
