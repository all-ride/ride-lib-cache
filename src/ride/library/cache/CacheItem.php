<?php

namespace ride\library\cache;

/**
 * Interface for a caching object
 */
interface CacheItem {

    /**
     * Sets the key to store the value under
     * @param string $key Key of the cache value
     * @return null
     */
    public function setKey($key);

    /**
     * Gets the key of this item
     * @return string Key of the cache value
     */
    public function getKey();

    /**
     * Sets the cached value
     * @param mixed $value Value to store in the cache
     * @return null
     */
    public function setValue($value);

    /**
     * Gets the cached value
     * @return mixed Value stored in the cache
     */
    public function getValue();

    /**
     * Sets a meta value
     * @param string $key Key of the meta value
     * @param mixed $value Meta value
     * @return null
     */
    public function setMeta($key, $value = null);

    /**
     * Gets a meta value
     * @param string|null $key Key of the meta value
     * @param mixed $default Default value for when the key is not set
     * @return mixed Value for the key if provided, all meta of no arguments
     * are provided
     */
    public function getMeta($key = null, $default = null);

    /**
     * Checks if this cache item is valid to use
     * @return boolean True if valid and useable, false if stale or unexistant
     */
    public function isValid();

}