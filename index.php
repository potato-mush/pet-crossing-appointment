<?php
session_start();
?>
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
    <?php include('includes/header.php'); ?>

    <!-- Hero Section -->
    <section id="home" class="hero parallax-section">
        <div class="parallax-overlay"></div>
        <div class="hero-content parallax-content fade-in">
            <h1>Welcome to Pet Crossing</h1>
            <p>Your trusted partner in pet care</p>
            <?php if(isset($_SESSION['user_id'])): ?>
                <a href="appointment.php" class="btn-appointment">Book an Appointment</a>
            <?php else: ?>
                <a href="login.php" class="btn-appointment">Book Appointment</a>
            <?php endif; ?>
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

    <?php include('includes/footer.php'); ?>

    <script src="assets/js/scroll-animations.js"></script>
    <script src="assets/js/smooth-scroll.js"></script>
</body>
</html>
