<?php $base_url = (isset($base_url)) ? $base_url : ((strpos($_SERVER['PHP_SELF'], '/admin/') !== false) ? '../' : ''); ?>
    </main>

    <section id="footer">
        <div class="box-container">
            <div class="box">
                <h3>About us</h3>
                <p>We aim to transform urban transportation by making bike rentals accessible, secure, and affordable for everyone.</p>
            </div>
            <div class="box">
                <h3>Quick links</h3>
                <a href="<?php echo $base_url; ?>index.php">home</a>
                <a href="<?php echo $base_url; ?>index.php#vehicles">vehicles</a>
                <a href="<?php echo $base_url; ?>index.php#services">services</a>
                <a href="<?php echo $base_url; ?>index.php#featured">featured</a>
                <a href="<?php echo $base_url; ?>index.php#reviews">reviews</a>
                <a href="<?php echo $base_url; ?>index.php#contact">contact</a>
            </div>
            <div class="box">
                <h3>Contact info</h3>
                <a href="#"><i class="fas fa-phone"></i> +91 9106093128</a>
                <a href="#"><i class="fas fa-phone"></i> +91 9726224163</a>
                <a href="#"><i class="fas fa-envelope"></i> rkgokani005@gmail.com</a>
                <a href="#"><i class="fas fa-location-dot"></i> dwarka, india - 361335</a>
            </div>
            <div class="box">
                <h3>Follow us</h3>
                <div class="share">
                    <a href="#" class="fab fa-facebook-f"></a>
                    <a href="#" class="fab fa-twitter"></a>
                    <a href="#" class="fab fa-instagram"></a>
                    <a href="#" class="fab fa-linkedin"></a>
                </div>
            </div>
        </div>
        <div class="credit"> created by <span>Team urbanride </span> | all rights reserved </div>
    </section>

    <!-- script.js should only be included once at the end -->
    <script src="<?php echo $base_url; ?>assets/js/script.js"></script>
</body>
</html>