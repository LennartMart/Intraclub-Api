<?php
use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;
use intraclub\managers\PlayerManager;
use intraclub\managers\MatchManager;
use intraclub\managers\RoundManager;
use intraclub\managers\RankingManager;

return function (App $app) {
    $app->get('/players', function (Request $request, Response $response, array $args) {
        $playerManager = new PlayerManager($this->db);
        $data = $playerManager->getAll();
        return $response->withJson($data);
    });
    $app->get('/players/{id}', function (Request $request, Response $response, array $args) {
        $id = $args['id'];
        $playerManager = new PlayerManager($this->db);
        $queryParams = $request->getQueryParams();
        $seasonId = $queryParams["seasonId"];
        $data = $playerManager->getByIdWithSeasonInfo($id, $seasonId);
        return $response->withJson($data);
    });
    $app->get('/rounds', function (Request $request, Response $response) {
        $roundManager = new RoundManager($this->db);
        $queryParams = $request->getQueryParams();
        $seasonId = $queryParams["seasonId"];
        
        $data = $roundManager->getAll($seasonId);
        return $response->withJson($data);
    });
    $app->get('/rankings', function (Request $request, Response $response, array $args) {
        $rankingManager = new RankingManager($this->db);
        $data = $rankingManager->get();
        return $response->withJson($data);
    });
    $app->get('/rounds/{id}', function (Request $request, Response $response, array $args) {
        $roundManager = new RoundManager($this->db);
        $data = $roundManager->getByIdWithMatches($args['id']);
        return $response->withJson($data);
    });
};


