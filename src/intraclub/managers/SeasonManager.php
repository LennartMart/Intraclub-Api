<?php

namespace intraclub\managers;

use intraclub\common\Utilities;
use intraclub\repositories\SeasonRepository;

class SeasonManager
{
    /**
     * Database connection
     *
     * @var PDO
     */
    protected $db;
    protected $rankingManager;
    protected $playerManager;
    protected $seasonRepository;

    public function __construct($db)
    {
        $this->db = $db;
        $this->rankingManager = new RankingManager($this->db);
        $this->playerManager = new PlayerManager($this->db);
        $this->seasonRepository = new SeasonRepository($this->db);
    }

    public function getStatistics($seasonId = null)
    {
        if (empty($seasonId)) {
            $seasonId = $this->seasonRepository->getCurrentSeasonId();
        }
        $statisticsInfo = $this->seasonRepository->getStatistics($seasonId);
        $response = array();
        if (!empty($statisticsInfo)) {
            for ($index = 0; $index < count($statisticsInfo); $index++) {
                $playerStats = $statisticsInfo[$index];
                $playerStatistics = Utilities::mapToPlayerStatisticsObject($playerStats);
                $response[] = $playerStatistics;
            }
        }
        return $response;
    }
    public function create($period)
    {
        //1. Get Current Season
        $previousSeasonId = $this->seasonRepository->getCurrentSeasonId();

        //2. Insert new season
        $insertSeasonQuery = "INSERT INTO intra_seizoen (seizoen) VALUES (?)";
        $insertStmt = $this->db->prepare($insertSeasonQuery);
        $insertStmt->execute([$period]);
        $newSeasonId = $this->db->lastInsertId();

        //3. Insert playerPerSeason Record for every player
        $players = $this->playerManager->getAll(false);

        $insertPlayerSeasonQuery = "INSERT INTO intra_spelerperseizoen
            SET
                speler_id = ?,
                seizoen_id = ?,
                basispunten = ?,
                gespeelde_sets = 0,
                gewonnen_sets = 0,
                gespeelde_punten = 0,
                gewonnen_punten = 0
                ";
        foreach ($players as $player) {
            $insertPlayerSeasonStmt = $this->db->prepare($insertPlayerSeasonQuery);
            $insertPlayerSeasonStmt->execute([$player["id"], $newSeasonId, 19]);
        }

        //4. Based on ranking -> Add some points
        $ranking = $this->rankingManager->get($previousSeasonId);

        $reversedRanking = array_reverse($ranking);
        $addedBasePoints = 19.0001;
        $updatePlayerSeasonQuery = "UPDATE intra_spelerperseizoen
            SET basispunten = ?
            WHERE speler_id = ? AND seizoen_id = ?";
        foreach ($reversedRanking as $rankedPlayer) {
            $updatePlayerSeasonStmt = $this->db->prepare($updatePlayerSeasonQuery);
            $updatePlayerSeasonStmt->execute([$addedBasePoints, $rankedPlayer["id"], $newSeasonId]);
            $addedBasePoints += 0.0001;
        }
    }
}
