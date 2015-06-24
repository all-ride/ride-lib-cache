<?php

namespace ride\library\cache;

use \PHPUnit_Framework_TestCase;

class GenericCacheItemTest extends PHPUnit_Framework_TestCase {

    public function testKeyAndValue() {
        $key = 'test';
        $value = 15;

        $cacheItem = new GenericCacheItem();

        $this->assertNull($cacheItem->getKey());
        $this->assertNull($cacheItem->getValue());

        $cacheItem->setKey($key);
        $cacheItem->setValue($value);

        $this->assertEquals($key, $cacheItem->getKey());
        $this->assertEquals($value, $cacheItem->getValue());
    }

    public function testMeta() {
        $key = 'test';
        $value = 15;

        $cacheItem = new GenericCacheItem();
        $cacheItem->setMeta($key, $value);

        $this->assertEquals($value, $cacheItem->getMeta($key));
        $this->assertNull($cacheItem->getMeta('unexistant'));
        $this->assertEquals($value, $cacheItem->getMeta('unexistant', $value));
        $this->assertEquals(array($key => $value), $cacheItem->getMeta());
    }

    public function testTtl() {
        $cacheItem = new GenericCacheItem();

        $this->assertEquals(0, $cacheItem->getTtl());

        $cacheItem->setTtl(15);

        $this->assertEquals(15, $cacheItem->getTtl());

        $cacheItem->setMeta('ttl', 'test');

        $this->assertEquals(0, $cacheItem->getTtl());
    }

    public function testTag() {
        $cacheItem = new GenericCacheItem();

        $this->assertEquals(array(), $cacheItem->getTags());

        $cacheItem->addTag('test');

        $this->assertEquals(array('test'), $cacheItem->getTags());

        $cacheItem->addTag('test2');

        $this->assertEquals(array('test', 'test2'), $cacheItem->getTags());

        $cacheItem->removeTag('test');

        $this->assertEquals(array('test2'), $cacheItem->getTags());

        $cacheItem->removeTag('test3');

        $this->assertEquals(array('test2'), $cacheItem->getTags());

        $cacheItem->removeTag('test2');

        $this->assertEquals(array(), $cacheItem->getTags());
    }

    public function testIsValid() {
        $cacheItem = new GenericCacheItem();

        $this->assertFalse($cacheItem->isValid());

        $cacheItem->setKey('test');

        $this->assertFalse($cacheItem->isValid());

        $cacheItem->setMeta('meta', true);

        $this->assertFalse($cacheItem->isValid());

        $cacheItem->setTtl(1);

        $this->assertFalse($cacheItem->isValid());

        $cacheItem->addTag('tag');

        $this->assertFalse($cacheItem->isValid());

        $cacheItem->setValue('value');

        $this->assertTrue($cacheItem->isValid());

        sleep(2);

        $this->assertFalse($cacheItem->isValid());
    }

    /**
     * @dataProvider providerExceptionOnInvalidKey
     * @expectedException ride\library\cache\exception\CacheException
     */
    public function testSetKeyThrowsExceptionOnInvalidKey($key) {
        $cacheItem = new GenericCacheItem();
        $cacheItem->setKey($key);
    }

    /**
     * @dataProvider providerExceptionOnInvalidKey
     * @expectedException ride\library\cache\exception\CacheException
     */
    public function testSetMetaThrowsExceptionOnInvalidKey($key) {
        $cacheItem = new GenericCacheItem();
        $cacheItem->setMeta($key, 15);
    }

    /**
     * @dataProvider providerExceptionOnInvalidKey
     * @expectedException ride\library\cache\exception\CacheException
     */
    public function testGetMetaThrowsExceptionOnInvalidKey($key) {
        if ($key === null) {
            $this->markTestSkipped('null returns all meta');

            return;
        }

        $cacheItem = new GenericCacheItem();
        $cacheItem->getMeta($key);
    }

    /**
     * @dataProvider providerExceptionOnInvalidKey
     * @expectedException ride\library\cache\exception\CacheException
     */
    public function testAddTagThrowsExceptionOnInvalidTag($tag) {
        $cacheItem = new GenericCacheItem();
        $cacheItem->addTag($tag);
    }

    /**
     * @dataProvider providerExceptionOnInvalidKey
     * @expectedException ride\library\cache\exception\CacheException
     */
    public function testRemoveTagThrowsExceptionOnInvalidTag($tag) {
        $cacheItem = new GenericCacheItem();
        $cacheItem->removeTag($tag);
    }

    public function providerExceptionOnInvalidKey() {
        return array(
            array(null),
            array(''),
            array($this),
            array(array()),
        );
    }

    public function testSetKeyAndTtlSetsCreationTime() {
        $cacheItem1 = new GenericCacheItem(); // empty item
        $cacheItem1->setKey('test');
        $cacheItem2 = new GenericCacheItem(); // default item
        $cacheItem2->setTtl(3600);

        // no need for creation time until ttl and key are set
        $this->assertNull($cacheItem1->getMeta(GenericCacheItem::META_CREATED));
        $this->assertNull($cacheItem2->getMeta(GenericCacheItem::META_CREATED));

        $cacheItem1->setTtl(3600);
        $cacheItem2->setKey('test');

        // now we need the creation time
        $this->assertNotNull($cacheItem1->getMeta(GenericCacheItem::META_CREATED));
        $this->assertNotNull($cacheItem2->getMeta(GenericCacheItem::META_CREATED));
    }

}
