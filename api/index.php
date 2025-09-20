<?php
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;
use Rafael\RespiraBem\classes\Curl;
use Dotenv\Dotenv;

require __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$app = AppFactory::create();

// -----------------
// Middleware de CORS
// -----------------
$app->add(function (ServerRequestInterface $request, RequestHandlerInterface $handler) use ($app): ResponseInterface {
    if ($request->getMethod() === 'OPTIONS') {
        $response = $app->getResponseFactory()->createResponse(200);
    } else {
        $response = $handler->handle($request);
    }

    return $response
        ->withHeader('Access-Control-Allow-Origin', '*')
        ->withHeader('Access-Control-Allow-Headers', 'Origin, X-Requested-With, Content-Type, Accept, Authorization')
        ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS')
        ->withHeader('Access-Control-Allow-Credentials', 'true');
});

$app->addBodyParsingMiddleware();
$app->addRoutingMiddleware();
$app->addErrorMiddleware(true, true, true);

// -----------------
// Rota coringa para OPTIONS
// -----------------
$app->options('/{routes:.+}', function ($request, $response, $args) {
    return $response;
});

// Rota para a view index.php
$app->get('/', function (ServerRequestInterface $request, ResponseInterface $response) {
    // Caminho da view
    $viewPath = __DIR__ . '/../views/index.php';
    
    // Verifica se existe
    if (file_exists($viewPath)) {
        // Captura o conteúdo da view
        ob_start();
        include $viewPath;
        $output = ob_get_clean();

        $response->getBody()->write($output);
        return $response;
    } else {
        $response->getBody()->write("View não encontrada!");
        return $response->withStatus(404);
    }
});

// -----------------
// Sua rota principal
// -----------------
$app->get('/get_pollutitions', function (ServerRequestInterface $request, ResponseInterface $response) {
    $params = $request->getQueryParams();
    $lat = $params['lat'] ?? null;
    $lon = $params['lon'] ?? null;

    if ($lat === null || $lon === null) {
        $response->getBody()->write(json_encode([
            'success' => false,
            'message' => 'Parâmetros lat e lon são obrigatórios'
        ]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
    }

    $curl = new Curl();
    $result = $curl->makeCurl("http://api.openweathermap.org/data/2.5/air_pollution?lat=$lat&lon=$lon&appid=" . $_ENV['API_KEY']);

    if (!$result['success']) {
        $response->getBody()->write(json_encode([
            'success' => false,
            'message' => 'Erro ao buscar dados de poluição'
        ]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
    }

    $response->getBody()->write(json_encode([
        'success' => true,
        'data' => $result['data']
    ]));

    return $response->withHeader('Content-Type', 'application/json');
});

$app->run();
