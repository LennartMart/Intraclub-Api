<?php
namespace intraclub\repositories;

use PDO;

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
        //TODO
        //$insertPlayerSeasonStmt->execute([$playerId, $seasonId, $basePoints]);
    }

    public function updateSeasonStatistics($seasonId, $playerId, $setsPlayed, $setsWon, $pointsPlayed, $pointsWon, $matchesPlayed, $matchesWon, $roundsPresent){

        $updatePlayerSeasonStmt = $this->db->prepare("UPDATE intra_spelerperseizoen
            SET
                gespeelde_sets = :setsPlayed,
                gewonnen_sets = :setsWon,
                gespeelde_punten= :pointsPlayed,
                gewonnen_punten = :pointsWon,
                gespeelde_matchen = :matchesPlayed,
                gewonnen_matchen = :matchesWon,
                speeldagen_aanwezig= :roundsPresent

            WHERE speler_id = :playerId AND seizoen_id = :seasonId");

        $updatePlayerSeasonStmt->bindParam(':setsPlayed', $setsPlayed, PDO::PARAM_INT);
        $updatePlayerSeasonStmt->bindParam(':setsWon', $setsWon, PDO::PARAM_INT);
        $updatePlayerSeasonStmt->bindParam(':pointsPlayed', $pointsPlayed, PDO::PARAM_INT);
        $updatePlayerSeasonStmt->bindParam(':pointsWon', $pointsWon, PDO::PARAM_INT);
        $updatePlayerSeasonStmt->bindParam(':matchesPlayed', $matchesPlayed, PDO::PARAM_INT);
        $updatePlayerSeasonStmt->bindParam(':matchesWon', $matchesWon, PDO::PARAM_INT);
        $updatePlayerSeasonStmt->bindParam(':playerId', $playerId, PDO::PARAM_INT);
        $updatePlayerSeasonStmt->bindParam(':seasonId', $seasonId, PDO::PARAM_INT);
        $updatePlayerSeasonStmt->bindParam(':roundsPresent', $roundsPresent, PDO::PARAM_INT);
        //TODO
        //$updatePlayerSeasonStmt->execute();
    }

    public function insertOrUpdateRoundStatistics($roundId, $playerId, $average){

        $updatePlayerSeasonStmt = $this->db->prepare("INSERT INTO
            intra_spelerperspeeldag
            SET
                gemiddelde = :average,
                speler_id = :playerId,
                speeldag_id = :roundId
            ON DUPLICATE KEY UPDATE
                gemiddelde = :average");

        $updatePlayerSeasonStmt->bindParam(':average', $average, PDO::PARAM_INT);
        $updatePlayerSeasonStmt->bindParam(':playerId', $playerId, PDO::PARAM_INT);
        $updatePlayerSeasonStmt->bindParam(':roundId', $roundId, PDO::PARAM_INT);
        //TODO
        //$updatePlayerSeasonStmt->execute();
    }
   
}