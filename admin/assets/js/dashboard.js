document.addEventListener('DOMContentLoaded', function() {
    // Handle sidebar navigation
    document.querySelectorAll('.sidebar a').forEach(link => {
        link.addEventListener('click', function(e) {
            if(this.dataset.page) {
                e.preventDefault();
                
                // Hide all pages
                document.querySelectorAll('.page').forEach(page => {
                    page.classList.remove('active');
                });
                
                // Show selected page
                document.getElementById(this.dataset.page).classList.add('active');
            }
        });
    });
});
