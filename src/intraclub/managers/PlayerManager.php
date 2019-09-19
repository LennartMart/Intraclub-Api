<?php
namespace intraclub\managers;

use intraclub\common\Utilities;
use intraclub\repositories\MatchRepository;
use intraclub\repositories\PlayerRepository;
use intraclub\repositories\RankingRepository;
use intraclub\repositories\SeasonRepository;

class PlayerManager {
    /**
     * Repo Layer
     *
     * @var PlayerRepository
     */
    protected $playerRepository;
    protected $seasonRepository;
    protected $rankingRepository;
    protected $matchRepository;

    public function __construct($db){
        $this->playerRepository = new PlayerRepository($db);
        $this->seasonRepository = new SeasonRepository($db);
        $this->rankingRepository = new RankingRepository($db);
        $this->matchRepository = new MatchRepository($db);
    }

    public function getAll($onlyMembers = true){
        return $this->playerRepository->getAll($onlyMembers);
    }

    public function getById($id){   
        return $this->playerRepository->getById($id);
    }

    public function getByIdWithSeasonInfo($id, $seasonId){
        $response = array();
        if(empty($id)){
            return $response;
        }
        if(empty($seasonId)){
            $seasonId = $this->seasonRepository->getCurrentSeasonId();
        }
        //GetById + base statistics
        $response = $this->getAndMapPlayerInfoWithSeasonStats($id, $seasonId);
        //GetMatches
        $response["matches"] = $this->getAndMapMatches($id, $seasonId);
        //GetRankingHistory
        $response["statistics"]["rankingHistory"] = $this->getAndMapRankingHistory($id, $seasonId);
        return $response;
    }
    private function getAndMapRankingHistory($id, $seasonId){
        $rankingHistory = $this->rankingRepository->getRankingHistoryByPlayerAndSeason($id, $seasonId);
        $mappedRankingHistory = array();
        if(!empty($rankingHistory)){
            for($index = 0; $index < count($rankingHistory); $index++){
                $rankingObject = array(
                    "id" => $rankingHistory[$index]["speeldag_id"],
                    "number" => intval($rankingHistory[$index]["speeldagnummer"]),
                    "average" => round($rankingHistory[$index]["average"], 2),
                    "rank" => intval($rankingHistory[$index]["rank"])
                );
                $mappedRankingHistory[] = $rankingObject;
            }
        }
        return $mappedRankingHistory;
    }

    private function getAndMapMatches($id, $seasonId){
        $matchesFromDB = $this->matchRepository->getAllBySeasonAndPlayerId($seasonId, $id);
        $matches = array();
        if(!empty($matchesFromDB)){
            for($index = 0; $index < count($matchesFromDB); $index++) {
                $match = Utilities::mapToMatchObject($matchesFromDB[$index]);
                $matches[] = $match;
            }
        }
        return $matches;
    }
    private function getAndMapPlayerInfoWithSeasonStats($id, $seasonId){
        $playerStats = $this->playerRepository->getByIdWithSeasonInfo($id, $seasonId);
        return array(
            "id" => $playerStats["id"],
            "firstName" => $playerStats["firstname"],
            "name" => $playerStats["name"],
            "statistics" => array(
                "points" => array(
                    "won" => intval($playerStats["wonPoints"]),
                    "lost" => $playerStats["playedPoints"] - $playerStats["wonPoints"],
                    "total" => intval($playerStats["playedPoints"])
                ),
                "sets" => array(
                    "won" => intval($playerStats["wonSets"]),
                    "lost" => $playerStats["playedSets"] - $playerStats["wonSets"],
                    "total" => intval($playerStats["playedSets"]) 
                ),
                "matches" => array(
                    "won" => intval($playerStats["wonMatches"]),
                    "lost" => $playerStats["playedMatches"] - $playerStats["wonMatches"],
                    "total" => intval($playerStats["playedMatches"]) 
                ),
                "rounds" => array(
                    "present" => intval($playerStats["roundsPresent"])
                )
            )
        );

    }

}