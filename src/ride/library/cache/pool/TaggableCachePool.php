<?php

namespace ride\library\cache\pool;

/**
 * Interface for a cache pool with tag support
 */
interface TaggableCachePool extends CachePool {

    /**
     * Flushes this pool by tag
     * @param string|array $tag Tag(s) the cache item must have to be flushed
     * @return null
     */
    public function flushByTag($tag);

}
