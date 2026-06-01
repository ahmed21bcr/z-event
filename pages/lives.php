<?php
$stmtThemes = $pdo->prepare("SELECT * FROM thematique");
$stmtThemes->execute();
$thematiques = $stmtThemes->fetchAll();

$stmtStreamers = $pdo->prepare("SELECT id_user, nom, prenom, nom_chaine FROM user WHERE role = 'streamer'");
$stmtStreamers->execute();
$streamers = $stmtStreamers->fetchAll();
?>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg">
    <div class="container">
        <a class="navbar-brand" href="index.php">Z-EVENT</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
            <span>☰</span>
        </button>
        <div class="collapse navbar-collapse" id="navMenu">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="index.php">Accueil</a></li>
                <li class="nav-item"><a class="nav-link active" href="index.php?page=lives">Lives</a></li>
                <li class="nav-item"><a class="nav-link" href="index.php?page=connexion">Connexion</a></li>
            </ul>
        </div>
    </div>
</nav>

<!-- SECTION FILTRES -->
<section class="py-4 section-filtres">
    <div class="container">
        <div class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label form-label-zevent">Thématique</label>
                <select id="filtre-thematique" class="form-select form-zevent">
                    <option value="">Toutes</option>
                    <?php foreach ($thematiques as $theme) : ?>
                        <option value="<?= $theme['id_thematique'] ?>">
                            <?= htmlspecialchars($theme['libelle']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label form-label-zevent">Date</label>
                <input type="date" id="filtre-date" class="form-control form-zevent">
            </div>

            <div class="col-md-3">
                <label class="form-label form-label-zevent">Streamer</label>
                <select id="filtre-streamer" class="form-select form-zevent">
                    <option value="">Tous</option>
                    <?php foreach ($streamers as $streamer) : ?>
                        <option value="<?= $streamer['id_user'] ?>">
                            <?= htmlspecialchars($streamer['nom_chaine']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-3">
                <button id="btn-filtrer" class="btn btn-accent w-100">Filtrer</button>
                <button id="btn-reset" class="btn btn-outline-accent w-100 mt-2">Réinitialiser</button>
            </div>
        </div>
    </div>
</section>

<!-- LISTE DES LIVES -->
<section class="py-5">
    <div class="container">
        <h2 class="mb-4">Tous les lives <span id="compteur" class="text-muted-zevent fs-6"></span></h2>
        <div id="liste-lives" class="row g-4">
            <div class="text-center py-5">
                <div class="spinner-border" style="color: var(--accent);" role="status"></div>
            </div>
        </div>
    </div>
</section>

<!-- FOOTER -->
<footer>
    <div class="container">
        <p class="mb-0">© Z-Event 2026 — Tous droits réservés</p>
    </div>
</footer>

<script>
function chargerLives() {
    const thematique = document.getElementById('filtre-thematique').value;
    const date = document.getElementById('filtre-date').value;
    const streamer = document.getElementById('filtre-streamer').value;

    const params = new URLSearchParams();
    if (thematique) params.append('thematique', thematique);
    if (date) params.append('date', date);
    if (streamer) params.append('streamer', streamer);

    fetch(`api/lives.php?${params.toString()}`)
        .then(response => response.json())
        .then(lives => {
            const container = document.getElementById('liste-lives');
            const compteur = document.getElementById('compteur');

            compteur.textContent = `(${lives.length} résultat${lives.length > 1 ? 's' : ''})`;

            if (lives.length === 0) {
                container.innerHTML = `
                    <div class="text-center py-5">
                        <p class="text-muted-zevent fs-5">Aucun live trouvé avec ces filtres.</p>
                        <button onclick="resetFiltres()" class="btn btn-outline-accent mt-2">Voir tous les lives</button>
                    </div>`;
                return;
            }

            container.innerHTML = lives.map(live => `
                <div class="col-md-4">
                    <div class="card h-100 p-3">
                        <div class="ratio ratio-16x9 mb-3 live-thumbnail">
                            <div class="d-flex align-items-center justify-content-center">
                                <span class="live-thumbnail-icon">▶</span>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <h5 class="card-title">${live.nom_live}</h5>
                            <p class="card-text">📅 ${live.date_live} à ${live.heure_live}</p>
                            <p class="card-text">🎮 ${live.nom_chaine}</p>
                            ${live.PEGI ? `<span class="badge badge-accent mb-2">PEGI ${live.PEGI}</span>` : ''}
                            <div class="mt-2">
                                <a href="index.php?page=detail_live&id=${live.id_live}" class="btn btn-outline-accent">Voir le live</a>
                            </div>
                        </div>
                    </div>
                </div>
            `).join('');
        })
        .catch(err => {
            document.getElementById('liste-lives').innerHTML = `
                <p class="text-muted-zevent">Erreur lors du chargement des lives.</p>`;
            console.error(err);
        });
}

function resetFiltres() {
    document.getElementById('filtre-thematique').value = '';
    document.getElementById('filtre-date').value = '';
    document.getElementById('filtre-streamer').value = '';
    chargerLives();
}

document.getElementById('btn-filtrer').addEventListener('click', chargerLives);
document.getElementById('btn-reset').addEventListener('click', resetFiltres);

// Chargement initial au démarrage de la page
chargerLives();
</script>