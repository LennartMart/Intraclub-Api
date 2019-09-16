<?php
namespace intraclub\managers;

class PlayerManager {
    /**
     * Database connection
     *
     * @var PDO
     */
    protected $db;

    protected $playerQuery = "SELECT id, voornaam AS firstname, naam AS name, is_lid AS member,
    geslacht AS gender, jeugd AS youth, is_veteraan AS veteran , klassement AS ranking
    FROM intra_spelers";

    public function __construct($db){
        $this->db = $db;
    }

    public function getAll($onlyMembers = true){
        $query = $this->playerQuery;

        if($onlyMembers){
            $query = $query . " WHERE is_lid = true";
        }
        $query = $query . " ORDER BY voornaam, naam";

        
        $data = $this->db->query($query)->fetchAll();
        return $data;
    }

    public function getById($id){   
        $stmt = $this->db->prepare($this->playerQuery . " WHERE id=?");
        $stmt->execute([$id]); 
        return $stmt->fetch();
    }

}