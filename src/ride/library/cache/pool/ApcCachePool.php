<?php

namespace ride\library\cache\pool;

use ride\library\cache\exception\CacheException;
use ride\library\cache\CacheItem;

/**
 * APC implementation for a opcode cache
 */
class ApcOpCachePool extends AbstractTaggableCachePool {

    /**
     * Constructs a new XCache facade
     * @param \ride\library\cache\CacheItem $emptyCacheItem Empty cache item to
     * clone for a new cache item
     * @return null
     * @throws \Exception when the xcache functions are not available
     */
    public function __construct(CacheItem $emptyCacheItem = null) {
        if (!function_exists('apc_fetch')) {
            throw new CacheException('Could not create an APC cache pool: APC is not installed or not enabled.');
        }

        parent::__construct($emptyCacheItem);
    }

    /**
     * Increases a value in the variable store
     * @param string $key Key of the variable
     * @param integer $step Value to increase with
     * @return mixed New value of the variable
     */
    public function increase($key, $step = 1) {
        // make sure the variable exists
        apc_add($key, 0);

        return apc_inc($key, $step);
    }

    /**
     * Decreases a value in the variable store
     * @param string $key Key of the variable
     * @param integer $step Value to decrease with
     * @return mixed New value of the variable
     */
    public function decrease($key, $step = 1) {
        // make sure the variable exists
        apc_add($key, 0);

        return apc_dec($key, $step);
    }

    /**
     * Sets an item to this pool
     * @param \ride\library\cache\CacheItem $item
     * @return null
     */
    public function set(CacheItem $item) {
        if (!$item->isValid()) {
            return;
        }

        apc_store($item->getKey(), $item, $item->getTtl());

        parent::set($item);
    }

    /**
     * Gets an item from this pool
     * @param string $key Key of the cached item
     * @return \ride\library\cache\CacheItem Instance of the cached item
     */
    public function get($key) {
        $value = apc_fetch($key, $success);
        if (!$success) {
            $item = $this->create($key);
        } elseif (!$value instanceof CacheItem) {
            $item = $this->create($key);
            $item->setValue($value);
        } else {
            $item = $value;
        }

        return $item;
    }

    /**
     * Flushes this pool
     * @param string $key Provide a key to only remove the cached item of that
     * key
     * @return null
     */
    public function flush($key = null) {
        if ($key !== null) {
            apc_add($key, 0);
            apc_delete($key);
        } else {
            apc_clear_cache();
        }

        parent::flush($key);
    }

}
