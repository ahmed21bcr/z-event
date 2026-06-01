<?php
require_once '../config/db.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$filtre_thematique = $_GET['thematique'] ?? '';
$filtre_date = $_GET['date'] ?? '';
$filtre_streamer = $_GET['streamer'] ?? '';

$sql = "
    SELECT l.id_live, l.nom_live, l.date_live, l.heure_live, l.description, l.PEGI,
           u.nom, u.prenom, u.nom_chaine
    FROM live l
    JOIN user u ON l.id_user = u.id_user
    WHERE 1=1
";

$params = [];

if (!empty($filtre_thematique)) {
    $sql .= " AND l.id_live IN (
        SELECT id_live FROM live_thematique WHERE id_thematique = :thematique
    )";
    $params[':thematique'] = $filtre_thematique;
}

if (!empty($filtre_date)) {
    $sql .= " AND l.date_live = :date";
    $params[':date'] = $filtre_date;
}

if (!empty($filtre_streamer)) {
    $sql .= " AND l.id_user = :streamer";
    $params[':streamer'] = $filtre_streamer;
}

$sql .= " ORDER BY l.date_live ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$lives = $stmt->fetchAll();

echo json_encode($lives);