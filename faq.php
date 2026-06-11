<?php

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

$pageTitle = 'FAQ';
$pageDescription = 'Frequently asked questions about DineSpot accounts, reservations, reviews, and privacy.';
$bodyClass = 'page-faq';

$faqs = [
    [
        'question' => 'What is DineSpot?',
        'answer' => 'DineSpot is a restaurant discovery and reservation platform. You can browse Canadian restaurants, read diner reviews, save favourites, and request table reservations from one account.',
    ],
    [
        'question' => 'Do I need an account to browse restaurants?',
        'answer' => 'No. Anyone can browse and search restaurant listings without signing in. An account is required to write reviews, save favourites, and make reservations.',
    ],
    [
        'question' => 'How do reservations work?',
        'answer' => 'When you request a reservation, DineSpot sends the details to the restaurant for approval. You can track pending, approved, and rejected reservations from your account dashboard. Approved reservations should be treated as confirmed bookings unless the restaurant contacts you directly.',
    ],
    [
        'question' => 'Can I cancel or change a reservation?',
        'answer' => 'Yes. Sign in and open your reservations page to cancel a booking or view its status. For last-minute changes on the day of your visit, we recommend contacting the restaurant directly as well.',
    ],
    [
        'question' => 'How are reviews moderated?',
        'answer' => 'Reviews must be tied to a registered DineSpot account. Our admin team may remove reviews that contain abusive language, spam, or content that violates our community guidelines.',
    ],
    [
        'question' => 'Is my personal information shared with restaurants?',
        'answer' => 'When you make a reservation, we share only the information needed to honour your booking — such as your name, party size, date, time, and contact details. We do not sell your personal data. See our Privacy Policy for full details.',
    ],
    [
        'question' => 'How can a restaurant join DineSpot?',
        'answer' => 'Restaurant owners or managers can email ' . SITE_EMAIL . ' to ask about joining the platform. Our team will help set up your listing and reservation workflow.',
    ],
    [
        'question' => 'Who do I contact for technical support?',
        'answer' => 'Email ' . SITE_EMAIL . ' with your account email and a brief description of the issue. Our support team typically responds within one business day.',
    ],
];

require_once __DIR__ . '/includes/header.php';
?>

<section class="page-hero">
    <div class="container">
        <h1>Frequently Asked Questions</h1>
        <p class="lead">Quick answers about accounts, reservations, reviews, and using DineSpot.</p>
    </div>
</section>

<section class="page-content">
    <div class="container">
        <div class="faq-list" role="list">
            <?php foreach ($faqs as $index => $faq): ?>
                <article class="faq-item" role="listitem">
                    <button
                        class="faq-question"
                        type="button"
                        aria-expanded="false"
                        aria-controls="faq-answer-<?= $index ?>"
                        id="faq-question-<?= $index ?>"
                    >
                        <?= e($faq['question']) ?>
                    </button>
                    <div
                        class="faq-answer"
                        id="faq-answer-<?= $index ?>"
                        role="region"
                        aria-labelledby="faq-question-<?= $index ?>"
                    >
                        <p><?= e($faq['answer']) ?></p>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>

        <div class="content-card" style="margin-top: 1.5rem;">
            <h2>Still have questions?</h2>
            <p>Email us at <a href="mailto:<?= e(SITE_EMAIL) ?>"><?= e(SITE_EMAIL) ?></a> or call <?= e(SITE_PHONE) ?> during support hours.</p>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
