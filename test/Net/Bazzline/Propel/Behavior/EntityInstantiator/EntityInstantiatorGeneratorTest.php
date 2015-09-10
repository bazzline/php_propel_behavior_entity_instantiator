<?php
/**
 * @author stev leibelt <artodeto@bazzline.net>
 * @since 2015-08-31
 */
namespace Test\Net\Bazzline\Propel\Behavior\EntityInstantiator;

use Exception;
use Mockery;
use Net\Bazzline\Propel\Behavior\EntityInstantiator\EntityCollection;
use Net\Bazzline\Propel\Behavior\EntityInstantiator\EntityInstantiatorGenerator;
use PHPUnit_Framework_TestCase;
use org\bovigo\vfs\vfsStream;
use ReflectionClass;
use RuntimeException;

class EntityInstantiatorGeneratorTest extends PHPUnit_Framework_TestCase
{
    /** @var string */
    private $className;

    /** @var Mockery\MockInterface|EntityCollection */
    private $collection;

    /** @var string */
    private $extends;

    /** @var \org\bovigo\vfs\vfsStreamDirectory */
    private $fileSystem;

    /** @var string */
    private $indention;

    /** @var string */
    private $namespace;

    protected function setUp()
    {
        $this->resetGenerator();

        $this->className    = 'ClassName';
        $this->collection   = Mockery::mock('Net\Bazzline\Propel\Behavior\EntityInstantiator\EntityCollection');
        $this->collection->shouldReceive('rewind');
        $this->collection->shouldReceive('valid')->andReturn(false);
        $this->fileSystem   = vfsStream::setup();
        $this->extends      = 'stdClass';
        $this->indention    = '  ';
        $this->namespace    = 'Name\Space';
    }

    protected function tearDown()
    {
        Mockery::close();
    }

    /**
     * @expectedException RuntimeException
     * @expectedExceptionMessage you have to call configure first
     */
    public function testCallingGenerateBeforeCallingConfigure()
    {
        $generator = $this->getGenerator();
        $generator->generate();
    }

    public function testIsNotConfigured()
    {
        $generator = $this->getGenerator();
        $this->assertTrue($generator->isNotConfigured());

        $generator->configure(
            $this->fileSystem->url(),
            $this->className,
            $this->collection,
            $this->extends,
            $this->indention,
            $this->namespace
        );
        $this->assertFalse($generator->isNotConfigured());
    }

    public function testGenerate()
    {
        $generator = $this->getGenerator();

        $generator->configure(
            $this->fileSystem->url(),
            $this->className,
            $this->collection,
            $this->extends,
            $this->indention,
            $this->namespace
        );

        $generator->generate();
        $filePath       = $this->fileSystem->url() . '/' . $this->className . '.php';
        $fileContent    = file_get_contents($filePath);

        $this->assertTrue(file_exists($filePath));

        $this->assertContains('class ' . $this->className, $fileContent);
        $this->assertContains('extends ' . $this->extends, $fileContent);
        $this->assertContains($this->indention . 'public function ', $fileContent);
        $this->assertContains('namespace ' . $this->namespace, $fileContent);
    }

    /**
     * @return EntityInstantiatorGenerator
     */
    private function getGenerator()
    {
        return EntityInstantiatorGenerator::getInstance();
    }

    /**
     * @return EntityInstantiatorGenerator
     */
    private function resetGenerator()
    {
        $singleton  = EntityInstantiatorGenerator::getInstance();
        $reflection = new ReflectionClass($singleton);
        $instance   = $reflection->getProperty('instance');

        $instance->setAccessible(true);
        $instance->setValue(null, null);
        $instance->setAccessible(false);

        return $singleton;
    }
}
