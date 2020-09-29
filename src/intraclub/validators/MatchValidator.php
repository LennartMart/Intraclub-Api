<?php
namespace intraclub\validators;

use intraclub\repositories\SeasonRepository;
use intraclub\repositories\MatchRepository;
use intraclub\repositories\RoundRepository;

class MatchValidator {

    /**
     * Database connection
     *
     * @var PDO
     */
    protected $db;
    protected $matchRepository;
    protected $roundRepository;
    protected $playerRepository;

    public function __construct($db){
        $this->db = $db;
        $this->matchRepository = new MatchRepository($db);
        $this->roundRepository = new RoundRepository($db);
    }

    public function validateCreateMatch($roundId, $playerId1, $playerId2, $playerId3, $playerId4, 
        $set1Home, $set1Away, $set2Home, $set2Away, $set3Home, $set3Awa){

        $errors = array();

        //Controleer of ronde bestaat

        //Controleer of spelers bestaan én moeten lid zijn

        //Controleer set 1
        if (filter_var($set1Home, FILTER_VALIDATE_INT) === false ) {
            $errors["E0001"] = "Thuisscore eerste set is ongeldig";
          }

        //Controleer set 2

        //Controleer set 3

        return $errors;

    }

}