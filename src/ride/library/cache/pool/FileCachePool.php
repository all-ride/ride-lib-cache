<?php

namespace ride\library\cache\pool;

use ride\library\cache\CacheItem;
use ride\library\system\file\File;

use \Exception;

/**
 * File implementation for the cache pool. All cache items are stored in 1
 * file which is read when the cache is first accessed and written when the
 * cache destructs.
 */
class FileCachePool extends AbstractTaggableCachePool {

    /**
     * File to store the values
     * @var \ride\library\system\file\File
     */
    protected $file;

    /**
     * Values of this pool
     * @var array
     */
    protected $values;

    /**
     * Constructs a new file cache pool
     * @param \ride\library\system\file\File $file File to store the values
     * @param \ride\library\cache\CacheItem $emptyCacheItem Empty cache item to
     * clone for a new cache item
     */
    public function __construct(File $file, CacheItem $emptyCacheItem = null) {
        $this->file = $file;
        $this->values = [];

        parent::__construct($emptyCacheItem);
    }

    /**
     * Writes the cache values to the file
     * @return void
     */
    public function __destruct() {
        parent::__destruct();

        try {
            $this->writeFile();
        } catch (Exception $exception) {

        }
    }

    /**
     * Sets an item to this pool
     * @param \ride\library\cache\CacheItem $item
     * @return void
     */
    public function set(CacheItem $item) {
        if (!$item->isValid()) {
            return;
        }

        if (empty($this->values)) {
            $this->readFile();
        }

        $this->values[$item->getKey()] = $item;

        parent::set($item);
    }

    /**
     * Gets an item from this pool
     * @param string $key Key of the cached item
     * @return \ride\library\cache\CacheItem|null Instance of the cached item if
     * found, null otherwise
     */
    public function get($key) {
        if (empty($this->values)) {
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
     * @return void
     */
    public function flush($key = null) {
        if (empty($this->values)) {
            $this->readFile();
        }

        if ($key !== null) {
            if (isset($this->values[$key])) {
                unset($this->values[$key]);
            }
        } else {
            $this->values = array();
        }

        parent::flush($key);
    }

    /**
     * Reads the cache items from the file
     * @return void
     */
    private function readFile() {
        if (!$this->file->exists()) {
            $this->values = array();

            return;
        }

        $serializedValue = $this->file->read();

        $this->values = unserialize($serializedValue) ?: [];
    }

    /**
     * Writes the cache file with all the cache items
     * @return void
     */
    private function writeFile() {
        if (empty($this->values)) {
            return;
        }

        $parent = $this->file->getParent();
        $parent->create();

        if ($this->values) {
            $output = serialize($this->values);

            $this->file->write($output);
        } elseif ($this->file->exists()) {
            $this->file->delete();
        }
    }

}
