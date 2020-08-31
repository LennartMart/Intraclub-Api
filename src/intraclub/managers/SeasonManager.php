<?php

namespace intraclub\managers;

use intraclub\common\Utilities;
use intraclub\repositories\SeasonRepository;
use intraclub\repositories\StatisticsRepository;


class SeasonManager
{
    /**
     * Database connection
     *
     * @var PDO
     */
    protected $db;
    protected $rankingManager;
    protected $seasonRepository;
    /**
     *Statistics Repository
     *
     * @var StatisticsRepository
     */
    protected $statisticsRepository;

    public function __construct($db)
    {
        $this->db = $db;
        $this->rankingManager = new RankingManager($this->db);
        $this->seasonRepository = new SeasonRepository($this->db);
        $this->statisticsRepository = new StatisticsRepository($this->db);
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
        $newSeasonId = $this->seasonRepository->create($period);    

        //3. Insert playerPerSeason Record for every player & Based on ranking -> Add some points
        $ranking = $this->rankingManager->get($previousSeasonId);

        $reversedRanking = array_reverse($ranking);
        $addedBasePoints = 19.000;
        foreach ($reversedRanking as $rankedPlayer) {
            $this->statisticsRepository->createSeasonStatistics($newSeasonId, $rankedPlayer["id"], $addedBasePoints);
            $addedBasePoints += 0.0001;
        }
    }
}
