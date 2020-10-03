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
    
    /**
     * Basisinfo speler
     *
     * @var string
     */
    protected $playerQuery = "SELECT IPLAYER.id, IPLAYER.voornaam AS firstname, IPLAYER.naam AS name, IPLAYER.is_lid AS member,
    IPLAYER.geslacht AS gender, IPLAYER.jeugd AS youth, IPLAYER.is_veteraan AS veteran , IPLAYER.klassement AS ranking
    FROM intra_spelers IPLAYER";
    
    /**
     * Spelerinfo mét seizoensgegevens
     *
     * @var string
     */
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
    
    /**
     * Haal alle spelers op
     *
     * @param  bool $onlyMembers enkel leden of alle spelers
     * @return array met spelers- en seizoeninfo
     */
    public function getAll($onlyMembers = true){
        $query = $this->playerQuery;

        if($onlyMembers){
            $query = $query . " WHERE IPLAYER.is_lid = true";
        }
        $query = $query . " ORDER BY voornaam, naam";

        
        $data = $this->db->query($query)->fetchAll();
        return $data;
    }    
    /**
     * Controle of speler bestaat
     *
     * @param  int $id
     * @return bool true indien speler bestaat
     */
    public function exists($id){
        $stmt = $this->db->prepare("SELECT COUNT(*) as num FROM intra_spelers WHERE id = ? ");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row["num"] > 0;
    }    
    /**
     * Controle of speler bestaat én lid is
     *
     * @param  int $id
     * @return bool indien speler bestaat en lid is
     */
    public function existsAndIsMember($id){
        $stmt = $this->db->prepare("SELECT id, is_lid as member FROM intra_spelers WHERE id = ? ");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row && $row["member"] == 1;
    }
    
    /**
     * Haal alle spelers op, met seizoensinfo
     *
     * @param  int $seasonId
     * @param  bool $onlyMembers true om enkel leden op te halen
     * @return array met spelers- en seizoeninfo
     */
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
    
    /**
     * Haal speler op met seizoensinfo
     *
     * @param  int $id
     * @param  int $seasonId
     * @return array met speler + seizoeninfo
     */
    public function getByIdWithSeasonInfo($id, $seasonId){
        $query = $this->playerWithSeasonInfoQuery . " AND IPLAYER.id=?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$seasonId, $id]); 
        return $stmt->fetch();
    }
    
    /**
     * Haal basisinfo speler op
     *
     * @param  int $id
     * @return array met spelerinfo
     */
    public function getById($id){   
        $stmt = $this->db->prepare($this->playerQuery . " WHERE IPLAYER.id=?");
        $stmt->execute([$id]); 
        return $stmt->fetch();
    }
    
    /**
     * Alle mogelijke klassementen
     *
     * @return array(string) 
     */
    public function getPossibleRankings()
    {
        $enum = array();
        $stmt = $this->db->prepare('SHOW COLUMNS FROM intra_spelers WHERE field=\'klassement\'');
        $stmt->execute();
        $row = $stmt->fetch();
        foreach(explode("','",substr($row["Type"],6,-2)) as $v)
        {
            $enum[] = $v;
        }
        return $enum;
    }

        
    /**
     * Geslachten in database
     *
     * @return array<string>
     */
    public function getPossibleGenders(){
        $enum = array();
        $stmt = $this->db->prepare('SHOW COLUMNS FROM intra_spelers WHERE field=\'geslacht\'');
        $stmt->execute();
        $row = $stmt->fetch();
        foreach(explode("','",substr($row["Type"],6,-2)) as $v)
        {
            $enum[] = $v;
        }
        return $enum;
    }
    
    /**
     * Maak een nieuwe speler aan
     *
     * @param  string $firstName
     * @param  string $name
     * @param  string $gender
     * @param  bool $isYouth
     * @param  bool $isVeteran
     * @param  int $ranking
     * @return void
     */
    public function create($firstName, $name, $gender, $isYouth, $isVeteran, $ranking)
    {
        $isYouthInteger =  $isYouth? 1 : 0;
        $isVeteranInteger = $isVeteran? 1: 0;
        
        $stmt = $this->db->prepare("INSERT INTO intra_spelers
            SET 
            voornaam = :firstName,
            naam = :lastName,
            geslacht = :gender,
            jeugd = :isYouth,
            klassement = :ranking,
            is_veteraan = :isVeteran,
            is_lid = 1");

        $stmt->bindParam(':firstName', $firstName, PDO::PARAM_STR);
        $stmt->bindParam(':lastName', $name, PDO::PARAM_STR);
        $stmt->bindParam(':gender', $gender, PDO::PARAM_STR);
        $stmt->bindParam(':isYouth', $isYouthInteger, PDO::PARAM_INT);
        $stmt->bindParam(':ranking', $ranking, PDO::PARAM_STR);
        $stmt->bindParam(':isVeteran', $isVeteranInteger, PDO::PARAM_INT);     
       
        $stmt->execute();
        return $this->db->lastInsertId();
    }    
    /**
     * Update een bestaande speler
     *
     * @param  int $id
     * @param  string $firstName
     * @param  string $name
     * @param  string $gender
     * @param  bool $isYouth
     * @param  bool $isVeteran
     * @param  string $ranking
     * @return void
     */
    public function update($id, $firstName, $name, $gender, $isYouth, $isVeteran, $ranking)
    {
        $isYouthInteger =  $isYouth? 1 : 0;
        $isVeteranInteger = $isVeteran? 1: 0;
        
        $stmt = $this->db->prepare("UPDATE intra_spelers
            SET 
            voornaam = :firstName,
            naam = :lastName,
            geslacht = :gender,
            jeugd = :isYouth,
            klassement = :ranking,
            is_veteraan = :isVeteran,
            is_lid = 1
            WHERE id = :id");

        $stmt->bindParam(':firstName', $firstName, PDO::PARAM_STR);
        $stmt->bindParam(':lastName', $name, PDO::PARAM_STR);
        $stmt->bindParam(':gender', $gender, PDO::PARAM_STR);
        $stmt->bindParam(':isYouth', $isYouthInteger, PDO::PARAM_INT);
        $stmt->bindParam(':ranking', $ranking, PDO::PARAM_STR);
        $stmt->bindParam(':isVeteran', $isVeteranInteger, PDO::PARAM_INT);     
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);  

        return $stmt->execute();
    }
}