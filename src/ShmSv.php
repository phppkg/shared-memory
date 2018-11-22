<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017/6/1
 * Time: 下午9:44
 */

namespace PhpComp\Shm;

/**
 * Class ShmSv
 *  - powered by system v shm. require enable  --enable-sysvshm
 *  - only support *nix system
 *
 * @package PhpComp\Shm
 */
class ShmSv extends BaseShm
{
    /**
     * The variable key
     * @var int
     */
    private $varKey;

    /**
     * {@inheritDoc}
     * @throws \RuntimeException
     * @throws \LogicException
     */
    protected function init()
    {
        if (!self::isSupported()) {
            throw new \RuntimeException(
                'To use sysvshm you will need to compile PHP with the --enable-sysvshm parameter in your configure line.',
                -500
            );
        }

        $this->driver = SharedMemory::DRIVER_SV;

        parent::init();

        if (!isset($this->config['varKey']) || ($this->config['varKey'] <= 0)) {
            throw new \LogicException("Must define the variable key: 'varKey'. (int and gt 0)");
        }

        $this->varKey = $this->config['varKey'] = (int)$this->config['varKey'];
    }

    /**
     * {@inheritDoc}
     */
    protected function doOpen()
    {
        return shm_attach($this->key, $this->config['size'], 0644);
    }

    /**
     * {@inheritDoc}
     */
    public function doWrite($data): bool
    {
        return shm_put_var($this->shmId, $this->varKey, $data);
    }

    /**
     * {@inheritDoc}
     */
    public function doRead($size = 0): string
    {
        return shm_get_var($this->shmId, $this->varKey);
    }

    /**
     * @return bool
     */
    public function clear(): bool
    {
        return shm_remove_var($this->shmId, $this->varKey);
    }

    /**
     * {@inheritDoc}
     */
    public function close(): bool
    {
        $this->clear();

        $ret = shm_detach($this->shmId);
        $this->shmId = null;

        return $ret;
    }

    /**
     * remove SHM
     * @return bool
     */
    public function remove(): bool
    {
        return shm_remove($this->shmId);
    }

    /**
     * @return bool
     */
    public static function isSupported(): bool
    {
        return \function_exists('shm_attach');
    }
}
