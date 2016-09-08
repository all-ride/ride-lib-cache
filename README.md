# Ride: Cache Library

Cache library of the PHP Ride framework.

## What's In This Library

### CacheItem

A _CacheItem_ is the container of your cached value.
The implementation contains the logic to see if the cached item is valid and not stale.

An extension to tag your cache items is available through the _TaggableCacheItem_ interface. 

The _GenericCacheItem_ class implements both interfaces.

### CachePool

A _CachePool_ is the backend or storage of your cache items.
The implementation decides how to store your cache items.
Each implementation has it's advantages and disadvantages.
Your choice should depend on the context of cache usage and server environment.

Available implementations (all implement taggable cache items):

* ride\library\cache\pool\ApcCachePool: APC implementation
* ride\library\cache\pool\DirectoryCachePool: Cache directory with one file per cached item
* ride\library\cache\pool\FileCachePool: One file for the complete pool
* ride\library\cache\pool\MemcacheCachePool: Memcache implementation
* ride\library\cache\pool\XCacheCachePool: XCache implementation

### CacheControl

A _CacheControl_ provides an interface to expose the management of your caches to the UI.

## Code Sample

Check the following code sample to see how the cache should be used:

```php
<?php

use ride\library\cache\pool\DirectoryCachePool;
use ride\library\system\System;

function foo(System $system) {
    // cache initialization
    $cacheDirectory = $system->getFileSystem()->getFile('/path/to/cache');
    $cachePool = new DirectoryCachePool($cacheDirectory);

    // cache usage
    $cacheItem = $cachePool->get('item.cache.key');
    if (!$cacheItem->isValid()) {
        // some value generation logic
        // ...

        // store value to the cache
        $cacheItem->setValue($value); // required
        $cacheItem->setTtl(60); // in seconds, optional
        $cacheItem->setTag('section'); // you can tag an item, optional

        $cachePool->set($cacheItem);
    } else {
        // retrieve value from cache
        $value = $cacheItem->getValue();
    }

    // cache flush
    $cachePool->flush(); // flush complete cache
    $cachePool->flush('item.cache.key'); // remove an item
    $cachePool->flushByTag('section'); // remove all items tagged with section
}
```

This code uses the item as returned from the pool to set the cached value.

When you warm up your cache in another place, you can easily create your cache item through the pool:

```php
<?php

use ride\library\cache\pool\CachePool;

function bar(CachePool $cachePool) {
    $cacheItem = $cachePool->create('item.cache.key');
    $cacheItem->setValue('some cache value');

    $cachePool->set($cacheItem);
}
```

## Installation

You can use [Composer](http://getcomposer.org) to install this library.

```
composer require ride/lib-cache
```
