<?php $base_url = (isset($base_url)) ? $base_url : ((strpos($_SERVER['PHP_SELF'], '/admin/') !== false) ? '../' : ''); ?>
    </main>

    <section id="footer">
        <div class="footer-grid">
            <div class="footer-box">
                <a href="<?php echo $base_url; ?>index.php" class="logo" style="color: white; margin-bottom: 20px;">
                    Urban<span>Ride</span>
                </a>
                <p>Revolutionizing urban mobility with premium bike rentals. Sustainable, convenient, and built for your journey.</p>
                <div class="share" style="display: flex; gap: 15px; margin-top: 25px;">
                    <a href="#" class="fab fa-facebook-f" style="color: #94A3B8;"></a>
                    <a href="#" class="fab fa-twitter" style="color: #94A3B8;"></a>
                    <a href="#" class="fab fa-instagram" style="color: #94A3B8;"></a>
                    <a href="#" class="fab fa-linkedin" style="color: #94A3B8;"></a>
                </div>
            </div>
            
            <div class="footer-box footer-links">
                <h3>Explore</h3>
                <a href="<?php echo $base_url; ?>index.php">Home</a>
                <a href="<?php echo $base_url; ?>index.php#fleet">Our Fleet</a>
                <a href="<?php echo $base_url; ?>index.php#how-it-works">How It Works</a>
                <a href="<?php echo $base_url; ?>index.php#search-section">Book Now</a>
            </div>

            <div class="footer-box">
                <h3>Contact</h3>
                <p style="margin-bottom: 10px;"><i class="fas fa-location-dot" style="margin-right: 10px;"></i> Dwarka, Gujarat, India</p>
                <p style="margin-bottom: 10px;"><i class="fas fa-phone" style="margin-right: 10px;"></i> +91 9106093128</p>
                <p style="margin-bottom: 10px;"><i class="fas fa-envelope" style="margin-right: 10px;"></i> rkgokani005@gmail.com</p>
            </div>

            <div class="footer-box">
                <h3>Legal</h3>
                <a href="privacy&policy.html" style="display: block; color: #94A3B8; margin-bottom: 10px;">Privacy Policy</a>
                <a href="terms.html" style="display: block; color: #94A3B8; margin-bottom: 10px;">Terms of Service</a>
                <a href="#" style="display: block; color: #94A3B8;">Safety Tips</a>
            </div>
            <div class="footer-box">
                <h3>Agent Login</h3>
                <a href="/BikeRentingwebsite(PROJECT)/agency/index.php" style="display: block; color: #94A3B8; margin-bottom: 10px;">Agent Login</a>
            </div>
        </div>
        <div class="credit"> 
            &copy; <?php echo date('Y'); ?> <span>Team UrbanRide</span>. All rights reserved. Built with &hearts; for the road.
        </div>
    </section>

    <!-- script.js should only be included once at the end -->
    <script src="<?php echo $base_url; ?>assets/js/script.js"></script>
</body>
</html>