<?php
require_once __DIR__ . '/../classes/UserRepository.php';

$erreur = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $erreur = 'Veuillez remplir tous les champs.';
    } else {
        $userRepository = new UserRepository();
        $user = $userRepository->findByEmail($email);

        if ($user && password_verify($password, $user->password)) {
            $_SESSION['user'] = [
                'id_user'    => $user->id_user,
                'nom'        => $user->nom,
                'prenom'     => $user->prenom,
                'role'       => $user->role,
                'nom_chaine' => $user->nom_chaine
            ];
            if ($user->isAdmin()) {
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
                    <h2 class="text-center mb-4 text-accent">Connexion</h2>

                    <?php if ($erreur) : ?>
                        <div class="alert alert-error-zevent">
                            <?= htmlspecialchars($erreur) ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="index.php?page=connexion">
                        <div class="mb-3">
                            <label class="form-label form-label-zevent">Email</label>
                            <input
                                type="email"
                                name="email"
                                class="form-control form-zevent"
                                placeholder="votre@email.com"
                                required
                            >
                        </div>
                        <div class="mb-4">
                            <label class="form-label form-label-zevent">Mot de passe</label>
                            <input
                                type="password"
                                name="password"
                                class="form-control form-zevent" 
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