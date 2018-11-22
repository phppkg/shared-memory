<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017/6/1
 * Time: 下午9:43
 */

namespace PhpComp\Shm;

/**
 * Class ShmOp
 *  - require enable --enable-shmop
 *  - support *nix and windows
 *
 * @package PhpComp\Shm
 */
class ShmOp extends BaseShm
{
    /**
     * {@inheritDoc}
     * @throws \RuntimeException
     */
    protected function init()
    {
        if (!self::isSupported()) {
            throw new \RuntimeException(
                'To use shmop you will need to compile PHP with the --enable-shmop parameter in your configure line.',
                -500
            );
        }

        $this->driver = SharedMemory::DRIVER_OP;

        parent::init();
    }

    /**
     * {@inheritDoc}
     */
    protected function doOpen()
    {
        /*
         * resource shmop_open ( int $key , string $flags , int $mode , int $size )
         * $flags:
         *      a 访问只读内存段
         *      c 创建一个新内存段，或者如果该内存段已存在，尝试打开它进行读写
         *      w 可读写的内存段
         *      n 创建一个新内存段，如果该内存段已存在，则会失败
         * $mode: 八进制格式  0655
         * $size: 开辟的数据大小 字节
         */
        return shmop_open($this->key, 'c', 0644, $this->config['size']);
    }

    /**
     * write data to SHM
     * @param string $data
     * @return bool
     */
    public function doWrite($data): bool
    {
        return shmop_write($this->shmId, $data, 0) === \strlen($data);
    }

    /**
     * read data form SHM
     * @param int $size
     * @return string
     */
    public function doRead($size = 0): string
    {
        return shmop_read($this->shmId, 0, (int)$size ?: $this->size());
    }

    /**
     * @return int
     */
    public function size(): int
    {
        return shmop_size($this->shmId);
    }

    /**
     * close
     * @throws \RuntimeException
     */
    public function close(): bool
    {
        // Now lets delete the block and close the shared memory segment
        if (!$this->clear()) {
            throw new \RuntimeException("Couldn't mark shared memory block for deletion.", __LINE__);
        }

        shmop_close($this->shmId);
        $this->shmId = null;

        return true;
    }

    /**
     * clear
     */
    public function clear(): bool
    {
        return shmop_delete($this->shmId);
        //return $this->write(0);
    }

    /**
     * @return bool
     */
    public static function isSupported(): bool
    {
        return \function_exists('shmop_open');
    }
}
