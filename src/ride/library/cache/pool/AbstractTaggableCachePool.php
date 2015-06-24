<?php

namespace ride\library\cache\pool;

use ride\library\cache\CacheItem;
use ride\library\cache\TaggableCacheItem;

/**
 * Abstract implementation for a cache pool.
 */
abstract class AbstractTaggableCachePool extends AbstractCachePool implements TaggableCachePool {

    /**
     * Cache key for the tag index
     * @var string
     */
    const KEY_TAG_INDEX = '__tagIndex';

    /**
     * Index of the tags with the item keys
     * @var array
     */
    private $tagIndex;

    /**
     * Flag to see if the tag index has been modified
     * @var boolean
     */
    private $isTagIndexModified;

    /**
     * Constructs a new abstract cache pool
     * @param \ride\library\cache\CacheItem $emptyCacheItem Empty cache item to
     * clone for a new cache item
     * @return null
     */
    public function __construct(CacheItem $emptyCacheItem = null) {
        parent::__construct($emptyCacheItem);

        $this->readTagIndex();
    }

    /**
     * Stores the modified tag index
     * @return null
     */
    public function __destruct() {
        $this->writeTagIndex();
    }

    /**
     * Handles the tags when setting an item to this pool
     * @param \ride\library\cache\CacheItem $item
     * @return null
     */
    public function set(CacheItem $item) {
        if (!$item instanceof TaggableCacheItem) {
            return;
        }

        $tags = $item->getTags();
        if (!$tags) {
            return;
        }

        $key = $item->getKey();

        foreach ($tags as $tag) {
            $this->tagIndex[$tag][$key] = true;
        }

        $this->isTagIndexModified = true;
    }

    /**
     * Handles the tags when flushing this pool or a single key from it
     * @param string $key Provide a key to remove a single cached item
     * @return null
     */
    public function flush($key = null) {
        if ($key === null) {
            if ($this->tagIndex) {
                $this->isTagIndexModified = true;
            }

            $this->tagIndex = array();

            return;
        }

        foreach ($this->tagIndex as $tag => $tagKeys) {
            if (isset($tagKeys[$key])) {
                unset($this->tagIndex[$tag][$key]);

                $this->isTagIndexModified = true;
            }
        }
    }

    /**
     * Flushes all items with the provided tag
     * @param string|array $tag Tag(s) the cache item must have to be flushed
     * @return null
     */
    public function flushByTag($tag) {
        $keys = $this->getKeysByTag($tag);
        foreach ($keys as $key => $null) {
            $this->flush($key);
        }
    }

    /**
     * Gets all the cache keys which are tagged with the provided tag
     * @param string|array $tag Tag(s) the cache item must have
     * @return array Array with the cache keys as array key
     */
    private function getKeysByTag($tags) {
        // extract first and remaining tags from input
        if (!is_array($tags)) {
            $tag = $tags;
            $tags = array();
        } else {
            $tag = array_shift($tags);
        }

        // check first tag
        if (!isset($this->tagIndex[$tag])) {
            return array();
        }

        $keys = $this->tagIndex[$tag];

        // check remaining tags
        foreach ($tags as $tag) {
            if (!isset($this->tagIndex[$tag])) {
                return array();
            }

            foreach ($keys as $key => $null) {
                if (!isset($this->tagIndex[$tag][$key])) {
                    unset($keys[$key]);
                }
            }
        }

        return $keys;
    }

    /**
     * Writes the tag index
     * @return null
     */
    protected function readTagIndex() {
        $cacheItem = $this->get(self::KEY_TAG_INDEX);
        if ($cacheItem->isValid()) {
            $tagIndex = $cacheItem->getValue();
        } else {
            $tagIndex = array();
        }

        $this->setTagIndex($tagIndex);
    }

    /**
     * Writes the tag index
     * @return null
     */
    protected function writeTagIndex() {
        if (!$this->isTagIndexModified()) {
            return;
        }

        $cacheItem = $this->create(self::KEY_TAG_INDEX);
        $cacheItem->setValue($this->getTagIndex());

        $this->set($cacheItem);

        $this->isTagIndexModified = false;
    }

    /**
     * Sets the tag index
     * @param array $tagIndex
     * @return array Array with the tag as key and a key indexed array as value
     * @return null
     */
    protected function setTagIndex(array $tagIndex) {
        $this->tagIndex = $tagIndex;

        $this->isTagIndexModified = false;
    }

    /**
     * Gets the tag index
     * @return array Array with the tag as key and a key indexed array as value
     */
    protected function getTagIndex() {
        return $this->tagIndex;
    }

    /**
     * Get whether the tag index is modified
     * @return boolean
     */
    protected function isTagIndexModified() {
        return $this->isTagIndexModified;
    }

}
