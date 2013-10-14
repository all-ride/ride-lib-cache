## Use The Cache

Check the following code sample to see how the cache should be used:

    $cache = $pallo->getDependency('pallo\\library\\cache\\pool\\CachePool', 'mycache');
    
    $item = $cache->get('item.cache.key');
    if (!$item->isValid()) {
        // some value generation logic

        $item->setValue($value);
        $item->setTtl(60); // in seconds, optional
        
        $cache->set($item);
    } else {
        $value = $item->getValue();
    }

This code uses the item as returned from the pool to set the cached value.

When you warm up your cache in another place, you can easily create your cache item through the pool:

    $cache = $pallo->getDependency('pallo\\library\\cache\\pool\\CachePool', 'mycache');
    
    $item = $cache->create('item.cache.key');
    $item->setValue('some cache value');
    
    $cache->set($item);

## Cache Pools

A [pallo\library\cache\pool\CachePool](/api/class/pallo/library/cache/pool/CachePool) is the backend or storage of your cache items.
You can define as much pools as you need through the [Dependencies](/manual/page/Core/Dependencies).

### File Cache Pool

The file cache pool will keep a file for each cached item in a containing directory.

The directory is best defined as a parameter.

myapp.ini:

    cache.pool.mycache = "%application%/data/cache/files"
 
dependencies.xml:

    <?xml version="1.0" encoding="UTF-8"?>
    <container>  
        <dependency class="pallo\library\filesystem\File" id="cache.pool.mycache">
            <call method="__construct">
                <argument name="file" type="parameter">
                    <property name="key" value="myapp.cache.pool.mycache" />
                </argument>
            </call>
        </dependency>

        <dependency interface="pallo\library\cache\pool\CachePool" class="pallo\library\cache\pool\FileCachePool" id="mycache">
            <call method="__construct">
                <argument name="file" type="dependency">
                    <property name="interface" value="pallo\library\filesystem\File" />
                    <property name="id" value="cache.pool.mycache" />
                </argument>
            </call>
        </dependency>
    </container>

### Memory Cache Pool

The memory cache pool will store all it's values in one file.
It's read when the first item is being accessed and written when the pool is destroyed by PHP.

The file is best defined as a parameter.

myapp.ini:

    cache.pool.mycache = "%application%/data/cache/memory.pool"
 
dependencies.xml:

    <?xml version="1.0" encoding="UTF-8"?>
    <container>  
        <dependency class="pallo\library\filesystem\File" id="cache.pool.mycache">
            <call method="__construct">
                <argument name="file" type="parameter">
                    <property name="key" value="myapp.cache.pool.mycache" />
                </argument>
            </call>
        </dependency>

        <dependency interface="pallo\library\cache\pool\CachePool" class="pallo\library\cache\pool\MemoryCachePool" id="mycache">
            <call method="__construct">
                <argument name="file" type="dependency">
                    <property name="interface" value="pallo\library\filesystem\File" />
                    <property name="id" value="cache.pool.mycache" />
                </argument>
            </call>
        </dependency>
    </container>
      
### Opcache Cache Pool

dependencies.xml for a APC opcache pool:  

    <?xml version="1.0" encoding="UTF-8"?>
    <container>  
        <dependency interface="pallo\library\cache\pool\CachePool" class="pallo\library\cache\pool\op\ApcOpCachePool" id="op" />
    </container>  

dependencies.xml for a XCache opcache pool:
   
    <?xml version="1.0" encoding="UTF-8"?>
    <container>  
        <dependency interface="pallo\library\cache\pool\CachePool" class="pallo\library\cache\pool\op\ApcOpCachePool" id="op" />
    </container>  
    
## Cache Items

A [pallo\library\cache\CacheItem](/api/class/pallo/library/cache/CacheItem) is the container of your cached value.
The implementation contains the logic to see if the cached item is valid and not stale.
