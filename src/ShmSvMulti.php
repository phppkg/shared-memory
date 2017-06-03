<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017/6/1
 * Time: 下午9:44
 */

namespace inhere\shm;
use inhere\library\helpers\PhpHelper;
use inhere\lock\Lock;
use inhere\lock\LockInterface;
use inhere\library\traits\LiteConfigTrait;

/**
 * Class ShmSvMulti
 *  - powered by system v shm. require enable  --enable-sysvshm
 *  - only support *nix system
 *
 * @package inhere\shm
 */
class ShmSvMulti
{
    use LiteConfigTrait;

    /**
     * @var LockInterface
     */
    private $locker;

    /**
     * The variable keys
     * @var int[]
     */
    private $varKeys = [];

    /**
     * A numeric shared memory segment ID
     * @var int
     */
    private $key;

    /**
     * Shared memory segment identifier.
     * @var int|resource
     */
    private $shmId;

    /**
     * @var array
     */
    protected $config = [
        'key' => null,
        'size' => 256000,
        'project' => 'php_shm', // shared memory project

        'locker' => [
            'driver' => Lock::DRIVER_SEM, // please
            'tmpDir' => '/tmp', // tmp path, if use FileLock
        ],
    ];

    /**
     * MsgQueue constructor.
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        if (!self::isSupported()) {
            throw new \RuntimeException(
                'To use sysvshm you will need to compile PHP with the --enable-sysvshm parameter in your configure line.',
                -500
            );
        }

        $this->setConfig($config);

        $this->init();
    }

    /**
     * init
     */
    protected function init()
    {
        if ($this->config['key'] > 0) {
            $this->key = (int)$this->config['key'];
        } else {
            // 定义共享内存
            $this->key = $this->config['key'] = PhpHelper::ftok(__FILE__, $this->config['project']);
        }

        $this->config['locker']['key'] = $this->key;

        $this->locker = new Lock($this->config['locker']);
    }

    /**
     * set Multi
     * @param array $map
     * @return array
     */
    public function setMulti(array $map)
    {
        $ret = [];

        foreach ($map as $varKey => $var) {
            $ret[$varKey] = $this->set($varKey, $var);
        }

        return $ret;
    }

    /**
     * get Multi
     * @param array $varKeys
     * @return array
     */
    public function getMulti(array $varKeys)
    {
        $ret = [];

        foreach ($varKeys as $varKey) {
            $ret[$varKey] = $this->get($varKey);
        }

        return $ret;
    }

    /**
     * Inserts or updates a variable in SHM
     * @param int $varKey
     * @param $var
     * @return bool
     */
    public function set($varKey, $var)
    {
        $ret = false;

        // lock
        if ($this->locker->lock($this->key)) {
            $varKey = (int)$varKey;
            $this->varKeys[$varKey] = true;

            // operate data
            $ret = shm_put_var($this->shmId, $varKey, $var);

            // unlock
            $this->locker->unlock($this->key);
        }

        return $ret;
    }

    /**
     * @param int $varKey
     * @param null $default
     * @return mixed|null
     */
    public function get($varKey, $default = null)
    {
        $ret = false;

        // lock
        if ($this->locker->lock($this->key)) {
            $varKey = (int)$varKey;
            $ret = $this->has($varKey) ? shm_get_var($this->shmId, (int)$varKey) : $default;

            // unlock
            $this->locker->unlock($this->key);
        }

        return $ret;
    }

    /**
     * has var in SHM
     * @param int $varKey
     * @return bool
     */
    public function has($varKey)
    {
        return shm_has_var($this->shmId, (int)$varKey);
    }

    /**
     * @param int $varKey
     * @return bool
     */
    public function del($varKey)
    {
        return shm_remove_var($this->shmId, (int)$varKey);
    }

    /**
     * @return bool
     */
    public static function isSupported()
    {
        return function_exists('shm_attach');
    }

    /**
     * @return LockInterface
     */
    public function getLocker()
    {
        return $this->locker;
    }

    /**
     * @return int[]
     */
    public function getVarKeys()
    {
        return $this->varKeys;
    }

    /**
     * @return int
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @return int|resource
     */
    public function getShmId()
    {
        return $this->shmId;
    }
}
