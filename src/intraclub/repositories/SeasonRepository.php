<?php

namespace intraclub\repositories;


class SeasonRepository
{
    /**
     * Database connection
     *
     * @var PDO
     */
    protected $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function getCurrentSeasonId()
    {
        $currentSeason = $this->db->query("SELECT id, seizoen as season FROM intra_seizoen ORDER BY id DESC LIMIT 1;")->fetch();
        return $currentSeason["id"];
    }

    public function getStatistics($seasonId)
    {
        $query = "SELECT IPLAYER.id, IPLAYER.voornaam AS firstname, IPLAYER.naam AS name, 
                ISPS.gespeelde_sets AS setsPlayed, ISPS.gewonnen_sets AS setsWon, ISPS.gespeelde_punten AS pointsPlayed,
                ISPS.gewonnen_punten AS pointsWon, ISPS.gespeelde_matchen as matchesPlayed, ISPS.gewonnen_matchen AS matchesWon,
                ISPS.speeldagen_aanwezig AS roundsPresent
            FROM intra_spelers IPLAYER
            INNER JOIN intra_spelerperseizoen ISPS ON ISPS.speler_id = IPLAYER.Id
            WHERE ISPS.seizoen_id = ? AND IPLAYER.is_lid = 1";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$seasonId]);
        return $stmt->fetchAll();
    }

    public function create($period){
        $insertSeasonQuery = "INSERT INTO intra_seizoen (seizoen) VALUES (?)";
        $insertStmt = $this->db->prepare($insertSeasonQuery);
        $insertStmt->execute([$period]);
        return $this->db->lastInsertId();
    }
}
