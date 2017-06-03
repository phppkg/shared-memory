<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017/6/3
 * Time: 下午8:01
 */

use inhere\shm\ShmFactory;
use inhere\shm\ShmMap;

$shm = ShmFactory::make([
    'key' => 1,
    'size' => 512
]);

$shm->write('data string');
$ret = $shm->read();

var_dump($ret);

$shmAry = new ShmMap([
    'key' => 2,
    'size' => 512
]);

$shmAry['one'] = 'val1';
$shmAry['two'] = 'val2';
$shmAry->set('three', 'val3');

var_dump($shmAry['three'], $shmAry->getMap());

unset($shmAry['one']);

var_dump($shmAry->getMap());
