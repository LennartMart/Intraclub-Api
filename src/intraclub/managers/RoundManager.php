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