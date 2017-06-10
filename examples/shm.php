<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017/6/3
 * Time: 下午8:01
 */

require dirname(__DIR__) . '/../../autoload.php';

use inhere\shm\SharedMemory;
use inhere\shm\ShmMap;

$shm = SharedMemory::make([
    'key' => 1,
    'size' => 1024
]);
$shm->open();

printf("Create SHM, driver: %s,key: %s \n", $shm->getDriver(), $shm->getKey());
//var_dump($shm);

$ret = $shm->write('data string');

printf("Write data %s\n", $ret ? 'success' : 'fail');

// print_r($shm->getError());

$data = $shm->read();

echo "Read data: $data\n";

$shm->clear();
$data = $shm->read();

echo "Clear then,Read data: $data\n";

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

$shmAry->clear();

var_dump($shmAry->getMap());
//var_dump($shmAry->getShm());

//$shmAry->close();
//var_dump($shmAry->getShm());
