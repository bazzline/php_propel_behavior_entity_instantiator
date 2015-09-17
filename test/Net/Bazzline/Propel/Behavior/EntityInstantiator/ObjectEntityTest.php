<?php

namespace Test\Net\Bazzline\Propel\Behavior\EntityInstantiator;

use Net\Bazzline\Propel\Behavior\EntityInstantiator\ObjectEntity;
use PHPUnit_Framework_TestCase;

/**
 * @author stev leibelt <artodeto@bazzline.net>
 * @since 2015-08-31
 */
class ObjectEntityTest extends PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $className              = 'bar';
        $databaseName           = 'foobar';
        $fullQualifiedClassName = '\Foo\Bar\Foobar';
        $methodNamePrefix       = 'foo';

        $entity = new ObjectEntity($className, $databaseName, $fullQualifiedClassName, $methodNamePrefix);

        $this->assertEquals($className, $entity->className());
        $this->assertEquals($databaseName, $entity->databaseName());
        $this->assertEquals($fullQualifiedClassName, $entity->fullQualifiedClassName());
        $this->assertEquals($methodNamePrefix, $entity->methodNamePrefix());
    }
}