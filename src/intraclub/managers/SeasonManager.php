<?php

namespace intraclub\managers;

use intraclub\common\Utilities;
use intraclub\repositories\SeasonRepository;
use intraclub\repositories\StatisticsRepository;
use intraclub\repositories\RoundRepository;
use intraclub\repositories\MatchRepository;
use intraclub\repositories\PlayerRepository;


class SeasonManager
{
    /**
     * Database connection
     *
     * @var PDO
     */
    protected $db;
    protected $rankingManager;
    protected $seasonRepository;
    protected $roundRepository;
    protected $matchRepository;
    protected $playerRepository;


    /**
     *Statistics Repository
     *
     * @var StatisticsRepository
     */
    protected $statisticsRepository;

    public function __construct($db)
    {
        $this->db = $db;
        $this->rankingManager = new RankingManager($this->db);
        $this->seasonRepository = new SeasonRepository($this->db);
        $this->statisticsRepository = new StatisticsRepository($this->db);
        $this->roundRepository = new RoundRepository($this->db);
        $this->matchRepository = new MatchRepository($this->db);
        $this->playerRepository = new PlayerRepository($this->db);

    }

    public function getStatistics($seasonId = null)
    {
        if (empty($seasonId)) {
            $seasonId = $this->seasonRepository->getCurrentSeasonId();
        }
        $statisticsInfo = $this->seasonRepository->getStatistics($seasonId);
        $response = array();
        if (!empty($statisticsInfo)) {
            for ($index = 0; $index < count($statisticsInfo); $index++) {
                $playerStats = $statisticsInfo[$index];
                $playerStatistics = Utilities::mapToPlayerStatisticsObject($playerStats);
                $response[] = $playerStatistics;
            }
        }
        return $response;
    }
    public function create($period)
    {
        //1. Get Current Season
        $previousSeasonId = $this->seasonRepository->getCurrentSeasonId();

        //2. Insert new season
        $newSeasonId = $this->seasonRepository->create($period);    

        //3. Insert playerPerSeason Record for every player & Based on ranking -> Add some points
        $ranking = $this->rankingManager->get($previousSeasonId);

        $reversedRanking = array_reverse($ranking);
        $addedBasePoints = 19.000;
        foreach ($reversedRanking as $rankedPlayer) {
            $this->statisticsRepository->createSeasonStatistics($newSeasonId, $rankedPlayer["id"], $addedBasePoints);
            $addedBasePoints += 0.0001;
        }
    }



    public function calculateCurrentSeason()
    {
        $currentSeasonId = $this->seasonRepository->getCurrentSeasonId();
        $roundsOfCurrentSeason = $this->roundRepository->getAll($currentSeasonId);

        $averageLosersArray = array();
        $roundNumber = 1;

        /*
         * BEGIN BEPALEN GEMIDDELDE VERLIEZERS / SPEELDAG
         */
        foreach ($roundsOfCurrentSeason as $round) {
            $averageLosers = 0;
            $totalMatches = 0;

            $matches = $this->matchRepository->getAllByRoundId($round["id"]);
            foreach ($matches as $match) {
                //TODO
                $score_array = Utilities::calculateMatchStatistics();
                $averageLosers += $score_array['averagePointsLosingTeam'];
                $totalMatches++;
            }

            $averageLosingCurrentRound = $averageLosers / $totalMatches;
            //TODO: Update Average Losing for current round

            $averageLosersArray[$roundNumber] = $averageLosingCurrentRound;
            $roundNumber++;
        }
        /*
         * EINDE BEPALEN VERLIEZERS
         */

        $lastRoundNumber = $roundNumber - 1;

        /*
         * Resultaat per speler bepalen
         */
        $allPlayers = $this->playerRepository->getAllWithSeasonInfo($currentSeasonId, true);

        foreach ($allPlayers as $player) {

            $resultArray = array();
            // basispunt als beginwaarde zetten
            $resultArray[0] = $player['basePoints'];
            $roundNumber = 1;

            $seasonStats = array(
                "setsPlayed" => 0,
                "setsWon" => 0,
                "pointsPlayed" => 0,
                "pointsWon" => 0,
                "matchesPlayed" => 0,
                "matchesWon" => 0,
                "roundsPresent" => 0
            );

            /*
             * Overloop de wedstrijden van de speler
             */
            $matchesCurrentPlayer = $this->matchRepository->getAllBySeasonAndPlayerId($currentSeasonId, $player["id"]);
            foreach ($matchesCurrentPlayer as $matchCurrentPlayer) {
                while ($matchCurrentPlayer["roundNumber"]> $roundNumber) {
                    //Speler niet aanwezig op $roundNumber
                    //Geef hem gemiddelde verliezers van die speeldag!
                    $resultArray[$roundNumber] = $averageLosersArray[$roundNumber];
                    $roundNumber++;
                }
                // meerdere spelletjes gespeeld, OVERSLAAN
                if ($roundNumber > $matchCurrentPlayer["roundNumber"]) {
                    //Meermaals aanwezig op huidige speeldag
                } //We zitten goed!
                else if ($roundNumber == $matchCurrentPlayer["roundNumber"]) {
                    //TODO
                    $matchStatistics = Utilities::calculateMatchStatistics();

                    $seasonStats["pointsPlayed"] += $matchStatistics["totalPoints"];
                    $seasonStats["setsPlayed"] += $matchStatistics["amountOfSets"];
                    $seasonStats["roundsPresent"]++;
                    $seasonStats["matchesPlayed"]++;

                    if (in_array($speler->id, $matchStatistics["winningTeamIds"])) {
                        // speler heeft gewonnen!
                        $resultArray[$roundNumber] = $matchStatistics["averagePointsWinningTeam"];
                        $seasonStats["pointsWon"] += $matchStatistics["totalPointsWinningTeam"];
                        $seasonStats["setsWon"] += 2;
                        $seasonStats["matchesWon"]++;
                    } else {
                        // speler heeft verloren, jammer
                        $resultArray[$roundNumber] = $matchStatistics["averagePointsLosingTeam"];
                        $seasonStats["pointsWon"] += $matchStatistics["totalPointsLosingTeam"];
                        $seasonStats["setsWon"] += $matchStatistics["amountOfSets"] - 2;
                    }

                    //Volgende speeldag...
                    $roundNumber++;
                }
            }
            // laatste speeldagen niet aanwezig
            while ($roundNumber <= $lastRoundNumber) {
                $resultArray[$roundNumber] = $averageLosersArray[$roundNumber];
                $roundNumber++;
            }

            //We hebben nu $resultArray[speeldag] met gemiddelde voor elke speeldag van de speler
            //Geef speeldag  mee, samen met uitslag speeldag.
            //Ranking = 0, want we weten dit niet!
            //Hebben gemiddelde speeldag, MAAR MOETEN GEMIDDELDE TOT DIE SPEELDAG BEREKENEN! => done

            //  spelers rankschikken per speeldag en dan pas update_speeldagstats => enkel nog update_speeldagstats

            foreach ($roundsOfCurrentSeason as $round) {
                $sumOfAveragePerRound = 0;
                $totalRounds = 0;
                for ($j = 0; $j <= $round["roundNumber"]; $j++) {
                    $sumOfAveragePerRound += $resultArray[$j];
                    $totalRounds++;
                }
                //Tussenstand speeldag delen door aantal speeldagen +1
                //+1 = basispunten
                // +1 valt weg : laatste for-lus hierboven
                $averageRound = $sumOfAveragePerRound / ($totalRounds);
                $this->statisticsRepository->insertOrUpdateRoundStatistics($round["id"], $player["id"], $averageRound);

            }
            $this->statisticsRepository->updateSeasonStatistics($currentSeasonId, $player["id"], $seasonStats["setsPlayed"], $seasonStats["setsWon"],
                $seasonStats["pointsPlayed"], $seasonStats["pointsWon"], $seasonStats["matchesPlayed"], $seasonStats["matchesWon"]);
        }


    }

}
