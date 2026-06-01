<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/LiveRepository.php';

class LiveController {
    private LiveRepository $liveRepository;

    public function __construct() {
        $this->liveRepository = new LiveRepository();
    }

    public function index(): array {
        return $this->liveRepository->findAll();
    }

    public function show(int $id): ?Live {
        return $this->liveRepository->findById($id);
    }

    public function getByUser(int $id_user): array {
        return $this->liveRepository->findByUserId($id_user);
    }

    public function filter(string $thematique = '', string $date = '', string $streamer = ''): array {
        return $this->liveRepository->findWithFilters($thematique, $date, $streamer);
    }

    public function create(array $data): bool {
        if (empty($data['nom_live']) || empty($data['date_live']) || empty($data['heure_live']) || empty($data['id_evenement'])) {
            return false;
        }
        return $this->liveRepository->create($data);
    }
}