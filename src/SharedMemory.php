<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017/6/1
 * Time: 下午8:58
 */

namespace inhere\shm;

/**
 * Class SharedMemory
 * @package inhere\shm
 */
class SharedMemory implements ShmInterface
{
    /**
     * @var ShmInterface
     */
    private $driver;

    /**
     * Lock constructor.
     * @param array $config
     * @param string $driverName
     */
    public function __construct(array $config = [], $driverName = null)
    {
        $this->driver = ShmFactory::make($config, $driverName);
    }

    /**
     * {@inheritDoc}
     */
    public function open()
    {
        $this->driver->open();
    }

    /**
     * {@inheritDoc}
     */
    public function write($data)
    {
        return $this->driver->write($data);
    }

    /**
     * {@inheritDoc}
     */
    public function read($size = 0)
    {
        return $this->driver->read($size);
    }

    /**
     * {@inheritDoc}
     */
    public function close()
    {
        $this->driver->close();
    }

    /**
     * @return bool
     */
    public static function isSupported()
    {
        return ShmFactory::isSupported();
    }
}
