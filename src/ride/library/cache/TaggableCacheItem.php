<?php

namespace ride\library\cache;

/**
 * Interface for a caching object
 */
interface TaggableCacheItem extends CacheItem {

    /**
     * Adds a tag to this cache item
     * @param string $tag Name of the tag
     * @return null
     */
    public function addTag($tag);

    /**
     * Removes a tag from this cache item
     * @param string $tag Name of the tag
     * @return null
     */
    public function removeTag($tag);

    /**
     * Gets the tags of this cache item
     * @return array
     */
    public function getTags();

}
