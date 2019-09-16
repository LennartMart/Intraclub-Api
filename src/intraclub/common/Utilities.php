<?php
namespace intraclub\common;

class Utilities
{
    private static $db;
    private static $initialized = false;

    private static function initialize($db)
    {
        if (self::$initialized)
            return;

        self::$db = $db;
        self::$initialized = true;
    }

    public static function getCurrentSeasonId($db)
    {
        self::initialize($db);
        $currentSeason = self::$db->query("SELECT id, seizoen as season FROM intra_seizoen ORDER BY id DESC LIMIT 1;")->fetch();
        return $currentSeason["id"];
    }
}



?>