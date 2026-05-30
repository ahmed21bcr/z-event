<?php
$filtre_thematique = $_GET['thematique'] ?? '';
$filtre_date = $_GET['date'] ?? '';
$filtre_streamer = $_GET['streamer'] ?? '';

$sql = "
    SELECT l.id_live, l.nom_live, l.date_live, l.heure_live, l.description, l.PEGI,
           u.nom, u.prenom, u.nom_chaine
    FROM Live l
    JOIN User u ON l.id_user = u.id_user
    WHERE 1=1
";

$params = [];

if (!empty($filtre_thematique)) {
    $sql .= " AND l.id_live IN (
        SELECT id_live FROM Live_Thematique WHERE id_thematique = :thematique
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

$stmtThemes = $pdo->prepare("SELECT * FROM Thematique");
$stmtThemes->execute();
$thematiques = $stmtThemes->fetchAll();

$stmtStreamers = $pdo->prepare("SELECT id_user, nom, prenom, nom_chaine FROM User WHERE role = 'streamer'");
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
                <li class="nav-item">
                    <a class="nav-link" href="index.php">Accueil</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="index.php?page=lives">Lives</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="index.php?page=connexion">Connexion</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- SECTION FILTRES -->
<section class="py-4 section-filtres">
    <div class="container">
        <form method="GET" action="index.php" class="row g-3 align-items-end">
            <input type="hidden" name="page" value="lives">

            <div class="col-md-3">
                <label class="form-label form-label-zevent">Thématique</label>
                <select name="thematique" class="form-select form-zevent">
                    <option value="">Toutes</option>
                    <?php foreach ($thematiques as $theme) : ?>
                        <option value="<?= $theme['id_thematique'] ?>" <?= $filtre_thematique == $theme['id_thematique'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($theme['libelle']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label form-label-zevent">Date</label>
                <input
                    type="date"
                    name="date"
                    class="form-control form-zevent"
                    value="<?= htmlspecialchars($filtre_date) ?>"
                >
            </div>

            <div class="col-md-3">
                <label class="form-label form-label-zevent">Streamer</label>
                <select name="streamer" class="form-select form-zevent">
                    <option value="">Tous</option>
                    <?php foreach ($streamers as $streamer) : ?>
                        <option value="<?= $streamer['id_user'] ?>" <?= $filtre_streamer == $streamer['id_user'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($streamer['nom_chaine']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-3">
                <button type="submit" class="btn btn-accent w-100">Filtrer</button>
                <a href="index.php?page=lives" class="btn btn-outline-accent w-100 mt-2">Réinitialiser</a>
            </div>
        </form>
    </div>
</section>

<!-- LISTE DES LIVES -->
<section class="py-5">
    <div class="container">
        <h2 class="mb-4">Tous les lives
            <span class="text-muted-zevent fs-6">(<?= count($lives) ?> résultat<?= count($lives) > 1 ? 's' : '' ?>)</span>
        </h2>

        <?php if (empty($lives)) : ?>
            <div class="text-center py-5">
                <p class="text-muted-zevent fs-5">Aucun live trouvé avec ces filtres.</p>
                <a href="index.php?page=lives" class="btn btn-outline-accent mt-2">Voir tous les lives</a>
            </div>
        <?php else : ?>
            <div class="row g-4">
                <?php foreach ($lives as $live) : ?>
                    <div class="col-md-4">
                        <div class="card h-100 p-3">
                            <div class="ratio ratio-16x9 mb-3 live-thumbnail">
                                <div class="d-flex align-items-center justify-content-center">
                                    <span class="live-thumbnail-icon">▶</span>
                                </div>
                            </div>
                            <div class="card-body p-0">
                                <h5 class="card-title"><?= htmlspecialchars($live['nom_live']) ?></h5>
                                <p class="card-text">📅 <?= htmlspecialchars($live['date_live']) ?> à <?= htmlspecialchars($live['heure_live']) ?></p>
                                <p class="card-text">🎮 <?= htmlspecialchars($live['nom_chaine']) ?></p>
                                <?php if ($live['PEGI']) : ?>
                                    <span class="badge badge-accent mb-2">PEGI <?= htmlspecialchars($live['PEGI']) ?></span>
                                <?php endif; ?>
                                <div class="mt-2">
                                    <a href="index.php?page=detail_live&id=<?= $live['id_live'] ?>" class="btn btn-outline-accent">Voir le live</a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- FOOTER -->
<footer>
    <div class="container">
        <p class="mb-0">© Z-Event 2026 - Tous droits réservés</p>
    </div>
</footer>