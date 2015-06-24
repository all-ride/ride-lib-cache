<?php

namespace ride\library\cache\pool;

use ride\library\cache\CacheItem;

use \Memcache;

/**
 * Memcache implementation for a opcode cache
 */
class MemcacheOpCachePool extends AbstractTaggableCachePool {

    /**
     * Instance of the memcache connection
     * @var \Memcache
     */
    protected $memcache;

    /**
     * Constructs a new Memcache facade
     * @param \Memcache $memcache Instance of a Memcache client
     * @param \ride\library\cache\CacheItem $emptyCacheItem Empty cache item to
     * clone for a new cache item
     * @return null
     * @throws \Exception when the xcache functions are not available
     */
    public function __construct(Memcache $memcache, CacheItem $emptyCacheItem = null) {
        $this->memcache = $memcache;

        parent::__construct($emptyCacheItem);
    }

    /**
     * Closes the Memcache connection
     * @return null
     */
    public function __destruct() {
        parent::__destruct();

        $this->memcache->close();
    }

    /**
     * Increases a value in the variable store
     * @param string $key Key of the variable
     * @param integer $step Value to increase with
     * @return mixed New value of the variable
     */
    public function increase($key, $step = 1) {
        return $this->memcache->increment($key, $step);
    }

    /**
     * Decreases a value in the variable store
     * @param string $key Key of the variable
     * @param integer $step Value to decrease with
     * @return mixed New value of the variable
     */
    public function decrease($key, $step = 1) {
        return $this->memcache->decrement($key, $step);
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

        $this->memcache->set($item->getKey(), $item, 0, $item->getTtl());

        parent::set($item);
    }

    /**
     * Gets an item from this pool
     * @param string $key Key of the cached item
     * @return \ride\library\cache\CacheItem Instance of the cached item
     */
    public function get($key) {
        $value = $this->memcache->get($key);
        if ($value === false) {
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
            $this->memcache->delete($key);
        } else {
            $this->memcache->flush();
        }

        parent::flush($key);
    }

}
