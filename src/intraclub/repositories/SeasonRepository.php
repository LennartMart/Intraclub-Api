<?php
namespace intraclub\repositories;


class SeasonRepository {
    /**
     * Database connection
     *
     * @var PDO
     */
    protected $db;

    public function __construct($db){
        $this->db = $db;
    }

    public function getCurrentSeasonId()
    {
        $currentSeason = $this->db->query("SELECT id, seizoen as season FROM intra_seizoen ORDER BY id DESC LIMIT 1;")->fetch();
        return $currentSeason["id"];
    }
}