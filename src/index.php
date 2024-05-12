<?php

use Dotenv\Dotenv;
use Memo\Game\Game;
use Psr\Http\Message\ServerRequestInterface as Request;
use Selective\BasePath\BasePathMiddleware;
use Slim\Factory\AppFactory;
use Slim\Http\Response;
use Slim\Routing\RouteCollectorProxy;

require __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->safeLoad();

$app = AppFactory::create();
$app->add(new BasePathMiddleware($app));
$app->addBodyParsingMiddleware();

$app->group('/api', function (RouteCollectorProxy $group) {
    
    $group->post('/game/start', function (Request $request, Response $response) {
        return $response->withJson(new Game());
    });

    $group->post('/game/answer', function (Request $request, Response $response) {
        $token  = $request->getParsedBody()['token']  ?? null;
        $answer = $request->getParsedBody()['answer'] ?? null;

        if ($token === null || $answer === null) {
            return $response->withStatus(400);
        }

        $game = new Game($token);
        $game->submitAnswer($answer);
        return $response->withJson($game);
    });

    $group->post('/game/next', function (Request $request, Response $response) {
        $token  = $request->getParsedBody()['token'] ?? null;
        if ($token === null) {
            return $response->withStatus(400);
        }

        $game = new Game($token);
        $game->next();
        return $response->withJson($game);
    });
});

$app->get('/', function (Request $request, Response $response, $args) {
    $response->getBody()->write("Hello world!");
    return $response;
});

$app->run();
