<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017/6/2
 * Time: 下午8:19
 */

namespace PhpComp\Shm;

/**
 * Class ShmFactory
 * @package PhpComp\Shm
 */
final class SharedMemory
{
    const DRIVER_OP = 'op'; // require enable --enable-shmop
    const DRIVER_SV = 'sv'; // require enable  --enable-sysvshm

    /**
     * @var array
     */
    private static $driverMap = [
        self::DRIVER_OP => ShmOp::class,
        self::DRIVER_SV => ShmSv::class,
    ];

    /**
     * @param array $config
     * @param string $driver
     * @return ShmInterface
     * @throws \RuntimeException
     */
    public static function make(array $config = [], $driver = null)
    {
        if (!$driver && isset($config['driver'])) {
            $driver = $config['driver'];
            unset($config['driver']);
        }

        /** @var ShmInterface $class */
        if (!isset(self::$driverMap[$driver])) {
            foreach (self::$driverMap as $class) {
                if ($class::isSupported()) {
                    return new $class($config);
                }
            }

            throw new \RuntimeException('No available SHM driver! MAP: ' . implode(',', self::$driverMap));
        }

        $class = self::$driverMap[$driver];

        return new $class($config);
    }

    /**
     * @return bool
     */
    public static function isSupported(): bool
    {
        foreach (self::$driverMap as $class) {
            if ($class::isSupported()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return array
     */
    public static function getDriverMap(): array
    {
        return self::$driverMap;
    }
}
