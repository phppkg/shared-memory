<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017/6/1
 * Time: 下午9:46
 */

namespace inhere\shm;

use inhere\lock\Lock;
use inhere\lock\LockInterface;
use inhere\library\helpers\PhpHelper;
use inhere\library\traits\LiteConfigTrait;

/**
 * Class BaseShm
 * @package inhere\shm
 */
abstract class BaseShm implements ShmInterface
{
    use LiteConfigTrait;

    /**
     * @var LockInterface
     */
    private $locker;

    /**
     * A numeric shared memory segment ID
     * @var int
     */
    protected $key;

    /**
     * Shared memory segment identifier.
     * @var int|resource
     */
    protected $shmId;

    /**
     * @var array
     */
    protected $config = [
        'key' => null,
        'size' => 256000,
        'project' => 'php_shm', // shared memory project

        'locker' => [
            'driver' => '', // allow: File Database Memcache Semaphore
            'tmpDir' => '/tmp', // tmp path, if use FileLock
        ],
    ];

    /**
     * MsgQueue constructor.
     * @param array $config
     */
    public function __construct(array $config = [])
    {
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
     * write data to SHM
     * @param string $data
     * @return bool
     */
    public function write($data)
    {
        $ret = false;

        // lock
        if ($this->lock($this->key)) {
            // write data
            $ret = $this->doWrite($data);

            // unlock
            $this->unlock($this->key);
        }

        return $ret;
    }

    /**
     * @param string $data
     * @return bool
     */
    abstract protected function doWrite($data);

    /**
     * read data form SHM
     * @param int $size
     * @return string
     */
    public function read($size = 0)
    {
        $ret = false;

        // lock
        if ($this->lock($this->key)) {
            $ret = $this->doRead($size);

            // unlock
            $this->unlock($this->key);
        }

        return $ret;
    }

    /**
     * @param int $size
     * @return bool
     */
    abstract protected function doRead($size = 0);

//////////////////////////////////////////////////////////////////////
/// helper method
//////////////////////////////////////////////////////////////////////

    /**
     * @param string $key
     * @param int $timeout
     * @return bool
     */
    public function lock($key, $timeout = 3)
    {
        return $this->locker->lock($key, $timeout);
    }

    /**
     * @param string $key
     * @return bool
     */
    public function unlock($key)
    {
        return $this->locker->unlock($key);
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
