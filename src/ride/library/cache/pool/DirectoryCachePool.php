<?php

namespace ride\library\cache\pool;

use ride\library\cache\CacheItem;
use ride\library\system\file\File;

use \Exception;

/**
 * Directory implementation of a cache pool. Each cache item will be stored in
 * a file in the directory of this pool
 */
class DirectoryCachePool extends AbstractTaggableCachePool {

    /**
     * Directory of the cache
     * @var \ride\library\system\file\File
     */
    protected $directory;

    /**
     * Constructs a new directory cache pool implementation
     * @param \ride\library\system\file\File $directory Directory for the pool
     * @param \ride\library\cache\CacheItem $emptyCacheItem Empty cache item to
     * clone for a new cache item
     * @return null
     */
    public function __construct(File $directory, CacheItem $emptyCacheItem = null) {
        // make sure directory exists
        $this->directory = $directory;
        $this->directory->create();

        // handle parent logic
        parent::__construct($emptyCacheItem);
    }

    /**
     * Sets an item to this pool
     * @param \ride\library\cache\CacheItem $item
     * @return null
     */
    public function set(CacheItem $item) {
        if (!$item->isValid()) {
            // not a valid item, don't store
            return;
        }

        $serializedValue = serialize($item);

        // write to file
        $cacheFile = $this->directory->getChild($item->getKey());
        $cacheFile->write($serializedValue);

        // handle parent logic
        parent::set($item);
    }

    /**
     * Gets an item from this pool
     * @param string $key Key of the cached item
     * @return \ride\library\cache\CacheItem Instance of the cached item
     */
    public function get($key) {
        $cacheFile = $this->directory->getChild($key);

        $item = null;

        if ($cacheFile->exists()) {
            $serializedValue = $cacheFile->read();

            try {
                $item = unserialize($serializedValue);
            } catch (Exception $exception) {

            }
        }

        if (!$item) {
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
        if ($key === null) {
            if (!$this->directory->exists()) {
                return;
            }

            $cacheFiles = $this->directory->read();
            foreach ($cacheFiles as $cacheFile) {
                $cacheFile->delete();
            }
        } else {
            $cacheFile = $this->directory->getChild($key);
            if ($cacheFile->exists()) {
                $cacheFile->delete();
            }
        }

        // handle parent logic
        parent::flush($key);
    }

}
