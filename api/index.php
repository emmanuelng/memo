<?php

use Dotenv\Dotenv;
use Memo\Game\Game;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpNotFoundException;
use Slim\Factory\AppFactory;
use Slim\Http\Response;

require __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->safeLoad();

$app = AppFactory::create();
$app->setBasePath("/memo/api");
$app->addBodyParsingMiddleware();

$app->options('/{routes:.+}', function (Request $request, Response $response) {
    return $response;
});

$app->add(function ($request, $handler) {
    $response = $handler->handle($request);
    return $response
        ->withHeader('Access-Control-Allow-Origin', '*')
        ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
        ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS');
});

$app->post('/game/start', function (Request $request, Response $response) {
    return $response->withJson(new Game());
});

$app->post('/game/answer', function (Request $request, Response $response) {
    $token  = $request->getParsedBody()['token']  ?? null;
    $answer = $request->getParsedBody()['answer'] ?? null;

    if ($token === null || $answer === null) {
        return $response->withStatus(400);
    }

    $game = new Game($token);
    $game->submitAnswer($answer);
    return $response->withJson($game);
});

$app->post('/game/next', function (Request $request, Response $response) {
    $token  = $request->getParsedBody()['token'] ?? null;
    if ($token === null) {
        return $response->withStatus(400);
    }

    $game = new Game($token);
    $game->next();
    return $response->withJson($game);
});

$app->get('/', function (Request $request, Response $response) {
    $response->write('Hello world!');
    return $response;
});

/**
 * Catch-all route to serve a 404 Not Found page if none of the routes match
 * NOTE: make sure this route is defined last
 */
$app->map(['GET', 'POST', 'PUT', 'DELETE', 'PATCH'], '/{routes:.+}', function (Request $request) {
    throw new HttpNotFoundException($request);
});

$app->run();
