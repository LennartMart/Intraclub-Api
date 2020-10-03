<?php
namespace intraclub\validators;

use intraclub\repositories\PlayerRepository;
use intraclub\common\Utilities;

class PlayerValidator {

    /**
     * Database connection
     *
     * @var PDO
     */
    protected $db;    
    /**
     * playerRepository
     *
     * @var PlayerRepository
     */
    protected $playerRepository;

    public function __construct($db){
        $this->db = $db;
        $this->playerRepository = new PlayerRepository($db);
    }
    
    /**
     * Validatie creatie nieuwe speler
     *
     * @param  string $firstName
     * @param  string $name
     * @param  string $gender
     * @param  bool $isYouth
     * @param  bool $isVeteran
     * @param  string $ranking
     * @param  int $basePoints
     * @return array(string) errors
     */
    public function validateNewPlayer($firstName, $name, $gender, $isYouth, $isVeteran, $ranking, $basePoints){
        $errors = array();
        $errors = $this->validatePlayer($firstName, $name, $gender, $isYouth, $isVeteran, $ranking, $errors);
        if (Utilities::isInt($basePoints) === false ) {
            $errors[] = "Ongeldige basispunten";
        }
        else if( $basePoints < 0 || $basePoints > 21){
            $errors[] = "Basispunten ongeldig";
        }
        return $errors;
    }
    
    /**
     * Validatie aanpassen bestaande speler
     *
     * @param  int $id
     * @param  string $firstName
     * @param  string $name
     * @param  string $gender
     * @param  bool $isYouth
     * @param  bool $isVeteran
     * @param  string $ranking
     * @return array(string) errors
     */
    public function validateExistingPlayer($id, $firstName, $name, $gender, $isYouth, $isVeteran, $ranking){
        $errors = array();
        if(!$this->playerRepository->exists($id)){
            $errors[] = "Speler met gegeven id bestaat niet!";
        }
        $errors = $this->validatePlayer($firstName, $name, $gender, $isYouth, $isVeteran, $ranking, $errors);
        return $errors;
    }
    
    /**
     * Validatie speler
     * 
     * voornaam/naam/veteraan/geslacht/klassement correct ingevuld
     *
     * @param  string $firstName
     * @param  string $name
     * @param  string $gender
     * @param  bool $isYouth
     * @param  bool $isVeteran
     * @param  string $ranking
     * @param  array(string) errors
     * @return array(string) errors
     */
    private function validatePlayer($firstName, $name, $gender, $isYouth, $isVeteran, $ranking, $errors){
        if (!isset($firstName) || trim($firstName) === ''){
            $errors[] = "Voornaam moet ingevuld zijn."; 
        }
        if (!isset($name) || trim($name) === ''){
            $errors[] = "Naam moet ingevuld zijn."; 
        }
        if (!is_bool($isYouth)){
            $errors[] = "Jeugd is niet ingevuld."; 
        }
        if (!is_bool($isVeteran)){
            $errors[] = "Veteraan is niet ingevuld."; 
        }
        if(!in_array($gender, $this->playerRepository->getPossibleGenders())){
            $errors[] = "Onbekend geslacht"; 
        }
        if(!in_array($ranking, $this->playerRepository->getPossibleRankings())){
            $errors[] = "Onbekende ranking"; 
        }

        return $errors;
    }
}
