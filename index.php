<?php
session_start();
include('includes/db.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // Check if user exists
    $query = "SELECT * FROM users WHERE email='$email'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);
        if (password_verify($password, $row['password'])) {
            // Password is correct, set session
            $_SESSION['loggedin'] = true;
            $_SESSION['email'] = $email;
            $_SESSION['user_role'] = $row['role']; 
            $_SESSION['user_id'] = $row['user_id']; 
            
            // Redirect based on role
            if ($row['role'] === 'admin') {
                $dashboard_url = isset($row['dashboard_url']) ? $row['dashboard_url'] : 'admin_default_dashboard.php';
                header("Location: $dashboard_url");
            } else {
                header("Location: index.php");
            }
            exit;
        } else {
            header("Location: index.php?error=incorrect_password");
            exit();
        }
    } else {
        header("Location: register.php?error=invalid_email");
        exit();
    }
}

$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
$user_name = "Guest";

if ($user_id) {
    $query = "SELECT name FROM users WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $user_name = $user['name'];
    }
}

require 'includes/header.php';
?>

<?php if (!isset($_SESSION['loggedin'])): ?>
<div id="loginModal" class="glass-effect">
    <div class="modal-content glass-effect">
        <div class="logo-container">
            <span class="white" style="color: white; font-family: 'Bebas Neue', sans-serif;">U</span>
            <span class="white" style="color: white; font-family: 'Bebas Neue', sans-serif;">R</span>
            <span class="white" style="color: white; font-family: 'Bebas Neue', sans-serif;">B</span>
            <span class="white" style="color: white; font-family: 'Bebas Neue', sans-serif;">A</span>
            <span class="white" style="color: white; font-family: 'Bebas Neue', sans-serif;">N</span>
            <span class="orange-italic" style="color: var(--primary); font-family: 'Bebas Neue', sans-serif; text-decoration: underline;">R</span>
            <span class="white" style="color: white; font-family: 'Bebas Neue', sans-serif;">I</span>
            <span class="white" style="color: white; font-family: 'Bebas Neue', sans-serif;">D</span>
            <span class="white" style="color: white; font-family: 'Bebas Neue', sans-serif;">E</span>
        </div>
        <div id="login-container">
            <h2 style="font-family: var(--font-heading); color: white; font-size: 2.5rem; margin-bottom: 2rem;">Welcome Back</h2>
            
            <?php if (isset($_GET['error'])): ?>
                <div class="error-toast">
                    <?php
                    if ($_GET['error'] == 'incorrect_password') echo "Incorrect email or password.";
                    elseif ($_GET['error'] == 'invalid_email') echo "Email not found. Please register.";
                    ?>
                </div>
            <?php endif; ?>

            <form action="index.php" method="POST">
                <div class="input-group">
                    <i class="fas fa-envelope"></i>
                    <input type="email" name="email" required placeholder="Email Address">
                </div>
                <div class="input-group">
                    <i class="fas fa-lock"></i>
                    <input type="password" name="password" required placeholder="Password">
                </div>
                <button type="submit" class="btn-primary">Login Now</button>
            </form>
            <div class="modal-footer">
                <p>New here? <a href="register.php">Create Account</a></p>
                <a href="request_reset.php" class="forgot-pass">Forgot Password?</a>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>


<!-- Hero Section -->
<section id="hero" class="hero" style="height: 100vh; background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('images/road.png') no-repeat center center/cover;">
    <div class="content">
        <h1 id="greet1" style="font-family: var(--font-heading); font-size: 5rem; color: var(--primary);">स्वागतम्</h1>
        <h1 id="greet" style="font-family: var(--font-heading); font-size: 3rem; margin-top: -10px;"> <?php echo htmlspecialchars($user_name); ?>!</h1>
        <p id="quote" style="font-size: 1.2rem; font-weight: 300; letter-spacing: 1px; max-width: 600px; margin: 10px auto;">
            <?php
            $pick_up_lines = [
                "Your journey starts with a perfect ride! 🏍",
                "Feel the wind, chase the freedom! ✊",
                "Fuel your wanderlust, one ride at a time! 🎯",
                "Adventure awaits—start your engine now! 🗺",
                "The road is calling, and we’ve got your ride! 🛵"
            ];
            echo $pick_up_lines[array_rand($pick_up_lines)];
            ?>
        </p>

        <script>
            const greetings = [
                { text: 'स्वागतम्', color: '#FF4D01' },
                { text: 'Welcome', color: '#00D1FF' },
                { text: 'સ્વાગત છે', color: '#FFB800' },
                { text: 'नमस्कार', color: '#FF4D01' },
                { text: 'Suvagatham', color: '#00D1FF' }
            ];
            let gIndex = 0;
            const gElement = document.getElementById('greet1');
            setInterval(() => {
                gElement.style.opacity = 0;
                setTimeout(() => {
                    gIndex = (gIndex + 1) % greetings.length;
                    gElement.innerText = greetings[gIndex].text;
                    gElement.style.color = greetings[gIndex].color;
                    gElement.style.opacity = 1;
                }, 500);
            }, 4000);
        </script>
        </div>

    <!-- Select City Drawer -->
            <div class="city-selector-container">
                <div class="city-selector-trigger glass-effect" onclick="toggleCitySelector(event)">
                    <div class="selector-content">
                        <i class="fas fa-location-dot"></i>
                        <span>Where's your next ride?</span>
                    </div>
                    <i class="fas fa-chevron-down arrow-icon"></i>
                </div>
                <div class="city-drawer glass-effect">
                    <div class="drawer-header">
                        <h3>Choose Your City</h3>
                        <i class="fas fa-times" onclick="toggleCitySelector(event)"></i>
                    </div>
                    <div class="city-grid">
                        <a href="Dwarka.php" class="city-card">
                            <i class="fas fa-gopuram"></i>
                            <span>Dwarka</span>
                        </a>
                        <a href="Somnath.php" class="city-card">
                            <i class="fas fa-om"></i>
                            <span>Somnath</span>
                        </a>
                        <a href="Diu.php" class="city-card">
                            <i class="fas fa-umbrella-beach"></i>
                            <span>Diu</span>
                        </a>
                        <a href="Goa.php" class="city-card">
                            <i class="fas fa-cocktail"></i>
                            <span>Goa</span>
                        </a>
                    </div>
                </div>
            </div>
        </section>
    

        

    <!-- User Reviews Section -->
    <section id="reviews" class="section-padding dark-bg">
        <div class="section-header centered">
            <span class="sub-heading">Testimonials</span>
            <h2 class="main-heading">What Riders Say</h2>
        </div>
        <div class="reviews-grid">
            <div class="review-card glass-effect hover-lift">
                <div class="user-info">
                    <img src="images/assured.png" alt="User">
                    <div>
                        <h4>Rohan Sharma</h4>
                        <div class="stars">
                            <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                        </div>
                    </div>
                </div>
                <p>"The booking was seamless. The bike was in perfect condition and the pick-up process was very quick. Highly recommended!"</p>
            </div>
            <div class="review-card glass-effect hover-lift">
                <div class="user-info">
                    <img src="images/assured.png" alt="User">
                    <div>
                        <h4>Sneha Patel</h4>
                        <div class="stars">
                            <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star-half-alt"></i>
                        </div>
                    </div>
                </div>
                <p>"Affordable prices and great service. I rented a Bullet for my Dwarka trip and it was amazing. No hidden costs!"</p>
            </div>
        </div>
    </section>

    <section id="offer-section" class="offer-section">
        <h2>What we offer?</h2>
        <div class="offer-container">
            <div class="offer-card">
                <img src="images/assured.png" alt="Assured Bikes">
                <h3>Assured Bikes</h3>
                <p>Bikes that are Assured will be of the highest possible quality.</p>
            </div>
            <div class="offer-card">
                <img src="images/money-back.png" alt="Money Back Guarantee">
                <h3>Money Back Guarantee</h3>
                <p>Anything that goes wrong, do not fret! We guarantee that you get your money back in one piece!</p>
            </div>
            <div class="offer-card">
                <img src="images/deposit.png" alt="Deposit Assurance">
                <h3>Deposit Assurance</h3>
                <p>With our payment gateway, be 100% sure about all your transactions within the website.</p>
            </div>
            <div class="offer-card">
                <img src="images/lowest-price.png" alt="Lowest Price Guarantee">
                <h3>Lowest Price Guarantee</h3>
                <p>Get all your favorite bikes for rent at the lowest possible price on the bike rental market.</p>
            </div>
        </div>
        
    </section>

    <section id="how-it-works" class="how-it-works">
        <h2>How it works</h2>
        <div class="steps-container">
            <div class="step">
                <img src="images/search-icon.png" alt="Search Icon">
                <h3>Search</h3>
                <p>Set the date of your ride and then search for the bike that you want.</p>
            </div>
            <div class="arrow">&#10095;</div>
            <div class="step">
                <img src="images/select-icon.png" alt="Select Icon">
                <h3>Select</h3>
                <p>Choose out of various bikes that best suits the trip you’re about to take.</p>
            </div>
            <div class="arrow">&#10095;</div>
            <div class="step">
                <img src="images/pickup-icon.png" alt="Pick-up Icon">
                <h3>Pick-up</h3>
                <p>Get suited up and head to the pick-up location to get your ride.</p>
            </div>
            <div class="arrow">&#10095;</div>
            <div class="step">
                <img src="images/ride-icon.png" alt="Ride Icon">
                <h3>Ride</h3>
                <p>Get ready to roll and have a nice time tripping!</p>
            </div>
        </div>
    </section>

<?php require 'includes/footer.php'?>

        
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script src="assets/js/script.js"></script>

<script>  // Get the modal element
        var modal = document.getElementById('loginModal');

        <?php if (!isset($_SESSION['loggedin'])): ?>
        // Display the modal if the user is not logged in
        window.onload = function() {
            modal.style.display = "flex"; // Show the modal as flex for centering
            // When modal opens
document.body.classList.add('modal-open');

// When modal closes
// document.body.classList.remove('modal-open');
        }
    

        <?php endif; ?>
    </script>
</body>
</html>