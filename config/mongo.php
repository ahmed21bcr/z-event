<?php
require_once __DIR__ . '/../vendor/autoload.php';

try {
    $mongoUrl = getenv('MONGO_URL') ?: 'mongodb://localhost:27017';
    $mongoClient = new MongoDB\Client($mongoUrl);
    $mongoDB = $mongoClient->z_event_stats;
} catch (Exception $e) {
    die("Erreur de connexion MongoDB : " . $e->getMessage());
}