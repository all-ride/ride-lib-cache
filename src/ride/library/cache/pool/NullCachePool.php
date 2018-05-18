<?php

namespace ride\library\cache\pool;

use ride\library\cache\CacheItem;

/**
 * Empty implementation to disable cache
 */
class NullCachePool extends AbstractTaggableCachePool {

    /**
     * Sets an item to this pool
     * @param \ride\library\cache\CacheItem $item
     * @return null
     */
    public function set(CacheItem $item) {

    }

    /**
     * Gets an item from this pool
     * @param string $key Key of the cached item
     * @return \ride\library\cache\CacheItem Instance of the cached item
     */
    public function get($key) {
        return $this->create($key);
    }

}
