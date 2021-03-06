<?php

use Gregwar\Cache\Cache;

/**
 * Unit testing for Cache
 */
class CacheTests extends \PHPUnit_Framework_TestCase
{
    /**
     * Testing that file names are good
     */
    public function testFileName()
    {
        $cache = $this->getCache();

        $cacheDir = $this->getCacheDirectory();
        $actualCacheDir = $this->getActualCacheDirectory();
        $cacheFile = $cache->getCacheFile('helloworld.txt');
        $actualCacheFile = $cache->getCacheFile('helloworld.txt', true);
        $this->assertEquals($cacheDir . '/h/e/l/l/o/helloworld.txt', $cacheFile);
        $this->assertEquals($actualCacheDir . '/h/e/l/l/o/helloworld.txt', $actualCacheFile);
        
        $cacheFile = $cache->getCacheFile('xy.txt');
        $actualCacheFile = $cache->getCacheFile('xy.txt', true);
        $this->assertEquals($cacheDir . '/x/y/xy.txt', $cacheFile);
        $this->assertEquals($actualCacheDir . '/x/y/xy.txt', $actualCacheFile);
    }

    /**
     * Testing caching a file
     */
    public function testCaching()
    {
        $cache = $this->getCache();

        $this->assertFalse($cache->exists('testing.txt'));
        $cache->write('testing.txt', 'toto');
        $this->assertTrue($cache->exists('testing.txt'));

        $this->assertFalse($cache->exists('testing.txt', array(
            'max-age' => -1
        )));
        $this->assertTrue($cache->exists('testing.txt', array(
            'max-age' => 2
        )));
    }

    /**
     * Testing the getOrCreate function
     */
    public function testGetOrCreate()
    {
        $cache = $this->getCache();

        $this->assertFalse($cache->exists('testing.txt'));

        $data = $cache->getOrCreate('testing.txt', array(), function() {
            return 'zebra';
        });

        $this->assertTrue($cache->exists('testing.txt'));
        $this->assertEquals('zebra', $data);
        
        $data = $cache->getOrCreate('testing.txt', array(), function() {
            return 'elephant';
        });
        $this->assertEquals('zebra', $data);
    }

    protected function getCache()
    {
        $cache = new Cache;

        return $cache
            ->setPrefixSize(5)
            ->setCacheDirectory($this->getCacheDirectory())
            ->setActualCacheDirectory($this->getActualCacheDirectory())
            ;
    }

    protected function getActualCacheDirectory()
    {
        return __DIR__.'/'.$this->getCacheDirectory();
    }

    protected function getCacheDirectory()
    {
        return 'cache';
    }

    public function tearDown()
    {
        $cacheDirectory = $this->getActualCacheDirectory();
        `rm -rf $cacheDirectory`;
    }
}
