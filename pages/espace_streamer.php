<?php
require_once ROOT_PATH . '/controllers/LiveController.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'streamer') {
    header('Location: index.php?page=connexion');
    exit;
}

$id_user = $_SESSION['user']['id_user'];
$onglet = $_GET['onglet'] ?? 'accueil';
$onglets_autorises = ['accueil', 'saisie', 'inscriptions', 'statistiques'];
if (!in_array($onglet, $onglets_autorises)) {
    $onglet = 'accueil';
}

$liveController = new LiveController();
$message_saisie = '';
$erreur_saisie = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'creer_live') {
    $data = [
        'nom_live'     => trim($_POST['nom_live'] ?? ''),
        'date_live'    => $_POST['date_live'] ?? '',
        'heure_live'   => $_POST['heure_live'] ?? '',
        'pegi'         => $_POST['pegi'] ?? '',
        'description'  => trim($_POST['description'] ?? ''),
        'id_user'      => $id_user,
        'id_evenement' => $_POST['id_evenement'] ?? ''
    ];

    if ($liveController->create($data)) {
        $message_saisie = 'Live créé avec succès !';
        $onglet = 'accueil';
    } else {
        $erreur_saisie = 'Veuillez remplir tous les champs obligatoires.';
        $onglet = 'saisie';
    }
}

$mes_lives = $liveController->getByUser($id_user);

$stmtInscriptions = Database::getInstance()->prepare("
    SELECT i.email, i.id_inscription, l.nom_live, l.date_live
    FROM inscription i
    JOIN live l ON i.id_live = l.id_live
    WHERE l.id_user = :id_user
    ORDER BY l.date_live ASC
");
$stmtInscriptions->execute([':id_user' => $id_user]);
$inscriptions = $stmtInscriptions->fetchAll();

$stmtEvenements = Database::getInstance()->prepare("SELECT * FROM evenement");
$stmtEvenements->execute();
$evenements = $stmtEvenements->fetchAll();

$stmtThemes = Database::getInstance()->prepare("SELECT * FROM thematique");
$stmtThemes->execute();
$thematiques = $stmtThemes->fetchAll();
?>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg">
    <div class="container">
        <a class="navbar-brand" href="index.php">Z-EVENT</a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="index.php">Accueil</a></li>
                <li class="nav-item"><a class="nav-link" href="index.php?page=lives">Lives</a></li>
                <li class="nav-item">
                    <a class="nav-link nav-link-accent" href="index.php?page=deconnexion">
                        Déconnexion (<?= htmlspecialchars($_SESSION['user']['prenom']) ?>)
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- ONGLETS -->
<div class="onglets-bar">
    <div class="container">
        <div class="d-flex gap-2 py-2">
            <?php foreach (['accueil' => 'Accueil', 'saisie' => 'Saisie', 'inscriptions' => 'Inscriptions', 'statistiques' => 'Statistiques'] as $key => $label) : ?>
                <a href="index.php?page=espace_streamer&onglet=<?= $key ?>"
                   class="btn <?= $onglet === $key ? 'btn-accent' : 'btn-outline-accent' ?>">
                    <?= $label ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- CONTENU DES ONGLETS -->
<section class="py-5">
    <div class="container">

        <?php if ($message_saisie) : ?>
            <div class="alert alert-success-zevent mb-4">
                <?= htmlspecialchars($message_saisie) ?>
            </div>
        <?php endif; ?>

        <!-- ONGLET ACCUEIL -->
        <?php if ($onglet === 'accueil') : ?>
            <h2 class="mb-4">Mes Lives —
                <span class="text-muted-zevent fs-6"><?= htmlspecialchars($_SESSION['user']['nom_chaine']) ?></span>
            </h2>

            <?php if (empty($mes_lives)) : ?>
                <p class="text-muted-zevent">Vous n'avez pas encore créé de live.</p>
                <a href="index.php?page=espace_streamer&onglet=saisie" class="btn btn-accent mt-2">Créer mon premier live</a>
            <?php else : ?>
                <div class="row g-4">
                    <?php foreach ($mes_lives as $live) : ?>
                        <div class="col-md-4">
                            <div class="card p-3">
                                <div class="ratio ratio-16x9 mb-3 live-thumbnail">
                                    <div class="d-flex align-items-center justify-content-center">
                                        <span class="live-thumbnail-icon">▶</span>
                                    </div>
                                </div>
                                <h5 class="card-title"><?= htmlspecialchars($live->nom_live) ?></h5>
                                    <p class="card-text">📅 <?= htmlspecialchars($live->date_live) ?> à <?= htmlspecialchars($live->heure_live) ?></p>
                                <a href="index.php?page=detail_live&id=<?= $live->id_live ?>" class="btn btn-outline-accent mt-2">Voir</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

        <!-- ONGLET SAISIE -->
        <?php elseif ($onglet === 'saisie') : ?>
            <h2 class="mb-4">Créer un Live</h2>

            <?php if ($erreur_saisie) : ?>
                <div class="alert alert-error-zevent mb-3">
                    <?= htmlspecialchars($erreur_saisie) ?>
                </div>
            <?php endif; ?>

            <div class="row justify-content-center">
                <div class="col-md-7">
                    <div class="card p-4">
                        <form method="POST" action="index.php?page=espace_streamer&onglet=saisie">
                            <input type="hidden" name="action" value="creer_live">

                            <div class="mb-3">
                                <label class="form-label form-label-zevent">Nom du live *</label>
                                <input type="text" name="nom_live" class="form-control form-zevent" required>
                            </div>

                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label form-label-zevent">Date *</label>
                                    <input type="date" name="date_live" class="form-control form-zevent" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label form-label-zevent">Heure *</label>
                                    <input type="time" name="heure_live" class="form-control form-zevent" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label form-label-zevent">PEGI</label>
                                <select name="pegi" class="form-select form-zevent">
                                    <option value="">Non défini</option>
                                    <option value="3">PEGI 3</option>
                                    <option value="7">PEGI 7</option>
                                    <option value="12">PEGI 12</option>
                                    <option value="16">PEGI 16</option>
                                    <option value="18">PEGI 18</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label form-label-zevent">Événement *</label>
                                <select name="id_evenement" class="form-select form-zevent" required>
                                    <option value="">Sélectionner un événement</option>
                                    <?php foreach ($evenements as $evt) : ?>
                                        <option value="<?= $evt['id_evenement'] ?>">
                                            <?= htmlspecialchars($evt['association']) ?> (<?= $evt['date_debut'] ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="mb-4">
                                <label class="form-label form-label-zevent">Description</label>
                                <textarea name="description" rows="3" class="form-control form-zevent"></textarea>
                            </div>

                            <button type="submit" class="btn btn-accent w-100">Créer le live</button>
                        </form>
                    </div>
                </div>
            </div>

        <!-- ONGLET INSCRIPTIONS -->
        <?php elseif ($onglet === 'inscriptions') : ?>
            <h2 class="mb-4">Inscriptions à mes lives</h2>

            <?php if (empty($inscriptions)) : ?>
                <p class="text-muted-zevent">Aucune inscription pour le moment.</p>
            <?php else : ?>
                <div class="table-responsive">
                    <table class="table table-zevent">
                        <thead>
                            <tr>
                                <th>Email</th>
                                <th>Live</th>
                                <th>Date du live</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($inscriptions as $inscription) : ?>
                                <tr>
                                    <td><?= htmlspecialchars($inscription['email']) ?></td>
                                    <td><?= htmlspecialchars($inscription['nom_live']) ?></td>
                                    <td><?= htmlspecialchars($inscription['date_live']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>

        <!-- ONGLET STATISTIQUES -->
        <?php elseif ($onglet === 'statistiques') : ?>
    <h2 class="mb-4">Statistiques</h2>
    <div class="row g-4">
        <!-- Stats MySQL -->
        <div class="col-md-4">
            <div class="card p-4 text-center">
                <h2 class="stat-number"><?= count($mes_lives) ?></h2>
                <p class="stat-label">Live<?= count($mes_lives) > 1 ? 's' : '' ?> créé<?= count($mes_lives) > 1 ? 's' : '' ?></p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card p-4 text-center">
                <h2 class="stat-number"><?= count($inscriptions) ?></h2>
                <p class="stat-label">Inscription<?= count($inscriptions) > 1 ? 's' : '' ?> totale<?= count($inscriptions) > 1 ? 's' : '' ?></p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card p-4 text-center">
                <h2 class="stat-number">
                    <?= count($mes_lives) > 0 ? round(count($inscriptions) / count($mes_lives), 1) : 0 ?>
                </h2>
                <p class="stat-label">Inscriptions par live en moyenne</p>
            </div>
        </div>

        <!-- Stats MongoDB -->
        <div class="col-12 mt-4">
            <h3 class="mb-3">Vues de mes lives <span class="text-muted-zevent fs-6">(données temps réel)</span></h3>
            <div class="row g-4">
            <?php foreach ($mes_lives as $live) :
    $nbVues = $mongoDB->vues->countDocuments(['id_live' => (int)$live->id_live]);
            ?>
    <div class="col-md-4">
        <div class="card p-3">
            <h5 class="card-title"><?= htmlspecialchars($live->nom_live) ?></h5>
                            <h3 class="stat-number"><?= $nbVues ?></h3>
                            <p class="stat-label">vue<?= $nbVues > 1 ? 's' : '' ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
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