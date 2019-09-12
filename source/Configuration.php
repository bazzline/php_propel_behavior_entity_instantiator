<?php

declare(strict_types=1);

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

    /** @var null|string */
    private $defaultConnectionMode;

    /** @var null|string */
    private $defaultConnectionName;

    /** @var string */
    private $extends;

    /** @var string */
    private $filePathToOutput;

    /** @var string */
    private $indention;

    /** @var bool */
    private $isConfigured = false;

    /** @var string */
    private $namespace;

    /** @var bool */
    private $useFullyQualifiedNames;

    /**
     * @param string $className
     * @param string $indention
     * @param string $pathToOutput
     * @param null|string $namespace
     * @param null|string $extends
     * @param null|string $defaultConnectionMode
     * @param null|string $defaultConnectionName
     * @param null|bool $useFullyQualifiedNames
     * @throws InvalidArgumentException
     */
    public function configure(
        $className,
        $indention,
        $pathToOutput,
        $namespace = null,
        $extends = null,
        $defaultConnectionMode = null,
        $defaultConnectionName = null,
        $useFullyQualifiedNames = null
    ): void {
        $this->setClassName($className);

        if (!is_null($extends)) {
            $this->setExtends($extends);
        }

        $this->setIndention($indention);

        if (!is_null($namespace)) {
            $this->setNamespace($namespace);
        }

        if (!is_null($defaultConnectionMode)) {
            $this->setDefaultConnectionMode($defaultConnectionMode);
        } else {
            $this->setDefaultConnectionMode('Propel::CONNECTION_WRITE');
        }

        if (!is_null($defaultConnectionName)) {
            $this->setDefaultConnectionName('\'' . $defaultConnectionName . '\'');
        } else {
            $this->setDefaultConnectionName('null');
        }

        if (!is_null($useFullyQualifiedNames)) {
            $this->setUseFullyQualifiedName($useFullyQualifiedNames);
        } else {
            $this->setUseFullyQualifiedName(false);
        }

        $this->tryToCreatePathNameToFileOutputOrThrowInvalidArgumentException($pathToOutput);
        $this->isConfigured = true;
    }

    /**
     * @return bool
     */
    public function doNotUseFullyQualifiedNames()
    {
        return ($this->useFullyQualifiedNames === false);
    }

    /**
     * @return string
     */
    public function getClassName()
    {
        return $this->className;
    }

    /**
     * @return null|string
     */
    public function getDefaultConnectionMode()
    {
        return $this->defaultConnectionMode;
    }

    /**
     * @return null|string
     */
    public function getDefaultConnectionName()
    {
        return $this->defaultConnectionName;
    }
    /**
     * @return null|string
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
     * @return null|string
     */
    public function getNamespace()
    {
        return $this->namespace;
    }

    /**
     * @return string
     */
    public function getFilePathToOutput()
    {
        return $this->filePathToOutput;
    }

    /**
     * @return bool
     */
    public function hasExtends()
    {
        return (!is_null($this->extends));
    }

    /**
     * @return bool
     */
    public function hasNamespace()
    {
        return (!is_null($this->namespace));
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
     * @return bool
     */
    public function useFullyQualifiedNames()
    {
        return ($this->useFullyQualifiedNames === true);
    }

    /**
     * @param string $className
     * @throws InvalidArgumentException
     */
    private function setClassName($className): void
    {
        $this->throwInvalidArgumentExceptionIfStringIsNotValid($className);
        $this->className = $className;
    }

    /**
     * @param string $defaultConnectionMode
     * @throws InvalidArgumentException
     */
    private function setDefaultConnectionMode($defaultConnectionMode): void
    {
        $this->throwInvalidArgumentExceptionIfStringIsNotValid($defaultConnectionMode);
        $this->defaultConnectionMode = $defaultConnectionMode;
    }

    /**
     * @param string $defaultConnectionName
     * @throws InvalidArgumentException
     */
    private function setDefaultConnectionName($defaultConnectionName): void
    {
        $this->throwInvalidArgumentExceptionIfStringIsNotValid($defaultConnectionName);
        $this->defaultConnectionName = $defaultConnectionName;
    }

    /**
     * @param string $extends
     * @throws InvalidArgumentException
     */
    private function setExtends($extends): void
    {
        $this->throwInvalidArgumentExceptionIfStringIsNotValid($extends);
        $this->extends = $extends;
    }

    /**
     * @param string $indention
     * @throws InvalidArgumentException
     */
    private function setIndention($indention): void
    {
        $this->throwInvalidArgumentExceptionIfStringIsNotValid($indention);
        $this->indention = $indention;
    }

    /**
     * @param string $namespace
     * @throws InvalidArgumentException
     */
    private function setNamespace($namespace): void
    {
        $this->throwInvalidArgumentExceptionIfStringIsNotValid($namespace);
        $this->namespace = $namespace;
    }

    /**
     * @param $useFullyQualifiedName
     * @throws InvalidArgumentException
     */
    private function setUseFullyQualifiedName($useFullyQualifiedName): void
    {
        if (!is_bool($useFullyQualifiedName)) {
            throw new InvalidArgumentException(
                'provided variable must be a boolean "' . var_export($useFullyQualifiedName, true) . '"" given'
            );
        }
        $this->useFullyQualifiedNames = $useFullyQualifiedName;
    }

    /**
     * @param string $string
     * @throws InvalidArgumentException
     */
    private function throwInvalidArgumentExceptionIfStringIsNotValid($string): void
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
    private function tryToCreatePathNameToFileOutputOrThrowInvalidArgumentException($path): void
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

        $this->filePathToOutput = $path . DIRECTORY_SEPARATOR . $this->className . '.php';
    }
}
