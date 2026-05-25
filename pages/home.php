<?php
// Récupération des lives depuis la BDD
$stmt = $pdo->prepare("
    SELECT l.id_live, l.nom_live, l.date_live, l.heure_live, l.description,
           u.nom, u.prenom, u.nom_chaine
    FROM Live l
    JOIN User u ON l.id_user = u.id_user
    ORDER BY l.date_live ASC
    LIMIT 3
");
$stmt->execute();
$lives = $stmt->fetchAll();

// Récupération des thématiques pour les filtres
$stmtThemes = $pdo->prepare("SELECT * FROM Thematique");
$stmtThemes->execute();
$thematiques = $stmtThemes->fetchAll();
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
                    <a class="nav-link active" href="index.php">Accueil</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="index.php?page=lives">Lives</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="index.php?page=connexion">Connexion</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- HERO SECTION -->
<section class="hero text-center py-5">
    <div class="container">
        <h1 class="display-4 fw-bold">Bienvenue sur <span style="color: var(--accent);">Z-EVENT</span></h1>
        <p class="lead" style="color: var(--text-secondary);">
            L'événement e-sport caritatif qui rassemble les plus grands streamers pour une bonne cause.
        </p>
        <a href="index.php?page=lives" class="btn btn-accent btn-lg mt-3">Voir les lives</a>
    </div>
</section>

<!-- SECTION LIVES -->
<section class="py-5">
    <div class="container">
        <h2 class="mb-4">Les prochains lives</h2>

        <?php if (empty($lives)) : ?>
            <p style="color: var(--text-secondary);">Aucun live prévu pour le moment.</p>
        <?php else : ?>
            <div class="row g-4">
                <?php foreach ($lives as $live) : ?>
                    <div class="col-md-4">
                        <div class="card h-100 p-3">
                            <!-- Thumbnail placeholder -->
                            <div class="ratio ratio-16x9 mb-3" style="background-color: var(--bg-primary); border-radius: 8px; border-left: 4px solid var(--accent);">
                                <div class="d-flex align-items-center justify-content-center">
                                    <span style="font-size: 2rem; color: var(--accent);">▶</span>
                                </div>
                            </div>
                            <div class="card-body p-0">
                                <h5 class="card-title"><?= htmlspecialchars($live['nom_live']) ?></h5>
                                <p class="card-text">
                                    📅 <?= htmlspecialchars($live['date_live']) ?> à <?= htmlspecialchars($live['heure_live']) ?>
                                </p>
                                <p class="card-text">
                                    🎮 <?= htmlspecialchars($live['nom_chaine']) ?>
                                </p>
                                <a href="index.php?page=detail_live&id=<?= $live['id_live'] ?>" class="btn btn-outline-accent mt-2">Voir le live</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- SECTION ASSOCIATION -->
<section class="py-5" style="background-color: var(--bg-secondary);">
    <div class="container text-center">
        <h2 class="mb-3">Notre mission</h2>
        <p style="color: var(--text-secondary); max-width: 600px; margin: 0 auto;">
            Z-Event rassemble 45 streamers pour jouer en direct et récolter des dons 
            pour des associations partageant les valeurs de l'événement.
        </p>
    </div>
</section>

<!-- FOOTER -->
<footer>
    <div class="container">
        <p class="mb-0">© Z-Event 2026 — Tous droits réservés</p>
    </div>
</footer>