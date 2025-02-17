<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pet Crossing - Home</title>
    <link rel="stylesheet" href="assets/css/landing.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/animations.css">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <div class="logo">
            <img src="assets/images/logo.png" alt="Pet Crossing Logo" height="50">
        </div>
        <ul class="nav-links">
            <li><a href="#home">Home</a></li>
            <li><a href="#services">Services</a></li>
            <li><a href="#about">About</a></li>
            <li><a href="#contact">Contact</a></li>
            <li><a href="login.php">Login / Sign Up</a></li>
        </ul>
    </nav>

    <!-- Hero Section -->
    <section id="home" class="hero parallax-section">
        <div class="parallax-overlay"></div>
        <div class="hero-content parallax-content fade-in">
            <h1>Welcome to Pet Crossing</h1>
            <p>Your trusted partner in pet care</p>
            <a href="appointment.php" class="btn-appointment">Book an Appointment</a>
        </div>
    </section>

    <!-- Services Section -->
    <section id="services" class="section">
        <div class="section-content">
            <h2 class="fade-in">Our Services</h2>
            <div class="services-grid">
                <div class="service-card scale-in">
                    <i class="fas fa-stethoscope"></i>
                    <h3>General Checkup</h3>
                    <p>Comprehensive health examinations for your pets</p>
                </div>
                <div class="service-card scale-in">
                    <i class="fas fa-syringe"></i>
                    <h3>Vaccinations</h3>
                    <p>Essential vaccines and immunizations</p>
                </div>
                <div class="service-card scale-in">
                    <i class="fas fa-cut"></i>
                    <h3>Grooming</h3>
                    <p>Professional pet grooming services</p>
                </div>
                <div class="service-card scale-in">
                    <i class="fas fa-hospital"></i>
                    <h3>Surgery</h3>
                    <p>Advanced surgical procedures</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Second Parallax Section -->
    <section class="parallax-section" style="background-image: url('assets/images/pets-playing.jpg');">
        <div class="parallax-overlay"></div>
        <div class="parallax-content">
            <h2>Quality Care for Your Pets</h2>
            <p>Professional veterinary services you can trust</p>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="section">
        <div class="section-content">
            <h2 class="fade-in">About Us</h2>
            <div class="about-content">
                <div class="about-text slide-in-left">
                    <p>Pet Crossing is a state-of-the-art veterinary clinic dedicated to providing the highest quality care for your beloved pets. Our team of experienced veterinarians and staff are committed to ensuring the health and happiness of your furry family members.</p>
                    <ul class="about-features">
                        <li>24/7 Emergency Services</li>
                        <li>Modern Facilities & Equipment</li>
                        <li>Experienced Veterinary Team</li>
                        <li>Comfortable & Clean Environment</li>
                    </ul>
                </div>
                <div class="about-image slide-in-right">
                    <img src="assets/images/clinic-interior.jpg" alt="Clinic Interior">
                </div>
            </div>
        </div>
    </section>

    <!-- Third Parallax Section -->
    <section class="parallax-section" style="background-image: url('assets/images/vet-team.jpg');">
        <div class="parallax-overlay"></div>
        <div class="parallax-content">
            <h2>Meet Our Team</h2>
            <p>Dedicated professionals committed to your pet's health</p>
        </div>
    </section>

    <!-- Footer -->
    <footer id="contact" class="footer">
        <div class="footer-content fade-in">
            <div class="footer-section">
                <h3>Contact Us</h3>
                <p><i class="fas fa-map-marker-alt"></i> 123 Pet Street, Veterinary District</p>
                <p><i class="fas fa-phone"></i> (123) 456-7890</p>
                <p><i class="fas fa-envelope"></i> info@petcrossing.com</p>
            </div>
            <div class="footer-section">
                <h3>Opening Hours</h3>
                <p>Monday - Friday: 8:00 AM - 6:00 PM</p>
                <p>Saturday: 9:00 AM - 4:00 PM</p>
                <p>Sunday: Closed</p>
            </div>
            <div class="footer-section">
                <h3>Follow Us</h3>
                <div class="social-links">
                    <a href="#"><i class="fab fa-facebook"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2024 Pet Crossing. All rights reserved.</p>
        </div>
    </footer>

    <script src="assets/js/scroll-animations.js"></script>
    <script src="assets/js/smooth-scroll.js"></script>
</body>
</html>
