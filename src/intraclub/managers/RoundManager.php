<?php
namespace intraclub\managers;

use intraclub\common\Utilities;
use intraclub\repositories;
use intraclub\repositories\RoundRepository;

class RoundManager {
    /**
     * Repo Layer
     *
     * @var RoundRepository
     */
    protected $roundRepository;


    public function __construct($db){
        $this->roundRepository = new RoundRepository($db);
    }

    public function getAll($seasonId = null){
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