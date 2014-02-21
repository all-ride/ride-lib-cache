<?php

namespace ride\library\cache\pool\op;

use ride\library\cache\exception\CacheException;
use ride\library\cache\pool\AbstractCachePool;
use ride\library\cache\CacheItem;

/**
 * Memcache implementation for a opcode cache
 */
class MemcacheOpCachePool extends AbstractCachePool implements OpCachePool {

    /**
     * Instance of the memcache connection
     * @var resource
     */
    protected $memcache;

    /**
     * Constructs a new Memcache facade
     * @param ride\library\cache\CacheItem $emptyCacheItem Empty cache item to
     * clone for a new cache item
     * @return null
     * @throws \Exception when the xcache functions are not available
     */
    public function __construct($host, $port, $timeout = 1, CacheItem $emptyCacheItem = null) {
        parent::__construct($emptyCacheItem);

        if (!function_exists('memcache_connect')) {
            throw new CacheException('Could not create the Memcache implementation. Memcache is not installed or not enabled.');
        }

        $this->memcache = memcache_connect($host, $port, $timeout);
    }

    /**
     * Destructs the Memcache facade
     * @return null
     */
    public function __destruct() {
        if ($this->memcache) {
            memcache_close($this->memcache);
        }
    }

    /**
     * Increases a value in the variable store
     * @param string $key Key of the variable
     * @param integer $step Value to increase with
     * @return mixed New value of the variable
     */
    public function increase($key, $step = 1) {
        return memcache_increment($this->memcache, $key, $step);
    }

    /**
     * Decreases a value in the variable store
     * @param string $key Key of the variable
     * @param integer $step Value to decrease with
     * @return mixed New value of the variable
     */
    public function decrease($key, $step = 1) {
        return memcache_decrement($this->memcache, $key, $step);
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

        memcache_set($this->memcache, $item->getKey(), $item, 0, $item->getTtl());
    }

    /**
     * Gets an item from this pool
     * @param string $key Key of the cached item
     * @return ride\library\cache\CacheItem Instance of the cached item
     */
    public function get($key) {
        $value = memcache_get($this->memcache, $key);
        if (!$value) {
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
            memcache_delete($this->memcache, $key);
        } else {
            memcache_flush($this->memcache);
        }
    }

}