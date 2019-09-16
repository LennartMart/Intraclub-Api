<?php
namespace intraclub\managers;

use intraclub\common\Utilities;

class MatchManager {
    /**
     * Database connection
     *
     * @var PDO
     */
    protected $db;
    protected $matchQuery = "
    SELECT IW.id, speeldag_id AS roundId,
        set1_1 AS firstSet_home, set1_2 AS firstSet_away, set2_1 AS secondSet_home, set2_2 AS secondSet_away, 
        set3_1 AS thirdSet_home, set3_2 AS thirdSet_away,
        PL1H.Id as home_firstPlayer_Id, PL1H.voornaam AS home_firstPlayer_firstName, PL1H.naam AS home_firstPlayer_name,
        PL2H.Id as home_secondPlayer_Id, PL2H.voornaam AS home_secondPlayer_firstName, PL2H.naam AS home_secondPlayer_name,
        PL1A.Id as away_firstPlayer_Id, PL1A.voornaam AS away_firstPlayer_firstName, PL1A.naam AS away_firstPlayer_name,
        PL2A.Id as away_secondPlayer_Id_firstPlayer_Id, PL2A.voornaam AS away_secondPlayer_firstName, PL2A.naam AS away_secondPlayer_name
    FROM intra_wedstrijden IW
    INNER JOIN intra_speeldagen ISP ON ISP.id = IW.speeldag_id
    INNER JOIN intra_spelers PL1H ON PL1H.id =  IW.team1_speler1
    INNER JOIN intra_spelers PL2H ON PL2H.id =  IW.team1_speler2
    INNER JOIN intra_spelers PL1A ON PL1A.id =  IW.team2_speler1
    INNER JOIN intra_spelers PL2A ON PL2A.id =  IW.team2_speler2
    INNER JOIN intra_seizoen ISEASON ON ISEASON.Id = ISP.seizoen_id
    ";

    public function __construct($db){
        $this->db = $db;
    }

    public function getAllBySeasonId($seasonId = null){        
        $currentSeasonId = Utilities::getCurrentSeasonId($this->db);
        $stmt = $this->db->prepare($this->matchQuery . " WHERE ISEASON.Id=?");
        $stmt->execute([$currentSeasonId]); 
        return $stmt->fetchAll();
    }

    public function getAllByRoundId($roundId){
        $stmt = $this->db->prepare($this->matchQuery . " WHERE ISP.Id=?");
        $stmt->execute([$roundId]); 
        return $stmt->fetchAll();
    }
}