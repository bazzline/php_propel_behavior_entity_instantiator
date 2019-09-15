<?php

declare(strict_types=1);

/**
 * @author stev leibelt <artodeto@bazzline.net>
 * @since 2015-09-01
 */
namespace Test\Net\Bazzline\Propel\Behavior\EntityInstantiator;

use InvalidArgumentException;
use Net\Bazzline\Propel\Behavior\EntityInstantiator\AbstractEntity;
use Net\Bazzline\Propel\Behavior\EntityInstantiator\EntityCollection;
use PHPUnit\Framework\TestCase;

class EntityCollectionTest extends TestCase
{
    public function testAdd(): void
    {
        $collection = $this->getNewCollection();
        $entity     = $this->getNewEntity();

        $collection->add($entity);

        static::assertEquals($entity, $collection[0]);
    }

    /**
     * @depends testAdd
     */
    public function testAddSameEntityTwice(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('you are trying to add "className" twice for the database "database_name"');

        $collection = $this->getNewCollection();
        $entity     = $this->getNewEntity();

        $collection->add($entity);
        $collection->add($entity);
    }

    /**
     * @depends testAdd
     */
    public function testArrayAccess(): void
    {
        $collection = $this->getNewCollection();
        $entity     = $this->getNewEntity();

        $collection->add($entity);

        static::assertTrue($collection->offsetExists(0));
        static::assertFalse($collection->offsetExists(1));

        static::assertEquals($entity, $collection->offsetGet(0));

        $collection->offsetSet(3, $entity);
        static::assertTrue($collection->offsetExists(3));

        $collection->offsetUnset(3);
        static::assertFalse($collection->offsetExists(3));
    }

    /**
     * @depends testAdd
     */
    public function testCountable(): void
    {
        $collection = $this->getNewCollection();
        $entity     = $this->getNewEntity();

        static::assertEquals(0, $collection->count());

        $collection->add($entity);
        static::assertEquals(1, $collection->count());
    }

    public function testIterator(): void
    {
        $collection = $this->getNewCollection();
        $array      = [
            $this->getNewEntity('method' . __LINE__),
            $this->getNewEntity('method' . __LINE__),
            $this->getNewEntity('method' . __LINE__)
        ];

        foreach ($array as $entity) {
            $collection->add($entity);
        }
        reset($array);

        foreach ($collection as $key => $value) {
            static::assertEquals(key($array), $key);
            static::assertEquals(current($array), $value);
            next($array);
        }
    }

    /**
     * @return EntityCollection
     */
    private function getNewCollection()
    {
        return new EntityCollection();
    }

    private function getNewEntity(string $methodName = 'className'): AbstractEntity
    {
        $entity = $this->prophesize(AbstractEntity::class);
        $entity->databaseName()->willReturn('database_name');
        $entity->className()->willReturn($methodName);

        return $entity->reveal();
    }
}
