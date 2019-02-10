<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017/6/1
 * Time: 下午9:43
 */

namespace PhpComp\Shm;

/**
 * Class ShmOpMap 可以当做是共享内存的数组结构(读取都会自动加锁)
 *  - shared map(array) structure.
 *  - require enable --enable-shmop
 *  - support *nix and windows
 *
 * @package PhpComp\Shm
 */
class ShmMap implements ShmMapInterface, \ArrayAccess, \Countable, \IteratorAggregate
{
    /**
     * @var ShmInterface
     */
    private $shm;

    /**
     * ShmOpMap constructor.
     * @param array $config
     * @param null|string $driver
     * @throws \RuntimeException
     */
    public function __construct(array $config = [], $driver = null)
    {
        $this->shm = SharedMemory::make($config, $driver);
        $this->shm->open();
    }

    /*****************************************************************
     * map method
     ****************************************************************/

    /**
     * {@inheritDoc}
     */
    public function get($name, $default = null)
    {
        $map = $this->getMap();

        return $map[$name] ?? $default;
    }

    /**
     * {@inheritDoc}
     */
    public function set($name, $value): bool
    {
        // if is empty, init.
        if (!$map = $this->getMap()) {
            $map = [];
        }

        $map[$name] = $value;

        return $this->setMap($map);
    }

    /**
     * {@inheritDoc}
     */
    public function has($name): bool
    {
        if ($map = $this->getMap()) {
            return isset($map[$name]);
        }

        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function del($name): bool
    {
        // if is empty, init.
        if (!$map = $this->getMap()) {
            return false;
        }

        if (isset($map[$name])) {
            // $value = $map[$name];
            unset($map[$name]);

            return $this->setMap($map);
        }

        return false;
    }

    /**
     * push elem to first
     * @param $data
     * @return bool
     */
    public function lPush($data): bool
    {
        if (!$map = $this->getMap()) {
            $map = [];
        }

        array_unshift($map, $data);

        return $this->setMap($map);
    }

    /**
     * push elem to last
     * @param $data
     * @return bool
     */
    public function rPush($data): bool
    {
        if (!$map = $this->getMap()) {
            $map = [];
        }

        $map[] = $data;

        return $this->setMap($map);
    }

    /**
     * @return bool|mixed
     */
    public function lPop()
    {
        if (!$map = $this->getMap()) {
            return false;
        }

        $val = array_shift($map);
        $this->setMap($map);

        return $val;
    }

    /**
     * @return bool|mixed
     */
    public function rPop()
    {
        if (!$map = $this->getMap()) {
            return false;
        }

        $val = array_pop($map);
        $this->setMap($map);

        return $val;
    }

    /**
     * @param array $map
     * @return bool
     */
    public function sets(array $map): bool
    {
        return $this->setMap($map, true);
    }

    /**
     * @param array $names
     * @return array
     */
    public function gets(array $names): array
    {
        $ret = [];
        $map = $this->getMap();

        foreach ($names as $name) {
            if (isset($map[$name])) {
                $ret[$name] = $map[$name];
            }
        }

        return $ret;
    }

    /**
     * alias of getMap()
     * @return array
     */
    public function all(): array
    {
        return $this->getMap();
    }

    /**
     * get map data
     * @return array
     */
    public function getMap(): array
    {
        if (!$read = $this->shm->read()) {
            return [];
        }

        $map = unserialize(trim($read), ['allowed_classes' => false]);

        if (!is_array($map)) {
            $map = [];
        }

        return $map;
    }

    /**
     * set map data
     * @param array $map
     * @param bool $merge
     * @return bool
     */
    public function setMap(array $map, $merge = false): bool
    {
        if (!$merge) {
            return $this->shm->write(serialize($map));
        }

        if ($old = $this->getMap()) {
            return $this->shm->write(serialize(array_merge($old, $map)));
        }

        return $this->shm->write(serialize($map));
    }

    /**
     * @return ShmInterface
     */
    public function getShm()
    {
        return $this->shm;
    }

    /**
     * @return string
     */
    public function getDriver(): string
    {
        return $this->shm->getDriver();
    }

    /**
     * clear data
     */
    public function clear()
    {
        $this->shm->clear();
    }

    /**
     * close
     */
    public function close()
    {
        $this->shm->close();
    }

    /*****************************************************************
     * array access method
     ****************************************************************
     *
     * /**
     * Checks whether an offset exists in the iterator.
     * @param   mixed $offset The array offset.
     * @return  boolean  True if the offset exists, false otherwise.
     */
    public function offsetExists($offset)
    {
        return $this->has($offset);
    }

    /**
     * Gets an offset in the iterator.
     * @param   mixed $offset The array offset.
     * @return  mixed  The array value if it exists, null otherwise.
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * Sets an offset in the iterator.
     * @param   mixed $offset The array offset.
     * @param   mixed $value The array value.
     * @return  void
     */
    public function offsetSet($offset, $value)
    {
        $this->set($offset, $value);
    }

    /**
     * Unset an offset in the iterator.
     * @param   mixed $offset The array offset.
     * @return  void
     */
    public function offsetUnset($offset)
    {
        $this->del($offset);
    }

    /**
     * Count elements of an object
     * @link http://php.net/manual/en/countable.count.php
     * @return int The custom count as an integer.
     * </p>
     * <p>
     * The return value is cast to an integer.
     * @since 5.1.0
     */
    public function count()
    {
        return count($this->getMap());
    }

    /**
     * Retrieve an external iterator
     * @link http://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return \Traversable An instance of an object implementing <b>Iterator</b> or
     * <b>Traversable</b>
     * @since 5.0.0
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->getMap());
    }
}
