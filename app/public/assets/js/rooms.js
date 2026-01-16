// RoomShift Interactive JavaScript
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
            .then(room => {
                this.displayRoomDetails(room);
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
        document.getElementById('playRoomBtn').href = `/play/${roomId}`;
        
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
    
    createCharCounter() {
        const descInput = document.getElementById('description');
        const counter = document.createElement('div');
        counter.className = 'form-text text-end';
        counter.id = 'charCounter';
        descInput.parentNode.appendChild(counter);
        this.updateCharCount();
    }
    
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
        // PREVENT DEFAULT FORM SUBMISSION
        event.preventDefault();
        
        const form = event.target;
        const formData = new FormData(form);
        
        // Add CSRF token if needed (assuming you have one in meta tag)
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
        if (csrfToken) {
            formData.append('csrf_token', csrfToken);
        }
        
        // Show loading state on button
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Creating...';
        submitBtn.disabled = true;
        
        // Clear any previous error messages
        const errorDiv = form.querySelector('.ajax-error');
        if (errorDiv) errorDiv.remove();
        
        // AJAX Submission
        fetch(form.action, {  // Use the form's action attribute
            method: form.method,
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest' // Mark as AJAX request
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json(); // Expect JSON response
        })
        .then(data => {
            // Handle successful response
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
            
            if (data.success) {
                // Show success message
                alert('Room created successfully!');
                
                // Option 1: Redirect to new room
                if (data.redirect) {
                    window.location.href = data.redirect;
                }
                // Option 2: Reset form for another room
                else {
                    form.reset();
                    // Update char counter if exists
                    if (typeof this.updateCharCount === 'function') {
                        this.updateCharCount();
                    }
                }
            } else {
                // Show error from server
                this.showFormError(form, data.message || 'Something went wrong');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
            
            // Show error to user
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
            ${message}
        `;
        
        // Insert after form or before submit button
        const submitBtn = form.querySelector('button[type="submit"]');
        form.insertBefore(errorDiv, submitBtn);
    }
    
    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
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

// Add this to your existing rooms page initialization
if (document.querySelector('.rooms-list')) {
    setupRoomSearch();
}