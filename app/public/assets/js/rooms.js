// RoomShift Interactive JavaScript
// I used a JavaScript class only where the page had complex,
//  related behaviors; simpler scripts remain function-based.”
class RoomManager {
    constructor() {
        this.init();
    }
    
    init() {
        // View room buttons
        document.querySelectorAll('.view-room-btn').forEach(btn => {
            btn.addEventListener('click', (e) => this.viewRoom(e));
        });
        
        // Filter rooms by search
        const searchInput = document.getElementById('roomSearch');
        if (searchInput) {
            searchInput.addEventListener('input', (e) => this.filterRooms(e.target.value));
        }
        
        // Real-time character counter for description
        const descInput = document.getElementById('description');
        if (descInput) {
            descInput.addEventListener('input', () => this.updateCharCount());
            this.createCharCounter();
        }
        
        // Form submission with AJAX (optional enhancement)
        const roomForm = document.getElementById('roomForm');
        if (roomForm) {
            roomForm.addEventListener('submit', (e) => this.handleFormSubmit(e));
        }
    }
    
    viewRoom(event) {
        const roomId = event.currentTarget.dataset.id;
        
        // Show loading state
        const modalBody = document.getElementById('roomModalBody');
        modalBody.innerHTML = `
            <div class="text-center py-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2">Loading room details...</p>
            </div>
        `;
        
        // Fetch room details via AJAX
        fetch(`/api/rooms/${roomId}`)
            .then(response => response.json())
            .then(data => {
            if (!data.success) throw new Error(data.message || "Failed");
            this.displayRoomDetails(data.room);
            })

            .catch(error => {
                modalBody.innerHTML = `
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle"></i>
                        Failed to load room details. Please try again.
                    </div>
                `;
                console.error('Error:', error);
            });
        
        // Update play button link
        document.getElementById('playRoomBtn').href = `/rooms/${roomId}/play`;
        
        // Show Bootstrap modal
        const roomModal = new bootstrap.Modal(document.getElementById('roomModal'));
        roomModal.show();
    }
    
    displayRoomDetails(room) {
        const modalBody = document.getElementById('roomModalBody');
        document.getElementById('roomModalTitle').textContent = room.title;
        
        modalBody.innerHTML = `
            <div class="row">
                <div class="col-md-12">
                    <h4 class="mb-3">${this.escapeHtml(room.title)}</h4>
                    <p class="lead">${this.escapeHtml(room.description)}</p>
                    
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6><i class="bi bi-calendar text-primary"></i> Created</h6>
                                    <p class="mb-0">${new Date(room.created_at).toLocaleDateString()}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6><i class="bi bi-puzzle text-success"></i> Type</h6>
                                    <p class="mb-0">Escape Room Adventure</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <h5>How to Play:</h5>
                        <ol class="list-group list-group-numbered">
                            <li class="list-group-item">Read the room description carefully</li>
                            <li class="list-group-item">Look for hidden clues and puzzles</li>
                            <li class="list-group-item">Solve puzzles to progress</li>
                            <li class="list-group-item">Find the key to escape!</li>
                        </ol>
                    </div>
                </div>
            </div>
        `;
    }
    
    // Filter rooms based on search input
    filterRooms(searchTerm) {
        const roomItems = document.querySelectorAll('.list-group-item');
        const roomCount = document.getElementById('roomCount');
        let visibleCount = 0;
        
        roomItems.forEach(item => {
            const title = item.querySelector('h5').textContent.toLowerCase();
            const description = item.querySelector('p').textContent.toLowerCase();
            const search = searchTerm.toLowerCase();
            
            if (title.includes(search) || description.includes(search)) {
                item.style.display = '';
                visibleCount++;
            } else {
                item.style.display = 'none';
            }
        });
        
        roomCount.textContent = `${visibleCount} room${visibleCount !== 1 ? 's' : ''}`;
    }
    
    // Create character counter element
    createCharCounter() {
        const descInput = document.getElementById('description');
        const counter = document.createElement('div');
        counter.className = 'form-text text-end';
        counter.id = 'charCounter';
        descInput.parentNode.appendChild(counter);
        this.updateCharCount();
    }
    
    // Update character count display
    updateCharCount() {
        const descInput = document.getElementById('description');
        const counter = document.getElementById('charCounter');
        const length = descInput.value.length;
        const min = 10;
        const max = 1000;
        
        counter.textContent = `${length} / ${max} characters`;
        counter.className = `form-text text-end ${length < min ? 'text-danger' : length > max ? 'text-warning' : 'text-success'}`;
    }
    
        handleFormSubmit(event) {
        event.preventDefault();
        
        const form = event.target;
        
        // Collect form data as an object instead of FormData
        const formData = new FormData(form);
        const data = {};
        
        // Convert FormData to plain object
        formData.forEach((value, key) => {
            // Handle checkboxes specifically
            if (form.elements[key]?.type === 'checkbox') {
                data[key] = form.elements[key].checked ? '1' : '0';
            } else {
                data[key] = value;
            }
        });
        
        // Add CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
        if (csrfToken) {
            data.csrf_token = csrfToken;
        }
        
        // Show loading state
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Creating...';
        submitBtn.disabled = true;
        
        // Clear any previous error messages
        const errorDiv = form.querySelector('.ajax-error');
        if (errorDiv) errorDiv.remove();
        
        // Send as JSON instead of FormData
        fetch(form.action, {
            method: form.method,
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(data)
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
            
            if (data.success) {
                alert('Room created successfully!');
                if (data.redirect) {
                    window.location.href = data.redirect;
                } else {
                    form.reset();
                    if (typeof this.updateCharCount === 'function') {
                        this.updateCharCount();
                    }
                }
            } else {
                this.showFormError(form, data.message || 'Something went wrong');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
            this.showFormError(form, 'Failed to create room. Please try again.');
        });
    }
    
    showFormError(form, message) {
        // Remove existing error
        const existingError = form.querySelector('.ajax-error');
        if (existingError) existingError.remove();
        
        // Create error div
        const errorDiv = document.createElement('div');
        errorDiv.className = 'alert alert-danger mt-3 ajax-error';
        errorDiv.innerHTML = `
            <i class="bi bi-exclamation-triangle me-2"></i>
            ${this.escapeHtml(message)}
        `;
        
        // Find a safe place to insert - try inserting at the end of the form
        form.appendChild(errorDiv);
    }

    // Simple HTML escaping to prevent XSS
    escapeHtml(str) {
        if (typeof str !== 'string') return '';

        return str
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }

}
// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    window.roomManager = new RoomManager();
});

// Utility function for AJAX requests
function fetchRoomDetails(roomId) {
    return fetch(`/api/rooms/${roomId}`)
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.json();
        });
}

// Live search for rooms
function setupRoomSearch() {
    const searchInput = document.createElement('input');
    searchInput.type = 'text';
    searchInput.className = 'form-control mb-3';
    searchInput.placeholder = 'Search rooms...';
    searchInput.id = 'roomSearch';
    
    const roomsContainer = document.querySelector('.card-body');
    if (roomsContainer) {
        roomsContainer.insertBefore(searchInput, roomsContainer.firstChild);
    }
}

if (document.querySelector('.rooms-list')) {
    setupRoomSearch();
}
