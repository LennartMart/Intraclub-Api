<?php
namespace intraclub\repositories;


class MatchRepository {
   /**
     * Database connection
     *
     * @var PDO
     */
    protected $db;

    public function __construct($db){
        $this->db = $db;
    }
    
    public function getAllBySeasonAndPlayerId($seasonId, $playerId){
        $query = "SELECT IW.Id, set1_1 AS firstSet_home, set1_2 AS firstSet_away, set2_1 AS secondSet_home, set2_2 AS secondSet_away, 
                    set3_1 AS thirdSet_home, set3_2 AS thirdSet_away,
                    PL1H.Id as home_firstPlayer_Id, PL1H.voornaam AS home_firstPlayer_firstName, PL1H.naam AS home_firstPlayer_name,
                    PL2H.Id as home_secondPlayer_Id, PL2H.voornaam AS home_secondPlayer_firstName, PL2H.naam AS home_secondPlayer_name,
                    PL1A.Id as away_firstPlayer_Id, PL1A.voornaam AS away_firstPlayer_firstName, PL1A.naam AS away_firstPlayer_name,
                    PL2A.Id as away_secondPlayer_Id, PL2A.voornaam AS away_secondPlayer_firstName, PL2A.naam AS away_secondPlayer_name,
                    ISP.Id as roundId, ISP.speeldagnummer AS roundNumber
                    FROM intra_wedstrijden IW 
                    INNER JOIN intra_speeldagen ISP ON ISP.id = IW.speeldag_id
                    INNER JOIN intra_spelers PL1H ON PL1H.id =  IW.team1_speler1
                    INNER JOIN intra_spelers PL2H ON PL2H.id =  IW.team1_speler2
                    INNER JOIN intra_spelers PL1A ON PL1A.id =  IW.team2_speler1
                    INNER JOIN intra_spelers PL2A ON PL2A.id =  IW.team2_speler2
                    WHERE (
                            (
                                PL1H.Id  = ? OR
                                PL2H.Id  = ? OR
                                PL1A.Id = ? OR
                                PL2A.Id = ?
                            ) AND ISP.seizoen_id = ?
                        )
                    ORDER BY IW.Id ASC;";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$playerId, $playerId, $playerId, $playerId, $seasonId]); 
        return $stmt->fetchAll();
    }
    
}