<?php
namespace intraclub\repositories;


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
    public function getByIdWithSeasonInfo($id, $seasonId){
        $query = "
        SELECT IPLAYER.id, IPLAYER.voornaam AS firstname, IPLAYER.naam AS name, IPLAYER.is_lid AS member,
            IPLAYER.geslacht AS gender, IPLAYER.jeugd AS youth, IPLAYER.is_veteraan AS veteran , IPLAYER.klassement AS ranking,
            ISPS.gespeelde_sets AS playedSets, ISPS.gewonnen_sets AS wonSets, ISPS.gespeelde_punten AS playedPoints,
            ISPS.gewonnen_punten AS wonPoints, ISPS.gespeelde_matchen as playedMatches, ISPS.gewonnen_matchen AS wonMatches,
            ISPS.speeldagen_aanwezig AS roundsPresent
            FROM intra_spelers IPLAYER
            INNER JOIN intra_spelerperseizoen ISPS ON ISPS.speler_id = IPLAYER.Id
            WHERE IPLAYER.id=? AND ISPS.seizoen_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$id, $seasonId]); 
        return $stmt->fetch();
    }
    public function getById($id){   
        $stmt = $this->db->prepare($this->playerQuery . " WHERE IPLAYER.id=?");
        $stmt->execute([$id]); 
        return $stmt->fetch();
    }
}