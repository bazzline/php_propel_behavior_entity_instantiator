<?php
/**
 * @author stev leibelt <artodeto@bazzline.net>
 * @since 2015-09-19
 */
namespace Net\Bazzline\Propel\Behavior\EntityInstantiator;

use InvalidArgumentException;

class Configuration
{
    /** @var string */
    private $className;

    /** @var string */
    private $extends;

    /** @var string */
    private $indention;

    /** @var bool */
    private $isConfigured = false;

    /** @var string */
    private $namespace;

    /** @var string */
    private $pathToOutput;

    /**
     * @param string $className
     * @param string $indention
     * @param string $pathToOutput
     * @param null|string $namespace
     * @param null|string $extends
     * @throws InvalidArgumentException
     */
    public function configure($className, $indention, $pathToOutput, $namespace = null, $extends = null)
    {
        $this->setClassName($className);

        if (!is_null($extends)) {
            $this->setExtends($extends);
        }

        $this->setIndention($indention);

        if (!is_null($namespace)) {
            $this->setNamespace($namespace);
        }

        $this->tryToCreatePathNameToFileOutputOrThrowInvalidArgumentException($pathToOutput);
        $this->isConfigured = true;
    }

    /**
     * @return string
     */
    public function getClassName()
    {
        return $this->className;
    }

    /**
     * @return string
     */
    public function getExtends()
    {
        return $this->extends;
    }

    /**
     * @return string
     */
    public function getIndention()
    {
        return $this->indention;
    }

    /**
     * @return string
     */
    public function getNamespace()
    {
        return $this->namespace;
    }

    /**
     * @return string
     */
    public function getPathToOutput()
    {
        return $this->pathToOutput;
    }

    /**
     * @return bool
     */
    public function isConfigured()
    {
        return ($this->isConfigured);
    }

    /**
     * @return bool
     */
    public function isNotConfigured()
    {
        return (!$this->isConfigured);
    }

    /**
     * @param string $className
     */
    private function setClassName($className)
    {
        $this->throwInvalidArgumentExceptionIfStringIsNotValid($className);
        $this->className = $className;
    }

    /**
     * @param string $extends
     */
    private function setExtends($extends)
    {
        $this->throwInvalidArgumentExceptionIfStringIsNotValid($extends);
        $this->extends = $extends;
    }

    /**
     * @param string $indention
     */
    private function setIndention($indention)
    {
        $this->throwInvalidArgumentExceptionIfStringIsNotValid($indention);
        $this->indention = $indention;
    }

    /**
     * @param string $namespace
     */
    private function setNamespace($namespace)
    {
        $this->throwInvalidArgumentExceptionIfStringIsNotValid($namespace);
        $this->namespace = $namespace;
    }

    /**
     * @param string $string
     * @throws InvalidArgumentException
     */
    private function throwInvalidArgumentExceptionIfStringIsNotValid($string)
    {
        if (!is_string($string)) {
            throw new InvalidArgumentException(
                'provided variable must be a string "' . var_export($string, true) . '"" given'
            );
        }

        if (strlen($string) < 1) {
            throw new InvalidArgumentException(
                'provided string must contain at least one character'
            );
        }
    }

    /**
     * @param string $path
     * @throws InvalidArgumentException
     */
    private function tryToCreatePathNameToFileOutputOrThrowInvalidArgumentException($path)
    {
        if (!is_dir($path)) {
            throw new InvalidArgumentException(
                'provided path "' . $path . '" is not a directory'
            );
        }

        if (!is_writable($path)) {
            throw new InvalidArgumentException(
                'provided path "' . $path . '" is not writable'
            );
        }

        $this->pathToOutput = $path . DIRECTORY_SEPARATOR . $this->className . '.php';
    }
}