// This file contains the main JavaScript logic for the login page.

// Function to handle form submission
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('login-form');
    
    form.addEventListener('submit', (event) => {
        event.preventDefault();
        const username = document.getElementById('username').value;
        const password = document.getElementById('password').value;

        // Simple validation
        if (username === '' || password === '') {
            alert('Please fill in all fields.');
        } else {
            // Handle login logic here
            console.log('Logging in with:', username, password);
            // You can add your login logic (e.g., API call) here
        }
    });
});