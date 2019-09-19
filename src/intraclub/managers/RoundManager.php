<?php
namespace intraclub\managers;

use intraclub\repositories\RoundRepository;
use intraclub\repositories\SeasonRepository;
use intraclub\common\Utilities;

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
                $match = Utilities::mapToMatchObject($roundInformation[$index]);
                $response["matches"][] = $match;
            }
        }
        return $response;
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