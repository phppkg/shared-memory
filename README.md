# php 共享内存

[![License](https://img.shields.io/packagist/l/php-comp/shm.svg?style=flat-square)](LICENSE)
[![Php Version](https://img.shields.io/badge/php-%3E=7.0-brightgreen.svg?maxAge=2592000)](https://packagist.org/packages/php-comp/shm)
[![Latest Stable Version](http://img.shields.io/packagist/v/php-comp/shm.svg)](https://packagist.org/packages/php-comp/shm)

php 共享内存操作的实现。基于 

- sysvshm扩展：实现system v方式的共享内存 `linux/mac`
- shmop扩展：共享内存操作扩展 `linux/mac/windows`

功能：

- 实现了共享内存的 `写入` `读取` `删除` `释放` 基本操作
- 扩展的类 `ShmMap` 实现了基于共享内存的数组结构(数组方式操作、pop/push、迭代器,读/取都会自动加锁)。

## 安装

- composer

```json
{
    "require": {
        "php-comp/shm": "dev-master"
    }
}
```

- 直接拉取

```bash
git clone https://github.com/php-comp/shared-memory.git // github
```

## 使用

```php

use PhpComp\Shm\ShmFactory;
use PhpComp\Shm\ShmMap;

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

unset($shmAry['two']);

var_dump($shmAry->getMap());
```

## License

[MIT](LICENSE)
