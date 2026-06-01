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
            SELECT * FROM user WHERE role = 'streamer' ORDER BY nom ASC
        ");
        $stmt->execute();
        return array_map(fn($row) => new User($row), $stmt->fetchAll());
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