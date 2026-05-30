<?php
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: index.php?page=connexion');
    exit;
}

$onglet = $_GET['onglet'] ?? 'streamers';
$onglets_autorises = ['streamers', 'materiels', 'creer_streamer'];
if (!in_array($onglet, $onglets_autorises)) {
    $onglet = 'streamers';
}

$message = '';
$erreur = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'creer_streamer') {
    $nom        = trim($_POST['nom'] ?? '');
    $prenom     = trim($_POST['prenom'] ?? '');
    $email      = trim($_POST['email'] ?? '');
    $password   = $_POST['password'] ?? '';
    $age        = $_POST['age'] ?? '';
    $nom_chaine = trim($_POST['nom_chaine'] ?? '');
    $matricule  = trim($_POST['matricule'] ?? '');

    if (empty($nom) || empty($prenom) || empty($email) || empty($password) || empty($nom_chaine)) {
        $erreur = 'Veuillez remplir tous les champs obligatoires.';
        $onglet = 'creer_streamer';
    } else {
        $stmtCheck = $pdo->prepare("SELECT id_user FROM User WHERE email = :email");
        $stmtCheck->execute([':email' => $email]);
        if ($stmtCheck->fetch()) {
            $erreur = 'Cet email est déjà utilisé.';
            $onglet = 'creer_streamer';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("
                INSERT INTO User (nom, prenom, email, password, age, nom_chaine, role, matricule)
                VALUES (:nom, :prenom, :email, :password, :age, :nom_chaine, 'streamer', :matricule)
            ");
            $stmt->execute([
                ':nom'        => $nom,
                ':prenom'     => $prenom,
                ':email'      => $email,
                ':password'   => $hash,
                ':age'        => $age,
                ':nom_chaine' => $nom_chaine,
                ':matricule'  => $matricule
            ]);
            $message = 'Streamer créé avec succès !';
            $onglet = 'streamers';
        }
    }
}

$stmtStreamers = $pdo->prepare("
    SELECT u.*, COUNT(l.id_live) as nb_lives
    FROM User u
    LEFT JOIN Live l ON u.id_user = l.id_user
    WHERE u.role = 'streamer'
    GROUP BY u.id_user
    ORDER BY u.nom ASC
");
$stmtStreamers->execute();
$streamers = $stmtStreamers->fetchAll();

$stmtMateriels = $pdo->prepare("
    SELECT m.*, COALESCE(SUM(lm.quantite), 0) as quantite_utilisee
    FROM Materiel m
    LEFT JOIN Live_Materiel lm ON m.id_materiel = lm.id_materiel
    GROUP BY m.id_materiel
    ORDER BY m.libelle ASC
");
$stmtMateriels->execute();
$materiels = $stmtMateriels->fetchAll();
?>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg">
    <div class="container">
        <a class="navbar-brand" href="index.php">Z-EVENT</a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="index.php">Accueil</a></li>
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
            <?php foreach (['streamers' => 'Streamers', 'materiels' => 'Matériels', 'creer_streamer' => 'Créer un streamer'] as $key => $label) : ?>
                <a href="index.php?page=espace_admin&onglet=<?= $key ?>"
                   class="btn <?= $onglet === $key ? 'btn-accent' : 'btn-outline-accent' ?>">
                    <?= $label ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</nav>
</div>

<!-- CONTENU -->
<section class="py-5">
    <div class="container">

        <?php if ($message) : ?>
            <div class="alert alert-success-zevent mb-4">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <!-- ONGLET STREAMERS -->
        <?php if ($onglet === 'streamers') : ?>
            <h2 class="mb-4">
                Streamers de l'événement
                <span class="text-muted-zevent fs-6">(<?= count($streamers) ?> streamer<?= count($streamers) > 1 ? 's' : '' ?>)</span>
            </h2>

            <?php if (empty($streamers)) : ?>
                <p class="text-muted-zevent">Aucun streamer enregistré.</p>
                <a href="index.php?page=espace_admin&onglet=creer_streamer" class="btn btn-accent mt-2">Créer un streamer</a>
            <?php else : ?>
                <div class="table-responsive">
                    <table class="table table-zevent">
                        <thead>
                            <tr>
                                <th>Nom</th>
                                <th>Chaîne</th>
                                <th>Email</th>
                                <th>Matricule</th>
                                <th>Lives</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($streamers as $streamer) : ?>
                                <tr>
                                    <td><?= htmlspecialchars($streamer['nom']) ?> <?= htmlspecialchars($streamer['prenom']) ?></td>
                                    <td><?= htmlspecialchars($streamer['nom_chaine']) ?></td>
                                    <td><?= htmlspecialchars($streamer['email']) ?></td>
                                    <td><?= htmlspecialchars($streamer['matricule']) ?></td>
                                    <td>
                                        <span class="badge badge-accent">
                                            <?= $streamer['nb_lives'] ?> live<?= $streamer['nb_lives'] > 1 ? 's' : '' ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>

        <!-- ONGLET MATERIELS -->
        <?php elseif ($onglet === 'materiels') : ?>
            <h2 class="mb-4">Matériels</h2>

            <?php if (empty($materiels)) : ?>
                <p class="text-muted-zevent">Aucun matériel enregistré.</p>
            <?php else : ?>
                <div class="row g-4">
                    <?php foreach ($materiels as $materiel) : ?>
                        <div class="col-md-4">
                            <div class="card p-3">
                                <h5 class="card-title"><?= htmlspecialchars($materiel['libelle']) ?></h5>
                                <p class="card-text">🏷️ <?= htmlspecialchars($materiel['marque']) ?></p>
                                <p class="card-text">
                                    Utilisé dans <span class="text-accent fw-bold"><?= $materiel['quantite_utilisee'] ?></span>
                                    live<?= $materiel['quantite_utilisee'] > 1 ? 's' : '' ?>
                                </p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

        <!-- ONGLET CREER STREAMER -->
        <?php elseif ($onglet === 'creer_streamer') : ?>
            <h2 class="mb-4">Créer un Streamer</h2>

            <?php if ($erreur) : ?>
                <div class="alert alert-error-zevent mb-3">
                    <?= htmlspecialchars($erreur) ?>
                </div>
            <?php endif; ?>

            <div class="row justify-content-center">
                <div class="col-md-7">
                    <div class="card p-4">
                        <form method="POST" action="index.php?page=espace_admin&onglet=creer_streamer">
                            <input type="hidden" name="action" value="creer_streamer">

                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label form-label-zevent">Nom *</label>
                                    <input type="text" name="nom" class="form-control form-zevent" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label form-label-zevent">Prénom *</label>
                                    <input type="text" name="prenom" class="form-control form-zevent" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label form-label-zevent">Email *</label>
                                <input type="email" name="email" class="form-control form-zevent" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label form-label-zevent">Mot de passe *</label>
                                <input type="password" name="password" class="form-control form-zevent" required>
                            </div>

                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label form-label-zevent">Nom de chaîne *</label>
                                    <input type="text" name="nom_chaine" class="form-control form-zevent" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label form-label-zevent">Âge</label>
                                    <input type="number" name="age" class="form-control form-zevent" min="18" max="99">
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label form-label-zevent">Matricule</label>
                                <input type="text" name="matricule" class="form-control form-zevent" placeholder="STR001">
                            </div>

                            <button type="submit" class="btn btn-accent w-100">Créer le streamer</button>
                        </form>
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