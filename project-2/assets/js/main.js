// Confirm delete actions
document.addEventListener('DOMContentLoaded', function() {
    // Handle delete confirmations
    const deleteButtons = document.querySelectorAll('.delete-confirm');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            if (!confirm('Êtes-vous sûr de vouloir supprimer cet élément ?')) {
                e.preventDefault();
            }
        });
    });

    // Handle form validation
    const forms = document.querySelectorAll('.needs-validation');
    forms.forEach(form => {
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        });
    });

    // Auto-hide alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert:not(.alert-permanent)');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 300);
        }, 5000);
    });

    // Project filter functionality
    const filterInput = document.getElementById('projectFilter');
    if (filterInput) {
        filterInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const projectCards = document.querySelectorAll('.project-card');
            
            projectCards.forEach(card => {
                const title = card.querySelector('.card-title').textContent.toLowerCase();
                const description = card.querySelector('.card-text').textContent.toLowerCase();
                const speciality = card.dataset.speciality.toLowerCase();
                
                if (title.includes(searchTerm) || 
                    description.includes(searchTerm) || 
                    speciality.includes(searchTerm)) {
                    card.style.display = '';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    }

    // Speciality filter
    const specialityFilter = document.getElementById('specialityFilter');
    if (specialityFilter) {
        specialityFilter.addEventListener('change', function() {
            const selectedSpeciality = this.value.toLowerCase();
            const projectCards = document.querySelectorAll('.project-card');
            
            projectCards.forEach(card => {
                if (selectedSpeciality === 'all' || 
                    card.dataset.speciality.toLowerCase() === selectedSpeciality) {
                    card.style.display = '';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    }

    // Dynamic form fields for project creation
    const nombreElevesSelect = document.getElementById('nombre_eleves');
    const student2Group = document.getElementById('student2Group');
    
    if (nombreElevesSelect && student2Group) {
        nombreElevesSelect.addEventListener('change', function() {
            student2Group.style.display = this.value === 'binome' ? 'block' : 'none';
        });
    }
}); 