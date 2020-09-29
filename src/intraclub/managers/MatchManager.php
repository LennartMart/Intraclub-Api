<?php
namespace intraclub\managers;
use intraclub\repositories\SeasonRepository;
use intraclub\repositories\MatchRepository;

class MatchManager {
    /**
     * Database connection
     *
     * @var PDO
     */
    protected $db;
    protected $seasonRepository;
    protected $matchRepository;

    public function __construct($db){
        $this->db = $db;
        $this->seasonRepository = new SeasonRepository($db);
        $this->matchRepository = new MatchRepository($db);
    }

    public function getAllBySeasonId($seasonId = null){        
        $currentSeasonId = $this->checkSeason($seasonId);
        return $this->matchRepository->getAllBySeasonId($currentSeasonId);
    }

    public function getAllByRoundId($roundId){
        return $this->matchRepository->getAllByRoundId($roundId);
    }

    public function create($roundId, $playerId1, $playerId2, $playerId3, $playerId4, 
        $set1Home, $set1Away, $set2Home, $set2Away, $set3Home, $set3Away){

        return $this->matchRepository->create($roundId, $playerId1, $playerId2, $playerId3, $playerId4,
            $set1Home, $set1Away, $set2Home, $set2Away, $set3Home, $set3Away);
    }

    private function checkSeason($seasonId){
        if(empty($seasonId)){
            return $this->seasonRepository->getCurrentSeasonId();
        }        
        return $seasonId;
    }
}