<?php

class TournamentController extends AbstractController 
{
    // --- LISTE DES TOURNOIS ---
    public function tournemant_list() : void
    {
        $isConnected = $this->isAuthenticated();
        $username = null;
        $user = null;

        if ($isConnected) {
            $username = $_SESSION['username'] ?? 'Utilisateur'; 
            $user = $_SESSION['user'] ?? null;
        }

        $manager = new TournamentManager();
        $tournaments = $manager->getAllTournaments();

        $this->render("tournemant_list", [
            "pageTitle"   => "Liste des Tournois",
            "isConnected" => $isConnected,
            "username"    => $username,
            "user"        => $user,
            "tournaments" => $tournaments
        ]);
    }

    // --- ACCUEIL D'UN TOURNOI ---
    public function tournemant_home() : void
    {
        $isConnected = $this->isAuthenticated();
        $user = $_SESSION['user'] ?? null;
        $username = $_SESSION['username'] ?? 'Utilisateur';

        $tournamentId = isset($_GET['id']) ? (int)$_GET['id'] : null;

        if (!$tournamentId) {
            header("Location: index.php?route=tournemant_list");
            exit();
        }

        $tm = new TournamentManager();
        $tournament = $tm->getTournamentById($tournamentId);

        if (!$tournament) {
            header("Location: index.php?route=tournemant_list");
            exit();
        }

        $message = null;
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register']) && $isConnected) {

            if ($tm->isUserRegistered($tournamentId, $user->getId())) {
                $message = ['type' => 'error', 'text' => 'Vous êtes déjà inscrit à ce tournoi.'];
            } else {
                $teamName  = trim($_POST['team_name'] ?? '');
                $memberIds = $_POST['members'] ?? [];
                $format    = (int)$tournament['format'];

                if (empty($teamName)) {
                    $message = ['type' => 'error', 'text' => 'Veuillez entrer un nom d\'équipe.'];
                } elseif ($format > 1 && count($memberIds) !== $format - 1) {
                    $message = ['type' => 'error', 'text' => 'Vous devez sélectionner exactement ' . ($format - 1) . ' coéquipier(s).'];
                } else {
                    $allMemberIds = array_merge([$user->getId()], array_map('intval', $memberIds));
                    $takenUsers = $tm->areUsersAvailable($tournamentId, $allMemberIds);

                    if (!empty($takenUsers)) {
                        $message = ['type' => 'error', 'text' => 'Ces joueurs sont déjà inscrits : ' . implode(', ', $takenUsers)];
                    } else {
                        $teamId = $tm->createTeamWithMembers($teamName, $user->getId(), $memberIds);
                        $teamsCount = $tm->countTeams($tournamentId);
                        if ($teamsCount >= $tournament['max_participant']) {
                            $message = ['type' => 'error', 'text' => 'Le tournoi est complet.'];
                        } else {
                            $tm->registerTeam($tournamentId, $teamId);
                            $message = ['type' => 'success', 'text' => 'Inscription réussie !'];
                        }
                    }
                }
            }
        }

        // Récupère les matchs par phase pour avoir le champ 'phase' rempli
        $matches = array_merge(
            $tm->getMatchesByPhase($tournamentId, 'poule'),
            $tm->getMatchesByPhase($tournamentId, 'quart'),
            $tm->getMatchesByPhase($tournamentId, 'demi'),
            $tm->getMatchesByPhase($tournamentId, 'finale')
        );

        $teams      = $tm->getTeamsByTournament($tournamentId);
        $teamsCount = $tm->countTeams($tournamentId);
        $allUsers   = $tm->getAllUsers();

        $this->render("tournemant_home", [
            "pageTitle"   => $tournament['name'],
            "isConnected" => $isConnected,
            "username"    => $username,
            "user"        => $user,
            "tournament"  => $tournament,
            "matches"     => $matches,
            "teams"       => $teams,
            "teamsCount"  => $teamsCount,
            "message"     => $message,
            "allUsers"    => $allUsers,
        ]);
    }

    // --- MATCHS D'UN TOURNOI ---
    public function tournemant_match() : void
    {
        $isConnected = $this->isAuthenticated();
        $username = null;
        $user = null;

        if ($isConnected) {
            $username = $_SESSION['username'] ?? 'Utilisateur'; 
            $user = $_SESSION['user'] ?? null;
        }

        $this->render("tournemant_match", [
            "pageTitle"   => "Matchs du Tournoi",
            "isConnected" => $isConnected,
            "username"    => $username,
            "user"        => $user,
        ]);
    }

    // --- RÉSULTATS D'UN TOURNOI ---
    public function tournemant_result() : void
    {
        $isConnected = $this->isAuthenticated();
        $username = null;
        $user = null;

        if ($isConnected) {
            $username = $_SESSION['username'] ?? 'Utilisateur'; 
            $user = $_SESSION['user'] ?? null;
        }

        $this->render("tournemant_result", [
            "pageTitle"   => "Classement & Résultats",
            "isConnected" => $isConnected,
            "username"    => $username,
            "user"        => $user,
        ]);
    }

    // --- LISTE DES JOUEURS D'UN TOURNOI ---
    public function tournemant_player_list() : void
    {
        $isConnected = $this->isAuthenticated();
        $username = null;
        $user = null;

        if ($isConnected) {
            $username = $_SESSION['username'] ?? 'Utilisateur'; 
            $user = $_SESSION['user'] ?? null;
        }

        $this->render("tournemant_player_list", [
            "pageTitle"   => "Joueurs Inscrits",
            "isConnected" => $isConnected,
            "username"    => $username,
            "user"        => $user,
        ]);
    }
}