<?php

namespace ride\library\cache;

use ride\library\cache\exception\CacheException;
/**
 * Generic implementation of a caching object
 */
class GenericCacheItem implements CacheItem {

    /**
     * Name of the creation time meta
     * @var string
     */
    const META_CREATED = 'created';

    /**
     * Name of the time to live meta
     * @var string
     */
    const META_TTL = 'ttl';

    /**
     * Key of this item
     * @var string
     */
    protected $key;

    /**
     * Value to cache
     * @var mixed
     */
    protected $value;

    /**
     * Meta data for this item
     * @var array
     */
    protected $meta;

    /**
     * Constructs a new cached item
     * @return null
     */
    public function __construct() {
        $this->key = null;
        $this->value = null;
        $this->meta = array();

        $this->isValueUnset = true;
    }

    /**
     * Sets the key to store the value under
     * @param string $key Key of the cache value
     * @return null
     */
    public function setKey($key) {
        if (!is_string($key) || $key == '') {
            throw new CacheException('Could not set the key of the cache item: provided key is invalid or empty');
        }

        $this->key = $key;
    }

    /**
     * Gets the key of this item
     * @return string Key of the cache value
     */
    public function getKey() {
        return $this->key;
    }

    /**
     * Sets the cached value
     * @param mixed $value Value to store in the cache
     * @return null
     */
    public function setValue($value) {
        $this->value = $value;

        unset($this->isValueUnset);
    }

    /**
     * Gets the cached value
     * @return mixed Value stored in the cache
     */
    public function getValue() {
        return $this->value;
    }

    /**
     * Sets a meta value
     * @param string $key Key of the meta value
     * @param mixed $value Meta value
     * @return null
     */
    public function setMeta($key, $value = null) {
        if (!is_string($key) || $key == '') {
            throw new CacheException('Could not set meta of the cache item: provided key is invalid or empty');
        }

        if ($value !== null) {
            $this->meta[$key] = $value;

            if ($key == self::META_TTL) {
                $this->meta[self::META_CREATED] = time();
            }
        } elseif (isset($this->meta[$key])) {
            unset($this->meta[$key]);
        }
    }

    /**
     * Gets a meta value
     * @param string|null $key Key of the meta value
     * @param mixed $default Default value for when the key is not set
     * @return mixed Value for the key if provided, all meta of no arguments
     * are provided
     */
    public function getMeta($key = null, $default = null) {
        if ($key === null) {
            return $this->meta;
        } elseif (!is_string($key) || $key == '') {
            throw new CacheException('Could not set meta of the cache item: provided key is invalid or empty');
        } elseif (isset($this->meta[$key])) {
            return $this->meta[$key];
        } else {
            return $default;
        }
    }

    /**
     * Set the time to live value
     * @param integer $ttl Number of seconds to live
     * @return null
     */
    public function setTtl($ttl) {
        $this->setMeta(self::META_TTL, $ttl);
    }

    /**
     * Gets the time to live value
     * @return integer Number of seconds to live
     */
    public function getTtl() {
        return $this->getMeta(self::META_TTL, 0);
    }

    /**
     * Checks if this cache item is valid to use
     * @return boolean True if valid, false otherwise
     */
    public function isValid() {
        if ($this->key === null || isset($this->isValueUnset)) {
            return false;
        }

        $ttl = $this->getTtl();
        if ($ttl !== 0) {
            $created = $this->getMeta(self::META_CREATED, 0);

            if (($created + $ttl) < time()) {
                return false;
            }
        }

        return true;
    }

}