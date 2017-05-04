<?php

namespace Mg\JmesPathServer;

use Arrayly\Arrayly;
use Mg\JmesPathServer\Context\ApplicationContext;
use Mg\JmesPathServer\Datasource\DatasourceService;
use Mg\JmesPathServer\RequestHandler\Search\SearchRequest;
use Mg\JmesPathServer\RequestHandler\Search\SearchRequestHandler;
use Mg\JmesPathServer\Util\ExceptionUtil;
use Psr\Http\Message\ResponseInterface as ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as RequestInterface;
use Slim\App as SlimApp;
use Slim\Container as SlimContainer;
use Slim\Http\Response as HttpResponse;
use Stringy\Stringy;

class ApiApplication
{
    /**
     * @var DatasourceService
     */
    private $applicationContext;

    /**
     * @var DatasourceService
     */
    private $datasourceService;

    /**
     *
     */
    public function __construct()
    {
        $this->applicationContext = new ApplicationContext();
        $this->datasourceService = new DatasourceService($this->applicationContext);
    }


    /**
     *
     */
    public function run()
    {
        $slimContainer = $this->createSlimContainer();

        //Create Slim App and inject container
        $slimApp = new SlimApp($slimContainer);

        // define Slim Routes
        $this->initRoutes($slimApp);

        // run slim app
        $slimApp->run();
    }

    /**
     * @return SlimContainer
     */
    private function createSlimContainer()
    {
        $slimContainer = new SlimContainer();
        //Override the default Not Found Handler
        $slimContainer['notFoundHandler'] = function (SlimContainer $c) {

            return function (RequestInterface $request, ResponseInterface $response) use ($c) {
                /** @var HttpResponse $r */
                $r = $c['response'];

                $responseData = [
                    'error' => [
                        'message' => 'Route Not Found.'
                    ]
                ];

                return $r
                    ->withStatus(404)
                    ->withHeader('Content-Type', 'application/json')
                    ->write(json_encode(
                        $responseData
                    ));
            };
        };

        $slimContainer['errorHandler'] = function (SlimContainer $c) {
            return function (RequestInterface $request, ResponseInterface $response, \Exception $e) use (
                $c
            ) {
                /** @var HttpResponse $r */
                $r = $c['response'];


                $responseData = [
                    'error' => ExceptionUtil::exceptionAsArray($e, true)
                ];

                return $r
                    ->withStatus(500)
                    ->withHeader('Content-Type', 'application/json')
                    ->write(json_encode(
                        $responseData
                    ));
            };
        };

        return $slimContainer;
    }

    /**
     * @param SlimApp $slimApp
     */
    private function initRoutes(SlimApp $slimApp)
    {
        $datasourceService = $this->datasourceService;
        // example
        $slimApp->get('/{source}/search',
            function (RequestInterface $request, ResponseInterface $response, array $args) use ($datasourceService) {
                try {
                    $handler = new SearchRequestHandler($datasourceService);

                    $source = $request->getAttribute('source');
                    $query = Arrayly::ofArray($request->getQueryParams())
                        ->getOrDefault('q', '');

                    $req = new SearchRequest(
                        Stringy::create($source),
                        Stringy::create($query)
                    );
                    $apiResponse = $handler->handleRequest($req);

                    if ($apiResponse->hasError()) {
                        $response->getBody()
                            ->write(json_encode([
                                "error" => $apiResponse->getError()
                            ]));
                    } else {
                        $response->getBody()
                            ->write(json_encode($apiResponse->getResult()));
                    }

                    return $response
                        ->withStatus($apiResponse->getStatusCode())
                        ->withHeader('Content-Type', 'application/json');


                } catch (\Throwable $all) {
                    $response->getBody()
                        ->write(json_encode([
                            "error" => ExceptionUtil::exceptionAsArray($all, false)
                        ]));

                    return $response
                        ->withStatus(500)
                        ->withHeader('Content-Type', 'application/json');
                }

                /*
                $handler = new SearchRequestHandler($datasourceService);

                $source = $request->getAttribute('source');
                $query=Arrayly::ofArray($request->getQueryParams())
                    ->getOrDefault('q', '');

                $req = new SearchRequest(
                    Stringy::create($source),
                    Stringy::create($query)
                );
                $response= $handler->handleRequest($req, $response, $args);

                return $response->withHeader('Content-Type', 'application/json');
                /*
                ini_set('memory_limit', ' 1024M');

                $startTs = microtime(true);
                $source = $request->getAttribute('source');

                $query=trim(
                    Arrayly::ofArray($request->getQueryParams())
                        ->getOrDefault('q', '')
                );
                var_dump($query);
                //$query = trim((string)ArrayAssocUtil::getKeyOrDefault($request->getQueryParams(), 'q', null));

                $responseData = new MutableHashMap([
                    'responseCode'=> 200,
                    'error' => null,
                    'data' => null,
                ]);


                $mapper = function() use($datasourceService, $source, $responseData, $query) {
                    $datasource=$datasourceService->getDatasource(trim($source));
                    if (!$datasource->isFile()) {
                        return $responseData->with('responseCode', 404);
                    }

                    $datasource=$datasourceService->getDatasource(trim($source));
                    if (!$datasource->isFile()) {
                        return $responseData->with('responseCode', 404);
                    }

                    $data = $datasourceService->load($datasource);
                    if ($data->isEmpty()) {
                        return $responseData->with('responseCode', 404);
                    }

                    return $responseData->with(
                        'data', $datasourceService->jq($query, $data)
                    );
                };



                try {
                    if ($query === '') {
                        $responseData->put('responseCode', 400);

                        throw new \OutOfBoundsException('request.q must be string, not empty!');
                    }

                    $responseData = $mapper();


                } catch (\Exception $e) {
                    $responseData->put(
                        'error',  ExceptionUtil::exceptionAsArray($e, true)
                    );
                }
                $stopTs = microtime(true);



                $responseData->put('memory', (memory_get_peak_usage(true) / (1024 * 1024)) . ' MB');
                $responseData->put('duration', (($stopTs - $startTs) * 1000) . ' ms');
                $response->getBody()->write(
                    json_encode($responseData->toArray())
                );

                return $response
                    ->withStatus($responseData->getOrNull('responseCode'))
                    ->withHeader('Content-Type', 'application/json');
                */
            });


    }


}