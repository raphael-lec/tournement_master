<?php
class AuthController extends AbstractController
{
    // ==========================================
    // 📝 INSCRIPTION
    // ==========================================
    public function register(): void
    {
        $error = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
            $password = filter_input(INPUT_POST, 'password');
            $username = filter_input(INPUT_POST, 'username');
            
            if (!$email || !$password || !$username) {
                $error = "Veuillez remplir tous les champs correctement.";
            } elseif (strlen($password) < 6) {
                $error = "Le mot de passe doit contenir au moins 6 caractères.";
            } else {
                $manager = new UsersManager();
                
                try {
                    if ($manager->register($email, $password, $username)) {
                        $this->redirect('index.php?route=login');
                        return;
                    } else {
                        $error = "Une erreur est survenue lors de l'enregistrement.";
                    }
                } catch (PDOException $e) {
                    $error = "Erreur de base de données : " . $e->getMessage();
                }
            }
        }

        $this->render('../auth/register', ['error' => $error]);
    }

    // ==========================================
    // 🔑 CONNEXION
    // ==========================================
    public function login(): void
    {
        $error = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
            $password = filter_input(INPUT_POST, 'password');

            if (!$email || !$password) {
                $error = "Veuillez remplir tous les champs.";
            } else {
                $manager = new UsersManager();
                $user = $manager->login($email, $password);

                if ($user) {
                    // On enregistre l'utilisateur dans la session globale
                    $_SESSION['user'] = $user;

                    // Redirection vers l'accueil une fois connecté
                    $this->redirect('index.php?route=home');
                    return;
                } else {
                    $error = "Identifiants incorrects.";
                }
            }
        }

        $this->render('../auth/login', ['error' => $error]);
    }
    public function logout() : void
{
    // Démarrer la session si ce n'est pas déjà fait
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Vider les variables de session
    $_SESSION = [];

    // Détruire la session côté serveur
    session_destroy();

    // Rediriger vers la page de connexion ou l'accueil
    header("Location: index.php?route=login");
    exit();
}
}