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
 *
 * @property string $driver
 */
interface ShmInterface
{
    /**
     * open shared memory resource
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
     * clear
     * @return bool
     */
    public function clear();

    /**
     * close
     * @return bool
     */
    public function close();

    /**
     * @return string
     */
    public function getDriver(): string;

    /**
     * @return int
     */
    public function getKey(): int;

    /**
     * @return int
     */
    public function getErrCode(): int;

    /**
     * @return string
     */
    public function getErrMsg(): string;

    /**
     * @return array
     */
    public function getError(): array;

    /**
     * @return bool
     */
    public static function isSupported();
}
