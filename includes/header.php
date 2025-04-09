<!-- Navbar -->
<nav class="navbar">
        <div class="logo">
            <img src="assets/images/logo.png" alt="Pet Crossing Logo" height="50">
        </div>
        <ul class="nav-links">
            <li><a href="index.php#home">Home</a></li>
            <li><a href="index.php#services">Services</a></li>
            <li><a href="index.php#about">About</a></li>
            <li><a href="index.php#contact">Contact</a></li>
            <li>
                <?php if(isset($_SESSION['user_id'])): ?>
                    <a href="logout.php">Sign Out</a>
                <?php else: ?>
                    <a href="login.php">Login / Sign Up</a>
                <?php endif; ?>
            </li>
        </ul>
    </nav>