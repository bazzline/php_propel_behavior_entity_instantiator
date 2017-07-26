<?php

namespace Net\Bazzline\Propel\Behavior\EntityInstantiator;

use InvalidArgumentException;
use RuntimeException;

class Manager
{
    /** @var Configuration */
    private $configuration;

    /** @var EntityCollection */
    private $collection;

    /** @var FileContentGenerator */
    private $generator;

    /** @var bool */
    private $generationIsDone;

    /** @var Manager */
    private static $instance;

    /**
     * @throws RuntimeException
     */
    public function __destruct()
    {
        if ($this->callGenerate()) {
            $this->generate();
        }
    }

    private function __clone() {}

    /**
     * @return Manager
     */
    public static function getInstance()
    {
        if (!(self::$instance instanceof Manager)) {
            self::$instance = new static();
            self::$instance->reset();
        }

        return self::$instance;
    }

    /**
     * @param AbstractEntity $entity
     * @throws InvalidArgumentException
     */
    public function add(AbstractEntity $entity)
    {
        $this->collection->add($entity);
    }

    /**
     * @param string $className
     * @param string $indention
     * @param string $pathToOutput
     * @param null|string $namespace
     * @param null|string $extends
     * @param null|string $defaultConnectionMode
     * @param null|string $defaultConnectionName
     * @throws InvalidArgumentException
     */
    public function configure(
        $className,
        $indention,
        $pathToOutput,
        $namespace = null,
        $extends = null,
        $defaultConnectionMode = null,
        $defaultConnectionName = null
    ) {
        $this->configuration->configure(
            $className,
            $indention,
            $pathToOutput,
            $namespace,
            $extends,
            $defaultConnectionMode,
            $defaultConnectionName
        );
    }

    /**
     * @throws RuntimeException
     */
    public function generate()
    {
        //begin of dependencies
        $configuration  = $this->configuration;
        $generator      = $this->generator;
        $collection     = $this->collection;
        $fileName       = $configuration->getFilePathToOutput();
        //end of dependencies

        $this->throwRuntimeExceptionIfConfigurationIsNotDone();
        $content = $generator->generate($collection, $configuration);
        $this->tryToWriteContentOrThrowRuntimeException($fileName, $content);
        $this->generationIsDone = true;
    }

    /**
     * @return bool
     */
    public function isNotConfigured()
    {
        return $this->configuration->isNotConfigured();
    }

    public function reset()
    {
        $this->collection       = new EntityCollection();
        $this->configuration    = new Configuration();
        $this->generator        = new FileContentGenerator();
        $this->generationIsDone = false;
    }

    /**
     * @param string $fileName
     * @param string $content
     */
    private function tryToWriteContentOrThrowRuntimeException($fileName, $content)
    {
        $contentCouldBeNotWritten = (file_put_contents($fileName, $content) === false);

        if ($contentCouldBeNotWritten) {
            throw new RuntimeException(
                'could not write content to "' . $fileName . '"'
            );
        }
    }

    /**
     * @return bool
     */
    private function noGenerationWasDone()
    {
        return (!$this->generationIsDone);
    }

    /**
     * @return bool
     */
    private function callGenerate()
    {
        return ($this->configuration->isConfigured()
            && $this->noGenerationWasDone());
    }

    /**
     * @throws RuntimeException
     */
    private function throwRuntimeExceptionIfConfigurationIsNotDone()
    {
        if ($this->isNotConfigured()) {
            throw new RuntimeException(
                'you have to call configure first'
            );
        }
    }
}
