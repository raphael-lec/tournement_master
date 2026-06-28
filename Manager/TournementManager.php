<?php
// Model/TournamentManager.php

class TournamentManager 
{
    private PDO $db;

    public function __construct()
    {
        $host     = $_ENV['DB_HOST'] ?? 'localhost';
        $dbname   = $_ENV['DB_NAME'] ?? 'nom_de_ta_base';
        $user     = $_ENV['DB_USER'] ?? 'root';
        $password = $_ENV['DB_PASSWORD'] ?? '';

        $this->db = new PDO(
            "mysql:host=$host;dbname=$dbname;charset=utf8", 
            $user, 
            $password
        );
        
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function addTournament(tournement $tournament) : bool
    {
        $query = $this->db->prepare("
            INSERT INTO tournement (game_id, owner, date, max_participant, status_id, format) 
            VALUES (:game_id, :owner, :date, :max_participant, :status_id, :format)
        ");

        return $query->execute([
            'game_id'         => $tournament->getGameId(),
            'owner'           => $tournament->getOwner(),
            'date'            => $tournament->getDate(),
            'max_participant' => $tournament->getMaxParticipant(),
            'status_id'       => $tournament->getStatusId(),
            'format'          => $tournament->getFormat()
        ]);
    }

    public function deleteTournament(int $id) : bool
    {
        $query = $this->db->prepare("DELETE FROM tournaments WHERE id = :id");
        return $query->execute(['id' => $id]);
    }

    public function getAllTournaments() : array
    {
        $query = $this->db->query("SELECT * FROM tournement");
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findByOrganizer(int $organizerId) : array
    {
        $query = $this->db->prepare("SELECT * FROM tournement WHERE owner = :owner_id");
        $query->execute(['owner_id' => $organizerId]);
        return $query->fetchAll(PDO::FETCH_ASSOC); 
    }

    public function create(array $data) : void
    {
        $query = $this->db->prepare("
            INSERT INTO tournement (name, format, date, owner, max_participant, status) 
            VALUES (:name, :format, :date, :owner, :max_participant, :status)
        ");
        
        $query->execute([
            'name'            => $data['name'],
            'format'          => $data['format'],
            'date'            => $data['date'],
            'owner'           => $data['owner'],
            'max_participant' => $data['max_participant'],
            'status'          => $data['status']
        ]);
    }

    public function updateStatus(int $id, string $status) : void
    {
        $query = $this->db->prepare("UPDATE tournement SET status = :status WHERE id = :id");
        $query->execute(['status' => $status, 'id' => $id]);
    }

    // Vrais matchs uniquement (score_team_1 NOT NULL)
    public function getMatchesByTournament(int $tournamentId) : array
    {
        $query = $this->db->prepare("
            SELECT p.*, 
                   t1.name as team1_name, 
                   t2.name as team2_name
            FROM party p
            LEFT JOIN party_participant pp1 ON pp1.party_id = p.id
            LEFT JOIN team t1 ON t1.id = pp1.team_id
            LEFT JOIN party_participant pp2 ON pp2.party_id = p.id AND pp2.team_id != pp1.team_id
            LEFT JOIN team t2 ON t2.id = pp2.team_id
            WHERE p.tournement_id = :tournament_id
            AND p.score_team_1 IS NOT NULL
            GROUP BY p.id
        ");
        $query->execute(['tournament_id' => $tournamentId]);
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    // Récupère toutes les équipes d'un tournoi via tournement_id
    public function getTeamsByTournament(int $tournamentId) : array
    {
        $query = $this->db->prepare("
            SELECT t.id, t.name, t.leader
            FROM team t
            WHERE t.tournement_id = :tournament_id
        ");
        $query->execute(['tournament_id' => $tournamentId]);
        $teams = $query->fetchAll(PDO::FETCH_ASSOC);

        foreach ($teams as &$team) {
            $q2 = $this->db->prepare("
                SELECT u.id, u.name 
                FROM users u
                JOIN team_member tu ON tu.user_id = u.id
                WHERE tu.team_id = :team_id
            ");
            $q2->execute(['team_id' => $team['id']]);
            $team['members'] = $q2->fetchAll(PDO::FETCH_ASSOC);
        }

        return $teams;
    }

    public function updateMatchScore(int $matchId, int $score1, int $score2, int $winnerId) : void
    {
        $query = $this->db->prepare("
            UPDATE party 
            SET score_team_1 = :s1, score_team_2 = :s2, winner_team_id = :winner
            WHERE id = :id
        ");
        $query->execute([
            's1'     => $score1,
            's2'     => $score2,
            'winner' => $winnerId,
            'id'     => $matchId
        ]);
    }

    public function getTournamentById(int $id) : ?array
    {
        $query = $this->db->prepare("SELECT * FROM tournement WHERE id = :id");
        $query->execute(['id' => $id]);
        return $query->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    // Compte les équipes via tournement_id
    public function countTeams(int $tournamentId) : int
    {
        $query = $this->db->prepare("
            SELECT COUNT(*) as total FROM team
            WHERE tournement_id = :tid
        ");
        $query->execute(['tid' => $tournamentId]);
        return (int)$query->fetch(PDO::FETCH_ASSOC)['total'];
    }

    // Vérifie si une équipe est déjà inscrite via tournement_id
    public function isTeamRegistered(int $tournamentId, int $teamId) : bool
    {
        $query = $this->db->prepare("
            SELECT COUNT(*) as cnt FROM team
            WHERE tournement_id = :tid AND id = :team_id
        ");
        $query->execute(['tid' => $tournamentId, 'team_id' => $teamId]);
        return (int)$query->fetch(PDO::FETCH_ASSOC)['cnt'] > 0;
    }

    // Inscrit une équipe en la liant au tournoi via tournement_id
    public function registerTeam(int $tournamentId, int $teamId) : void
    {
        $query = $this->db->prepare("
            UPDATE team SET tournement_id = :tid WHERE id = :team_id
        ");
        $query->execute(['tid' => $tournamentId, 'team_id' => $teamId]);
    }

    public function getTeamByUser(int $userId) : ?array
    {
        $query = $this->db->prepare("
            SELECT t.* FROM team t
            JOIN team_member tu ON tu.team_id = t.id
            WHERE tu.user_id = :uid
            LIMIT 1
        ");
        $query->execute(['uid' => $userId]);
        return $query->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function createSoloTeam(int $userId, string $userName) : int
    {
        $query = $this->db->prepare("
            INSERT INTO team (name, leader) VALUES (:name, :leader)
        ");
        $query->execute(['name' => $userName . '_solo', 'leader' => $userId]);
        $teamId = (int)$this->db->lastInsertId();

        $query2 = $this->db->prepare("
            INSERT INTO team_member (team_id, user_id) VALUES (:tid, :uid)
        ");
        $query2->execute(['tid' => $teamId, 'uid' => $userId]);

        return $teamId;
    }

    public function getAllUsers() : array
    {
        $query = $this->db->query("SELECT id, name FROM users ORDER BY name");
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createTeamWithMembers(string $teamName, int $leaderId, array $memberIds) : int
    {
        $query = $this->db->prepare("INSERT INTO team (name, leader) VALUES (:name, :leader)");
        $query->execute(['name' => $teamName, 'leader' => $leaderId]);
        $teamId = (int)$this->db->lastInsertId();

        $q = $this->db->prepare("INSERT INTO team_member (team_id, user_id) VALUES (:tid, :uid)");
        $q->execute(['tid' => $teamId, 'uid' => $leaderId]);

        foreach ($memberIds as $memberId) {
            if ((int)$memberId !== $leaderId) {
                $q->execute(['tid' => $teamId, 'uid' => (int)$memberId]);
            }
        }

        return $teamId;
    }
    public function isUserRegistered(int $tournamentId, int $userId) : bool
{
    $query = $this->db->prepare("
        SELECT COUNT(*) as cnt
        FROM team t
        JOIN team_member tm ON tm.team_id = t.id
        WHERE t.tournement_id = :tid AND tm.user_id = :uid
    ");
    $query->execute(['tid' => $tournamentId, 'uid' => $userId]);
    return (int)$query->fetch(PDO::FETCH_ASSOC)['cnt'] > 0;
}
public function areUsersAvailable(int $tournamentId, array $userIds) : array
{
    $taken = [];
    foreach ($userIds as $uid) {
        $query = $this->db->prepare("
            SELECT u.name FROM users u
            JOIN team_member tm ON tm.user_id = u.id
            JOIN team t ON t.id = tm.team_id
            WHERE t.tournement_id = :tid AND u.id = :uid
        ");
        $query->execute(['tid' => $tournamentId, 'uid' => (int)$uid]);
        $result = $query->fetch(PDO::FETCH_ASSOC);
        if ($result) {
            $taken[] = $result['name'];
        }
    }
    return $taken;
}
public function createPoules(int $tournamentId, array $poules) : void
{
    foreach ($poules as $pouleName => $teamIds) {
        for ($i = 0; $i < count($teamIds); $i++) {
            for ($j = $i + 1; $j < count($teamIds); $j++) {
                $query = $this->db->prepare("
                    INSERT INTO party (tournement_id, score_team_1, score_team_2, winner_team_id, phase, poule_name)
                    VALUES (:tid, 0, 0, 0, 'poule', :poule)
                ");
                $query->execute(['tid' => $tournamentId, 'poule' => $pouleName]);
                $partyId = (int)$this->db->lastInsertId();

                $q = $this->db->prepare("INSERT INTO party_participant (party_id, team_id) VALUES (:pid, :tid)");
                $q->execute(['pid' => $partyId, 'tid' => $teamIds[$i]]);
                $q->execute(['pid' => $partyId, 'tid' => $teamIds[$j]]);
            }
        }
    }
}
public function getMatchesByPhase(int $tournamentId, string $phase) : array
{
    $query = $this->db->prepare("
        SELECT p.*, 
               t1.name as team1_name, t1.id as team1_id,
               t2.name as team2_name, t2.id as team2_id
        FROM party p
        LEFT JOIN party_participant pp1 ON pp1.party_id = p.id
        LEFT JOIN team t1 ON t1.id = pp1.team_id
        LEFT JOIN party_participant pp2 ON pp2.party_id = p.id AND pp2.team_id != pp1.team_id
        LEFT JOIN team t2 ON t2.id = pp2.team_id
        WHERE p.tournement_id = :tid AND p.phase = :phase
        AND p.score_team_1 IS NOT NULL
        GROUP BY p.id
        ORDER BY p.poule_name, p.id
    ");
    $query->execute(['tid' => $tournamentId, 'phase' => $phase]);
    return $query->fetchAll(PDO::FETCH_ASSOC);
}

public function isPhaseComplete(int $tournamentId, string $phase) : bool
{
    $query = $this->db->prepare("
        SELECT COUNT(*) as total,
               SUM(CASE WHEN winner_team_id > 0 THEN 1 ELSE 0 END) as done
        FROM party
        WHERE tournement_id = :tid AND phase = :phase
    ");
    $query->execute(['tid' => $tournamentId, 'phase' => $phase]);
    $result = $query->fetch(PDO::FETCH_ASSOC);
    return $result['total'] > 0 && $result['total'] == $result['done'];
}

public function getQualifiedFromPoules(int $tournamentId, int $sortantsParPoule) : array
{
    $query = $this->db->prepare("
        SELECT DISTINCT poule_name FROM party 
        WHERE tournement_id = :tid AND phase = 'poule'
        ORDER BY poule_name
    ");
    $query->execute(['tid' => $tournamentId]);
    $poules = $query->fetchAll(PDO::FETCH_COLUMN);

    $qualified = [];
    foreach ($poules as $pouleName) {
        $query2 = $this->db->prepare("
            SELECT pp.team_id, t.name,
                SUM(CASE WHEN p.winner_team_id = pp.team_id THEN 3
                         WHEN p.winner_team_id = 0 THEN 1
                         ELSE 0 END) as points
            FROM party_participant pp
            JOIN party p ON p.id = pp.party_id
            JOIN team t ON t.id = pp.team_id
            WHERE p.tournement_id = :tid AND p.phase = 'poule' AND p.poule_name = :poule
            GROUP BY pp.team_id, t.name
            ORDER BY points DESC
            LIMIT :limit
        ");
        $query2->bindValue(':tid', $tournamentId, PDO::PARAM_INT);
        $query2->bindValue(':poule', $pouleName, PDO::PARAM_STR);
        $query2->bindValue(':limit', $sortantsParPoule, PDO::PARAM_INT);
        $query2->execute();
        $qualified = array_merge($qualified, $query2->fetchAll(PDO::FETCH_ASSOC));
    }
    return $qualified;
}

public function generateEliminatoires(int $tournamentId, int $sortantsParPoule) : void
{
    $qualified = $this->getQualifiedFromPoules($tournamentId, $sortantsParPoule);
    $teamIds = array_column($qualified, 'team_id');

    $nb = count($teamIds);
    if ($nb >= 8) $phase = 'quart';
    elseif ($nb >= 4) $phase = 'demi';
    else $phase = 'finale';

    for ($i = 0; $i < count($teamIds) - 1; $i += 2) {
        if (!isset($teamIds[$i + 1])) break;
        $query = $this->db->prepare("
            INSERT INTO party (tournement_id, score_team_1, score_team_2, winner_team_id, phase)
            VALUES (:tid, 0, 0, 0, :phase)
        ");
        $query->execute(['tid' => $tournamentId, 'phase' => $phase]);
        $partyId = (int)$this->db->lastInsertId();

        $q = $this->db->prepare("INSERT INTO party_participant (party_id, team_id) VALUES (:pid, :tid)");
        $q->execute(['pid' => $partyId, 'tid' => $teamIds[$i]]);
        $q->execute(['pid' => $partyId, 'tid' => $teamIds[$i + 1]]);
    }
}

public function generateNextRound(int $tournamentId, string $currentPhase) : void
{
    $nextPhase = match($currentPhase) {
        'quart' => 'demi',
        'demi'  => 'finale',
        default => null
    };
    if (!$nextPhase) return;

    $query = $this->db->prepare("
        SELECT winner_team_id FROM party
        WHERE tournement_id = :tid AND phase = :phase AND winner_team_id > 0
    ");
    $query->execute(['tid' => $tournamentId, 'phase' => $currentPhase]);
    $winners = $query->fetchAll(PDO::FETCH_COLUMN);

    for ($i = 0; $i < count($winners) - 1; $i += 2) {
        if (!isset($winners[$i + 1])) break;
        $query2 = $this->db->prepare("
            INSERT INTO party (tournement_id, score_team_1, score_team_2, winner_team_id, phase)
            VALUES (:tid, 0, 0, 0, :phase)
        ");
        $query2->execute(['tid' => $tournamentId, 'phase' => $nextPhase]);
        $partyId = (int)$this->db->lastInsertId();

        $q = $this->db->prepare("INSERT INTO party_participant (party_id, team_id) VALUES (:pid, :tid)");
        $q->execute(['pid' => $partyId, 'tid' => $winners[$i]]);
        $q->execute(['pid' => $partyId, 'tid' => $winners[$i + 1]]);
    }
}
}