<?php

namespace Mg\JmesPathServer\Datasource;

use Arrayly\Arrayly;
use JmesPath;
use Mg\JmesPathServer\Context\ApplicationContext;
use Mg\JmesPathServer\Definition\HashMap;
use Mg\JmesPathServer\Util\Filesystem\FilesystemUtil;

class DatasourceService
{

    /**
     * @var DatasourceConfig
     */
    private $config;
    /**
     * @var
     */
    private $applicationContext;

    /**
     * @param ApplicationContext $applicationContext
     */
    public function __construct(ApplicationContext $applicationContext)
    {
        $this->applicationContext = $applicationContext;
        $configFile = $applicationContext->getResource('datasource-config.json');
        $data = FilesystemUtil::jsonDecodeContents($configFile);
        $this->config = new DatasourceConfig($data);
    }

    /**
     * @param $name
     * @return null|\SplFileInfo
     */
    public function getDatasource(string $name):?\SplFileInfo
    {
        $resourceName = $this->config->getOrNull($name);
        if (!$resourceName) {

            return null;
        }

        return $this->applicationContext
            ->getResource($resourceName);
    }

    /**
     * @param \SplFileInfo $datasource
     * @return HashMap
     */
    public function load(\SplFileInfo $datasource)
    {

        $data = FilesystemUtil::jsonDecodeContents($datasource);
        if (!is_array($data)) {
            $data = array();
        }

        return new HashMap($data);
    }

    /**
     * @param $query
     * @param Arrayly $data
     * @return array|mixed|null
     */
    public function jq(string $query, Arrayly $data)
    {
        if ($query === '.') {

            return $data->toArray();
        }

        return JmesPath\search($query, $data->toArray());
    }

}