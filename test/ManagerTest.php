<?php
/**
 * @author stev leibelt <artodeto@bazzline.net>
 * @since 2015-08-31
 */
namespace Test\Net\Bazzline\Propel\Behavior\EntityInstantiator;

use Exception;
use Mockery;
use Net\Bazzline\Propel\Behavior\EntityInstantiator\EntityCollection;
use Net\Bazzline\Propel\Behavior\EntityInstantiator\Manager;
use PHPUnit_Framework_TestCase;
use org\bovigo\vfs\vfsStream;
use ReflectionClass;
use RuntimeException;

class ManagerTest extends PHPUnit_Framework_TestCase
{
    /** @var string */
    private $className;

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
        $this->extends      = 'stdClass';
        $this->fileSystem   = vfsStream::setup();
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
        $manager = $this->getManager();
        $manager->generate();
    }

    public function testGenerateByUsingNamespace()
    {
        $manager = $this->getManager();

        $manager->configure(
            $this->className,
            $this->indention,
            $this->fileSystem->url(),
            $this->namespace,
            $this->extends
        );

        $manager->generate();
        $filePath       = $this->fileSystem->url() . '/' . $this->className . '.php';
        $fileContent    = file_get_contents($filePath);

        $this->assertTrue(file_exists($filePath));

        $this->assertContains('class ' . $this->className, $fileContent);
        $this->assertContains('extends ' . $this->extends, $fileContent);
        $this->assertContains($this->indention . 'public function ', $fileContent);
        $this->assertContains('namespace ' . $this->namespace, $fileContent);
        $this->assertContains('use Propel;' , $fileContent);
        $this->assertContains('use PDO;' , $fileContent);
        $this->assertContains(' Propel::CONNECTION_WRITE' , $fileContent);
    }

    public function testGenerateWithoutUsingNamespace()
    {
        $manager = $this->getManager();

        $manager->configure(
            $this->className,
            $this->indention,
            $this->fileSystem->url(),
            null,
            $this->extends
        );

        $manager->generate();
        $filePath       = $this->fileSystem->url() . '/' . $this->className . '.php';
        $fileContent    = file_get_contents($filePath);

        $this->assertTrue(file_exists($filePath));

        $this->assertContains('class ' . $this->className, $fileContent);
        $this->assertContains('extends ' . $this->extends, $fileContent);
        $this->assertContains($this->indention . 'public function ', $fileContent);
        $this->assertNotContains('namespace', $fileContent);
        $this->assertNotContains('use Propel;' , $fileContent);
        $this->assertNotContains('use PDO;' , $fileContent);
    }

    /**
     * @return Manager
     */
    private function getManager()
    {
        return Manager::getInstance();
    }

    /**
     * @return Manager
     */
    private function resetGenerator()
    {
        $singleton  = Manager::getInstance();
        $reflection = new ReflectionClass($singleton);
        $instance   = $reflection->getProperty('instance');

        $instance->setAccessible(true);
        $instance->setValue(null, null);
        $instance->setAccessible(false);

        return $singleton;
    }
}
