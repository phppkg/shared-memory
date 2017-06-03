<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017/6/2
 * Time: 下午7:40
 */

namespace inhere\shm;

/**
 * Interface ShmInterface
 * @package inhere\shm
 */
interface ShmInterface
{
    /**
     * open resource
     */
    public function open();

    /**
     * @param string $data
     * @return bool
     */
    public function write($data);

    /**
     * @param int $size
     * @return string
     */
    public function read($size = 0);

    /**
     * close
     */
    public function close();

    /**
     * @return bool
     */
    public static function isSupported();
}
