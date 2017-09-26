<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017/6/3
 * Time: 上午12:55
 */

namespace Inhere\Shm;

/**
 * Interface ShmMapInterface - 可以当做是共享内存的数组结构
 * @package Inhere\Shm
 */
interface ShmMapInterface
{
    /**
     * set a value to SHM-Map
     * @param string $name
     * @param mixed $value
     * @return bool
     */
    public function set($name, $value);

    /**
     * get a value form SHM-Map
     * @param null|string $name
     * @param mixed $default
     * @return mixed
     */
    public function get($name, $default = null);

    /**
     * has key
     * @param $name
     * @return bool
     */
    public function has($name);

    /**
     * del a value
     * @param string $name
     * @return bool
     */
    public function del($name);

    /**
     * get map data
     * @return array
     */
    public function getMap();

    /**
     * set map data
     * @param array $map
     * @param bool $merge
     * @return bool
     */
    public function setMap(array $map, $merge = false);
}
