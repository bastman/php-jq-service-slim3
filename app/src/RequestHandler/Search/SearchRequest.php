<?php
/**
 * Created by PhpStorm.
 * User: sebastians
 * Date: 04.05.17
 * Time: 14:59
 */

namespace Mg\JmesPathServer\RequestHandler\Search;


use Stringy\Stringy;

class SearchRequest
{
    /**
     * @var Stringy
     */
    private $dataSourceName;
    /**
     * @var Stringy
     */
    private $query;

    /**
     * SearchRequest constructor.
     * @param string $dataSourceName
     * @param string $query
     */
    public function __construct(Stringy $dataSourceName, Stringy $query)
    {
        $this->dataSourceName = $dataSourceName;
        $this->query = $query;
    }


    /**
     * @return Stringy
     */
    public function getDataSourceName()
    {
        return $this->dataSourceName;
    }

    /**
     * @return Stringy
     */
    public function getQuery()
    {
        return $this->query;
    }


}