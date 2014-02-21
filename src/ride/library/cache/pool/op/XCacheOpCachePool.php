<?php

namespace ride\library\cache\pool\op;

use ride\library\cache\exception\CacheException;
use ride\library\cache\pool\AbstractCachePool;
use ride\library\cache\CacheItem;

/**
 * XCache implementation for a opcode cache
 */
class XCacheOpCachePool extends AbstractCachePool implements OpCachePool {

    /**
     * Constructs a new XCache pool
     * @param ride\library\cache\CacheItem $emptyCacheItem Empty cache item to
     * clone for a new cache item
     * @return null
     * @throws \Exception when the xcache functions are not available
     */
    public function __construct(CacheItem $emptyCacheItem = null) {
        parent::__construct($emptyCacheItem);

        if (!function_exists('xcache_get')) {
            throw new CacheException('Could not create the XCache implementation. XCache is not installed or not enabled.');
        }
    }

    /**
     * Increases a value in the variable store
     * @param string $key Key of the variable
     * @param integer $step Value to increase with
     * @return mixed New value of the variable
     */
    public function increase($key, $step = 1) {
        return xcache_inc($key, $step);
    }

    /**
     * Decreases a value in the variable store
     * @param string $key Key of the variable
     * @param integer $step Value to decrease with
     * @return mixed New value of the variable
     */
    public function decrease($key, $step = 1) {
        return xcache_dec($key, $step);
    }

    /**
     * Sets an item to this pool
     * @param ride\library\cache\CacheItem $item
     * @return null
     */
    public function set(CacheItem $item) {
        if (!$item->isValid()) {
            return;
        }

        $value = serialize($item);

        xcache_set($item->getKey(), $value, $item->getTtl());
    }

    /**
     * Gets an item from this pool
     * @param string $key Key of the cached item
     * @return ride\library\cache\CacheItem Instance of the cached item
     */
    public function get($key) {
        if (xcache_isset($key)) {
            $value = xcache_get($key);

            try {
                $item = unserialize($value);
            } catch (Exception $e) {
                $item = $this->create($key);
                $item->setValue($value);
            }
        } else {
            $item = $this->create($key);
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
            xcache_unset($key);
        } else {
            xcache_clear();
        }
    }

}