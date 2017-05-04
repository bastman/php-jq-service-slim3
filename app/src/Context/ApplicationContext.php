<?php

namespace Mg\JmesPathServer\Context;

use Mg\JmesPathServer\Util\Filesystem\IOException;

class ApplicationContext
{
    /**
     * @var \SplFileInfo
     */
    private $appRoot;

    /**
     * ApplicationContext constructor.
     */
    public function __construct()
    {
        $appRootPath = __DIR__ . "/../../../app";
        if (!realpath($appRootPath)) {

            throw new IOException("ConfigurationError! appRootPath does not exist");
        }

        $this->appRoot = new \SplFileInfo(realpath($appRootPath));
    }

    /**
     * @param $name
     * @return \SplFileInfo
     */
    public function getResource($name)
    {
        $appRoot = $this->getAppRoot()
            ->getPathname();

        return new \SplFileInfo($appRoot . '/resources/' . $name);
    }

    /**
     * @return \SplFileInfo
     */
    public function getAppRoot()
    {
        return $this->appRoot;
    }


}