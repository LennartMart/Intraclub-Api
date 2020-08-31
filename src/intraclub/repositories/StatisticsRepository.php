<?php
namespace intraclub\repositories;


class StatisticsRepository {
    /**
     * Database connection
     *
     * @var PDO
     */
    protected $db;

    public function __construct($db){
        $this->db = $db;
    }

    public function createSeasonStatistics($seasonId, $playerId, $basePoints){
        $insertPlayerSeasonQuery = "INSERT INTO intra_spelerperseizoen
            SET
                speler_id = ?,
                seizoen_id = ?,
                basispunten = ?,
                gespeelde_sets = 0,
                gewonnen_sets = 0,
                gespeelde_punten = 0,
                gewonnen_punten = 0,
                gespeelde_matchen = 0,
                gewonnen_matchen = 0
                ";
        $insertPlayerSeasonStmt = $this->db->prepare($insertPlayerSeasonQuery);
        $insertPlayerSeasonStmt->execute([$playerId, $seasonId, $basePoints]);
    }

    public function updateSeasonStatistics($seasonId, $playerId, $playedSets, $setsWon, $playedPoints, $pointsWon, $playedMatches, $matchesWon){
        $updatePlayerSeasonStmt = $this->db->prepare("UPDATE intra_spelerperseizoen
            SET
                gespeelde_sets = ?,
                gewonnen_sets = ?,
                gespeelde_punten= ?,
                gewonnen_punten = ?,
                gespeelde_matchen = ?,
                gewonnen_matchen = ?

            WHERE speler_id = ? AND seizoen_id = ?");

        $stmt->bind_param("iiiiiiii", $playedSets, $setsWon, $playedPoints, $pointsWon, $playedMatches, $matchesWon, $playerId, $seasonId);
        return $stmt->execute();
    }

    public function insertOrUpdateRoundStatistics($roundId, $playerId, $average){
        $updatePlayerSeasonStmt = $this->db->prepare("INSERT INTO
            intra_spelerperspeeldag
            SET
                gemiddelde = ?,
                speler_id = ?,
                speeldag_id = ?
            ON DUPLICATE KEY UPDATE
                gemiddelde = ?");

        $stmt->bind_param("iiii", $average, $playerId, $roundId, $average);
        return $stmt->execute();
    }
   
}