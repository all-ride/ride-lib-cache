<?php

namespace pallo\library\cache\pool;

use pallo\library\cache\CacheItem;

/**
 * Interface for a cache pool. This is the storage or backend of a cache.
 */
interface CachePool {

    /**
     * Creates a item for this pool
     * @param string $key Key of the cached item
     * @return pallo\library\cache\CacheItem New instance of a cache item for
     * the provided key
     */
    public function create($key);

    /**
     * Sets an item to this pool
     * @param pallo\library\cache\CacheItem $item
     * @return null
     */
    public function set(CacheItem $item);

    /**
     * Gets an item from this pool
     * @param string $key Key of the cached item
     * @return pallo\library\cache\CacheItem Instance of the cached item
     */
    public function get($key);

    /**
     * Flushes this pool or a single key from it
     * @param string $key Provide a key to remove a single cached item
     * @return null
     */
    public function flush($key = null);

}