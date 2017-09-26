<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017/6/3
 * Time: 下午8:01
 */

require dirname(__DIR__) . '/../../autoload.php';

use Inhere\Shm\SharedMemory;
use Inhere\Shm\ShmMap;

$shm = SharedMemory::make([
    'key' => 1,
    'size' => 1024
]);
$shm->open();

printf("Create SHM, driver: %s,key: %s \n", $shm->getDriver(), $shm->getKey());
//var_dump($shm);

$raw = 'first';
$ret = $shm->write($raw);

printf("#1 Write data [$raw] %s\n", $ret ? 'success' : 'fail');

// print_r($shm->getError());

$data = $shm->read();

echo "#1 Read data: $data\n";

$raw = 'second';
$shm->write($raw);
printf("#2 Write data [$raw] %s\n", $ret ? 'success' : 'fail');

$data = $shm->read();

echo "#2 Read data: $data\n";

$shm->clear();
echo "Clear data\n";

$data = $shm->read();

echo "Now,Read data: $data\n";
die;
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

//$shmAry->clear();

var_dump($shmAry->getMap());
//var_dump($shmAry->getShm());

$shmAry->close();
//var_dump($shmAry->getShm());
