document.addEventListener('DOMContentLoaded', function() {
    // Modal functionality
    const modals = document.querySelectorAll('.modal');
    const modalTriggers = document.querySelectorAll('[data-modal]');
    const closeBtns = document.querySelectorAll('.close-btn');
    
    // Open modal
    modalTriggers.forEach(trigger => {
        trigger.addEventListener('click', function() {
            const modalId = this.getAttribute('data-modal');
            document.getElementById(modalId).style.display = 'block';
            
            // If it's an edit modal, populate with data from the row
            if (modalId.includes('edit')) {
                const row = this.closest('tr');
                populateEditModal(modalId, row);
            }
            
            // If it's a delete confirmation modal, set the ID to delete
            if (modalId.includes('delete')) {
                const row = this.closest('tr');
                const idCell = row.querySelector('td:nth-child(2)');
                const id = idCell.textContent.trim();
                const confirmBtn = document.querySelector(`#${modalId} .btn-danger`);
                confirmBtn.setAttribute('data-id', id);
            }
        });
    });
    
    // Close modal with close button
    closeBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const modal = this.closest('.modal');
            modal.style.display = 'none';
        });
    });
    
    // Close modal when clicking outside
    window.addEventListener('click', function(e) {
        modals.forEach(modal => {
            if (e.target === modal) {
                modal.style.display = 'none';
            }
        });
    });
    
    // Toast notification function
    function showToast(message, isError = false) {
        const toast = document.getElementById('toast');
        if (!toast) {
            // Create toast if it doesn't exist
            const newToast = document.createElement('div');
            newToast.id = 'toast';
            newToast.className = 'toast';
            document.body.appendChild(newToast);
            
            // Set the message
            newToast.textContent = message;
            if (isError) newToast.classList.add('error');
            
            // Show the toast
            setTimeout(() => newToast.classList.add('show'), 10);
            
            // Hide after 3 seconds
            setTimeout(() => {
                newToast.classList.remove('show');
                setTimeout(() => {
                    newToast.remove();
                }, 300);
            }, 3000);
        }
    }
    
    // Function to populate edit modals with data
    function populateEditModal(modalId, row) {
        const modal = document.getElementById(modalId);
        const form = modal.querySelector('form');
        const inputs = form.querySelectorAll('input, select');
        
        // Get the row cells (excluding checkbox and actions)
        const cells = Array.from(row.querySelectorAll('td')).slice(1, -1);
        
        // For each input, find the corresponding cell and set its value
        inputs.forEach((input, index) => {
            // Skip hidden inputs
            if (input.type === 'hidden') return;
            
            // Find the corresponding cell
            let cellIndex = index;
            if (input.id === 'OldSectionName') {
                // Special case for section edit which has a hidden field
                const sectionNameCell = row.querySelector('td:nth-child(2)');
                input.value = sectionNameCell.textContent.trim();
                return;
            }
            
            if (cells[index - 1]) {
                const value = cells[index - 1].textContent.trim();
                input.value = value;
                
                // Store the original value for comparison
                input.setAttribute('data-original', value);
            }
        });
        
        // Enable the update button initially for better UX, validation will happen on form submission
        const updateBtn = form.querySelector('[type="submit"]');
        if (updateBtn) {
            updateBtn.disabled = false;
        }
        
        // Add input validation for specific fields
        const studentNumberInput = form.querySelector('#EditStudentNumber');
        if (studentNumberInput) {
            studentNumberInput.addEventListener('input', function() {
                validateStudentNumber(this);
            });
        }
        
        const unitInput = form.querySelector('#EditUnit');
        if (unitInput) {
            unitInput.addEventListener('input', function() {
                validateUnit(this);
            });
        }
        
        // Also add validation to create forms
        const newStudentNumberInput = document.querySelector('#StudentNumber');
        if (newStudentNumberInput) {
            newStudentNumberInput.addEventListener('input', function() {
                validateStudentNumber(this);
            });
        }
        
        const newUnitInput = document.querySelector('#Unit');
        if (newUnitInput) {
            newUnitInput.addEventListener('input', function() {
                validateUnit(this);
            });
        }
    }
    
    // Validate student number (must be exactly 7 digits)
    function validateStudentNumber(input) {
        const value = input.value.trim();
        const isValid = /^\d{7}$/.test(value);
        
        if (!isValid) {
            input.setCustomValidity('Student number must be exactly 7 digits');
            input.style.borderColor = '#e74a3b';
        } else {
            input.setCustomValidity('');
            input.style.borderColor = '#4BB543';
        }
    }
    
    // Validate unit (must be 1-3)
    function validateUnit(input) {
        const value = parseInt(input.value, 10);
        const isValid = value >= 1 && value <= 3;
        
        if (!isValid) {
            input.setCustomValidity('Unit must be between 1 and 3');
            input.style.borderColor = '#e74a3b';
        } else {
            input.setCustomValidity('');
            input.style.borderColor = '#4BB543';
        }
    }
    
    // Function to check if form values have changed
    function checkFormChanges(form) {
        const inputs = form.querySelectorAll('input, select');
        const updateBtn = form.querySelector('[type="submit"]');
        let hasChanges = false;
        
        inputs.forEach(input => {
            const original = input.getAttribute('data-original');
            if (original !== null && input.value !== original) {
                hasChanges = true;
            }
        });
        
        if (updateBtn) {
            updateBtn.disabled = !hasChanges;
        }
    }
    
    // Checkbox functionality
    const selectAllCheckbox = document.getElementById('select-all');
    const checkboxes = document.querySelectorAll('tbody input[type="checkbox"]');
    const deleteSelectedBtn = document.querySelector('.delete-selected');
    
    // Select all checkboxes
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateDeleteButtonVisibility();
        });
    }
    
    // Individual checkbox change
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateDeleteButtonVisibility);
    });
    
    // Update delete button visibility
    function updateDeleteButtonVisibility() {
        const checkedCount = document.querySelectorAll('tbody input[type="checkbox"]:checked').length;
        if (checkedCount > 0) {
            deleteSelectedBtn.style.display = 'inline-block';
        } else {
            deleteSelectedBtn.style.display = 'none';
        }
    }
    
    // Delete selected items functionality
    if (deleteSelectedBtn) {
        deleteSelectedBtn.addEventListener('click', function() {
            const selectedIds = [];
            const rows = document.querySelectorAll('tbody tr');
            
            rows.forEach(row => {
                const checkbox = row.querySelector('input[type="checkbox"]');
                if (checkbox && checkbox.checked) {
                    const idCell = row.querySelector('td:nth-child(2)');
                    if (idCell) {
                        selectedIds.push(idCell.textContent.trim());
                    }
                }
            });
            
            if (selectedIds.length > 0) {
                // Show delete confirmation modal
                const deleteMultipleModal = document.getElementById('deleteMultipleModal');
                if (deleteMultipleModal) {
                    const countSpan = deleteMultipleModal.querySelector('.item-count');
                    if (countSpan) {
                        countSpan.textContent = selectedIds.length;
                    }
                    
                    const confirmBtn = deleteMultipleModal.querySelector('.btn-danger');
                    confirmBtn.onclick = function() {
                        const currentPage = window.location.pathname.split('/').pop();
                        let deleteUrl = '';
                        
                        if (currentPage === 'students.php') {
                            deleteUrl = '../PHP/deletemultiple.php?type=students';
                        } else if (currentPage === 'sections.php') {
                            deleteUrl = '../PHP/deletemultiple.php?type=sections'; 
                        } else if (currentPage === 'subjects.php') {
                            deleteUrl = '../PHP/deletemultiple.php?type=subjects';
                        } else if (currentPage === 'grades.php') {
                            deleteUrl = '../PHP/deletemultiple.php?type=grades';
                        }
                        
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = deleteUrl;
                        
                        selectedIds.forEach(id => {
                            const input = document.createElement('input');
                            input.type = 'hidden';
                            input.name = 'ids[]';
                            input.value = id;
                            form.appendChild(input);
                        });
                        
                        document.body.appendChild(form);
                        form.submit();
                    };
                    
                    deleteMultipleModal.style.display = 'block';
                }
            }
        });
    }
    
    // Delete confirmation button functionality
    const deleteConfirmBtns = document.querySelectorAll('.delete-confirm');
    deleteConfirmBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const type = this.getAttribute('data-type');
            
            if (id && type) {
                window.location.href = `../PHP/delete${type}.php?id=${id}`;
            }
            
            // Close the modal
            const modal = this.closest('.modal');
            modal.style.display = 'none';
        });
    });
    
    // Close modal with cancel button for delete modals
    const deleteModalCancelBtns = document.querySelectorAll('.delete-modal .btn-secondary');
    deleteModalCancelBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const modal = this.closest('.modal');
            modal.style.display = 'none';
        });
    });
    
    // Form submission for modals
    const modalForms = document.querySelectorAll('.modal-form');
    
    modalForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            // Skip if this form is being directly handled (like the student edit form)
            if (form.hasAttribute('data-direct-submit') || 
                (form.action && form.action.includes('edit_student'))) {
                return true;
            }
            
            e.preventDefault();
            
            // Check form validity
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }
            
            const formData = new FormData(this);
            const formAction = this.getAttribute('action');
            
            // Show loading state
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalBtnText = submitBtn.textContent;
            submitBtn.textContent = 'Processing...';
            submitBtn.disabled = true;
            
            // Check if it's an edit form
            const isEditForm = formAction.includes('edit_student');
            
            fetch(formAction, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                // Reset button state
                submitBtn.textContent = originalBtnText;
                submitBtn.disabled = false;
                
                if (data.success) {
                    // Close the modal
                    const modal = this.closest('.modal');
                    modal.style.display = 'none';
                    
                    // Show success toast
                    showToast(data.message || 'Operation completed successfully');
                    
                    // If it's an edit form, check if we need to update URL params
                    if (isEditForm) {
                        const currentUrl = new URL(window.location.href);
                        currentUrl.searchParams.set('updated', '1');
                        window.history.replaceState({}, '', currentUrl.toString());
                    }
                    
                    // Reload the page after a brief delay
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else {
                    // Show error toast
                    showToast(data.message || 'An error occurred', true);
                }
            })
            .catch(error => {
                // Reset button state
                submitBtn.textContent = originalBtnText;
                submitBtn.disabled = false;
                
                console.error('Error:', error);
                showToast('An error occurred while processing your request', true);
            });
        });
    });
    
    // Create toast element if needed for URL params
    if (window.location.search.includes('success=')) {
        const urlParams = new URLSearchParams(window.location.search);
        const successMsg = urlParams.get('success');
        if (successMsg) {
            showToast(decodeURIComponent(successMsg));
            
            // Clean the URL without reloading the page
            const newUrl = window.location.pathname;
            window.history.replaceState({}, document.title, newUrl);
        }
    }
    
    if (window.location.search.includes('error=')) {
        const urlParams = new URLSearchParams(window.location.search);
        const errorMsg = urlParams.get('error');
        if (errorMsg) {
            showToast(decodeURIComponent(errorMsg), true);
            
            // Clean the URL without reloading the page
            const newUrl = window.location.pathname;
            window.history.replaceState({}, document.title, newUrl);
        }
    }
    
    // Global function to show toast messages
    window.showToast = showToast;
}); 