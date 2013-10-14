# Pallo: Cache Library

Cache library of the PHP Pallo framework.

## CacheItem

A _CacheItem_ is the container of your cached value.
The implementation contains the logic to see if the cached item is valid and not stale.

## CachePool

A _CachePool_ is the backend or storage of your cache items.

Available implementations:

* pallo\library\cache\pool\DirectoryCachePool: Cache directory with one file per cached item
* pallo\library\cache\pool\FileCachePool: one file for the complete pool
* pallo\library\cache\pool\op\ApcOpCachePool: APC implementation
* pallo\library\cache\pool\op\MemcacheOpCachePool: Memcache implementation
* pallo\library\cache\pool\op\XCacheOpCachePool: XCache implementation

## Code Sample

Check the following code sample to see how the cache should be used:

    <?php

    use pallo\library\cache\pool\DirectoryCachePool;    
    use pallo\library\system\System;
    
    $system = new System();
    $cacheDirectory = $system->getFileSystem()->getFile('/path/to/cache');
    
    $cachePool = new DirectoryCachePool($cacheDirectory);
    
    $cacheItem = $cachePool->get('item.cache.key');
    if (!$cacheItem->isValid()) {
        // some value generation logic

        $cacheItem->setValue($value);
        $cacheItem->setTtl(60); // in seconds, optional
        
        $cachePool->set($cacheItem);
    } else {
        $value = $cacheItem->getValue();
    }

This code uses the item as returned from the pool to set the cached value.

When you warm up your cache in another place, you can easily create your cache item through the pool:

    <?php

    use pallo\library\cache\pool\DirectoryCachePool;    
    use pallo\library\system\System;
    
    $system = new System();
    $cacheDirectory = $system->getFileSystem()->getFile('/path/to/cache');
    
    $cachePool = new DirectoryCachePool($cacheDirectory);
    
    $cacheItem = $cachePool->create('item.cache.key');
    $cacheItem->setValue('some cache value');
    
    $cachePool->set($cacheItem);