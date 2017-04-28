<?php

namespace Mg\JmesPathServer\Util\Filesystem;

class FilesystemUtil
{

    /**
     * @param \SplFileInfo $fileInfo
     * @param $suffix
     * @return \SplFileInfo
     */
    public static function appendToPathname(\SplFileInfo $fileInfo, $suffix)
    {
        return new \SplFileInfo($fileInfo->getPathname() . (string)$suffix);
    }

    /**
     * @param \SplFileInfo $dirInfo
     * @throws IOException
     * @return \SplFileInfo
     */
    public static function requireRealPathToDirectoryIsReadable(\SplFileInfo $locationInfo)
    {
        return static::requireRealPathToDirectory(
            $locationInfo,
            function (\SplFileInfo $resource) {
                if (!$resource->isReadable()) {

                    throw new IOException('Dir not readable!');
                }
            }
        );
    }

    /**
     * @param \SplFileInfo $dirInfo
     * @throws IOException
     * @return \SplFileInfo
     */
    public static function requireRealPathToDirectory(\SplFileInfo $locationInfo, \Closure $validator)
    {

        $realPath = '';

        try {
            $realPath = (string)$locationInfo->getRealPath();
            $isValidRealPath = trim($realPath) !== '';
            $isDir = $locationInfo->isDir() && $isValidRealPath;
            if (!$isDir) {

                throw new IOException('Dir not found!');
            }

            $realPathInfo = new \SplFileInfo($realPath);
            $isDir = $realPathInfo->isDir();
            if (!$isDir) {

                throw new IOException('Dir (realpath) not found!');
            }

            call_user_func_array($validator, [$realPathInfo]);

            return $realPathInfo;
        } catch (\Exception $e) {

            $message = 'IOERROR: Failed to access resource: '
                . ' location: ' . $locationInfo->getPathname()
                . ' realPath: ' . $realPath
                . ' Reason: ' . $e->getMessage();

            throw new IOException($message);
        }
    }

    /**
     * @param \SplFileInfo $dirInfo
     * @throws IOException
     * @return \SplFileInfo
     */
    public static function requireRealPathToDirectoryIsWriteable(\SplFileInfo $locationInfo)
    {
        return static::requireRealPathToDirectory(
            $locationInfo,
            function (\SplFileInfo $resource) {
                if (!$resource->isWritable()) {

                    throw new IOException('Dir not writeable!');
                }
            }
        );
    }

    /**
     * @param \SplFileInfo $dirInfo
     * @throws IOException
     * @return \SplFileInfo
     */
    public static function requireRealPathToFileIsReadable(\SplFileInfo $locationInfo)
    {
        $realPath = '';

        try {
            $realPath = (string)$locationInfo->getRealPath();
            $isValidRealPath = trim($realPath) !== '';
            $isFile = $locationInfo->isFile() && $isValidRealPath;
            if (!$isFile) {

                throw new IOException('File not found!');
            }

            $realPathInfo = new \SplFileInfo($realPath);
            $isFile = $realPathInfo->isFile();
            if (!$isFile) {

                throw new IOException('File (realpath) not found!');
            }
            if (!$realPathInfo->isReadable()) {

                throw new IOException('File (realpath) is not readable!');
            }

            return $realPathInfo;
        } catch (\Exception $e) {

            $message = 'IOERROR: Failed to access resource: '
                . ' location: ' . $locationInfo->getPathname()
                . ' realPath: ' . $realPath
                . ' Reason: ' . $e->getMessage();

            throw new IOException($message);
        }
    }


    /**
     * @param \SplFileInfo $locationInfo
     * @param \Closure $validator
     * @return \SplFileInfo
     */
    public static function requireRealPathToFile(\SplFileInfo $locationInfo, \Closure $validator)
    {

        $realPath = '';

        try {
            $realPath = (string)$locationInfo->getRealPath();
            $isValidRealPath = trim($realPath) !== '';
            $isFile = $locationInfo->isFile() && $isValidRealPath;
            if (!$isFile) {

                throw new IOException('File not found!');
            }

            $realPathInfo = new \SplFileInfo($realPath);
            $isFile = $realPathInfo->isFile();
            if (!$isFile) {

                throw new IOException('File (realpath) not found!');
            }

            call_user_func_array($validator, [$realPathInfo]);

            return $realPathInfo;
        } catch (\Exception $e) {

            $message = 'IOERROR: Failed to access resource: '
                . ' location: ' . $locationInfo->getPathname()
                . ' realPath: ' . $realPath
                . ' Reason: ' . $e->getMessage();

            throw new IOException($message);
        }
    }

    /**
     * @param \SplFileInfo $fileInfo
     * @return mixed
     */
    public static function jsonDecodeContents(\SplFileInfo $fileInfo)
    {
        $content = static::getContents($fileInfo);

        return json_decode($content, true);
    }

    /**
     * Returns the contents of the file.
     * taken from Symfony/Finder v3
     * @return string the contents of the file
     *
     * @throws IOException
     */
    public static function getContents(\SplFileInfo $fileInfo)
    {
        $level = error_reporting(0);
        $content = file_get_contents($fileInfo->getPathname());
        error_reporting($level);
        if (false === $content) {
            $error = error_get_last();

            throw new IOException(
                'IOERROR: Failed get content from file: ' . $fileInfo->getPathname() . ' !'
                . ' details: ' . $error['message']
            );
        }

        return (string)$content;
    }


}