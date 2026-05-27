<?php
session_start(); // Démarrage de la session pour gérer les connexions, pour éviter de se reconnecter à chaque page

// Si déjà connecté, rediriger vers la bonne page
if (isset($_SESSION['user'])) {
    if ($_SESSION['user']['role'] === 'admin') { //stock info de l'utilisateur dans la session, on peut vérifier son rôle pour le rediriger vers la bonne page
        header('Location: index.php?page=espace_admin');
    } else {
        header('Location: index.php?page=espace_streamer');
    }
    exit;
}

// Traitement du formulaire
$erreur = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') { // Si le formulaire est soumis
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $erreur = 'Veuillez remplir tous les champs.';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM User WHERE email = :email");
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user'] = [
                'id_user'    => $user['id_user'],
                'nom'        => $user['nom'],
                'prenom'     => $user['prenom'],
                'role'       => $user['role'],
                'nom_chaine' => $user['nom_chaine']
            ];
            if ($user['role'] === 'admin') {
                header('Location: index.php?page=espace_admin');
            } else {
                header('Location: index.php?page=espace_streamer');
            }
            exit;
        } else {
            $erreur = 'Email ou mot de passe incorrect.';
        }
    }
}
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
                    <a class="nav-link" href="index.php?page=lives">Lives</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="index.php?page=connexion">Connexion</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- FORMULAIRE CONNEXION -->
<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card p-4">
                    <h2 class="text-center mb-4" style="color: var(--accent);">Connexion</h2>

                    <?php if ($erreur) : ?>
                        <div class="alert" style="background-color: var(--bg-secondary); color: var(--accent); border: 1px solid var(--accent);">
                            <?= htmlspecialchars($erreur) ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="index.php?page=connexion">
                        <div class="mb-3">
                            <label class="form-label" style="color: var(--text-secondary);">Email</label>
                            <input 
                                type="email" 
                                name="email" 
                                class="form-control" 
                                style="background-color: var(--bg-secondary); border: 1px solid var(--accent); color: var(--text-primary);"
                                placeholder="votre@email.com"
                                required
                            >
                        </div>
                        <div class="mb-4">
                            <label class="form-label" style="color: var(--text-secondary);">Mot de passe</label>
                            <input 
                                type="password" 
                                name="password" 
                                class="form-control"
                                style="background-color: var(--bg-secondary); border: 1px solid var(--accent); color: var(--text-primary);"
                                placeholder="••••••••"
                                required
                            >
                        </div>
                        <button type="submit" class="btn btn-accent w-100">Se connecter</button>
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