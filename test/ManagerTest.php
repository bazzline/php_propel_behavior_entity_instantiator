<?php

declare(strict_types=1);

/**
 * @author stev leibelt <artodeto@bazzline.net>
 * @since 2015-08-31
 */
namespace Test\Net\Bazzline\Propel\Behavior\EntityInstantiator;

use Net\Bazzline\Propel\Behavior\EntityInstantiator\Manager;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use RuntimeException;

class ManagerTest extends TestCase
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

    /** @var bool */
    private $useFullyQualifiedName;

    protected function setUp(): void
    {
        $this->resetGenerator();

        $this->className                = 'ClassName';
        $this->defaultConnectionMode    = 'Propel::CONNECTION_WRITE';
        $this->defaultConnectionName    = 'default_configuration_name';
        $this->extends                  = 'stdClass';
        $this->fileSystem               = vfsStream::setup();
        $this->indention                = '  ';
        $this->namespace                = 'Name\Space';
        $this->useFullyQualifiedName    = false;
    }

    public function testCallingGenerateBeforeCallingConfigure(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('you have to call configure first');
        $manager = $this->getManager();
        $manager->generate();
    }

    public function testGenerateByUsingNamespace(): void
    {
        $manager = $this->getManager();

        $manager->configure(
            $this->className,
            $this->indention,
            $this->fileSystem->url(),
            $this->namespace,
            $this->extends,
            $this->defaultConnectionMode,
            $this->defaultConnectionName,
            $this->useFullyQualifiedName
        );

        $manager->generate();
        $filePath       = $this->fileSystem->url() . '/' . $this->className . '.php';
        $fileContent    = file_get_contents($filePath);

        self::assertTrue(file_exists($filePath));

        self::assertStringContainsString('class ' . $this->className, $fileContent);
        self::assertStringContainsString('extends ' . $this->extends, $fileContent);
        self::assertStringContainsString($this->indention . 'public function ', $fileContent);
        self::assertStringContainsString('namespace ' . $this->namespace, $fileContent);
        self::assertStringContainsString('use Propel;' , $fileContent);
        self::assertStringContainsString('use PDO;' , $fileContent);
        self::assertStringContainsString('$name = \'' . $this->defaultConnectionName . '\'' , $fileContent);
        self::assertStringContainsString('$mode = ' . $this->defaultConnectionMode , $fileContent);
    }

    public function testGenerateWithoutUsingNamespace(): void
    {
        $manager = $this->getManager();

        $manager->configure(
            $this->className,
            $this->indention,
            $this->fileSystem->url(),
            null,
            $this->extends,
            $this->defaultConnectionMode,
            $this->defaultConnectionName,
            $this->useFullyQualifiedName
        );

        $manager->generate();
        $filePath       = $this->fileSystem->url() . '/' . $this->className . '.php';
        $fileContent    = file_get_contents($filePath);

        self::assertTrue(file_exists($filePath));

        self::assertStringContainsString('class ' . $this->className, $fileContent);
        self::assertStringContainsString('extends ' . $this->extends, $fileContent);
        self::assertStringContainsString($this->indention . 'public function ', $fileContent);
        self::assertStringNotContainsString('namespace', $fileContent);
        self::assertStringNotContainsString('use Propel;' , $fileContent);
        self::assertStringNotContainsString('use PDO;' , $fileContent);
        self::assertStringContainsString('$name = \'' . $this->defaultConnectionName . '\'' , $fileContent);
        self::assertStringContainsString('$mode = ' . $this->defaultConnectionMode , $fileContent);
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
