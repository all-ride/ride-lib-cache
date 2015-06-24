<?php

namespace ride\library\cache\pool;

use ride\library\cache\CacheItem;

/**
 * Interface for a cache pool. This is the backend or storage of a cache.
 */
interface CachePool {

    /**
     * Creates a item for this pool
     * @param string $key Key of the cached item
     * @return \ride\library\cache\CacheItem New instance of a cache item for
     * the provided key
     */
    public function create($key);

    /**
     * Increases a value in the variable store
     * @param string $key Key of the variable
     * @param integer $step Value to increase with
     * @return mixed New value of the variable
     */
    public function increase($key, $step = 1);

    /**
     * Decreases a value in the variable store
     * @param string $key Key of the variable
     * @param integer $step Value to decrease with
     * @return mixed New value of the variable
     */
    public function decrease($key, $step = 1);

    /**
     * Sets an item to this pool
     * @param \ride\library\cache\CacheItem $item
     * @return null
     */
    public function set(CacheItem $item);

    /**
     * Gets an item from this pool
     * @param string $key Key of the cached item
     * @return \ride\library\cache\CacheItem Instance of the cached item
     */
    public function get($key);

    /**
     * Flushes this pool or a single key from it
     * @param string $key Provide a key to remove a single cached item
     * @return null
     */
    public function flush($key = null);

}
