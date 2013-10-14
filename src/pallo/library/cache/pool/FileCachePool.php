<?php

namespace pallo\library\cache\pool;

use pallo\library\cache\CacheItem;
use pallo\library\system\file\File;

use \Exception;

/**
 * File implementation for the cache pool. All cache items are stored in 1
 * file which is read when the cache is first accessed and written when the
 * cache destructs.
 */
class FileCachePool extends AbstractCachePool {

    /**
     * File to store the values
     * @var pallo\library\system\file\File
     */
    protected $file;

    /**
     * Values of this pool
     * @var array
     */
    protected $values;

    /**
     * Constructs a new file cache pool
     * @param pallo\library\system\file\File $file File to store the values
     * @param pallo\library\cache\CacheItem $emptyCacheItem Empty cache item to
     * clone for a new cache item
     * @return null
     */
    public function __construct(File $file, CacheItem $emptyCacheItem = null) {
        parent::__construct($emptyCacheItem);

        $this->file = $file;
        $this->values = null;
    }

    /**
     * Writes the cache values to the file
     * @return null
     */
    public function __destruct() {
        try {
            $this->writeFile();
        } catch (Exception $exception) {

        }
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

        if ($this->values === null) {
            $this->readFile();
        }

        $this->values[$item->getKey()] = $item;
    }

    /**
     * Gets an item from this pool
     * @param string $key Key of the cached item
     * @return pallo\library\cache\CacheItem|null Instance of the cached item if
     * found, null otherwise
     */
    public function get($key) {
        if ($this->values === null) {
            $this->readFile();
        }

        if (!isset($this->values[$key])) {
            return $this->create($key);
        }

        return $this->values[$key];
    }

    /**
     * Flushes this pool
     * @param string $key Provide a key to only remove the cached item of that
     * key
     * @return null
     */
    public function flush($key = null) {
        if ($this->values === null) {
            $this->readFile();
        }

        if ($key !== null) {
            if (isset($this->values[$key])) {
                unset($this->values[$key]);
            }
        } else {
            $this->values = array();
        }
    }

    /**
     * Reads the cache items from the file
     * @return null
     */
    private function readFile() {
        if (!$this->file->exists()) {
            $this->values = array();

            return;
        }

        $serializedValue = $this->file->read();

        $this->values = unserialize($serializedValue);
    }

    /**
     * Writes the cache file with all the cache items
     * @return null
     */
    private function writeFile() {
        if ($this->values === null) {
            return;
        }

        $parent = $this->file->getParent();
        $parent->create();

        $output = serialize($this->values);

        $this->file->write($output);
    }

}