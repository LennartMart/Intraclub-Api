<?php
namespace intraclub\managers;

use intraclub\repositories\RankingRepository;
use intraclub\repositories\RoundRepository;
use intraclub\repositories\SeasonRepository;

class RankingManager
{
    /**
     * Ranking Repo
     *
     * @var RankingRepository
     */
    protected $rankingRepository;

    /**
     * Round Repo
     *
     * @var RoundRepository
     */
    protected $roundRepository;

    protected $seasonRepository;

    public function __construct($db)
    {
        $this->rankingRepository = new RankingRepository($db);
        $this->roundRepository = new RoundRepository($db);
        $this->seasonRepository = new SeasonRepository($db);
    }

    public function get($items = null, $showGeneral = false, $showWomen = false, $showVeterans = false, $showRecreants = false, $seasonId = null, $roundId = null)
    {
        // Check if parameters are filled in
        // If not => return latest season, and latest calculated round
        $seasonId = $this->checkSeason($seasonId);
        $round = $this->checkRound($roundId, $seasonId);

        // If round is still empty => no calculated round for current season
        if (empty($round)) {
            return $this->rankingRepository->getRankingForNewSeason($seasonId);
        }

        $ranking = $this->rankingRepository->getRankingAfterRound($round["id"]);
        $previousRanking = array();

        if ($round["number"] > 1) {
            $previousRound = $this->roundRepository->getBySeasonAndNumber($seasonId, $round["number"] - 1);
            $previousRanking = $this->rankingRepository->getRankingAfterRound($previousRound["id"]);
        }
        //Build the rankings
        //Response
        $response = array("seasonId" => $seasonId);
        if ($showGeneral) {
            $response["general"] = $this->buildRanking($ranking, $previousRanking, "filterNothing", $items);
        }
        if ($showWomen) {
            $response["women"] = $this->buildRanking($ranking, $previousRanking, "filterWoman", $items);
        }
        if ($showRecreants) {
            $response["recreants"] = $this->buildRanking($ranking, $previousRanking, "filterRecreant", $items);
        }
        if ($showVeterans) {
            $response["veterans"] = $this->buildRanking($ranking, $previousRanking, "filterVeteran", $items);
        };
        return $response;
    }

    private function checkSeason($seasonId)
    {
        if (empty($seasonId)) {
            return $this->seasonRepository->getCurrentSeasonId();
        }
        return $seasonId;
    }

    private function checkRound($roundId, $seasonId)
    {
        if (empty($roundId)) {
            return $this->roundRepository->getLastCalculated($seasonId);
        }
        return $this->roundRepository->getById($roundId);
    }

    /*
    Generic Ranking builder function
    Accepts filterfunction to filter players on specific property
     */
    private function buildRanking($ranking, $previousRanking, $filterfunction, $items)
    {
        // Use array_values to reset keys
        $specificCurrentRanking = array_values(array_filter($ranking, array($this, $filterfunction)));
        $specificPreviousRanking = array();

        if (!empty($previousRanking)) {
            $specificPreviousRanking = array_values(array_filter($previousRanking, array($this, $filterfunction)));
        }
        $specificRanking = array();
        if (empty($items) || $items > $specificCurrentRanking) {
            $items = count($specificCurrentRanking);
        }
        for ($index = 0; $index < $items; $index++) {
            $player = $this->mapToRankingObject($index, $specificCurrentRanking, $specificPreviousRanking);
            $specificRanking[] = $player;
        }
        return $specificRanking;
    }

    /*
    Filter function to build rankings
     */
    private function filterNothing($player)
    {
        return true;
    }
    private function filterWoman($player)
    {
        return $player["gender"] == "Vrouw";
    }
    private function filterRecreant($player)
    {
        return $player["ranking"] == "Recreant";
    }
    private function filterVeteran($player)
    {
        return $player["veteran"] == 1;

    }

    /*
    Map to response object
     */
    private function mapToRankingObject($index, $currentRanking, $previousRanking)
    {
        return array(
            "id" => $currentRanking[$index]["id"],
            "name" => $currentRanking[$index]["name"],
            "firstName" => $currentRanking[$index]["firstName"],
            "average" => round($currentRanking[$index]["average"], 2),
            "rank" => $index + 1,
            "difference" => $this->findPreviousRanking($currentRanking[$index]["id"], $index + 1, $previousRanking),
        );
    }

    /*
    Find difference with previous ranking
    Returns 0 if no previous ranking available
     */
    private function findPreviousRanking($playerId, $currentRank, $previousRanking)
    {
        $difference = 0;
        if (!empty($previousRanking)) {
            $foundIndex = array_search($playerId, array_column($previousRanking, 'id'));
            $previousRank = $foundIndex + 1;
            $difference = $previousRank - $currentRank;
        }
        return $difference;
    }
}
