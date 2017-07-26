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

        $this->assertNull($configuration->getExtends());
        $this->assertEquals($className, $configuration->getClassName());
        $this->assertEquals('Propel::CONNECTION_WRITE', $configuration->getDefaultConnectionMode());
        $this->assertEquals('null', $configuration->getDefaultConnectionName());
        $this->assertEquals($indention, $configuration->getIndention());
        $this->assertNull($configuration->getNamespace());
        $this->assertContains($pathToOutput, $configuration->getFilePathToOutput());
        $this->assertFalse($configuration->hasExtends());
        $this->assertFalse($configuration->hasNamespace());
        //end of business logic
    }

    public function testMaximumConfiguration()
    {
        $this->markTestIncomplete('needs to be implemented.');
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

        $this->assertNull($configuration->getExtends());
        $this->assertEquals($className, $configuration->getClassName());
        $this->assertEquals('Propel::CONNECTION_WRITE', $configuration->getDefaultConnectionMode());
        $this->assertEquals('null', $configuration->getDefaultConnectionName());
        $this->assertEquals($indention, $configuration->getIndention());
        $this->assertNull($configuration->getNamespace());
        $this->assertContains($pathToOutput, $configuration->getFilePathToOutput());
        $this->assertFalse($configuration->hasExtends());
        $this->assertFalse($configuration->hasNamespace());
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