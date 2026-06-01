<?php

class Live {
    public int $id_live;
    public string $nom_live;
    public string $date_live;
    public string $heure_live;
    public ?string $pegi;
    public ?string $description;
    public int $id_user;
    public int $id_evenement;
    public ?string $nom_chaine;
    public ?string $nom;
    public ?string $prenom;

    public function __construct(array $data) {
        $this->id_live      = $data['id_live'];
        $this->nom_live     = $data['nom_live'];
        $this->date_live    = $data['date_live'];
        $this->heure_live   = $data['heure_live'];
        $this->pegi         = $data['PEGI'] ?? null;
        $this->description  = $data['description'] ?? null;
        $this->id_user      = $data['id_user'];
        $this->id_evenement = $data['id_evenement'];
        $this->nom_chaine   = $data['nom_chaine'] ?? null;
        $this->nom          = $data['nom'] ?? null;
        $this->prenom       = $data['prenom'] ?? null;
    }

    public function getStreamerName(): string {
        return $this->nom_chaine ?? 'Streamer inconnu';
    }

    public function hasPegi(): bool {
        return $this->pegi !== null && $this->pegi !== '';
    }

    public function getFormattedDate(): string {
        return date('d/m/Y', strtotime($this->date_live));
    }
}