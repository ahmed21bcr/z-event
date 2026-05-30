<?php
$id_live = $_GET['id'] ?? null;

if (!$id_live || !is_numeric($id_live)) {
    header('Location: index.php?page=lives');
    exit;
}

$stmt = $pdo->prepare("
    SELECT l.*, u.nom, u.prenom, u.nom_chaine, e.association, e.date_debut, e.date_fin
    FROM Live l
    JOIN User u ON l.id_user = u.id_user
    JOIN Evenement e ON l.id_evenement = e.id_evenement
    WHERE l.id_live = :id
");
$stmt->execute([':id' => $id_live]);
$live = $stmt->fetch();

if (!$live) {
    header('Location: index.php?page=lives');
    exit;
}

$stmtThemes = $pdo->prepare("
    SELECT t.libelle
    FROM Thematique t
    JOIN Live_Thematique lt ON t.id_thematique = lt.id_thematique
    WHERE lt.id_live = :id
");
$stmtThemes->execute([':id' => $id_live]);
$thematiques = $stmtThemes->fetchAll();

$stmtMateriel = $pdo->prepare("
    SELECT m.libelle, m.marque, lm.quantite
    FROM Materiel m
    JOIN Live_Materiel lm ON m.id_materiel = lm.id_materiel
    WHERE lm.id_live = :id
");
$stmtMateriel->execute([':id' => $id_live]);
$materiels = $stmtMateriel->fetchAll();

$message = '';
$erreur = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');

    if (empty($email)) {
        $erreur = 'Veuillez entrer votre email.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erreur = 'Format d\'email invalide.';
    } else {
        $stmtCheck = $pdo->prepare("SELECT id_inscription FROM Inscription WHERE email = :email AND id_live = :id_live");
        $stmtCheck->execute([':email' => $email, ':id_live' => $id_live]);

        if ($stmtCheck->fetch()) {
            $erreur = 'Vous êtes déjà inscrit à ce live.';
        } else {
            $stmtInsert = $pdo->prepare("INSERT INTO Inscription (email, id_live) VALUES (:email, :id_live)");
            $stmtInsert->execute([':email' => $email, ':id_live' => $id_live]);
            $message = 'Inscription confirmée ! Vous recevrez un rappel par email.';
        }
    }
}

$stmtCount = $pdo->prepare("SELECT COUNT(*) as total FROM Inscription WHERE id_live = :id");
$stmtCount->execute([':id' => $id_live]);
$nbInscrits = $stmtCount->fetch()['total'];
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

<!-- DÉTAIL DU LIVE -->
<section class="py-5">
    <div class="container">
        <a href="index.php?page=lives" class="btn btn-outline-accent mb-4">← Retour aux lives</a>

        <div class="row g-4">
            <!-- Colonne gauche -->
            <div class="col-md-8">
                <div class="card p-4">
                    <div class="ratio ratio-16x9 mb-4 live-thumbnail">
                        <div class="d-flex align-items-center justify-content-center">
                            <span class="live-thumbnail-icon-lg">▶</span>
                        </div>
                    </div>

                    <h1 class="mb-3 text-accent"><?= htmlspecialchars($live['nom_live']) ?></h1>

                    <div class="d-flex gap-3 mb-3 flex-wrap">
                        <span class="text-muted-zevent">📅 <?= htmlspecialchars($live['date_live']) ?> à <?= htmlspecialchars($live['heure_live']) ?></span>
                        <span class="text-muted-zevent">🎮 <?= htmlspecialchars($live['nom_chaine']) ?></span>
                        <?php if ($live['PEGI']) : ?>
                            <span class="badge badge-accent">PEGI <?= htmlspecialchars($live['PEGI']) ?></span>
                        <?php endif; ?>
                    </div>

                    <?php if ($live['description']) : ?>
                        <p class="text-muted-zevent"><?= htmlspecialchars($live['description']) ?></p>
                    <?php endif; ?>

                    <?php if (!empty($thematiques)) : ?>
                        <div class="mb-3">
                            <strong class="text-muted-zevent">Thématiques :</strong>
                            <?php foreach ($thematiques as $theme) : ?>
                                <span class="badge badge-outline-accent ms-1">
                                    <?= htmlspecialchars($theme['libelle']) ?>
                                </span>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($materiels)) : ?>
                        <div class="mb-3">
                            <strong class="text-muted-zevent">Matériel utilisé :</strong>
                            <ul class="mt-2 text-muted-zevent">
                                <?php foreach ($materiels as $materiel) : ?>
                                    <li><?= htmlspecialchars($materiel['libelle']) ?> — <?= htmlspecialchars($materiel['marque']) ?> (x<?= $materiel['quantite'] ?>)</li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Colonne droite -->
            <div class="col-md-4">
                <div class="card p-3 mb-3">
                    <h5 class="text-accent">Événement</h5>
                    <p class="mb-1 text-muted-zevent">📅 Du <?= htmlspecialchars($live['date_debut']) ?> au <?= htmlspecialchars($live['date_fin']) ?></p>
                    <p class="mb-0 text-muted-zevent">🤝 <?= htmlspecialchars($live['association']) ?></p>
                </div>

                <div class="card p-3 mb-3 text-center">
                    <h2 class="stat-number"><?= $nbInscrits ?></h2>
                    <p class="stat-label">personne<?= $nbInscrits > 1 ? 's inscrite·s' : ' inscrite' ?></p>
                </div>

                <div class="card p-3">
                    <h5 class="text-accent">S'inscrire au live</h5>

                    <?php if ($message) : ?>
                        <div class="alert alert-success-zevent">
                            <?= htmlspecialchars($message) ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($erreur) : ?>
                        <div class="alert alert-error-zevent">
                            <?= htmlspecialchars($erreur) ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="index.php?page=detail_live&id=<?= $id_live ?>">
                        <div class="mb-3">
                            <label class="form-label form-label-zevent">Votre email</label>
                            <input
                                type="email"
                                name="email"
                                class="form-control form-zevent"
                                placeholder="votre@email.com"
                                required
                            >
                        </div>
                        <button type="submit" class="btn btn-accent w-100">S'inscrire</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FOOTER -->
<footer>
    <div class="container">
        <p class="mb-0">© Z-Event 2026 - Tous droits réservés</p>
    </div>
</footer>