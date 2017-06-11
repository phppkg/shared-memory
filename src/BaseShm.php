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
     * @var string
     */
    protected $driver;

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
        'project' => 's', // shared memory project id. only allow one char

        'locker' => [
            'driver' => '', // allow: File Database Memcache Semaphore
            'tmpDir' => '/tmp', // tmp path, if use FileLock
        ],
    ];

    /**
     * @var int
     */
    private $errCode = 0;

    /**
     * @var string
     */
    private $errMsg;

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
     * @throws \LogicException
     * @throws \RuntimeException
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

        $this->locker = Lock::make($this->config['locker']);
    }

    /**
     * {@inheritDoc}
     * @throws \RuntimeException
     */
    public function open()
    {
        try {
            // open
            $this->shmId = $this->doOpen();
        } catch (\Exception $e) {
            $this->errCode = $e->getCode();
            $this->errMsg = $e->getMessage();
        }

        if (!$this->shmId) {
            throw new \RuntimeException('Create shared memory block failed', -200);
        }
    }

    /**
     * do open shared memory
     * @return resource
     */
    abstract protected function doOpen();

    /**
     * write data to SHM
     * @param string $data
     * @return bool
     * @throws \LogicException
     */
    public function write($data)
    {
        if (null === $this->shmId) {
            throw new \LogicException('Please open shared memory use open() before write.');
        }

        $ret = false;

        try {
            // lock
            if ($this->lock($this->key)) {
                // write data
                $ret = $this->doWrite($data);

                // unlock
                $this->unlock($this->key);
            }
        } catch (\Exception $e) {
            $this->errCode = $e->getCode();
            $this->errMsg = $e->getMessage();
        }

        return $ret;
    }

    /**
     * @param string $data
     * @return bool
     */
    abstract protected function doWrite($data);

    /**
     * @param string $data
     * @return bool
     * @throws \LogicException
     */
    public function prepend($data)
    {
        return $this->write($data . $this->read());
    }

    /**
     * @param string $data
     * @return bool
     * @throws \LogicException
     */
    public function append($data)
    {
        $old = $this->read();

        return $this->write($old . $data);
    }

    /**
     * read data form SHM
     * @param int $size
     * @return string
     * @throws \LogicException
     */
    public function read($size = 0)
    {
        if (null === $this->shmId) {
            throw new \LogicException('Please open shared memory use open() before read.');
        }

        $ret = '';

        try {
            // lock
            if ($this->lock($this->key)) {
                $ret = $this->doRead($size);

                // unlock
                $this->unlock($this->key);
            }
        } catch (\Exception $e) {
            $ret = false;
            $this->errCode = $e->getCode();
            $this->errMsg = $e->getMessage();
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
     * @return array
     */
    public function getError(): array
    {
        return [$this->errCode, $this->errMsg];
    }

//////////////////////////////////////////////////////////////////////
/// getter/setter method
//////////////////////////////////////////////////////////////////////

    /**
     * @return int
     */
    public function getKey(): int
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

    /**
     * @return string
     */
    public function getDriver(): string
    {
        return $this->driver;
    }

    /**
     * @return int
     */
    public function getErrCode(): int
    {
        return $this->errCode;
    }

    /**
     * @param int $errCode
     */
    public function setErrCode(int $errCode)
    {
        $this->errCode = $errCode;
    }

    /**
     * @return string
     */
    public function getErrMsg(): string
    {
        return $this->errMsg;
    }

    /**
     * @param string $errMsg
     */
    public function setErrMsg(string $errMsg)
    {
        $this->errMsg = $errMsg;
    }
}
