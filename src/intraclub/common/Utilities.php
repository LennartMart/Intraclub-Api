<?php
namespace intraclub\common;

class Utilities
{
    public static function mapToMatchObject($match){
        return array(
            "home" => array (
                "firstPlayer" => array(
                    "id" => $match["home_firstPlayer_Id"],
                    "firstName" => $match["home_firstPlayer_firstName"],
                    "name" => $match["home_firstPlayer_name"]
                ),
                "secondPlayer" => array(
                    "id" => $match["home_secondPlayer_Id"],
                    "firstName" => $match["home_secondPlayer_firstName"],
                    "name" => $match["home_secondPlayer_name"]
                ),               
            ),
            "away" => array (
                "firstPlayer" => array(
                    "id" => $match["away_firstPlayer_Id"],
                    "firstName" => $match["away_firstPlayer_firstName"],
                    "name" => $match["away_firstPlayer_name"]
                ),
                "secondPlayer" => array(
                    "id" => $match["away_secondPlayer_Id"],
                    "firstName" => $match["away_secondPlayer_firstName"],
                    "name" => $match["away_secondPlayer_name"]
                ),               
            ),
            "firstSet" => array(
                "home" => intval($match["firstSet_home"]),
                "away" => intval($match["firstSet_away"])
            ),
            "secondSet" => array(
                "home" => intval($match["secondSet_home"]),
                "away" => intval($match["secondSet_away"])
            ),
            "thirdSet" => array(
                "home" => intval($match["thirdSet_home"]),
                "away" => intval($match["thirdSet_away"]),
                "played" => $match["thirdSet_home"] != "0" &&  $match["thirdSet_away"] != "0"
            )         
        );
    }
}



?>