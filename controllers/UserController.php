<?php

require_once ROOT_PATH . '/classes/UserRepository.php';

class UserController {
    private UserRepository $userRepository;

    public function __construct() {
        $this->userRepository = new UserRepository();
    }

    public function login(string $email, string $password): ?User {
        $user = $this->userRepository->findByEmail($email);
        if ($user && password_verify($password, $user->password)) {
            return $user;
        }
        return null;
    }

    public function getAllStreamers(): array {
        return $this->userRepository->findAllStreamers();
    }

    public function createStreamer(array $data): bool|string {
        if (empty($data['nom']) || empty($data['prenom']) || empty($data['email']) || empty($data['password']) || empty($data['nom_chaine'])) {
            return 'Veuillez remplir tous les champs obligatoires.';
        }
        if ($this->userRepository->emailExists($data['email'])) {
            return 'Cet email est déjà utilisé.';
        }
        $this->userRepository->create([
            'nom'        => $data['nom'],
            'prenom'     => $data['prenom'],
            'email'      => $data['email'],
            'password'   => password_hash($data['password'], PASSWORD_DEFAULT),
            'age'        => $data['age'] ?? null,
            'nom_chaine' => $data['nom_chaine'],
            'role'       => 'streamer',
            'matricule'  => $data['matricule'] ?? null
        ]);
        return true;
    }

    public function getById(int $id): ?User {
        return $this->userRepository->findById($id);
    }
}