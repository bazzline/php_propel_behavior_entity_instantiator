<?php declare(strict_types=1);

namespace Test\Net\Bazzline\Propel\Behavior\EntityInstantiator;

use Net\Bazzline\Propel\Behavior\EntityInstantiator\ObjectEntity;
use PHPUnit\Framework\TestCase;

/**
 * @author stev leibelt <artodeto@bazzline.net>
 * @since 2015-08-31
 */
class ObjectEntityTest extends TestCase
{
    public function testConstructor(): void
    {
        $className              = 'bar';
        $databaseName           = 'foobar';
        $fullQualifiedClassName = '\Foo\Bar\Foobar';
        $methodNamePrefix       = 'foo';

        $entity = new ObjectEntity($className, $databaseName, $fullQualifiedClassName, $methodNamePrefix);

        static::assertEquals($className, $entity->className());
        static::assertEquals($databaseName, $entity->databaseName());
        static::assertEquals($fullQualifiedClassName, $entity->fullQualifiedClassName());
        static::assertEquals($methodNamePrefix, $entity->methodNamePrefix());
    }
}