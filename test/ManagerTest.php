<?php
/**
 * @author stev leibelt <artodeto@bazzline.net>
 * @since 2015-08-31
 */
namespace Test\Net\Bazzline\Propel\Behavior\EntityInstantiator;

use Mockery;
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
    private $defaultConnectionMode;

    /** @var string */
    private $defaultConnectionName;

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

        $this->className                = 'ClassName';
        $this->defaultConnectionMode    = 'Propel::CONNECTION_WRITE';
        $this->defaultConnectionName    = 'default_configuration_name';
        $this->extends                  = 'stdClass';
        $this->fileSystem               = vfsStream::setup();
        $this->indention                = '  ';
        $this->namespace                = 'Name\Space';
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
            $this->extends,
            $this->defaultConnectionMode,
            $this->defaultConnectionName
        );

        $manager->generate();
        $filePath       = $this->fileSystem->url() . '/' . $this->className . '.php';
        $fileContent    = file_get_contents($filePath);

        self::assertTrue(file_exists($filePath));

        self::assertContains('class ' . $this->className, $fileContent);
        self::assertContains('extends ' . $this->extends, $fileContent);
        self::assertContains($this->indention . 'public function ', $fileContent);
        self::assertContains('namespace ' . $this->namespace, $fileContent);
        self::assertContains('use Propel;' , $fileContent);
        self::assertContains('use PDO;' , $fileContent);
        self::assertContains('$name = \'' . $this->defaultConnectionName . '\'' , $fileContent);
        self::assertContains('$mode = ' . $this->defaultConnectionMode , $fileContent);
    }

    public function testGenerateWithoutUsingNamespace()
    {
        $manager = $this->getManager();

        $manager->configure(
            $this->className,
            $this->indention,
            $this->fileSystem->url(),
            null,
            $this->extends,
            $this->defaultConnectionMode,
            $this->defaultConnectionName
        );

        $manager->generate();
        $filePath       = $this->fileSystem->url() . '/' . $this->className . '.php';
        $fileContent    = file_get_contents($filePath);

        self::assertTrue(file_exists($filePath));

        self::assertContains('class ' . $this->className, $fileContent);
        self::assertContains('extends ' . $this->extends, $fileContent);
        self::assertContains($this->indention . 'public function ', $fileContent);
        self::assertNotContains('namespace', $fileContent);
        self::assertNotContains('use Propel;' , $fileContent);
        self::assertNotContains('use PDO;' , $fileContent);
        self::assertContains('$name = \'' . $this->defaultConnectionName . '\'' , $fileContent);
        self::assertContains('$mode = ' . $this->defaultConnectionMode , $fileContent);
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
