<?php

require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/User.php';

class UserRepository {
    private PDO $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance();
    }

    public function findByEmail(string $email): ?User {
        $stmt = $this->pdo->prepare("
            SELECT * FROM user WHERE email = :email
        ");
        $stmt->execute([':email' => $email]);
        $row = $stmt->fetch();
        return $row ? new User($row) : null;
    }

    public function findById(int $id): ?User {
        $stmt = $this->pdo->prepare("
            SELECT * FROM user WHERE id_user = :id
        ");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ? new User($row) : null;
    }

    public function findAllStreamers(): array {
    $stmt = $this->pdo->prepare("
        SELECT u.*, COUNT(l.id_live) as nb_lives
        FROM user u
        LEFT JOIN live l ON u.id_user = l.id_user
        WHERE u.role = 'streamer'
        GROUP BY u.id_user
        ORDER BY u.nom ASC
    ");
    $stmt->execute();
    $rows = $stmt->fetchAll();
    return array_map(function($row) {
        $user = new User($row);
        $user->nb_lives = $row['nb_lives'];
        return $user;
    }, $rows);
}

    public function create(array $data): bool {
        $stmt = $this->pdo->prepare("
            INSERT INTO user (nom, prenom, email, password, age, nom_chaine, role, matricule)
            VALUES (:nom, :prenom, :email, :password, :age, :nom_chaine, :role, :matricule)
        ");
        return $stmt->execute([
            ':nom'        => $data['nom'],
            ':prenom'     => $data['prenom'],
            ':email'      => $data['email'],
            ':password'   => $data['password'],
            ':age'        => $data['age'] ?? null,
            ':nom_chaine' => $data['nom_chaine'] ?? null,
            ':role'       => $data['role'],
            ':matricule'  => $data['matricule'] ?? null
        ]);
    }

    public function emailExists(string $email): bool {
        $stmt = $this->pdo->prepare("
            SELECT COUNT(*) FROM user WHERE email = :email
        ");
        $stmt->execute([':email' => $email]);
        return $stmt->fetchColumn() > 0;
    }
}