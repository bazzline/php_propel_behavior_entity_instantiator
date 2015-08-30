<?php

namespace Net\Bazzline\Propel\Behavior\EntityInstantiator;

/**
 * @author stev leibelt <artodeto@bazzline.net>
 * @since 2015-08-29
 */
abstract class AbstractEntity
{
    /** @var string */
    private $databaseName;

    /** @var string */
    private $fullQualifiedClassName;

    /** @var string */
    private $methodName;

    /** @var string */
    private $methodNamePrefix;

    /**
     * @param string $databaseName
     * @param string $fullQualifiedClassName
     * @param string $methodName
     * @param string $methodNamePrefix
     */
    public function __construct($databaseName, $fullQualifiedClassName, $methodName, $methodNamePrefix)
    {
        $this->databaseName             = $databaseName;
        $this->fullQualifiedClassName   = $fullQualifiedClassName;
        $this->methodName               = $methodName;
        $this->methodNamePrefix         = $methodNamePrefix;
    }

    /**
     * @return string
     */
    public function databaseName()
    {
        return $this->databaseName;
    }

    /**
     * @return string
     */
    public function fullQualifiedClassName()
    {
        return $this->fullQualifiedClassName;
    }

    /**
     * @return string
     */
    public function methodName()
    {
        return $this->methodName;
    }

    /**
     * @return string
     */
    public function methodNamePrefix()
    {
        return $this->methodNamePrefix;
    }
}
