<?php
$root_path = trim(__DIR__);
require($root_path . '/vendor/autoload.php');
$dotenv = Dotenv\Dotenv::createImmutable($root_path);
$dotenv->load();
$relative_root = $_ENV['ROOT'];

$settings = Settings::getInstance();
$current_year = date('Y');
?>

<footer class="footer mt-3 bg-dark w-100">
    <div class="d-flex justify-content-center text-warning w-100 pt-3">
        <ul class="list-unstyled text-center">
            <li><a class="text-decoration-none text-warning" href="<?= $relative_root?>about.php">Developer Info</a></li>
            <li><?= $settings->getSiteLogo() . " $current_year" ?></li>
        </ul>
    </div>    
</footer>