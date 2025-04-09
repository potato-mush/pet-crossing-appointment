<?php
session_start();

// Database connection
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'pet_clinic';

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$error = '';
$signup_error = '';

// Handle signup form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['signup'])) {
    $first_name = $conn->real_escape_string($_POST['first_name']);
    $last_name = $conn->real_escape_string($_POST['last_name']);
    $email = $conn->real_escape_string($_POST['signup_email']);
    $password = password_hash($_POST['signup_password'], PASSWORD_DEFAULT);
    $phone = $conn->real_escape_string($_POST['phone']);

    // Check if email exists
    $check_email = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($check_email);
    if ($stmt === false) {
        $signup_error = "Prepare failed: " . $conn->error;
    } else {
        $stmt->bind_param("s", $email);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            $signup_error = "Email already exists!";
        } else {
            $stmt->close();
            $sql = "INSERT INTO users (first_name, last_name, email, password, phone) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            if ($stmt === false) {
                $signup_error = "Prepare failed: " . $conn->error;
            } else {
                $stmt->bind_param("sssss", $first_name, $last_name, $email, $password, $phone);
                if ($stmt->execute()) {
                    $_SESSION['success'] = "Account created successfully! Please login.";
                    $stmt->close();
                    header("Location: login.php");
                    exit();
                } else {
                    $signup_error = "Registration failed. Please try again.";
                }
            }
        }
        $stmt->close();
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        $error = "Prepare failed: " . $conn->error;
    } else {
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['email'] = $user['email'];
                $stmt->close();
                header("Location: index.php");
                exit();
            } else {
                $error = "Invalid password!";
            }
        } else {
            $error = "Email not found!";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pet Clinic Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/styles.css">
    <style>
        .nav-tabs {
            border: none;
            margin: 40px 20px;
            display: flex;
            justify-content: center;
            gap: 20px;
            list-style: none;
            padding: 0;
        }

        .nav-link {
            color: #666;
            text-decoration: none;
            padding: 10px 20px;
            position: relative;
            cursor: pointer;
            transition: color 0.3s ease;
        }

        .nav-link::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 100%;
            height: 2px;
            background: #228145;
            transform: scaleX(0);
            transition: transform 0.3s ease;
        }

        .nav-link.active {
            color: #228145;
            font-weight: 500;
        }

        .nav-link.active::after {
            transform: scaleX(1);
        }

        .tab-pane {
            display: none;
            opacity: 0;
        }

        .tab-pane.active {
            display: block;
            opacity: 1;
            transition: opacity 0.3s ease-in;
        }

        .login-container {
            padding-top: 60px;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        @media (max-width: 480px) {
            .form-row {
                grid-template-columns: 1fr;
            }
        }

        .back-btn {
            position: fixed;
            top: 20px;
            left: 20px;
            display: flex;
            align-items: center;
            gap: 5px;
            color: #666;
            text-decoration: none;
            transition: color 0.3s ease;
            z-index: 1000;
        }

        .back-btn:hover {
            color: #228145;
        }

        .back-btn i {
            font-size: 20px;
        }
    </style>
</head>

<body>
    <a href="index.php" class="back-btn">
        <i class="fas fa-arrow-left"></i>
        Back to Home
    </a>
    <div class="custom-cursor"></div>
    <div class="login-container">
        <div class="dog">
            <img src="assets/images/dog2.png" alt="Dog" class="dog-image">
            <div class="eyes">
                <div class="eye">
                    <div class="pupil"></div>
                </div>
                <div class="eye">
                    <div class="pupil"></div>
                </div>
            </div>
        </div>

        <div class="title-container">
            <img src="assets/images/logo.png" alt="Pet Logo" class="pet-logo">
            <div class="title-wrapper">
                <h1 class="main-title">
                    <span class="pet-text">pet</span>
                    <span class="crossing-text">crossing</span>
                </h1>
                <h2 class="clinic-text">ANIMAL CLINIC</h2>
            </div>
        </div>

        <?php if ($error || $signup_error): ?>
            <div class="error-message">
                <?php echo $error . $signup_error; ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="success-message">
                <?php echo $_SESSION['success'];
                unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <ul class="nav-tabs">
            <li class="nav-item">
                <a class="nav-link active" onclick="switchTab('login')">Login</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" onclick="switchTab('signup')">Sign Up</a>
            </li>
        </ul>

        <div class="tab-content">
            <!-- Login Tab -->
            <div id="login" class="tab-pane active">
                <form method="POST">
                    <div class="input-group">
                        <input type="email" name="email" placeholder=" " required>
                        <label>Email</label>
                    </div>
                    <div class="input-group">
                        <input type="password" name="password" placeholder=" " required>
                        <label>Password</label>
                    </div>
                    <button type="submit" name="login">Login</button>
                </form>
                <div class="forgot-password">
                    <a href="forgot-password.php">Forgot Password?</a>
                </div>
            </div>

            <!-- Signup Tab -->
            <div id="signup" class="tab-pane">
                <form method="POST">
                    <div class="form-row">
                        <div class="input-group">
                            <input type="text" name="first_name" placeholder=" " required>
                            <label>First Name</label>
                        </div>
                        <div class="input-group">
                            <input type="text" name="last_name" placeholder=" " required>
                            <label>Last Name</label>
                        </div>
                    </div>
                    <div class="input-group">
                        <input type="email" name="signup_email" placeholder=" " required>
                        <label>Email</label>
                    </div>
                    <div class="input-group">
                        <input type="password" name="signup_password" placeholder=" " required>
                        <label>Password</label>
                    </div>
                    <div class="input-group">
                        <input type="tel" name="phone" placeholder=" " required>
                        <label>Phone Number</label>
                    </div>
                    <button type="submit" name="signup">Sign Up</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function switchTab(tabId) {
            const tabs = document.querySelectorAll('.tab-pane');
            const links = document.querySelectorAll('.nav-link');

            links.forEach(link => link.classList.remove('active'));
            event.target.classList.add('active');

            tabs.forEach(tab => {
                if (tab.id === tabId) {
                    tab.style.display = 'block';
                    requestAnimationFrame(() => tab.classList.add('active'));
                } else {
                    tab.classList.remove('active');
                    tab.style.display = 'none';
                }
            });
        }
    </script>
    <script src="assets/js/dogAnimation.js"></script>
</body>

</html>