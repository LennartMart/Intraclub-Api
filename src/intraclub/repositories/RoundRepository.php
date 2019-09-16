<?php
namespace intraclub\repositories;

class RoundRepository {
    /**
     * Database connection
     *
     * @var PDO
     */
    protected $db;

    protected $roundQuery = "SELECT id, speeldagnummer AS 'number', gemiddeld_verliezend AS averageAbsent, 
        datum AS 'date', is_berekend AS calculated
        FROM intra_speeldagen";

    public function __construct($db){
        $this->db = $db;
    }

    public function getAll($seasonId = null){
        if(empty($seasonId)){
            return null;
        }
        $stmt = $this->db->prepare($this->roundQuery . " WHERE seizoen_id = ? ORDER BY id  ASC;");

        $stmt->execute([$seasonId]); 
        return $stmt->fetchAll();
    }

    public function getById($id){   
        $stmt = $this->db->prepare($this->roundQuery . " WHERE id=?");
        $stmt->execute([$id]); 
        return $stmt->fetch();
    }
    public function getBySeasonAndNumber($seasonId, $number){   
        $stmt = $this->db->prepare($this->roundQuery . " WHERE seizoen_id = :seasonId and speeldagnummer = :roundNumber;");
        $stmt->execute(array(':seasonId' => $seasonId, ':roundNumber' =>$number));
        return $stmt->fetch();
    }

    public function getLastCalculated($seasonId = null){
        if(empty($seasonId)){
            return null;
        }
        $stmt = $this->db->prepare($this->roundQuery . " WHERE seizoen_id=? AND is_berekend = 1 ORDER BY speeldagnummer DESC LIMIT 1;");
        $stmt->execute([$seasonId]); 
        return $stmt->fetch();
    }
}