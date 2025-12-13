    </main>

    <!-- Simple Bootstrap Footer -->
    <footer class="mt-auto py-4">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6 mb-3 mb-md-0">
                    <ul class="nav">
                        <li class="nav-item">
                            <a href="/" class="nav-link px-2 text-body-secondary">Home</a>
                        </li>
                        <li class="nav-item">
                            <a href="/features" class="nav-link px-2 text-body-secondary">Features</a>
                        </li>
                        <li class="nav-item">
                            <a href="/pricing" class="nav-link px-2 text-body-secondary">Pricing</a>
                        </li>
                        <li class="nav-item">
                            <a href="/faqs" class="nav-link px-2 text-body-secondary">FAQs</a>
                        </li>
                        <li class="nav-item">
                            <a href="/about" class="nav-link px-2 text-body-secondary">About</a>
                        </li>
                    </ul>
                </div>
                
                <div class="col-md-6 text-md-end">
                    <span class="text-body-secondary">
                        &copy; <?= date('Y') ?> RoomShift, Inc
                    </span>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" 
            integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" 
            crossorigin="anonymous"></script>
    
    <!-- Your Custom JavaScript -->
    <script src="/assets/js/rooms.js"></script>
    
    <!-- Initialize Bootstrap components -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-dismiss alerts after 5 seconds
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                setTimeout(function() {
                    if (alert) {
                        const bsAlert = new bootstrap.Alert(alert);
                        bsAlert.close();
                    }
                }, 5000);
            });
        });
    </script>
</body>
</html>