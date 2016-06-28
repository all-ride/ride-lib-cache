<?php

namespace ride\library\cache\pool;

use ride\library\system\System;

class DirectoryCachePoolTest extends AbstractCachePoolTest {

    private $directory;

    public function setUp() {
        $system = new System();
        $this->directory = $system->getFileSystem()->getFile(__DIR__ . '/cache.directory');
        $this->cache = new DirectoryCachePool($this->directory);
    }

    public function tearDown() {
        $this->cache = null;

        if ($this->directory->exists()) {
            $this->directory->delete();
        }
    }

    public function testSetAndGet() {
        $key = 'item.key';
        $value = 'My cached value';
        $file = $this->directory->getChild($key);

        // empty cache
        $this->assertFalse($file->exists());

        // set a value to it
        $cacheItem = $this->cache->create($key);
        $cacheItem->setValue($value);

        $this->cache->set($cacheItem);

        $this->assertTrue($file->exists());

        // read value from it
        $cacheItem = $this->cache->get($key);
        $this->assertTrue($cacheItem->isValid());
        $this->assertEquals($value, $cacheItem->getValue());
    }

    public function testFlush() {
        $values = array(
            'key1' => 'value1',
            'key2' => 'value2',
            'key3' => 'value3',
            'key4' => 'value4',
        );

        // set a value to it
        foreach ($values as $key => $value) {
            $cacheItem = $this->cache->create($key);
            $cacheItem->setValue($value);

            $this->cache->set($cacheItem);
        }

        // flush single value from it
        reset($values);
        $flushKey = current($values);

        $this->cache->flush($flushKey);

        // test flushed value
        foreach ($values as $key => $value) {
            if ($key == $flushKey) {
                $this->assertFalse($this->cache->get($key)->isValid(), $key);
                $this->assertFalse($this->directory->getChild($key)->exists(), $key);
            } else {
                $this->assertTrue($this->cache->get($key)->isValid(), $key);
                $this->assertTrue($this->directory->getChild($key)->exists(), $key);
            }
        }

        // flush full cache
        $this->cache->flush();

        foreach ($values as $key => $value) {
            $this->assertFalse($this->cache->get($key)->isValid(), $key);
            $this->assertFalse($this->directory->getChild($key)->exists(), $key);
        }

        $this->assertEquals(array(), $this->directory->read());
    }

    public function testFlushByTag() {
        $tag = 'tag1';
        $expectedKey = 'key3';

        $values = array(
            'key1' => array(
                'value' => 'value1',
                'tags' => array('tag1', 'tag2'),
            ),
            'key2' => array(
                'value' => 'value2',
                'tags' => array('tag1'),
            ),
            'key3' => array(
                'value' => 'value3',
                'tags' => array('tag2'),
            ),
        );

        // set a value to it
        foreach ($values as $key => $value) {
            $cacheItem = $this->cache->create($key);
            $cacheItem->setValue($value['value']);
            foreach ($value['tags'] as $tag) {
                $cacheItem->addTag($tag);
            }

            $this->cache->set($cacheItem);
        }

        $this->cache->flushByTag('tag1');

        // test flushed value
        foreach ($values as $key => $value) {
            if ($key == $expectedKey) {
                $this->assertTrue($this->cache->get($key)->isValid(), $key);
            } else {
                $this->assertFalse($this->cache->get($key)->isValid(), $key);
            }
        }
    }

}
