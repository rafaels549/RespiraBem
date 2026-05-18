<?php

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Factory\AppFactory;
use Rafael\RespiraBem\services\ViewRender;
use Rafael\RespiraBem\services\Pollution;
use Rafael\RespiraBem\services\HttpClient;
use Dotenv\Dotenv;

// autoload SEMPRE volta uma pasta
require __DIR__ . '/../vendor/autoload.php';

// carrega .env apenas se existir (local)
if (file_exists(__DIR__ . '/../.env')) {
    $dotenv = Dotenv::createImmutable(__DIR__ . '/../');
    $dotenv->load();
}

$app = AppFactory::create();

/**
 * Rota principal
 */
$app->get('/', function (ServerRequestInterface $request, ResponseInterface $response) {
    try {
        $html = ViewRender::render('home');
        $response->getBody()->write($html);
        return $response;
    } catch (\Throwable $e) {
        $response->getBody()->write('Erro ao renderizar a view: ' . $e->getMessage());
        return $response->withStatus(500);
    }
});

/**
 * API de poluição
 */
$app->get('/get_pollutitions', function (ServerRequestInterface $request, ResponseInterface $response) {
    $params = $request->getQueryParams();

    if (!isset($params['lat'], $params['lon'])) {
        $response->getBody()->write(json_encode([
            'success' => false,
            'message' => 'lat e lon são obrigatórios'
        ]));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(400);
    }

    $pollution = new Pollution(
        new HttpClient(),
        $_ENV['API_KEY']
    );

    $result = $pollution->getPollutionData(
        $params['lat'],
        $params['lon']
    );

    $response->getBody()->write(json_encode($result));

    return $response->withHeader('Content-Type', 'application/json');
});

$app->run();
