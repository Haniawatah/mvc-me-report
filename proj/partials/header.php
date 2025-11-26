<?php
$siteBase = rtrim($baseVar ?? '', '/'); // e.g. /~maix24/dbwebb-kurser/mvc/me/report/public
$asset = fn($path) => ($siteBase ? $siteBase : '') . '/proj/assets/' . $path;
?>
<!doctype html>
<html lang="sv">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Book Tracker</title>

    <!-- Load site public CSS first (so site navbar styles are preserved) -->
    <?php if ($siteBase): ?>
        <link rel="stylesheet" href="<?= htmlspecialchars($siteBase . '/css/style.css') ?>">
    <?php else: ?>
        <link rel="stylesheet" href="/css/style.css">
    <?php endif; ?>

    <!-- Then project-specific CSS (overrides if needed) -->
    <link rel="stylesheet" href="<?= htmlspecialchars($asset('css/style.css')) ?>">
</head>
<body>

<!-- Top site header (matches templates/base.html.twig) -->
<header>
    <div class="container">
        <h1>MVC Course Report</h1>
    </div>
</header>

<nav>
    <div class="container">
        <ul>
            <li><a href="<?= htmlspecialchars($siteBase ?: '/') ?>">Home</a></li>
            <li><a href="<?= htmlspecialchars(($siteBase ?: '') . '/about') ?>">About</a></li>
            <li><a href="<?= htmlspecialchars(($siteBase ?: '') . '/report') ?>">Report</a></li>
            <li><a href="<?= htmlspecialchars(($siteBase ?: '') . '/card') ?>">Card Game</a></li>
            <li><a href="<?= htmlspecialchars(($siteBase ?: '') . '/game') ?>">Game 21</a></li>
            <li><a href="<?= htmlspecialchars(($siteBase ?: '') . '/lucky') ?>">Lucky Number</a></li>
            <li><a href="<?= htmlspecialchars(($siteBase ?: '') . '/library') ?>">Library</a></li>
            <li><a href="<?= htmlspecialchars(($siteBase ?: '') . '/api') ?>">API</a></li>
            <li><a href="<?= htmlspecialchars(($siteBase ?: '') . '/metrics') ?>">Metrics</a></li>
            <li><a href="<?= htmlspecialchars(($siteBase ?: '') . '/blackjack') ?>">Black Jack</a></li>
        </ul>
    </div>
</nav>

<?php
// Start wrapper with project sidebar + content area so proj pages show full top navbar and project links on the side.
?>
<div class="site-wrapper">
    <aside class="proj-sidebar">
        <div class="proj-sidebar-inner">
            <a class="brand" href="<?= htmlspecialchars(($siteBase ?: '') . '/proj') ?>">Book Tracker</a>
            <ul>
                <li><a href="<?= htmlspecialchars(($siteBase ?: '') . '/proj') ?>">Hem</a></li>
                <li><a href="<?= htmlspecialchars(($siteBase ?: '') . '/proj/about') ?>">Om</a></li>
            </ul>
        </div>
    </aside>

    <div class="site-content">
    <!-- proj page content follows -->
</div>
</div>
</body>
</html>
