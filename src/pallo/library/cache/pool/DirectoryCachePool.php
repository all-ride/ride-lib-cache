<?php

namespace pallo\library\cache\pool;

use pallo\library\cache\CacheItem;
use pallo\library\system\file\File;

use \Exception;

/**
 * Directory implementation for a cache pool. Each cache item will be stored in
 * a file in the directory of this pool
 */
class DirectoryCachePool extends AbstractCachePool {

    /**
     * Directory of the cache
     * @var pallo\library\system\file\File
     */
    protected $directory;

    /**
     * Constructs a new directory cache pool implementation
     * @param pallo\library\system\file\File $directory Directory for the pool
     * @param pallo\library\cache\CacheItem $emptyCacheItem Empty cache item to
     * clone for a new cache item
     * @return null
     */
    public function __construct(File $directory, CacheItem $emptyCacheItem = null) {
        parent::__construct($emptyCacheItem);

        $this->directory = $directory;
        $this->directory->create();
    }

    /**
     * Sets an item to this pool
     * @param pallo\library\cache\CacheItem $item
     * @return null
     */
    public function set(CacheItem $item) {
        if (!$item->isValid()) {
            return;
        }

        $serializedValue = serialize($item);

        $cacheFile = $this->directory->getChild($item->getKey());
        $cacheFile->write($serializedValue);
    }

    /**
     * Gets an item from this pool
     * @param string $key Key of the cached item
     * @return pallo\library\cache\CacheItem Instance of the cached item
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
    }

}