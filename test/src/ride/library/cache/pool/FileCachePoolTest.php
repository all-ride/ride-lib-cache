<?php

namespace ride\library\cache\pool;

use ride\library\system\System;

use \PHPUnit_Framework_TestCase;

class FileCachePoolTest extends PHPUnit_Framework_TestCase {

    private $file;

    private $cache;

    public function setUp() {
        $system = new System();
        $this->file = $system->getFileSystem()->getFile(__DIR__ . '/cache.file');
        $this->cache = new FileCachePool($this->file);
    }

    public function tearDown() {
        $this->cache = null;

        if ($this->file->exists()) {
            $this->file->delete();
        }
    }

    public function testSetAndGet() {
        $key = 'item.key';
        $value = 'My cached value';

        // empty cache
        $this->assertFalse($this->file->exists());

        // set a value to it
        $cacheItem = $this->cache->create($key);
        $cacheItem->setValue($value);

        $this->cache->set($cacheItem);

        // destroy object to call destructor and write
        $this->cache = null;
        $this->assertTrue($this->file->exists());

        // recreate cache
        $this->cache = new FileCachePool($this->file);

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

        // empty cache
        $this->assertFalse($this->file->exists());

        // set a value to it
        foreach ($values as $key => $value) {
            $cacheItem = $this->cache->create($key);
            $cacheItem->setValue($value);

            $this->cache->set($cacheItem);
        }

        // destroy object to call destructor and write
        $this->cache = null;
        $this->assertTrue($this->file->exists());

        // recreate cache
        $this->cache = new FileCachePool($this->file);

        // flush single value from it
        reset($values);
        $flushKey = current($values);
        $this->cache->flush($flushKey);

        // destroy object to call destructor and write
        $this->cache = null;
        $this->assertTrue($this->file->exists());

        // recreate cache
        $this->cache = new FileCachePool($this->file);

        // test flushed value
        foreach ($values as $key => $value) {
            if ($key == $flushKey) {
                $this->assertFalse($this->cache->get($key)->isValid(), $key);
            } else {
                $this->assertTrue($this->cache->get($key)->isValid(), $key);
            }
        }

        // flush full cache
        $this->cache->flush();

        foreach ($values as $key => $value) {
            $this->assertFalse($this->cache->get($key)->isValid());
        }

        // destroy object to call destructor and write
        $this->cache = null;
        $this->assertFalse($this->file->exists());
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

        // empty cache
        $this->assertFalse($this->file->exists());

        // set a value to it
        foreach ($values as $key => $value) {
            $cacheItem = $this->cache->create($key);
            $cacheItem->setValue($value['value']);
            foreach ($value['tags'] as $tag) {
                $cacheItem->addTag($tag);
            }

            $this->cache->set($cacheItem);
        }

        // destroy object to call destructor and write
        $this->cache = null;
        $this->assertTrue($this->file->exists());

        // recreate cache
        $this->cache = new FileCachePool($this->file);
        $this->cache->flushByTag('tag1');

        // destroy object to call destructor and write
        $this->cache = null;
        $this->assertTrue($this->file->exists());

        // recreate cache
        $this->cache = new FileCachePool($this->file);

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
