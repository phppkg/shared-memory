<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017/6/2
 * Time: 下午8:19
 */

namespace inhere\shm;

/**
 * Class ShmFactory
 * @package inhere\shm
 */
class ShmFactory
{
    const DRIVER_OP = 'ShmOp'; // require enable --enable-shmop
    const DRIVER_SV = 'ShmSv'; // require enable  --enable-sysvshm

    /**
     * @var array
     */
    private static $driverMap = [
        self::DRIVER_OP,
        self::DRIVER_SV,
    ];

    /**
     * @param array $config
     * @param string $driver
     * @return ShmInterface
     */
    public static function make(array $config = [], $driver = null)
    {
        if (!$driver && isset($config['driver'])) {
            $driver = $config['driver'];
            unset($config['driver']);
        }

        if (!in_array($driver, self::$driverMap, true)) {
            foreach (self::$driverMap as $driver) {
                if ($driver::isSupported()){
                    return new $driver($config);
                }
            }

            throw new \RuntimeException('No available SHM driver! MAP: ' . implode(',', self::$driverMap));
        }

        return new $driver($config);
    }

    /**
     * @return bool
     */
    public static function isSupported()
    {
        foreach (self::$driverMap as $driver) {
            if ($driver::isSupported()){
                return true;
            }
        }

        return false;
    }

    /**
     * @return array
     */
    public static function getDriverMap()
    {
        return self::$driverMap;
    }
}
