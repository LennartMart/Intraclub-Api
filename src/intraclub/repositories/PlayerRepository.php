<?php
namespace intraclub\repositories;
use PDO;

class PlayerRepository {
   /**
     * Database connection
     *
     * @var PDO
     */
    protected $db;

    protected $playerQuery = "SELECT IPLAYER.id, IPLAYER.voornaam AS firstname, IPLAYER.naam AS name, IPLAYER.is_lid AS member,
    IPLAYER.geslacht AS gender, IPLAYER.jeugd AS youth, IPLAYER.is_veteraan AS veteran , IPLAYER.klassement AS ranking
    FROM intra_spelers IPLAYER";

    protected $playerWithSeasonInfoQuery = "
    SELECT IPLAYER.id, IPLAYER.voornaam AS firstname, IPLAYER.naam AS name, IPLAYER.is_lid AS member,
        IPLAYER.geslacht AS gender, IPLAYER.jeugd AS youth, IPLAYER.is_veteraan AS veteran , IPLAYER.klassement AS ranking,
        ISPS.basispunten AS basePoints, ISPS.gespeelde_sets AS setsPlayed, ISPS.gewonnen_sets AS setsWon, ISPS.gespeelde_punten AS pointsPlayed,
        ISPS.gewonnen_punten AS pointsWon, ISPS.gespeelde_matchen as matchesPlayed, ISPS.gewonnen_matchen AS matchesWon,
        ISPS.speeldagen_aanwezig AS roundsPresent
        FROM intra_spelers IPLAYER
        INNER JOIN intra_spelerperseizoen ISPS ON ISPS.speler_id = IPLAYER.Id
        WHERE ISPS.seizoen_id = ?";


    public function __construct($db){
        $this->db = $db;
    }
    public function getAll($onlyMembers = true){
        $query = $this->playerQuery;

        if($onlyMembers){
            $query = $query . " WHERE IPLAYER.is_lid = true";
        }
        $query = $query . " ORDER BY voornaam, naam";

        
        $data = $this->db->query($query)->fetchAll();
        return $data;
    }
    public function getAllWithSeasonInfo($seasonId, $onlyMembers = true){
        $query = $this->playerWithSeasonInfoQuery;

        if($onlyMembers){
            $query = $query . " AND IPLAYER.is_lid = true";
        }
        $query = $query . " ORDER BY voornaam, naam";

        $stmt = $this->db->prepare($query);
        $stmt->execute([$seasonId]); 
        return $stmt->fetchAll();
    }
    public function getByIdWithSeasonInfo($id, $seasonId){
        $query = $this->playerWithSeasonInfoQuery . " AND IPLAYER.id=?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$seasonId, $id]); 
        return $stmt->fetch();
    }
    public function getById($id){   
        $stmt = $this->db->prepare($this->playerQuery . " WHERE IPLAYER.id=?");
        $stmt->execute([$id]); 
        return $stmt->fetch();
    }
}