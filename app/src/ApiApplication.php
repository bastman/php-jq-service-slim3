<?php

namespace Mg\JmesPathServer;

use JmesPath;
use Mg\JmesPathServer\Util\ArrayAssocUtil;
use Mg\JmesPathServer\Util\ExceptionUtil;
use Mg\JmesPathServer\Util\Filesystem\FilesystemUtil;
use Psr\Http\Message\ResponseInterface as ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as RequestInterface;
use Slim\App as SlimApp;
use Slim\Container as SlimContainer;
use Slim\Http\Response as HttpResponse;

class ApiApplication
{


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
        // example
        $slimApp->get('/{source}/search',
            function (RequestInterface $request, ResponseInterface $response, array $args) {
                ini_set('memory_limit', ' 1024M');
                $datasourceRoot = __DIR__ . '/../resources/datasource';

                $startTs = microtime(true);
                $source = $request->getAttribute('source');
                $query = trim((string)ArrayAssocUtil::getKeyOrDefault($request->getQueryParams(), 'q', null));

                $responseData = [
                    'error' => null,
                    'data' => null,
                ];
                $responseCode = 200;


                try {
                    if ($query === '') {
                        $responseCode = 400;

                        throw new \OutOfBoundsException('request.queryparams.q must be string, not empty!');
                    }

                    $dataSource = new \SplFileInfo($datasourceRoot . '/' . trim($source) . '.json');
                    if (!$dataSource->isFile()) {
                        $responseCode = 404;
                    }
                    $data = FilesystemUtil::jsonDecodeContents($dataSource);
                    if (!is_array($data)) {
                        $responseCode = 404;
                    }
                    if ($query === '.') {
                        $responseData['data'] = $data;
                    } else {
                        $responseData['data'] = JmesPath\search($query, $data);
                    }

                } catch (\Exception $e) {
                    $responseData['error'] = ExceptionUtil::exceptionAsArray($e, true);
                }
                $stopTs = microtime(true);

                $responseData['memory'] = (memory_get_peak_usage(true) / (1024 * 1024)) . ' MB';
                $responseData['duration'] = (($stopTs - $startTs) * 1000) . ' ms';
                $response->getBody()->write(
                    json_encode($responseData)
                );

                return $response
                    ->withStatus($responseCode)
                    ->withHeader('Content-Type', 'application/json');


            });


    }


}