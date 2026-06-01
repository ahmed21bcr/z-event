<?php
require_once __DIR__ . '/../controllers/LiveController.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$thematique = $_GET['thematique'] ?? '';
$date       = $_GET['date'] ?? '';
$streamer   = $_GET['streamer'] ?? '';

$liveController = new LiveController();
$lives = $liveController->filter($thematique, $date, $streamer);

$result = array_map(fn($live) => [
    'id_live'    => $live->id_live,
    'nom_live'   => $live->nom_live,
    'date_live'  => $live->date_live,
    'heure_live' => $live->heure_live,
    'PEGI'       => $live->pegi,
    'nom_chaine' => $live->getStreamerName(),
    'description'=> $live->description,
], $lives);

echo json_encode($result);