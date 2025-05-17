document.addEventListener('DOMContentLoaded', function() {
    const selectAllCheckbox = document.getElementById('select-all');
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('tbody input[type="checkbox"]');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });
    }
    
    const rowCheckboxes = document.querySelectorAll('tbody input[type="checkbox"]');
    rowCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const row = this.closest('tr');
            if (this.checked) {
                row.classList.add('selected-row');
            } else {
                row.classList.remove('selected-row');
            }
        });
    });
    
    // const editButtons = document.querySelectorAll('.edit-button');
    // editButtons.forEach(button => {
    //     button.addEventListener('click', function() {
    //         const row = this.closest('tr');
    //         alert('Edit row');
    //     });
    // });
    
    // const deleteButtons = document.querySelectorAll('.delete-button');
    // deleteButtons.forEach(button => {
    //     button.addEventListener('click', function() {
    //         const row = this.closest('tr');
    //         if (confirm('Are you sure you want to delete this item?')) {
    //             row.remove();
    //         }
    //     });
    // });
    
    const searchInput = document.getElementById('search');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const tableRows = document.querySelectorAll('tbody tr');
            
            tableRows.forEach(row => {
                let found = false;
                const cells = row.querySelectorAll('td:not(:first-child):not(:last-child)');
                
                cells.forEach(cell => {
                    if (cell.textContent.toLowerCase().includes(searchTerm)) {
                        found = true;
                    }
                });
                
                if (found || searchTerm === '') {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }
    
    const actionButtons = document.querySelectorAll('.action-button');
    if (actionButtons.length > 0) {
        actionButtons.forEach(button => {
            button.addEventListener('click', function() {
                if (this.textContent.includes('row')) {
                    alert('Add new row to grades table');
                } else if (this.textContent.includes('column')) {
                    alert('Add new column to grades table');
                }
            });
        });
    }
});

