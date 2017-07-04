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

    /**
     * @return Configuration
     */
    private function getNewConfiguration()
    {
        return new Configuration();
    }
}