document.addEventListener('DOMContentLoaded', function() {
    const pupils = document.querySelectorAll('.pupil');
    const eyes = document.querySelectorAll('.eye');
    
    document.addEventListener('mousemove', function(event) {
        eyes.forEach((eye, index) => {
            const rect = eye.getBoundingClientRect();
            const eyeCenterX = rect.left + (rect.width / 2);
            const eyeCenterY = rect.top + (rect.height / 2);
            
            const distanceX = event.clientX - eyeCenterX;
            const distanceY = event.clientY - eyeCenterY;
            
            const angle = Math.atan2(distanceY, distanceX);
            const radius = 5; // Reduced movement radius
            
            const moveX = Math.cos(angle) * radius;
            const moveY = Math.sin(angle) * radius;
            
            pupils[index].style.transform = `translate(${moveX}px, ${moveY}px)`;
        });
    });
});
