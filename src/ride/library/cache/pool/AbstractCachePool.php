<?php

namespace ride\library\cache\pool;

use ride\library\cache\CacheItem;
use ride\library\cache\GenericCacheItem;

/**
 * Abstract implementation for a cache pool.
 */
abstract class AbstractCachePool implements CachePool {

    /**
     * Empty cache item to clone for a new cache item
     * @var ride\library\cache\CacheItem
     */
    protected $emptyCacheItem;

    /**
     * Constructs a new abstract cache pool
     * @param ride\library\cache\CacheItem $emptyCacheItem Empty cache item to
     * clone for a new cache item
     * @return null
     */
    public function __construct(CacheItem $emptyCacheItem = null) {
        if (!$emptyCacheItem) {
            $emptyCacheItem = new GenericCacheItem();
        }

        $this->setEmptyCacheItem($emptyCacheItem);
    }

    /**
     * Sets the empty cache item to clone for a new cache item
     * @param ride\library\cache\CacheItem $emptyCacheItem
     * @return null
     */
    public function setEmptyCacheItem(CacheItem $emptyCacheItem) {
        $this->emptyCacheItem = $emptyCacheItem;
    }

    /**
     * Creates a item for this pool
     * @param string $key Key of the cached item
     * @return ride\library\cache\CacheItem New instance of a cache item for
     * the provided key
     */
    public function create($key) {
        $cacheItem = clone $this->emptyCacheItem;
        $cacheItem->setKey($key);

        return $cacheItem;
    }

}