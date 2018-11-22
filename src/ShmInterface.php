<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017/6/2
 * Time: 下午7:40
 */

namespace PhpComp\Shm;

/**
 * Interface ShmInterface
 * @package PhpComp\Shm
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
    public function write($data): bool;

    /**
     * @param string $data
     * @return bool
     */
    public function prepend($data): bool;

    /**
     * @param string $data
     * @return bool
     */
    public function append($data): bool;

    /**
     * @param int $size
     * @return string
     */
    public function read($size = 0): string;

    /**
     * clear
     * @return bool
     */
    public function clear(): bool;

    /**
     * close
     * @return bool
     */
    public function close(): bool;

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
