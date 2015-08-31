<?php

namespace Test\Net\Bazzline\Propel\Behavior\EntityInstantiator;

use Net\Bazzline\Propel\Behavior\EntityInstantiator\QueryEntity;
use PHPUnit_Framework_TestCase;

/**
 * @author stev leibelt <artodeto@bazzline.net>
 * @since 2015-08-31
 */
class QueryEntityTest extends PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $databaseName           = 'foobar';
        $fullQualifiedClassName = '\Foo\Bar\Foobar';
        $methodName             = 'bar';
        $methodNamePrefix       = 'foo';

        $entity = new QueryEntity($databaseName, $fullQualifiedClassName, $methodName, $methodNamePrefix);

        $this->assertEquals($databaseName, $entity->databaseName());
        $this->assertEquals($fullQualifiedClassName, $entity->fullQualifiedClassName());
        $this->assertEquals($methodNamePrefix, $entity->methodNamePrefix());
        $this->assertEquals($methodName, $entity->methodName());
    }
}