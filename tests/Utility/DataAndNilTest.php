<?php
namespace TypeRocket\tests\Utility;

use PHPUnit\Framework\TestCase;
use TypeRocket\Engine7\Utility\Data;
use TypeRocket\Engine7\Utility\Nil;
use stdClass;
use TypeRocket\Engine7\Utility\Arr;

class DataAndNilTest extends TestCase
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
        $arr['one'] = true;
        $nil = Data::nil($arr);
        $nil->offsetUnset('one');

        $this->assertTrue(empty($nil['one']));

        $nil->offsetSet('one', 'one');

        $v = $nil['one']->get();

        $this->assertTrue($v === 'one');
    }

    public function testNilUnset()
    {
        $arr = [];
        $arr['one'] = [];
        $arr['one']['two'] = [true];
        $this->assertTrue(Data::nil(Data::nil($arr['one'])['two'])['three']['four'] instanceof Nil);
        $this->assertTrue(isset(Data::nil($arr['one'])['two']));
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

    public function testDataWalkMissing()
    {
        $data = [
            'one' => ['two' => 'hi' ]
        ];

        $v = Data::walk('one.two.0', $data);

        $this->assertTrue($v === null);
    }

    public function testDataWalkObject()
    {
        $class = new \stdClass();
        $class->two = 2;

        $data = [
            'one' => $class
        ];

        $v = Data::walk('one.two', $data);

        $this->assertTrue($v === 2);
    }

    public function testDataWalkToNull()
    {
        $data = [
            'one' => ['two' => null ]
        ];

        $v = Data::walk('one.two', $data, 'one');

        $this->assertTrue($v === 'one');
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

    public function testDataValue()
    {
        $this->assertTrue(Data::value('one') === 'one');
    }

    public function testDataValueCallback()
    {
        $this->assertTrue(Data::value(fn($name) => $name, ['name' => 'one']) === 'one');
    }

    public function testDataEmptyRecursive()
    {
        $this->assertTrue(Data::emptyRecursive(''));
        $this->assertTrue(! Data::emptyRecursive(' '));
        $this->assertTrue(Data::emptyRecursive(0));
        $this->assertTrue(Data::emptyRecursive(0.0));
        $this->assertTrue(Data::emptyRecursive(false));
        $this->assertTrue(Data::emptyRecursive(null));
        $this->assertTrue(Data::emptyRecursive([[[]]]));
        $this->assertTrue(Data::emptyRecursive([false,[null,[null]]]));
        $this->assertTrue(!Data::emptyRecursive([[['one']]]));
    }

    public function testDataEmptyOrBlankRecursive()
    {
        $this->assertTrue(Data::emptyOrBlankRecursive(''));
        $this->assertTrue(! Data::emptyRecursive(' '));
        $this->assertTrue(! Data::emptyOrBlankRecursive(0)); // emptyOrBlankRecursive sees 0 integers
        $this->assertTrue(! Data::emptyOrBlankRecursive(0.0)); // emptyOrBlankRecursive sees 0.0 floats
        $this->assertTrue(Data::emptyOrBlankRecursive(false));
        $this->assertTrue(Data::emptyOrBlankRecursive(null));
        $this->assertTrue(Data::emptyOrBlankRecursive([[[]]]));
        $this->assertTrue(Data::emptyOrBlankRecursive([false,[null,[null]]]));
        $this->assertTrue(! Data::emptyOrBlankRecursive([0,[null,[null]]])); // emptyOrBlankRecursive sees 0 integers
        $this->assertTrue(! Data::emptyOrBlankRecursive([0.0,[null,[null]]])); // emptyOrBlankRecursive sees 0.0 floats
        $this->assertTrue(! Data::emptyOrBlankRecursive([[['one']]]));
    }

    public function testCastingNoneType()
    {
        $types = [
            'none',
            '',
        ];

        $values = [
            json_encode([]),
            serialize([]),
            null,
            false,
            true,
            [],
            '',
            new stdClass(),
            1,
            0,
            33,
            '33',
            '1',
            'abc',
        ];

        foreach ($values as $v) {
            foreach($types as $t) {
                $c = Data::cast($v, $t);
                $this->assertTrue($v === $c);
            }
        }
    }

    public function testCastingFloat()
    {
        $types = [
            'real',
            'double',
            'float',
        ];

        $ints = [
            1,
            null,
            '1',
            'abc',
            0,
            33,
            '33',
            false,
            '',
            true,
            json_encode([]),
            serialize([]),
        ];

        $nulls = [
            [],
            new stdClass(),
        ];

        foreach ($nulls as $v) {
            foreach($types as $t) {
                $this->assertTrue(Data::cast($v, $t) === null);
            }
        }

        foreach ($ints as $v) {
            foreach($types as $t) {
                $int = Data::cast($v, $t);
                $this->assertTrue(is_float($int));
            }
        }
    }

    public function testCastingStr()
    {
        $types = [
            'str',
            'string'
        ];

        $values = [
            1,
            null,
            '1',
            0,
            33,
            '',
            '33',
            'abc',
            false,
            true,
            json_encode([]),
            serialize([]),
            [],
            new stdClass()
        ];

        foreach ($values as $v) {
            foreach($types as $t) {
                $str = Data::cast($v, $t);
                $this->assertTrue(is_string($str));
            }
        }
    }

    public function testCastingBool()
    {
        $types = [
            'bool',
            'boolean'
        ];

        $values = [
            1,
            0,
            33,
            '33',
            null,
            '1',
            'abc',
            '',
            false,
            true,
            json_encode([]),
            serialize([]),
            [],
            new stdClass()
        ];

        foreach ($values as $v) {
            foreach($types as $t) {
                $bool = Data::cast($v, $t);
                $this->assertTrue(is_bool($bool));
            }
        }
    }

    public function testCastingObject()
    {
        $types = [
            'object',
            'obj'
        ];

        $values = [
            json_encode([]),
            serialize([]),
            null,
            false,
            true,
            [],
            new stdClass()
        ];

        $same = [
            1,
            0,
            '',
            33,
            '33',
            '1',
            'abc',
        ];

        foreach ($values as $v) {
            foreach($types as $t) {
                $c = Data::cast($v, $t);
                $this->assertTrue(is_object($c));
            }
        }

        foreach ($same as $v) {
            foreach($types as $t) {
                $c = Data::cast($v, $t);
                $this->assertTrue($c === $v);
            }
        }
    }

    public function testCastingArray()
    {
        $types = [
            'array',
        ];

        $values = [
            json_encode([]),
            serialize([]),
            null,
            false,
            true,
            [],
            new stdClass()
        ];

        $same = [
            1,
            0,
            33,
            '',
            '33',
            '1',
            'abc',
        ];

        foreach ($values as $v) {
            foreach($types as $t) {
                $c = Data::cast($v, $t);
                $this->assertTrue(is_array($c));
            }
        }

        foreach ($same as $v) {
            foreach($types as $t) {
                $c = Data::cast($v, $t);
                $this->assertTrue($c === $v);
            }
        }
    }

    public function testCastingJson()
    {
        $types = [
            'json',
        ];

        $values = [
            json_encode([]),
            serialize([]),
            null,
            false,
            true,
            [],
            ' ',
            new stdClass(),
            1,
            0,
            33,
            '33',
            '1',
            'abc',
        ];

        foreach ($values as $v) {
            foreach($types as $t) {
                $c = Data::cast($v, $t);
                $this->assertTrue(Data::isJson($c));
            }
        }
    }

    public function testCastingSerial()
    {
        $types = [
            'serialize',
            'serial',
        ];

        $values = [
            json_encode([]),
            serialize([]),
            null,
            false,
            true,
            [],
            '',
            new stdClass(),
            1,
            0,
            33,
            '33',
            '1',
            'abc',
        ];

        foreach ($values as $v) {
            foreach($types as $t) {
                $c = Data::cast($v, $t);
                $this->assertTrue(is_serialized($c));
            }
        }
    }

    public function testCastingInt()
    {
        $types = [
            'int',
            'integer'
        ];

        $ints = [
            1,
            null,
            '1',
            'abc',
            0,
            '',
            33,
            '33',
            false,
            true,
            json_encode([]),
            serialize([]),
        ];

        $nulls = [
            [],
            new stdClass()
        ];

        foreach ($nulls as $v) {
            foreach($types as $t) {
                $this->assertTrue(Data::cast($v, $t) === null);
            }
        }

        foreach ($ints as $v) {
            foreach($types as $t) {
                $int = Data::cast($v, $t);
                $this->assertTrue(is_int($int));
            }
        }
    }

    public function testDataCreateMapIndexByTypeError()
    {
        $data = [
            'kevin',
            ['name' => 'kim'],
            ['name' => 'jim'],
        ];

        try {
            $v = Data::createMapIndexBy('name', $data);
        } catch (\Exception $e) {
            $this->assertTrue($e->getMessage() === 'Nested array or object required for Data::createMapIndexBy(): string is not valid.');
        }
    }

    public function testDataCreateMapIndexByUniqueError()
    {
        $data = [
            ['name' => 'kim'],
            ['name' => 'kim'],
        ];

        try {
            $v = Data::createMapIndexBy('name', $data);
        } catch (\Exception $e) {
            $this->assertTrue($e->getMessage() === 'Index key must be unique for Data::createMapIndexBy(): kim already taken.');
        }
    }

    public function testDataCreateMapIndexBy()
    {
        $data = [
            ['name' => 'kim'],
            ['name' => 'jim'],
        ];

        $v = Data::createMapIndexBy('name', $data);
        $this->assertTrue($v['kim'] === ['name' => 'kim']);
    }

    public function testDataMapDeep()
    {
        $class = new stdClass();
        $class->one = 'One';
        $class->two = ['Two'];

        $d = Data::mapDeep(fn($v) => mb_strtolower($v), ['One', 'Two']);
        $this->assertTrue($d === ['one', 'two']);

        $d = Data::mapDeep(fn($v) => mb_strtolower($v), $class);
        $this->assertTrue($d->one === 'one' && $d->two[0] === 'two');
    }
}
