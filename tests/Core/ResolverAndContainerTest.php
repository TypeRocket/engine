<?php
declare(strict_types=1);

namespace Core;

use PHPUnit\Framework\TestCase;
use TypeRocket\Engine7\Core\Container;
use TypeRocket\Engine7\Core\Resolver;
use TypeRocket\Engine7\Interfaces\ResolvesWith;

class ResolverAndContainerTest extends TestCase
{
    public function testResolverAndContainer()
    {
        $test = $this;
        $class = Resolver::new()->resolve(ForResolverTestClass::class, ['test' => $this]);

        Container::singleton(ForResolverTestClass::class, function() use ($test) {
            return new ForResolverTestClass($test, [1,2,3], 'test one two three');
        }, 'typerocket.test.test-resolver');

        $singleton = Resolver::new()->resolve(ForResolverTestClass::class);
        $singletonAlias = Resolver::new()->resolve('typerocket.test.test-resolver');

        $this->assertTrue($class instanceof ForResolverTestClass);
        $this->assertTrue($singleton instanceof ForResolverTestClass);
        $this->assertTrue($class !== $singleton);
        $this->assertTrue($singletonAlias === $singleton);
    }

    public function testContainerAlreadyExists()
    {
        $none = Container::register(
            ForResolverTestClass::class,
            fn() => new ForResolverTestClass($this)
        );

        $this->assertTrue($none === false);
    }

    public function testContainerGetAliases()
    {
        $list = Container::aliases();
        $has = Container::aliasExists('typerocket.test.test-resolver');

        $this->assertTrue(is_array($list));
        $this->assertTrue($has);
    }

    public function testContainerFindOrNewSingleton()
    {
        $instance = Container::findOrNewSingleton(ForResolverTestClass::class);
        $this->assertTrue($instance instanceof ForResolverTestClass);

        $instance = Container::findOrNewSingleton(ForResolverTestClassFindOrNewSingleton::class);
        $this->assertTrue($instance instanceof ForResolverTestClassFindOrNewSingleton);
    }

    public function testResolveCallable()
    {
        Resolver::new()->resolveCallable(function($array, $int, $string, $bool, $null = null) {
            $this->assertIsArray($array);
            $this->assertIsInt($int);
            $this->assertIsString($string);
            $this->assertIsBool($bool);
            $this->assertTrue(is_null($null));
        }, [
           'array' => [1,2,3],
           'int' => 1,
           'string' => 'text',
           'bool' => false,
        ]);

        Resolver::new()->resolveCallable(function($array, $int, $string, $bool) {
            $this->assertIsArray($array);
            $this->assertIsInt($int);
            $this->assertIsString($string);
            $this->assertIsBool($bool);
        }, [
            [1,2,3],
            1,
            'string',
            false
        ]);

        Resolver::new()->resolveCallable(function($int, $array, $string, $bool) {
            $this->assertIsArray($array);
            $this->assertIsInt($int);
            $this->assertIsString($string);
            $this->assertIsBool($bool);
        }, [
            'array' => [1,2,3],
            1,
            'string',
            false
        ]);

        $c = new class {
            public function method($test, $array, $int, $string, $bool, $null = null)
            {
                $test->assertIsArray($array);
                $test->assertIsInt($int);
                $test->assertIsString($string);
                $test->assertIsBool($bool);
                $test->assertTrue(is_null($null));
            }

            public function onDependencyInjection()
            {

            }
        };

        Resolver::new()->resolveCallable([$c, 'method'], [
            '@first' => $this,
            'array' => [1,2,3],
            'int' => 1,
            'string' => 'text',
            'bool' => false,
        ]);
    }

    public function testResolverBuild()
    {
        $c = Resolver::build(ForResolverTestClass::class, [$this]);

        $this->assertIsArray($c->array);
    }

    public function testResolverOnDependencyInjection()
    {
        Resolver::new()->resolveCallable([new ForResolverTestClassDependencyInjection, 'method'], [
            $this,
            'me'
        ]);
    }

    public function testResolverNonClass()
    {
        try {
            Resolver::build(ForResolverTestClassAbstract::class);
        } catch (\Exception $exception) {
            $this->assertTrue($exception->getMessage() === 'Core\ForResolverTestClassAbstract is not instantiable');
        }
    }

    public function testResolverResolvesWith()
    {
        Resolver::build(ForResolverTestClassResolvesWith::class)->method($this);
    }

    public function testResolverNonDefaultValue()
    {
        try {
            $c = new class {
                public function method($test, $array){}
            };

            Resolver::new()->resolveCallable([$c, 'method'], [
                '@first' => $this,
            ]);
        } catch (\Exception $exception) {
            $this->assertTrue($exception->getMessage() === 'Resolver failed because there is no default value for the parameter: $array');
        }
    }
}

class ForResolverTestClass {

    public function __construct(
        public ?TestCase $test,
        public array $array = [],
        public string $str = 'test'
    )
    {
        $test->assertIsArray($array);
        $test->assertIsString($str);
    }

}

class ForResolverTestClassFindOrNewSingleton {}
abstract class ForResolverTestClassAbstract {}

class ForResolverTestClassDependencyInjection {

    public string $name = 'you';

    public function method($test, ForResolverTestClassDependencyInjection $that): void
    {
        $test->assertTrue($that->name === 'me');
    }

    public function onDependencyInjection($v): mixed
    {
        $this->name = $v;

        return $this;
    }

    public function onResolution(): static
    {
        $this->name = 'them';

        return $this;
    }
};

class ForResolverTestClassResolvesWith implements ResolvesWith {

    public string $name = 'you';

    public function method($test): void
    {
        $test->assertTrue($this->name === 'them');
    }

    public function onResolution(): static
    {
        $this->name = 'them';

        return $this;
    }
};