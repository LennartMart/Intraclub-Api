<?php
namespace intraclub\common;

class Utilities
{
    public static function mapToPlayerStatisticsObject($playerStats){
        return array(
            "id" => $playerStats["id"],
            "firstName" => $playerStats["firstname"],
            "name" => $playerStats["name"],
            "statistics" => array(
                "points" => array(
                    "won" => intval($playerStats["pointsWon"]),
                    "lost" => $playerStats["pointsPlayed"] - $playerStats["pointsWon"],
                    "total" => intval($playerStats["pointsPlayed"])
                ),
                "sets" => array(
                    "won" => intval($playerStats["setsWon"]),
                    "lost" => $playerStats["setsPlayed"] - $playerStats["setsWon"],
                    "total" => intval($playerStats["setsPlayed"]) 
                ),
                "matches" => array(
                    "won" => intval($playerStats["matchesWon"]),
                    "lost" => $playerStats["matchesPlayed"] - $playerStats["matchesWon"],
                    "total" => intval($playerStats["matchesPlayed"]) 
                ),
                "rounds" => array(
                    "present" => intval($playerStats["roundsPresent"])
                )
            )
        );
    }
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
            ),
            "round" => array(
                "id" => intval($match["roundId"]),
                "number" => intval($match["roundNumber"])
            ),                     
        );
    }
    private static function trimSets($firstScore, $secondScore)
    {
        return ($firstScore > 21 || $secondScore > 21) ? 21 / max($firstScore, $secondScore) * $firstScore : $firstScore;
    }
    public static function calculateMatchStatistics($home_firstPlayer_Id, $home_secondPlayer_Id, 
        $away_firstPlayer_Id, $away_secondPlayer_Id, 
        $firstSet_home, $firstSet_away, $secondSet_home, $secondSet_away, $thirdSet_home, $thirdSet_away)
    {
        $setsWonHometeam = 0;
        $setsWonAwayteam = 0;
        $totalPointsWinningTeam = 0;
        $totalPointsLosingTeam = 0;
        $amountOfSetsPlayed = 0;

        if ($firstSet_home > $firstSet_away) {
            $setsWonHometeam++;
        } else {
            $setsWonAwayteam++;
        }
        if ($secondSet_home > $secondSet_away) {
            $setsWonHometeam++;
        } else {
            $setsWonAwayteam++;
        }
        if (($thirdSet_home != '' && $thirdSet_away != '') && ($thirdSet_home != 0 && $thirdSet_away != 0)) {
            $amountOfSetsPlayed = 3;
            if ($thirdSet_home > $thirdSet_away) {
                $setsWonHometeam++;
            } else {
                $setsWonAwayteam++;
            }
        } else {
            $amountOfSetsPlayed = 2;
        }

        $winner = ($setsWonHometeam > $setsWonAwayteam) ? 1 : 2;

        $totalHometeam = Utilities::trimSets($firstSet_home, $firstSet_away) + Utilities::trimSets($secondSet_home, $secondSet_away) + Utilities::trimSets($thirdSet_home, $thirdSet_away);
        $totalAwayteam = Utilities::trimSets($firstSet_away, $firstSet_home) + Utilities::trimSets($secondSet_away, $secondSet_home) + Utilities::trimSets($thirdSet_away, $thirdSet_home);

        if ($winner == 1) {
            $trimmedPointsWinningTeam = $totalHometeam;
            $trimmedPointsLosingTeam = $totalAwayteam;
            $totalPointsWinningTeam =$firstSet_home + $secondSet_home + $thirdSet_home;
            $totalPointsLosingTeam = $firstSet_away + $secondSet_away + $thirdSet_away;
            $winningTeamIds = array($home_firstPlayer_Id, $home_secondPlayer_Id);
            $losingTeamIds = array($away_firstPlayer_Id, $away_secondPlayer_Id);
        } else {
            $trimmedPointsWinningTeam = $totalAwayteam;
            $trimmedPointsLosingTeam = $totalHometeam;
            $totalPointsWinningTeam = $firstSet_away + $secondSet_away + $thirdSet_away;
            $totalPointsLosingTeam = $firstSet_home + $secondSet_home + $thirdSet_home;
            $winningTeamIds = array($away_firstPlayer_Id, $away_secondPlayer_Id);
            $losingTeamIds = array($home_firstPlayer_Id, $home_secondPlayer_Id);
        }

        $return = array(
            "winner" => $winner,
            "amountOfSets" => $amountOfSetsPlayed,
            "totalPointsWinningTeam" => $totalPointsWinningTeam,
            "totalPointsLosingTeam" => $totalPointsLosingTeam,
            "averagePointsWinningTeam" => $trimmedPointsWinningTeam / $amountOfSetsPlayed,
            "averagePointsLosingTeam" => $trimmedPointsLosingTeam / $amountOfSetsPlayed,
            "winningTeamIds" => $winningTeamIds,
            "losingTeamIds" => $losingTeamIds,
            "totalPoints" => $totalPointsLosingTeam + $totalPointsWinningTeam
        );
        return $return;
    }
}
