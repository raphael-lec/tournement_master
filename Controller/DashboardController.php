<?php

class DashboardController extends AbstractController 
{
    public function admin_dashboard() : void
    {
        if (!$this->isAuthenticated() || !isset($_SESSION['user']) || $_SESSION['user']->getType() !== 'admin') {
            header("Location: index.php?route=login");
            exit();
        }

        $user = $_SESSION['user'];
        $username = $user->getName();

        $usersManager = new UsersManager();
        $usersList = $usersManager->getAllUsers();

        $tournamentManager = new TournamentManager();
        $tournamentsList = $tournamentManager->getAllTournaments();

        $this->render("admin_dashboard", [
            "pageTitle"   => "Tableau de bord Admin",
            "isConnected" => true,
            "username"    => $username,
            "user"        => $user,
            "users"       => $usersList,
            "tournaments" => $tournamentsList
        ]);
    }

    public function player_dashboard() : void
    {
        if (!$this->isAuthenticated()) {
            header("Location: index.php?route=login");
            exit();
        }

        $user = $_SESSION['user'];

        $message = null;
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
            $newName  = trim($_POST['name'] ?? '');
            $newEmail = trim($_POST['email'] ?? '');

            if (!empty($newName) && !empty($newEmail)) {
                $um = new UsersManager();
                $um->updateProfile($user->getId(), $newName, $newEmail);
                $updatedUser = $um->getUserById($user->getId());
                $_SESSION['user'] = $updatedUser;
                $user = $updatedUser;
                $message = ['type' => 'success', 'text' => 'Profil mis à jour !'];
            } else {
                $message = ['type' => 'error', 'text' => 'Champs invalides.'];
            }
        }

        $tm = new TournamentManager();
        $tournaments  = $tm->getTournamentsByUser($user->getId());
        $matchHistory = $tm->getMatchHistoryByUser($user->getId());
        $stats        = $tm->getUserStats($user->getId());

        $this->render("player_dashboard", [
            "pageTitle"    => "Mon Espace Joueur",
            "isConnected"  => true,
            "username"     => $user->getName(),
            "user"         => $user,
            "tournaments"  => $tournaments,
            "matchHistory" => $matchHistory,
            "stats"        => $stats,
            "message"      => $message,
        ]);
    }

    public function gestionary_dashboard() : void
    {
        if (!$this->isAuthenticated()) {
            header("Location: index.php?route=login");
            exit();
        }

        $user = $_SESSION['user'] ?? null;
        $username = $_SESSION['username'] ?? ($user ? $user->getName() : 'Utilisateur');

        $tm = new TournamentManager();
        $tournaments = $tm->findByOrganizer($user->getId());

        $gm = new GameManager();
        $games = $gm->getAllGames();

        $this->render("gestionary_dashboard", [
            "pageTitle"   => "Espace Gestionnaire",
            "isConnected" => true,
            "username"    => $username,
            "user"        => $user,
            "tournaments" => $tournaments,
            "games"       => $games,
        ]);
    }

    public function gestionary_tournemant_dashboard() : void
    {
        if (!$this->isAuthenticated()) {
            header("Location: index.php?route=login");
            exit();
        }

        $user = $_SESSION['user'];
        $tournamentId = isset($_GET['id']) ? (int)$_GET['id'] : null;

        if (!$tournamentId) {
            header("Location: index.php?route=gestionary_dashboard");
            exit();
        }

        $tm = new TournamentManager();

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_poules'])) {
            $nbPoules = (int)$_POST['nb_poules'];
            $sortants = (int)$_POST['sortants_par_poule'];
            $poules   = [];

            $_SESSION['sortants_' . $tournamentId] = $sortants;

            for ($i = 0; $i < $nbPoules; $i++) {
                $pouleName = chr(65 + $i);
                $poules[$pouleName] = array_map('intval', $_POST['poule_' . $pouleName] ?? []);
            }

            $tm->createPoules($tournamentId, $poules);
            header("Location: index.php?route=gestionary_tournemant_dashboard&id=$tournamentId");
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['match_id'])) {
            $sortants = (int)($_SESSION['sortants_' . $tournamentId] ?? 1);

            $tm->updateMatchScore(
                (int)$_POST['match_id'],
                (int)$_POST['score_team_1'],
                (int)$_POST['score_team_2'],
                (int)$_POST['winner_team_id']
            );

            if ($tm->isPhaseComplete($tournamentId, 'poule')) {
                $existingQuart  = $tm->getMatchesByPhase($tournamentId, 'quart');
                $existingDemi   = $tm->getMatchesByPhase($tournamentId, 'demi');
                $existingFinale = $tm->getMatchesByPhase($tournamentId, 'finale');
                if (empty($existingQuart) && empty($existingDemi) && empty($existingFinale)) {
                    $tm->generateEliminatoires($tournamentId, $sortants);
                }
            }

            if ($tm->isPhaseComplete($tournamentId, 'quart')) {
                $existingDemi = $tm->getMatchesByPhase($tournamentId, 'demi');
                if (empty($existingDemi)) {
                    $tm->generateNextRound($tournamentId, 'quart');
                }
            }

            if ($tm->isPhaseComplete($tournamentId, 'demi')) {
                $existingFinale = $tm->getMatchesByPhase($tournamentId, 'finale');
                if (empty($existingFinale)) {
                    $tm->generateNextRound($tournamentId, 'demi');
                }
            }

            if ($tm->isPhaseComplete($tournamentId, 'finale')) {
                $finaleMatchs = $tm->getMatchesByPhase($tournamentId, 'finale');
                $finaleMatch  = $finaleMatchs[0] ?? null;
                if ($finaleMatch && $finaleMatch['winner_team_id'] > 0) {
                    $winnerTeamId   = (int)$finaleMatch['winner_team_id'];
                    $runnerUpTeamId = ($winnerTeamId == $finaleMatch['team1_id'])
                        ? (int)$finaleMatch['team2_id']
                        : (int)$finaleMatch['team1_id'];
                    $tm->setTournamentResult($tournamentId, $winnerTeamId, $runnerUpTeamId);
                }
            }

            header("Location: index.php?route=gestionary_tournemant_dashboard&id=$tournamentId");
            exit();
        }

        $teams        = $tm->getTeamsByTournament($tournamentId);
        $poulesMatchs = $tm->getMatchesByPhase($tournamentId, 'poule');
        $quartMatchs  = $tm->getMatchesByPhase($tournamentId, 'quart');
        $demiMatchs   = $tm->getMatchesByPhase($tournamentId, 'demi');
        $finaleMatchs = $tm->getMatchesByPhase($tournamentId, 'finale');
        $hasPoules    = !empty($poulesMatchs);

        $this->render("gestionary_tournemant_dashboard", [
            "pageTitle"    => "Gestion du Tournoi",
            "isConnected"  => true,
            "username"     => $user->getName(),
            "user"         => $user,
            "tournamentId" => $tournamentId,
            "teams"        => $teams,
            "poulesMatchs" => $poulesMatchs,
            "quartMatchs"  => $quartMatchs,
            "demiMatchs"   => $demiMatchs,
            "finaleMatchs" => $finaleMatchs,
            "hasPoules"    => $hasPoules,
        ]);
    }

    public function profile() : void
    {
        if (!$this->isAuthenticated() || !isset($_SESSION['user'])) {
            header("Location: index.php?route=login");
            exit();
        }

        $user = $_SESSION['user'];

        switch ($user->getType()) {
            case 'admin':
                $this->admin_dashboard();
                break;
            case 'gestionnaire':
                $this->gestionary_dashboard();
                break;
            case 'user':
            default:
                $this->player_dashboard();
                break;
        }
    }


    public function delete_user() : void
    {
        if (!$this->isAuthenticated() || $_SESSION['user']->getType() !== 'admin') {
            header("Location: index.php?route=home");
            exit();
        }

        $userId = isset($_POST['user_id']) ? (int)$_POST['user_id'] : null;

        if ($userId) {
            $usersManager = new UsersManager();
            $usersManager->deleteUser($userId);
        }

        header("Location: index.php?route=admin_dashboard");
        exit();
    }

    public function delete_tournament() : void
    {
        if (!$this->isAuthenticated() || $_SESSION['user']->getType() !== 'admin') {
            header("Location: index.php?route=home");
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tournament_id'])) {
            $tournamentId = (int)$_POST['tournament_id'];
            $tournamentManager = new TournamentManager();
            $tournamentManager->deleteTournament($tournamentId);
        }

        header("Location: index.php?route=admin_dashboard");
        exit();
    }

    public function create_game() : void
    {
        if (!$this->isAuthenticated() || $_SESSION['user']->getType() !== 'admin') {
            header("Location: index.php?route=login");
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['game_name'])) {
            $gm = new GameManager();
            $gm->addGame($_POST['game_name']);
        }

        header("Location: index.php?route=admin_dashboard");
        exit();
    }

    public function change_role() : void
    {
        if (!$this->isAuthenticated() || $_SESSION['user']->getType() !== 'admin') {
            header("Location: index.php?route=home");
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'], $_POST['new_type'])) {
            $userId  = (int)$_POST['user_id'];
            $newType = $_POST['new_type'];

            $usersManager = new UsersManager();
            $usersManager->updateUserType($userId, $newType);
        }

        header("Location: index.php?route=admin_dashboard");
        exit();
    }

    public function create_tournament() : void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $tm = new TournamentManager();
            $tm->create([
                'name'            => $_POST['name'],
                'date'            => $_POST['date'],
                'format'          => (int)$_POST['format'],
                'max_participant' => (int)$_POST['max_participant'],
                'status'          => 'open',
                'owner'           => $_SESSION['user']->getId(),
                'discord_link'    => $_POST['discord_link'] ?? null,
                'game_id'         => (int)$_POST['game_id'],
            ]);
        }
        header("Location: index.php?route=gestionary_dashboard");
        exit();
    }

    public function update_status() : void
    {
        if (isset($_GET['id'], $_GET['status'])) {
            $tm = new TournamentManager();
            $tm->updateStatus((int)$_GET['id'], $_GET['status']);
        }
        header("Location: index.php?route=gestionary_dashboard");
        exit();
    }
}