<?php

class User {
    public int $id_user;
    public string $nom;
    public string $prenom;
    public string $email;
    public string $password;
    public ?int $age;
    public ?string $nom_chaine;
    public string $role;
    public ?string $matricule;

    public function __construct(array $data) {
        $this->id_user    = $data['id_user'];
        $this->nom        = $data['nom'];
        $this->prenom     = $data['prenom'];
        $this->email      = $data['email'];
        $this->password   = $data['password'];
        $this->age        = $data['age'] ?? null;
        $this->nom_chaine = $data['nom_chaine'] ?? null;
        $this->role       = $data['role'];
        $this->matricule  = $data['matricule'] ?? null;
    }

    public function isAdmin(): bool {
        return $this->role === 'admin';
    }

    public function isStreamer(): bool {
        return $this->role === 'streamer';
    }

    public function getFullName(): string {
        return $this->prenom . ' ' . $this->nom;
    }
}