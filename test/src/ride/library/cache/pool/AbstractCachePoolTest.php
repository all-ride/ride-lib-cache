<?php

namespace ride\library\cache\pool;

use ride\library\cache\GenericCacheItem;

use \PHPUnit_Framework_TestCase;

abstract class AbstractCachePoolTest extends PHPUnit_Framework_TestCase {

    protected $cache;

    public function testCreate() {
        $key = 'cache.key';
        $item = $this->cache->create($key);

        $this->assertNotNull($item);
        $this->assertTrue($item instanceof GenericCacheItem);
        $this->assertEquals($key, $item->getKey());
    }

    /**
     * @expectedException ride\library\cache\exception\CacheException
     */
    public function testIncrease() {
        $this->cache->increase('cache.key');
    }

    /**
     * @expectedException ride\library\cache\exception\CacheException
     */
    public function testDecrease() {
        $this->cache->increase('cache.key');
    }

}
