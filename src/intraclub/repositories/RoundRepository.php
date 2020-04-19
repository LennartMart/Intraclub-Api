<?php
namespace intraclub\repositories;

class RoundRepository {
    /**
     * Database connection
     *
     * @var PDO
     */
    protected $db;

    protected $roundQuery = "SELECT id, speeldagnummer AS number, ROUND(gemiddeld_verliezend,2) AS averageAbsent, 
        datum AS date, is_berekend AS calculated
        FROM intra_speeldagen";

    public function __construct($db){
        $this->db = $db;
    }

    public function getAll($seasonId = null){
        if(empty($seasonId)){
            return null;
        }
        $stmt = $this->db->prepare($this->roundQuery . " WHERE seizoen_id = ? ORDER BY id ASC;");

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

    public function getWithMatches($id){
        if(empty($id)){
            return null;
        }
        $stmt = $this->db->prepare("SELECT ISP.id AS roundId, ISP.speeldagnummer AS roundNumber, ROUND(ISP.gemiddeld_verliezend,2) AS averageAbsent, 
            ISP.datum AS date, ISP.is_berekend AS calculated, set1_1 AS firstSet_home, set1_2 AS firstSet_away, set2_1 AS secondSet_home, set2_2 AS secondSet_away, 
            set3_1 AS thirdSet_home, set3_2 AS thirdSet_away,
            PL1H.Id as home_firstPlayer_Id, PL1H.voornaam AS home_firstPlayer_firstName, PL1H.naam AS home_firstPlayer_name,
            PL2H.Id as home_secondPlayer_Id, PL2H.voornaam AS home_secondPlayer_firstName, PL2H.naam AS home_secondPlayer_name,
            PL1A.Id as away_firstPlayer_Id, PL1A.voornaam AS away_firstPlayer_firstName, PL1A.naam AS away_firstPlayer_name,
            PL2A.Id as away_secondPlayer_Id, PL2A.voornaam AS away_secondPlayer_firstName, PL2A.naam AS away_secondPlayer_name
            FROM intra_speeldagen ISP
            INNER JOIN intra_wedstrijden IW ON ISP.id = IW.speeldag_id
            INNER JOIN intra_spelers PL1H ON PL1H.id =  IW.team1_speler1
            INNER JOIN intra_spelers PL2H ON PL2H.id =  IW.team1_speler2
            INNER JOIN intra_spelers PL1A ON PL1A.id =  IW.team2_speler1
            INNER JOIN intra_spelers PL2A ON PL2A.id =  IW.team2_speler2 WHERE ISP.id=?
            ORDER BY IW.Id ASC;
        ");
        $stmt->execute([$id]); 
        return $stmt->fetchAll();
    }
}