<?php
use intraclub\managers\PlayerManager;
use intraclub\managers\RankingManager;
use intraclub\managers\RoundManager;
use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;

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
        $items = $request->getQueryParam('$top');
        $rankingManager = new RankingManager($this->db);
        $data = $rankingManager->get($items, true, true, true, true);
        return $response->withJson($data);
    });
    $app->get('/rankings/general', function (Request $request, Response $response, array $args) {
        $items = $request->getQueryParam('$top');
        $rankingManager = new RankingManager($this->db);
        $data = $rankingManager->get($items, true);
        return $response->withJson($data);
    });
    $app->get('/rankings/women', function (Request $request, Response $response, array $args) {
        $items = $request->getQueryParam('$top');
        $rankingManager = new RankingManager($this->db);
        $data = $rankingManager->get($items, false, true);
        return $response->withJson($data);
    });
    $app->get('/rankings/veterans', function (Request $request, Response $response, array $args) {
        $items = $request->getQueryParam('$top');
        $rankingManager = new RankingManager($this->db);
        $data = $rankingManager->get($items, false, false, true);
        return $response->withJson($data);
    });
    $app->get('/rankings/recreants', function (Request $request, Response $response, array $args) {
        $items = $request->getQueryParam('$top');
        $rankingManager = new RankingManager($this->db);
        $data = $rankingManager->get($items, false, false, false, true);
        return $response->withJson($data);
    });
    $app->get('/rounds/{id}', function (Request $request, Response $response, array $args) {
        $roundManager = new RoundManager($this->db);
        $data = $roundManager->getByIdWithMatches($args['id']);
        return $response->withJson($data);
    });
};
