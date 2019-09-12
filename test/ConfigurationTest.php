<?php declare(strict_types=1);

/**
 * @author stev leibelt <artodeto@bazzline.net>
 * @since 2015-09-20
 */
namespace Test\Net\Bazzline\Propel\Behavior\EntityInstantiator;

use Net\Bazzline\Propel\Behavior\EntityInstantiator\Configuration;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_TestCase;

class ConfigurationTest extends TestCase
{
    public function testIsNotConfigured(): void
    {
        $configuration = $this->getNewConfiguration();

        static::assertFalse($configuration->isConfigured());
        static::assertTrue($configuration->isNotConfigured());

        $configuration->configure(
            __CLASS__,
            '    ',
            __DIR__
        );

        static::assertTrue($configuration->isConfigured());
        static::assertFalse($configuration->isNotConfigured());
    }

    public function testMinimumConfiguration(): void
    {
        //begin of dependencies
        $className      = __CLASS__;
        $configuration  = $this->getNewConfiguration();
        $indention      = '   ';
        $pathToOutput   = __DIR__;
        //end of dependencies

        //begin of business logic
        $configuration->configure(
            $className,
            $indention,
            $pathToOutput
        );

        self::assertTrue($configuration->doNotUseFullyQualifiedNames());
        self::assertNull($configuration->getExtends());
        self::assertEquals($className, $configuration->getClassName());
        self::assertEquals('Propel::CONNECTION_WRITE', $configuration->getDefaultConnectionMode());
        self::assertEquals('null', $configuration->getDefaultConnectionName());
        self::assertEquals($indention, $configuration->getIndention());
        self::assertNull($configuration->getNamespace());
        self::assertStringContainsString($pathToOutput, $configuration->getFilePathToOutput());
        self::assertFalse($configuration->hasExtends());
        self::assertFalse($configuration->hasNamespace());
        self::assertFalse($configuration->useFullyQualifiedNames());
        //end of business logic
    }

    public function testMaximumConfiguration(): void
    {
        //begin of dependencies
        $className              = __CLASS__;
        $configuration          = $this->getNewConfiguration();
        $defaultConnectionName  = 'Propel::CONNECTION_FOO';
        $defaultConnectionMode  = 'foo_bar';
        $extends                = 'BarFoo';
        $indention              = '   ';
        $pathToOutput           = __DIR__;
        $namespace              = 'Foo\Bar';
        $useFullyQualifiedName  = true;
        //end of dependencies

        //begin of business logic
        $configuration->configure(
            $className,
            $indention,
            $pathToOutput,
            $namespace,
            $extends,
            $defaultConnectionMode,
            $defaultConnectionName,
            $useFullyQualifiedName
        );

        self::assertFalse($configuration->doNotUseFullyQualifiedNames());
        self::assertEquals($extends, $configuration->getExtends());
        self::assertEquals($className, $configuration->getClassName());
        self::assertEquals($defaultConnectionMode, $configuration->getDefaultConnectionMode());
        self::assertEquals('\'' . $defaultConnectionName . '\'', $configuration->getDefaultConnectionName());
        self::assertEquals($indention, $configuration->getIndention());
        self::assertEquals($namespace, $configuration->getNamespace());
        self::assertStringContainsString($pathToOutput, $configuration->getFilePathToOutput());
        self::assertTrue($configuration->hasExtends());
        self::assertTrue($configuration->hasNamespace());
        self::assertTrue($configuration->useFullyQualifiedNames());
        //end of business logic
    }

    /**
     * @return Configuration
     */
    private function getNewConfiguration()
    {
        return new Configuration();
    }
}
