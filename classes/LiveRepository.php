<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/Database.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/Live.php';

class LiveRepository {
    private PDO $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance();
    }

    public function findAll(): array {
        $stmt = $this->pdo->prepare("
            SELECT l.*, u.nom, u.prenom, u.nom_chaine
            FROM live l
            JOIN user u ON l.id_user = u.id_user
            ORDER BY l.date_live ASC
        ");
        $stmt->execute();
        return array_map(fn($row) => new Live($row), $stmt->fetchAll());
    }

    public function findById(int $id): ?Live {
        $stmt = $this->pdo->prepare("
            SELECT l.*, u.nom, u.prenom, u.nom_chaine
            FROM live l
            JOIN user u ON l.id_user = u.id_user
            WHERE l.id_live = :id
        ");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ? new Live($row) : null;
    }

    public function findByUserId(int $id_user): array {
        $stmt = $this->pdo->prepare("
            SELECT l.*, u.nom, u.prenom, u.nom_chaine
            FROM live l
            JOIN user u ON l.id_user = u.id_user
            WHERE l.id_user = :id_user
            ORDER BY l.date_live ASC
        ");
        $stmt->execute([':id_user' => $id_user]);
        return array_map(fn($row) => new Live($row), $stmt->fetchAll());
    }

    public function findWithFilters(string $thematique = '', string $date = '', string $streamer = ''): array {
        $sql = "
            SELECT l.*, u.nom, u.prenom, u.nom_chaine
            FROM live l
            JOIN user u ON l.id_user = u.id_user
            WHERE 1=1
        ";
        $params = [];

        if (!empty($thematique)) {
            $sql .= " AND l.id_live IN (SELECT id_live FROM live_thematique WHERE id_thematique = :thematique)";
            $params[':thematique'] = $thematique;
        }
        if (!empty($date)) {
            $sql .= " AND l.date_live = :date";
            $params[':date'] = $date;
        }
        if (!empty($streamer)) {
            $sql .= " AND l.id_user = :streamer";
            $params[':streamer'] = $streamer;
        }

        $sql .= " ORDER BY l.date_live ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return array_map(fn($row) => new Live($row), $stmt->fetchAll());
    }

    public function create(array $data): bool {
        $stmt = $this->pdo->prepare("
            INSERT INTO live (nom_live, date_live, heure_live, PEGI, description, id_user, id_evenement)
            VALUES (:nom_live, :date_live, :heure_live, :pegi, :description, :id_user, :id_evenement)
        ");
        return $stmt->execute([
            ':nom_live'     => $data['nom_live'],
            ':date_live'    => $data['date_live'],
            ':heure_live'   => $data['heure_live'],
            ':pegi'         => $data['pegi'] ?? null,
            ':description'  => $data['description'] ?? null,
            ':id_user'      => $data['id_user'],
            ':id_evenement' => $data['id_evenement']
        ]);
    }
}