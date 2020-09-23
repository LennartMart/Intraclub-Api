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
    private function checkSeason($seasonId){
        if(empty($seasonId)){
            return $this->seasonRepository->getCurrentSeasonId();
        }        
        return $seasonId;
    }
}