<?php
session_start();
include('includes/db.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    $query = "SELECT * FROM users WHERE email='$email'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);
        if (password_verify($password, $row['password'])) {
            $_SESSION['loggedin'] = true;
            $_SESSION['email'] = $email;
            $_SESSION['user_role'] = $row['role'];
            $_SESSION['user_id'] = $row['user_id'];

            session_write_close();
            if ($row['role'] === 'admin') {
                header("Location: admin/dashboard.php");
            } else {
                $redirect_url = isset($_POST['redirect_url']) ? $_POST['redirect_url'] : 'index.php';
                header("Location: $redirect_url");
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

require 'includes/header.php';
?>
<link rel="stylesheet" href="css/style.css">


<!-- Premium Hero Section -->
<section class="hero">
    <img src="images/home_3.png" alt="Hero Background" class="hero-bg-img">
    <div class="hero-overlay"></div>
    <div class="hero-container">
        <div class="hero-content reveal">
            <h1>Explore India <span>On Two Wheels.</span></h1>
            <p>Premium bike rentals for the modern explorer. From city commutes to mountain trails, we have the perfect
                ride for every journey.</p>
        </div>
    </div>
</section>


<!-- Redesigned Search Section -->
<div id="search-section" class="search-float reveal">
    <?php
    $city_query = "SELECT DISTINCT city FROM abike WHERE city IS NOT NULL AND city != ''";
    $city_result = $conn->query($city_query);
    ?>
    <form action="agencies.php" method="GET" onsubmit="return validateDates()" class="search-grid">
        <div class="search-item">
            <label>City Location</label>
            <select name="city" required>
                <option value="" disabled selected>Choose Your City</option>
                <?php while ($row = $city_result->fetch_assoc()): ?>
                    <option value="<?php echo htmlspecialchars($row['city']); ?>">
                        <?php echo htmlspecialchars($row['city']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="search-item">
            <label>Pick-up Date</label>
            <input type="date" name="start_date" min="<?php echo date('Y-m-d'); ?>" required>
        </div>
        <div class="search-item">
            <label>Return Date</label>
            <input type="date" name="end_date" min="<?php echo date('Y-m-d'); ?>" required>
        </div>
        <button type="submit" class="search-go">Search Bikes <i class="fas fa-arrow-right"></i></button>
    </form>
</div>

<!-- Updated Timeline -->
<section id="how-it-works" class="how-it-works reveal">
    <div class="section-title">
        <span class="sub-heading">Guidelines</span>
        <h2>How to Start Your Ride</h2>
    </div>

    <div class="process-timeline">
        <div class="timeline-item">
            <div class="timeline-dot"></div>
            <div class="timeline-content">
                <h3>01. Select City & Dates</h3>
                <p>Choose your destination and the duration of your trip to see available partners.</p>
            </div>
        </div>
        <div class="timeline-item">
            <div class="timeline-dot"></div>
            <div class="timeline-content">
                <h3>02. Pick Your Machine</h3>
                <p>Browse our extensive fleet and choose the bike that fits your style and budget.</p>
            </div>
        </div>
        <div class="timeline-item">
            <div class="timeline-dot"></div>
            <div class="timeline-content">
                <h3>03. Quick KYC</h3>
                <p>Choose a valid driving license and ID proof for verification.</p>
            </div>
        </div>
        <div class="timeline-item">
            <div class="timeline-dot"></div>
            <div class="timeline-content">
                <h3>04. Ride Away</h3>
                <p>Collect your keys from the designated center and begin your adventure.</p>
            </div>
        </div>
    </div>
</section>

<!-- Hotel Advertisement Section -->
<section id="hotels" class="featured-fleet reveal">
    <div class="section-title">
        <span class="sub-heading">Partner Stays</span>
        <h2>Premium Hotel Partners</h2>
    </div>
    <div class="fleet-grid">
        <div class="hotel-card">
            <div class="hotel-images">
                <img src="https://images.unsplash.com/photo-1566073771259-6a8506099945?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80"
                    class="hotel-img-main">
                <img
                    src="https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?ixlib=rb-1.2.1&auto=format&fit=crop&w=400&q=80">
                <img
                    src="https://images.unsplash.com/photo-1540518614846-7eded433c457?ixlib=rb-1.2.1&auto=format&fit=crop&w=400&q=80">
            </div>
            <div class="hotel-info">
                <h3>The Grand Dwarka</h3>
                <p>Luxury suites with breathtaking sea views. 15% OFF for UrbanRide explorers.</p>
            </div>
        </div>
        <div class="hotel-card">
            <div class="hotel-images">
                <img src="https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80"
                    class="hotel-img-main">
                <img
                    src="https://images.unsplash.com/photo-1571896349842-33c89424de2d?ixlib=rb-1.2.1&auto=format&fit=crop&w=400&q=80">
                <img
                    src="https://images.unsplash.com/photo-1564501049412-61c2a3083791?ixlib=rb-1.2.1&auto=format&fit=crop&w=400&q=80">
            </div>
            <div class="hotel-info">
                <h3>Heritage Inn</h3>
                <p>A blend of tradition and comfort in the heart of the city.</p>
            </div>
        </div>
        <div class="hotel-card">
            <div class="hotel-images">
                <img src="https://images.unsplash.com/photo-1517840901100-8179e982ad4e?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80"
                    class="hotel-img-main">
                <img
                    src="https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?ixlib=rb-1.2.1&auto=format&fit=crop&w=400&q=80">
                <img
                    src="https://images.unsplash.com/photo-1551882547-ff43c638f614?ixlib=rb-1.2.1&auto=format&fit=crop&w=400&q=80">
            </div>
            <div class="hotel-info">
                <h3>Oceanic Breeze</h3>
                <p>Coastal tranquility meets modern design. Exclusive rider lounge available.</p>
            </div>
        </div>
</section>

<!-- Lite Search Section -->
<section class="lite-search reveal" style="padding: 100px 7% 60px; background: var(--bg-sub);">
    <div class="section-title">
        <span class="sub-heading">Quick Find</span>
        <h2 style="font-family: var(--font-heading); font-size: 2rem; margin-bottom: 30px;">GOOO!!</h2>
    </div>
    <div class="lite-search-compact">
        <form action="agencies.php" method="GET" onsubmit="return validateDates()" class="search-grid">
            <div class="search-item">
                <label>Location</label>
                <select name="city" required>
                    <option value="" disabled selected>Select City</option>
                    <?php 
                    $city_result->data_seek(0);
                    while ($row = $city_result->fetch_assoc()): ?>
                        <option value="<?php echo htmlspecialchars($row['city']); ?>">
                            <?php echo htmlspecialchars($row['city']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="search-item">
                <label>Pickup Date</label>
                <input type="date" name="start_date" min="<?php echo date('Y-m-d'); ?>" required>
            </div>
            <div class="search-item">
                <label>Return Date</label>
                <input type="date" name="end_date" min="<?php echo date('Y-m-d'); ?>" required>
            </div>
            <button type="submit" class="search-go">Search Bikes</button>
        </form>
    </div>
</section>

<script>
    function validateDates() {
        const start = document.getElementsByName('start_date')[0].value;
        const end = document.getElementsByName('end_date')[0].value;
        if (new Date(start) > new Date(end)) {
            alert('Return date cannot be before pick-up date.');
            return false;
        }
        return true;
    }

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('revealed');
            }
        });
    }, { threshold: 0.1 });
    document.querySelectorAll('.reveal').forEach(el => observer.observe(el));
</script>

<?php require 'includes/footer.php' ?>