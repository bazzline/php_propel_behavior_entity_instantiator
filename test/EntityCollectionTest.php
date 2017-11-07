<?php
/**
 * @author stev leibelt <artodeto@bazzline.net>
 * @since 2015-09-01
 */
namespace Test\Net\Bazzline\Propel\Behavior\EntityInstantiator;

use InvalidArgumentException;
use Mockery;
use Net\Bazzline\Propel\Behavior\EntityInstantiator\AbstractEntity;
use Net\Bazzline\Propel\Behavior\EntityInstantiator\EntityCollection;
use PHPUnit_Framework_TestCase;

class EntityCollectionTest extends PHPUnit_Framework_TestCase
{
    protected function tearDown()
    {
        Mockery::close();
    }

    public function testAdd()
    {
        $collection = $this->getNewCollection();
        $entity     = $this->getNewEntity();

        $collection->add($entity);
    }

    /**
     * @depends testAdd
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage  you are trying to add "className" twice for the database "database_name"
     */
    public function testAddSameEntityTwice()
    {
        $collection = $this->getNewCollection();
        $entity     = $this->getNewEntity();

        $collection->add($entity);
        $collection->add($entity);
    }

    /**
     * @depends testAdd
     */
    public function testArrayAccess()
    {
        $collection = $this->getNewCollection();
        $entity     = $this->getNewEntity();

        $collection->add($entity);

        $this->assertTrue($collection->offsetExists(0));
        $this->assertFalse($collection->offsetExists(1));

        $this->assertEquals($entity, $collection->offsetGet(0));

        $collection->offsetSet(3, $entity);
        $this->assertTrue($collection->offsetExists(3));

        $collection->offsetUnset(3);
        $this->assertFalse($collection->offsetExists(3));
    }

    /**
     * @depends testAdd
     */
    public function testCountable()
    {
        $collection = $this->getNewCollection();
        $entity     = $this->getNewEntity();

        $this->assertEquals(0, $collection->count());

        $collection->add($entity);
        $this->assertEquals(1, $collection->count());
    }

    public function testIterator()
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
            $this->assertEquals(key($array), $key);
            $this->assertEquals(current($array), $value);
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

    /**
     * @param string $methodName
     * @return Mockery\MockInterface|AbstractEntity
     */
    private function getNewEntity(
        $methodName = 'className'
    ) {
        $entity = Mockery::mock(AbstractEntity::class);

        $entity->shouldReceive('databaseName')
            ->andReturn('database_name')
            ->byDefault();
        $entity->shouldReceive('className')
            ->andReturn($methodName)
            ->byDefault();

        return $entity;
    }
}
