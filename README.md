# php 共享内存

php 共享内存操作的实现。基于 

- sysvshm扩展：实现system v方式的共享内存 `linux/mac`
- shmop扩展：共享内存操作扩展 `linux/mac/windows`

功能：

- 实现了共享内存的 `写入` `读取` `删除` `释放` 基本操作
- 扩展的类 `ShmMap` 实现了基于共享内存的数组结构(数组方式操作、已实现了迭代器接口)。

## 安装

- composer

```json
{
    "require": {
        "inhere/shm": "dev-master"
    }
}
```

- 直接拉取

```bash
git clone https://git.oschina.net/inhere/php-shared-memory.git // git@osc
git clone https://github.com/inhere/php-shared-memory.git // github
```

## 使用

```php

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

unset($shmAry['two']);

var_dump($shmAry->getMap());

```

## License

MIT
