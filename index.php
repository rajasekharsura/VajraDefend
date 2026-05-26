<?php
// Basic site config
define('SITE_NAME', 'My Website');
define('SITE_VERSION', '1.0');

// Simple routing
$page = isset($_GET['page']) ? basename($_GET['page']) : 'home';
$allowed_pages = ['home', 'about', 'contact'];
if (!in_array($page, $allowed_pages)) {
    $page = 'home';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars(SITE_NAME) ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<header>
    <div class="container">
        <h1><?= SITE_NAME ?></h1>
        <nav>
            <a href="?page=home">Home</a>
            <a href="?page=about">About</a>
            <a href="?page=contact">Contact</a>
        </nav>
    </div>
</header>

<main class="container">
    <?php include "pages/{$page}.php"; ?>
</main>

<footer>
    <div class="container">
        <p>&copy; <?= date('Y') ?> <?= SITE_NAME ?>. All rights reserved.</p>
    </div>
</footer>

</body>
</html>
