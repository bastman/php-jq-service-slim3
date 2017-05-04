<?php

namespace Mg\JmesPathServer\RequestHandler\Search;

use Arrayly\Arrayly;
use Mg\JmesPathServer\Datasource\DatasourceService;
use Mg\JmesPathServer\RequestHandler\ApiResponse;
use Stringy\Stringy;

class SearchRequestHandler
{
    /**
     * @var DatasourceService
     */
    private $datasourceService;

    /**
     * SearchRequestHandler constructor.
     * @param DatasourceService $datasourceService
     */
    public function __construct(DatasourceService $datasourceService)
    {
        $this->datasourceService = $datasourceService;
    }

    /**
     * @param SearchRequest $request
     * @return ApiResponse
     */
    function handleRequest(
        SearchRequest $request
    ): ApiResponse
    {

        $startTs = microtime(true);

        $source = $request->getDataSourceName()
            ->trim();
        $query = $request->getQuery()->trim();

        $apiResponse = ApiResponse::ofStatusCode(200);

        if (!$query->length()) {

            return $apiResponse
                ->withStatusCode(400)
                ->withError([
                    "message" => 'request.q must be string, not empty!'
                ]);
        }

        $sourceData = $this->loadDatasourceOrNull($source);
        if (!$sourceData) {

            return $apiResponse
                ->withStatusCode(404)
                ->withError([
                    "message" => 'datasource not found!'
                ]);
        }


        try {
            $sinkData = $this->jq($query, $sourceData);
        } catch (\Throwable $all) {

            return $apiResponse
                ->withStatusCode(400)
                ->withError([
                    "message" => 'failed to compile jq query! reason=' . $all->getMessage()
                ]);
        }


        $stopTs = microtime(true);

        $responseData = [
            'data' => $sinkData,
            'memory' => (memory_get_peak_usage(true) / (1024 * 1024)) . ' MB',
            'duration' => (($stopTs - $startTs) * 1000) . ' ms',
        ];

        return $apiResponse->withResult($responseData);
    }


    /**
     * @param Stringy $source
     * @return Arrayly|null
     */
    private function loadDatasourceOrNull(Stringy $source):?Arrayly
    {

        $datasource = $this->datasourceService->getDatasource($source);

        $data = $this->datasourceService->load($datasource);

        if ($data->isEmpty()) {

            return null;
        }

        return Arrayly::ofArray($data->toArray());
    }

    /**
     * @param Stringy $query
     * @param Arrayly $sourceData
     * @return array|mixed|null
     */
    private function jq(Stringy $query, Arrayly $sourceData)
    {

        return $this->datasourceService->jq($query, $sourceData);
    }

}