<?php

declare(strict_types=1);

namespace Net\Bazzline\Propel\Behavior\EntityInstantiator;

/**
 * @author stev leibelt <artodeto@bazzline.net>
 * @since 2015-08-29
 */
abstract class AbstractEntity
{
    /** @var string */
    private $className;

    /** @var string */
    private $databaseName;

    /** @var string */
    private $fullQualifiedClassName;

    /** @var string */
    private $methodNamePrefix;

    /**
     * @param string $className
     * @param string $databaseName
     * @param string $fullQualifiedClassName
     * @param string $methodNamePrefix
     */
    public function __construct(
        $className,
        $databaseName,
        $fullQualifiedClassName,
        $methodNamePrefix
    ) {
        $this->className                = $className;
        $this->databaseName             = $databaseName;
        $this->fullQualifiedClassName   = $fullQualifiedClassName;
        $this->methodNamePrefix         = $methodNamePrefix;
    }

    /**
     * @return string
     */
    public function className()
    {
        return $this->className;
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
    public function methodNamePrefix()
    {
        return $this->methodNamePrefix;
    }
}
