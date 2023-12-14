<?php
declare(strict_types=1);

namespace Utility;

use PHPUnit\Framework\TestCase;
use TypeRocket\Engine7\Core\Booter;
use TypeRocket\Engine7\Utility\RuntimeCache;

class RuntimeCacheTest extends TestCase
{
    public function testRuntimeCache()
    {
        $key = 'typerocket.test';

        $cache = RuntimeCache::new()->add($key, 10);
        $v = $cache->get($key);

        $this->assertTrue($v === 10);

        try {
            $cache->add($key, 10);
        } catch (\Exception $e) {
            $this->assertTrue($e->getMessage() === "Runtime cache already set. key:{$key}");
        }
    }

    public function testRuntimeCacheWalk()
    {
        $cache = RuntimeCache::new()->add('typerocket.test', ['ten' => 10]);
        $v = $cache->walk('typerocket.test', 'ten');

        $this->assertTrue($v === 10);
    }

    public function testRuntimeCacheDelete()
    {
        $cache = RuntimeCache::new()
            ->add('typerocket.test', ['ten' => 10])
            ->delete('typerocket.test');

        $this->assertTrue($cache->get('typerocket.test') === null);
    }

    public function testRuntimeCacheUpdate()
    {
        $cache = RuntimeCache::new()->update('typerocket.test', ['ten' => 10]);
        $cache->update('typerocket.test', ['two' => 2]);
        $ten = $cache->walk('typerocket.test', 'ten');
        $two = $cache->walk('typerocket.test', 'two');

        $this->assertTrue($ten === null);
        $this->assertTrue($two === 2);
    }

    public function testRuntimeCacheGetOtherwisePut()
    {

        $cache = RuntimeCache::new()->update('typerocket.test', ['ten' => 10]);
        $tenGotten = $cache->getOtherwisePut('typerocket.test', ['two' => 2]);
        $ten = $cache->walk('typerocket.test', 'ten');
        $two = $cache->walk('typerocket.test', 'two');

        $this->assertTrue($ten === 10);
        $this->assertTrue($tenGotten['ten'] === 10);
        $this->assertTrue($two === null);
    }

    public function testRuntimeCacheGetOtherwisePutWithPut()
    {
        $cache = RuntimeCache::new()->update('typerocket.test', ['ten' => 10]);
        $twoGotten = $cache->getOtherwisePut('typerocket.testing', ['two' => 2]);
        $ten = $cache->walk('typerocket.test', 'ten');
        $two = $cache->walk('typerocket.testing', 'two');

        $this->assertTrue($ten === 10);
        $this->assertTrue($twoGotten->get('typerocket.testing')['two'] === 2);
        $this->assertTrue($two === 2);
    }

    public function testRuntimeCacheGetFromContainer()
    {
        $cache = RuntimeCache::getFromContainer();

        $this->assertTrue($cache->get('typerocket.booted') === true);
    }

    public function testRuntimeCacheAddReadonly()
    {
        $key = 'typerocket.testing';

        $cache = RuntimeCache::getFromContainer()
            ->addReadonly($key, true);

        $this->assertTrue($cache->get($key) === true);

        try {
            $cache->delete($key);
        } catch (\Exception $e) {
            $this->assertTrue($e->getMessage() === "Runtime cache is readonly. key:{$key}");
        }
    }
}