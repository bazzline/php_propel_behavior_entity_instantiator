<?php

namespace Net\Bazzline\Propel\Behavior\EntityInstantiator;

use InvalidArgumentException;
use RuntimeException;

class EntityInstantiatorGenerator
{
    /** @var bool */
    private $configurationNotDone;

    /** @var string */
    private $className;

    /** @var EntityCollection */
    private $collection;

    /** @var null|string */
    private $extends;

    /** @var bool */
    private $generationDone;

    /** @var string */
    private $indention;

    /** @var null|string */
    private $namespace;

    /** @var string */
    private $pathNameForOutputFile;

    /** @var EntityInstantiatorGenerator */
    private static $instance;

    protected function __construct()
    {
        //@todo maybe replace by a state machine instance?
        $this->configurationNotDone = true;
        $this->generationDone       = false;
    }

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
     * @return EntityInstantiatorGenerator
     */
    public static function getInstance()
    {
        if (!(self::$instance instanceof EntityInstantiatorGenerator)) {
            self::$instance = new static();
        }

        return self::$instance;
    }

    /**
     * @param string $absolutePathToOutput
     * @param string $className
     * @param EntityCollection $collection
     * @param null|string $extends
     * @param null|string $indention
     * @param string $namespace
     * @throws InvalidArgumentException
     * @todo maybe put them together in a InstantiatorConfiguration?
     */
    public function configure($absolutePathToOutput, $className, EntityCollection $collection, $extends = null, $indention = null, $namespace)
    {
        $this->setClassName($className);
        $this->setCollection($collection);

        if (!is_null($extends)) {
            $this->setExtends($extends);
        }

        $this->setIndention($indention);

        if (!is_null($namespace)) {
            $this->setNamespace($namespace);
        }

        $this->tryToCreatePathNameToFileOutputOrThrowInvalidArgumentException($absolutePathToOutput);
        $this->configurationNotDone = false;
    }

    /**
     * @return bool
     */
    public function isNotConfigured()
    {
        return ($this->configurationNotDone);
    }

    /**
     * @param AbstractEntity $entity
     * @return $this
     * @throws InvalidArgumentException
     */
    public function add(AbstractEntity $entity)
    {
        $this->collection->add($entity);
        $this->generationDone = false;

        return $this;
    }

    /**
     * @throws RuntimeException
     */
    public function generate()
    {
        //begin of dependencies
        $className  = $this->className;
        $collection = $this->collection;
        $extends    = $this->extends;
        $fileName   = $this->pathNameForOutputFile;
        $indention  = $this->indention;
        $namespace  = $this->namespace;
        //end of dependencies

        $this->throwRuntimeExceptionIfConfigurationIsNotDone();
        $content = $this->generateContent($collection, $className, $extends, $indention, $namespace);
        $this->tryToWriteContentOrThrowRuntimeException($fileName, $content);
        $this->generationDone = true;
    }

    /**
     * @param EntityCollection $collection
     * @param string $className
     * @param null|string $extends
     * @param string $indention
     * @param null|string $namespace
     * @return string
     */
    private function generateContent(EntityCollection $collection, $className, $extends, $indention, $namespace)
    {
        $hasExtends     = !(is_null($extends));
        $hasNamespace   = !(is_null($namespace));

        $extends        = ($hasExtends) ? ' extends ' . $extends : '';

        $content        = '<?php';
        $content       .= ($hasNamespace)
            ? str_repeat(PHP_EOL, 2) . 'namespace ' . $this->namespace . ';' . PHP_EOL
            : PHP_EOL;

//@todo find a better way to have it indented and readable
$content .= '
/**
 * Class ' . $className . '
 *
 * @author ' . __NAMESPACE__ . __CLASS__ . '
 * @since ' . date('Y-m-d') . '
 * @see http://www.bazzline.net
 */
class ' . $className . $extends . '
{
' . $indention . '/**
' . $indention . ' * @return PDO
' . $indention . ' */
' . $indention . 'public function getConnection()
' . $indention . '{
' . (str_repeat($indention, 2)) . 'return Propel::getConnection();
' . $indention . '}
';

        foreach ($collection as $entity) {
            $methodName = lcfirst($entity->methodNamePrefix() . ucfirst($entity->methodName()));
            $content   .= PHP_EOL .
                $indention . '/**' . PHP_EOL .
                $indention . ' * @return \\' . $entity->fullQualifiedClassName() . PHP_EOL .
                $indention . ' */' . PHP_EOL .
                $indention . 'public function ' . $methodName . '()' . PHP_EOL .
                $indention . '{' . PHP_EOL;
            if ($entity instanceof ObjectEntity) {
                $content .= $indention . $indention . 'return new \\' . $entity->fullQualifiedClassName() . '();' . PHP_EOL;
            } else if ($entity instanceof QueryEntity) {
                $content .= $indention . $indention . 'return \\' . $entity->fullQualifiedClassName() . '::create();' . PHP_EOL;
            }
            $content .= $indention . '}' . PHP_EOL;
        }

        $content .= '}';

        return $content;
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
     * @param string $className
     */
    private function setClassName($className)
    {
        $this->throwInvalidArgumentExceptionIfStringIsNotValid($className);
        $this->className = $className;
    }

    /**
     * @param EntityCollection $collection
     */
    private function setCollection(EntityCollection $collection)
    {
        $this->collection = $collection;
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

        $this->pathNameForOutputFile = $path . DIRECTORY_SEPARATOR . $this->className . '.php';
    }

    /**
     * @return bool
     */
    private function noGenerationWasDone()
    {
        return (!$this->generationDone);
    }

    /**
     * @return bool
     */
    private function callGenerate()
    {
        return (!$this->configurationNotDone
            && $this->noGenerationWasDone());
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
     * @throws RuntimeException
     */
    private function throwRuntimeExceptionIfConfigurationIsNotDone()
    {
        if ($this->configurationNotDone) {
            throw new RuntimeException(
                'you have to call configure first'
            );
        }
    }
}