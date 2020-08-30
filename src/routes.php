<?php

use intraclub\managers\PlayerManager;
use intraclub\managers\RankingManager;
use intraclub\managers\RoundManager;
use intraclub\managers\SeasonManager;
use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;

define('_JEXEC', 1);
if (file_exists('/home/bclandegem/domains/bclandegem.be/public_html'. '/defines.php'))
{
    include_once '/home/bclandegem/domains/bclandegem.be/public_html' . '/defines.php';
}
if (!defined('_JDEFINES'))
{
    define('JPATH_BASE', '/home/bclandegem/domains/bclandegem.be/public_html');
    require_once JPATH_BASE . '/includes/defines.php';
}
require_once JPATH_BASE . '/includes/framework.php';

function checkAccessRights(){
    $joomla_app = JFactory::getApplication('site');
    $joomla_app->initialise();
    $user = JFactory::getUser();
    $authorisedViewLevels = $user->getAuthorisedViewLevels();
    //5 = id intrclub access level
    if( !in_array(5,$authorisedViewLevels)){
        die("Onvoldoende rechten !");
    }

}

return function (App $app) {

    $app->post('/seasons', function (Request $request, Response $response, array $args) {
        checkAccessRights();
        return "hello";
    });
    $app->post('/rounds', function (Request $request, Response $response, array $args) {
        checkAccessRights();
        return "hello";
    });
    $app->post('/matches', function (Request $request, Response $response, array $args) {
        checkAccessRights();
        return "hello";
    });
    $app->post('/players', function (Request $request, Response $response, array $args) {
        checkAccessRights();
        return "hello";
    });
    $app->post('/rounds/calculate', function (Request $request, Response $response, array $args) {
        checkAccessRights();
        return "hello";
    });

    $app->put('/players/{id}', function (Request $request, Response $response, array $args) {
        checkAccessRights();
        return "hello";
    });

    $app->put('/matches/{id}', function (Request $request, Response $response, array $args) {
        checkAccessRights();
        return "hello";
    });

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
    $app->get('/seasons/latest/statistics', function (Request $request, Response $response, array $args) {
        $seasonManager = new SeasonManager($this->db);
        $data = $seasonManager->getStatistics();
        return $response->withJson($data);
    });
};
