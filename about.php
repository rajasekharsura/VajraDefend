<?php // pages/about.php ?>

<h2 style="margin-bottom:20px;">About Us</h2>

<div class="cards">
    <div class="card">
        <h3>Our Mission</h3>
        <p>We build fast, reliable, and accessible web experiences hosted on scalable cloud infrastructure.</p>
    </div>
    <div class="card">
        <h3>Our Stack</h3>
        <p>Apache 2.4 · PHP 8.x · AWS EC2 · Linux · Tor Hidden Service</p>
    </div>
    <div class="card">
        <h3>Server Info</h3>
        <p>
            PHP: <?= phpversion() ?><br>
            Server: <?= $_SERVER['SERVER_SOFTWARE'] ?? 'Apache' ?><br>
            OS: <?= PHP_OS ?>
        </p>
    </div>
</div>
