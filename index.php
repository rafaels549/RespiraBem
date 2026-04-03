<?php

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Factory\AppFactory;
use Rafael\RespiraBem\services\ViewRender;
use Rafael\RespiraBem\services\Pollution;
use Dotenv\Dotenv;
use Rafael\RespiraBem\services\HttpClient;

require __DIR__ . '/vendor/autoload.php';

/*
|--------------------------------------------------------------------------
| Permitir arquivos estáticos (assets, favicon, etc)
|--------------------------------------------------------------------------
*/
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$file = __DIR__ . $uri;

if ($uri !== '/' && file_exists($file) && !is_dir($file)) {
    header("Content-Type: application/javascript");
    readfile($file);
    exit;
}

/*
|--------------------------------------------------------------------------
| Carregar .env (se existir)
|--------------------------------------------------------------------------
*/
if (file_exists(__DIR__ . '/.env')) {
    $dotenv = Dotenv::createImmutable(__DIR__);
    $dotenv->load();
}

/*
|--------------------------------------------------------------------------
| Criar aplicação Slim
|--------------------------------------------------------------------------
*/
$app = AppFactory::create();

/*
|--------------------------------------------------------------------------
| Rota da página principal
|--------------------------------------------------------------------------
*/
$app->get('/', function (ServerRequestInterface $request, ResponseInterface $response) {
    try {
        $html = ViewRender::render('home');
        $response->getBody()->write($html);
        return $response;
    } catch (\Exception $e) {
        $response->getBody()->write("Erro ao renderizar a view: " . $e->getMessage());
        return $response->withStatus(500);
    }
});

/*
|--------------------------------------------------------------------------
| API de poluição
|--------------------------------------------------------------------------
*/
$app->get('/get_pollutitions', function (ServerRequestInterface $request, ResponseInterface $response) {

    $params = $request->getQueryParams();
    $lat = $params['lat'] ?? null;
    $lon = $params['lon'] ?? null;

    if ($lat === null || $lon === null) {
        $response->getBody()->write(json_encode([
            'success' => false,
            'message' => 'Parâmetros lat e lon são obrigatórios'
        ]));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(400);
    }

    $pollution = new Pollution(new HttpClient(), $_ENV['API_KEY']);
    $result = $pollution->getPollutionData($lat, $lon);

    if (!$result['success']) {
        $pollution->getPollutionDataOpenMeteo($lat, $lon);
        if (!$result['success']) {  
        $response->getBody()->write(json_encode([
            'success' => false,
            'message' => 'Erro ao buscar dados de poluição'
        ]));

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(500);
        }
            $response->getBody()->write(json_encode([
                'success' => true,
                'data' => $result['data']
            ]));

            return $response->withHeader('Content-Type', 'application/json');
    }

    $response->getBody()->write(json_encode([
        'success' => true,
        'data' => $result['data']
    ]));

    return $response->withHeader('Content-Type', 'application/json');
});

/*
|--------------------------------------------------------------------------
| Executar aplicação
|--------------------------------------------------------------------------
*/
$app->run();