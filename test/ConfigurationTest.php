<?php
/**
 * @author stev leibelt <artodeto@bazzline.net>
 * @since 2015-09-20
 */
namespace Test\Net\Bazzline\Propel\Behavior\EntityInstantiator;

use Net\Bazzline\Propel\Behavior\EntityInstantiator\Configuration;
use PHPUnit_Framework_TestCase;

class ConfigurationTest extends PHPUnit_Framework_TestCase
{
    public function testIsNotConfigured()
    {
        $configuration = $this->getNewConfiguration();

        $this->assertFalse($configuration->isConfigured());
        $this->assertTrue($configuration->isNotConfigured());

        $configuration->configure(
            __CLASS__,
            '    ',
            __DIR__
        );

        $this->assertTrue($configuration->isConfigured());
        $this->assertFalse($configuration->isNotConfigured());
    }

    public function testMinimumConfiguration()
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

        self::assertNull($configuration->getExtends());
        self::assertEquals($className, $configuration->getClassName());
        self::assertEquals('Propel::CONNECTION_WRITE', $configuration->getDefaultConnectionMode());
        self::assertEquals('null', $configuration->getDefaultConnectionName());
        self::assertEquals($indention, $configuration->getIndention());
        self::assertNull($configuration->getNamespace());
        self::assertContains($pathToOutput, $configuration->getFilePathToOutput());
        self::assertFalse($configuration->hasExtends());
        self::assertFalse($configuration->hasNamespace());
        //end of business logic
    }

    public function testMaximumConfiguration()
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
        //end of dependencies

        //begin of business logic
        $configuration->configure(
            $className,
            $indention,
            $pathToOutput,
            $namespace,
            $extends,
            $defaultConnectionMode,
            $defaultConnectionName
        );

        self::assertEquals($extends, $configuration->getExtends());
        self::assertEquals($className, $configuration->getClassName());
        self::assertEquals($defaultConnectionMode, $configuration->getDefaultConnectionMode());
        self::assertEquals('\'' . $defaultConnectionName . '\'', $configuration->getDefaultConnectionName());
        self::assertEquals($indention, $configuration->getIndention());
        self::assertEquals($namespace, $configuration->getNamespace());
        self::assertContains($pathToOutput, $configuration->getFilePathToOutput());
        self::assertTrue($configuration->hasExtends());
        self::assertTrue($configuration->hasNamespace());
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
