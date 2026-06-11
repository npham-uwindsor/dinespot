<?php

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

$pageTitle = 'Privacy Policy';
$pageDescription = 'Read how DineSpot collects, uses, and protects your personal information.';
$bodyClass = 'page-privacy';

require_once __DIR__ . '/includes/header.php';
?>

<section class="page-hero">
    <div class="container">
        <h1>Privacy Policy</h1>
        <p class="lead">Your privacy matters to us. This policy explains what information DineSpot collects and how we use it.</p>
        <p><small>Last updated: June 11, 2026</small></p>
    </div>
</section>

<section class="page-content">
    <div class="container">
        <div class="content-card legal-content">
            <h2>1. Information We Collect</h2>
            <p>When you use DineSpot, we may collect the following types of information:</p>
            <ul>
                <li><strong>Account information:</strong> name, email address, password (stored securely), and optional profile details.</li>
                <li><strong>Reservation data:</strong> restaurant selected, date, time, party size, and special requests.</li>
                <li><strong>User content:</strong> reviews, ratings, and saved favourites.</li>
                <li><strong>Technical data:</strong> browser type, device information, IP address, and pages visited for security and performance.</li>
            </ul>

            <h2>2. How We Use Your Information</h2>
            <p>We use your information to:</p>
            <ul>
                <li>Create and manage your DineSpot account</li>
                <li>Process and display reservation requests to participating restaurants</li>
                <li>Publish and moderate reviews you submit</li>
                <li>Respond to support requests and improve our services</li>
                <li>Protect the platform against fraud, abuse, and unauthorized access</li>
            </ul>

            <h2>3. Information Sharing</h2>
            <p>We do not sell your personal information. We may share limited data with:</p>
            <ul>
                <li><strong>Restaurants:</strong> when you make a reservation, so they can confirm and manage your booking.</li>
                <li><strong>Service providers:</strong> who help us host, secure, or maintain the platform under confidentiality obligations.</li>
                <li><strong>Legal authorities:</strong> when required by law or to protect the rights and safety of users.</li>
            </ul>

            <h2>4. Data Retention</h2>
            <p>We retain account and reservation records for as long as your account is active or as needed to provide services, comply with legal obligations, and resolve disputes. You may request account deletion by contacting us.</p>

            <h2>5. Cookies &amp; Similar Technologies</h2>
            <p>DineSpot may use session cookies to keep you signed in and remember basic preferences. You can control cookies through your browser settings, though some features may not work correctly if cookies are disabled.</p>

            <h2>6. Your Rights</h2>
            <p>Depending on your location, you may have the right to access, correct, or delete personal information we hold about you. To make a request, email <?= e(SITE_EMAIL) ?> from the address associated with your account.</p>

            <h2>7. Security</h2>
            <p>We use reasonable administrative, technical, and physical safeguards to protect your data. No method of transmission over the internet is completely secure, so we cannot guarantee absolute security.</p>

            <h2>8. Children's Privacy</h2>
            <p>DineSpot is not intended for users under 13 years of age. We do not knowingly collect personal information from children.</p>

            <h2>9. Changes to This Policy</h2>
            <p>We may update this Privacy Policy from time to time. Material changes will be posted on this page with an updated effective date.</p>

            <h2>10. Contact Us</h2>
            <p>If you have questions about this policy, contact us at <a href="mailto:<?= e(SITE_EMAIL) ?>"><?= e(SITE_EMAIL) ?></a>.</p>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
