<?php
namespace intraclub\managers;

use intraclub\repositories\RoundRepository;
use intraclub\repositories\SeasonRepository;

class RoundManager {
    /**
     * Repo Layer
     *
     * @var RoundRepository
     */
    protected $roundRepository;
    protected $seasonRepository;

    public function __construct($db){
        $this->roundRepository = new RoundRepository($db);
        $this->seasonRepository = new SeasonRepository($db);
    }

    public function getByIdWithMatches($id){
        $roundInformation = $this->roundRepository->getWithMatches($id);
        $response = array();
        if(!empty($roundInformation)){
            $response = array(
                "id" => $roundInformation[0]["id"],
                "number" => $roundInformation[0]["number"],
                "averageAbsent" => $roundInformation[0]["averageAbsent"],
                "date" => $roundInformation[0]["date"]
            );
            $response["matches"]= array();
            for($index = 0; $index < count($roundInformation); $index++){
                $match = $this->mapToMatchObject($roundInformation[$index]);
                $response["matches"][] = $match;
            }
        }
        return $response;
    }
    public function mapToMatchObject($match){
        return array(
            "home" => array (
                "firstPlayer" => array(
                    "id" => $match["home_firstPlayer_Id"],
                    "firstName" => $match["home_firstPlayer_firstName"],
                    "name" => $match["home_firstPlayer_name"]
                ),
                "secondPlayer" => array(
                    "id" => $match["home_secondPlayer_Id"],
                    "firstName" => $match["home_secondPlayer_firstName"],
                    "name" => $match["home_secondPlayer_name"]
                ),               
            ),
            "away" => array (
                "firstPlayer" => array(
                    "id" => $match["away_firstPlayer_Id"],
                    "firstName" => $match["away_firstPlayer_firstName"],
                    "name" => $match["away_firstPlayer_name"]
                ),
                "secondPlayer" => array(
                    "id" => $match["away_secondPlayer_Id"],
                    "firstName" => $match["away_secondPlayer_firstName"],
                    "name" => $match["away_secondPlayer_name"]
                ),               
            ),
            "firstSet" => array(
                "home" => $match["firstSet_home"],
                "away" => $match["firstSet_away"]
            ),
            "secondSet" => array(
                "home" => $match["secondSet_home"],
                "away" => $match["secondSet_away"]
            ),
            "thirdSet" => array(
                "home" => $match["thirdSet_home"],
                "away" => $match["thirdSet_away"],
                "played" => $match["thirdSet_home"] != "0" &&  $match["thirdSet_away"] != "0"
            )         
        );
    }
    public function getAll($seasonId = null){
        if(empty($seasonId)){
            $seasonId = $this->seasonRepository->getCurrentSeasonId();
        }
        return $this->roundRepository->getAll($seasonId);
    }

    public function getById($id){   
        return $this->roundRepository->getById($id);
    }

    public function getBySeasonAndNumber($seasonId, $number){   
        return $this->roundRepository->getBySeasonAndNumber($seasonId, $number);
    }

    public function getLastCalculated($seasonId = null){
        return $this->roundRepository->getLastCalculated($seasonId);
    }

}