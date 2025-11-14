<?php
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;
use Rafael\RespiraBem\services\ViewRender;
use Rafael\RespiraBem\services\Pollution;
use Dotenv\Dotenv;
use Rafael\RespiraBem\services\HttpClient;

require __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$app = AppFactory::create();



// Rota para a view index.php
$app->get('/', function (ServerRequestInterface $request, ResponseInterface $response) {
   try{
        $html = ViewRender::render('home');
        $response->getBody()->write($html);
        return $response;
   } catch (\Exception $e) {
       $response->getBody()->write("Erro ao renderizar a view: " . $e->getMessage());
       return $response->withStatus(500);
   }
});

// Rota para obter dados de poluição
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

    $pollution = new Pollution(new HttpClient() , $_ENV['API_KEY']);
    $result = $pollution->getPollutionData($lat, $lon);

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
