<?php
declare(strict_types=1);

namespace Utility;

use PHPUnit\Framework\TestCase;
use TypeRocket\Engine7\Core\Container;

class InjectorTest extends TestCase
{

    public function testRegisterAndDestroy()
    {

        Container::register('php.stdClass', function () {
            $obj = new \stdClass();
            $obj->test = 1;
            return $obj;
        }, true);

        $obj = Container::resolve('php.stdClass');

        $this->assertTrue( $obj->test === 1 );
    }
}