<?php

define('ROOT_PATH', __DIR__);

session_start();
require_once 'config/db.php';
require_once 'config/mongo.php';

$page = $_GET['page'] ?? 'home';
$pages_autorisees = ['home', 'lives', 'detail_live', 'connexion', 'deconnexion', 'espace_streamer', 'espace_admin'];
if (!in_array($page, $pages_autorisees)) {
    $page = 'home';
}

// Traitement PHP pur AVANT tout HTML
ob_start();
include "pages/$page.php";
$content = ob_get_clean();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Z-Event</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@600;700&family=Inter:wght@400;500&display=swap" rel="stylesheet">
</head>
<body>
<?php echo $content; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/main.js"></script>
</body>
</html>