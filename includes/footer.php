    </main>

    <footer class="site-footer">
        <div class="container footer-grid">
            <div class="footer-brand">
                <a class="logo" href="<?= e($assetPrefix ?? '') ?>index.php">
                    <img class="logo-mark" src="<?= e($assetPrefix ?? '') ?>assets/images/DineSpot-logo.jpg" alt="DineSpot Logo">
                    <span class="logo-text"><?= e(SITE_NAME) ?></span>
                </a>
                <p><?= e(SITE_TAGLINE) ?></p>
            </div>
            <div>
                <h2>Explore</h2>
                <ul class="footer-links">
                    <li><a href="<?= e($assetPrefix ?? '') ?>restaurants/index.php">Browse Restaurants</a></li>
                    <li><a href="<?= e($assetPrefix ?? '') ?>restaurants/search.php">Search</a></li>
                    <li><a href="<?= e($assetPrefix ?? '') ?>guide.php">User Guide</a></li>
                    <?php if (is_admin()): ?>
                        <li><a href="<?= e($assetPrefix ?? '') ?>charts/index.php">Insights</a></li>
                    <?php endif; ?>
                    <li><a href="<?= e(client_path('register.php')) ?>">Create Account</a></li>
                    <li><a href="<?= e($assetPrefix ?? '') ?>sitemap.php">Site Map</a></li>
                </ul>
            </div>

            <div>
                <h2>Company</h2>
                <ul class="footer-links">
                    <li><a href="<?= e($assetPrefix ?? '') ?>about.php">About Us</a></li>
                    <li><a href="<?= e($assetPrefix ?? '') ?>faq.php">FAQ</a></li>
                    <li><a href="<?= e($assetPrefix ?? '') ?>contact.php">Contact</a></li>
                </ul>
            </div>

            <div>
                <h2>Legal</h2>
                <ul class="footer-links">
                    <li><a href="<?= e($assetPrefix ?? '') ?>privacy.php">Privacy Policy</a></li>
                    <li><a href="<?= e($assetPrefix ?? '') ?>terms.php">Terms of Service</a></li>
                </ul>
            </div>
        </div>

        <div class="container footer-bottom">
            <p>&copy; <?= date('Y') ?> <?= e(SITE_NAME) ?>. Tuong Nguyen Pham. All rights reserved.</p>
            <p><?= e(SITE_EMAIL) ?> &middot; <?= e(SITE_PHONE) ?></p>
        </div>
    </footer>

    <script src="<?= e($assetPrefix ?? '') ?>assets/js/main.js"></script>
</body>
</html>
