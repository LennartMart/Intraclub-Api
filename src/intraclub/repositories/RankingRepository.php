<?php
namespace intraclub\repositories;


class RankingRepository {
    /**
     * Database connection
     *
     * @var PDO
     */
    protected $db;

    public function __construct($db){
        $this->db = $db;
    }

    public function getRankingForNewSeason($seasonId){
        $query = "SELECT ROW_NUMBER() OVER (ORDER BY ISPS.basispunten DESC) AS [rank],
            ISP.id AS id, ISP.naam AS [name], ISP.voornaam as firstName,
            ISP.geslacht AS gender, ISP.is_veteraan as veteran, ISP.klassement AS ranking, ISP.jeugd as youth, ISPS.basispunten AS average
        FROM  intra_spelerperseizoen ISPS
        INNER JOIN intra_spelers ISP ON ISP.id = ISPS.speler_id
        WHERE ISPS.seizoen_id = ? AND ISP.is_lid = 1
        ORDER BY average DESC;";

        $stmt = $this->db->prepare($query);
        $stmt->execute([$seasonId]); 
        return $stmt->fetchAll();
    }

    public function getRankingAfterRound($roundId){
        $query ="SELECT ROW_NUMBER() OVER (ORDER BY ISPS.gemiddelde DESC) AS rank, ISP.id AS id, ISP.naam AS name, ISP.voornaam as firstName, 
            ISP.geslacht AS gender, ISP.is_veteraan as veteran, ISP.klassement AS ranking, ISP.jeugd as youth, ISPS.gemiddelde AS average
        FROM  intra_spelerperspeeldag ISPS
        INNER JOIN intra_spelers ISP ON ISP.id = ISPS.speler_id
        WHERE ISPS.speeldag_id = ? AND ISP.is_lid = 1
        ORDER BY rank;";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$roundId]);
        return $stmt->fetchAll();
    }
}