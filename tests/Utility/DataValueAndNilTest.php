<?php
namespace TypeRocket\tests\Utility;

use PHPUnit\Framework\TestCase;
use TypeRocket\Engine7\Utility\Data;
use TypeRocket\Engine7\Utility\Nil;

class DataValueAndNilTest extends TestCase
{
    public function testNilObj()
    {
        $obj = new \stdClass();
        $obj->one = new \stdClass();
        $obj->one->two = new \stdClass();
        $null = Data::nil($obj)->one->two->three->get() ?? null;

        $this->assertTrue(is_null($null));
        $this->assertTrue(Data::nil($obj->one)->two->three->four instanceof Nil);
        $this->assertTrue(isset(Data::nil($obj->one)->two));
        $this->assertTrue(!isset(Data::nil($obj->one->two)->three));
        $this->assertTrue(!isset(Data::nil($obj->one->two)->three->four));
    }

    public function testDataNil()
    {
        $obj = new \stdClass();
        $obj->one = new \stdClass();
        $obj->one->two = new \stdClass();
        $null = Data::nil($obj)->one->two->three->get() ?? null;

        $this->assertTrue(is_null($null));
        $this->assertTrue(Data::nil($obj->one)->two->three->four instanceof Nil);
        $this->assertTrue(isset(Data::nil($obj->one)->two));
        $this->assertTrue(!isset(Data::nil($obj->one->two)->three));
        $this->assertTrue(!isset(Data::nil($obj->one->two)->three->four));
    }

    public function testNilArray()
    {
        $arr = [];
        $arr['one'] = [];
        $arr['one']['two'] = [true];
        $this->assertTrue(Data::nil(Data::nil($arr['one'])['two'])['three']['four'] instanceof Nil);
        $this->assertTrue(isset(Data::nil($arr['one'])['two']));
        $this->assertTrue(!isset(Data::nil($arr['one']['two'])['three']['four']));
    }

    public function testNilCombo()
    {
        $arr = [];
        $arr['one'] = [];
        $arr['one']['two'] = [true];

        $this->assertTrue(Data::nil(Data::nil($arr['one'])['two'])->three['four'] instanceof Nil);
        $this->assertTrue(isset(Data::nil($arr['one'])->two));
        $this->assertTrue(!isset(Data::nil($arr['one']['two'])['three']->four));
    }

    public function testNilHelper()
    {
        $arr = [];
        $arr['one'] = [];
        $arr['one']['two'] = [true];

        $this->assertTrue(Data::nil(Data::nil($arr['one'])['two'])->three['four'] instanceof Nil);
        $this->assertTrue(Data::nil(Data::nil($arr['one'])['two'])->three['four']->get() === null);
        $this->assertTrue(isset(Data::nil($arr)['one']->two));
        $this->assertTrue(!isset(Data::nil($arr['one']['two'])['three']['four']));
    }

    public function testDataWalkBasic()
    {
        $data = [
            'one' => ['two' => 'hi' ]
        ];

        $v = Data::walk('one.two', $data);

        $this->assertTrue($v === 'hi');
    }

    public function testDataWalkDeep()
    {
        $data = [
            'one' => [
                'two' => [[1],[2],[3]],
                'three' => [[4],[5],[6]],
            ]
        ];

        $v = Data::walk('one.*.0', $data);
        $v2 = Data::walk('one.*.1', $data);

        $v = array_reduce($v, function($carry, $v) {
            return $carry + $v[0];
        });

        $v2 = array_reduce($v2, function($carry, $v) {
            return $carry + $v[0];
        });

        $this->assertTrue($v === 5);
        $this->assertTrue($v2 === 7);
    }

    public function testDataMap()
    {
        $v = Data::map(function($c) {
            return $c * 4;
        }, 2);

        $this->assertTrue($v === 8);
    }

    public function testDataMapArray()
    {
        $v = Data::map(function($c) {
            return $c * 4;
        }, [2,2]);

        $this->assertTrue($v === [8,8]);
    }

    public function testDataMapArrayNested()
    {
        $v = Data::map(function($c) {
            return count($c);
        }, [[1],[1]]);

        $this->assertTrue($v === [1,1]);
    }

    public function testDataMapDeepArrayNested()
    {
        $v = Data::mapDeep(function($c) {
            return $c * 4;
        }, [[2],[2]]);

        $this->assertTrue($v === [[8],[8]]);
    }

    public function testDataMapObject()
    {
        $o = new \stdClass();
        $o->one = 2;
        $o->two = 2;

        $v = Data::map(function($c) {
            return $c * 4;
        }, $o);

        $this->assertTrue($v->one === 8);
        $this->assertTrue($v->two === 8);
    }

    public function testDataGet()
    {
        $o2 = new \stdClass();
        $o2->name = 'kevin';
        $o2->number = 123;

        $o = new \stdClass();
        $o->one = 1;
        $o->two = $o2;

        $v = Data::get($o, 'one');
        $v1 = Data::get($o, 'three', 3);
        $v2 = Data::get($o, 'two.name');
        $v3 = Data::get($o, ['two.name', 'one']);
        $v4 = Data::get($o, ['two.name', 'three'], 3);

        $this->assertTrue($v === 1);
        $this->assertTrue($v1 === 3);
        $this->assertTrue($v2 === 'kevin');
        $this->assertTrue($v3 === ['two.name' => 'kevin',  'one' => 1]);
        $this->assertTrue($v4 === ['two.name' => 'kevin',  'three' => 3]);
    }
}
