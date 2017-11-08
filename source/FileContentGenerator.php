<?php
/**
 * @author stev leibelt <artodeto@bazzline.net>
 * @since 2015-09-19
 */
namespace Net\Bazzline\Propel\Behavior\EntityInstantiator;

class FileContentGenerator
{
    /**
     * @param EntityCollection $collection
     * @param Configuration $configuration
     * @return string
     */
    public function generate(
        EntityCollection $collection,
        Configuration $configuration
    ) {
        $useStatements = $this->generateUseStatements(
            $configuration->doNotUseFullyQualifiedNames(),
            $configuration->getNamespace(),
            $collection
        );

        $content = $this->generateFileHeader(
            $configuration->getNamespace(),
            $useStatements
        );

        $content .= $this->generateClassHeader(
            $configuration->getClassName(),
            $configuration->getExtends()
        );

        $content .= $this->generateGetConnectionMethod(
            $configuration->getIndention(),
            $configuration->getDefaultConnectionMode(),
            $configuration->getDefaultConnectionName()
        );

        foreach ($collection as $entity) {
            $content .= $this->generateObjectEntityOrQueryEntityGetMethod(
                $entity,
                $configuration->getIndention(),
                $configuration->useFullyQualifiedNames()
            );
        }

        $content .= '}';

        return $content;
    }

    /**
     * @param string $className
     * @param null|string $extends
     * @return string
     * @todo find a better way to have it indented and readable
     */
    private function generateClassHeader(
        $className,
        $extends
    ) {
        $extends = ($this->isValidString($extends)) ? ' extends ' . $extends : '';

    return '
/**
 * Class ' . $className . '
 *
 * @author ' . __CLASS__ . '
 * @since ' . date('Y-m-d') . '
 * @link http://www.bazzline.net
 */
class ' . $className . $extends . '
{' . PHP_EOL;
    }

    /**
     * @param AbstractEntity $entity
     * @param string $indention
     * @param boolean $useFullyQualifiedName
     * @return string
     */
    private function generateObjectEntityOrQueryEntityGetMethod(
        AbstractEntity $entity,
        $indention,
        $useFullyQualifiedName
    ) {
        $methodName = lcfirst($entity->methodNamePrefix() . ucfirst($entity->className()));
        $className  = ($useFullyQualifiedName)
            ? '\\' . $entity->fullQualifiedClassName()
            : $entity->className();

        $content = PHP_EOL .
            $indention . '/**' . PHP_EOL .
            $indention . ' * @return ' . $className . PHP_EOL .
            $indention . ' */' . PHP_EOL .
            $indention . 'public function ' . $methodName . '()' . PHP_EOL .
            $indention . '{' . PHP_EOL;

        if ($entity instanceof ObjectEntity) {
            $content .= $indention . $indention . 'return new ' . $className . '();' . PHP_EOL;
        } else if ($entity instanceof QueryEntity) {
            $content .= $indention . $indention . 'return ' . $className . '::create();' . PHP_EOL;
        }

        $content .= $indention . '}' . PHP_EOL;

        return $content;
    }

    /**
     * @param null|string $namespace
     * @param array $uses
     * @return string
     */
    private function generateFileHeader(
        $namespace,
        $uses
    ) {
        $content = '<?php';
        $content .= ($this->isValidString($namespace))
            ? str_repeat(PHP_EOL, 2) . 'namespace ' . $namespace . ';' . PHP_EOL
            : PHP_EOL;

        $thereAreUseStatements = (!empty($uses));

        if ($thereAreUseStatements) {
            $content .= PHP_EOL;
            $content .= implode(PHP_EOL, $uses);
            $content .= PHP_EOL;
        }

        return $content;
    }

    /**
     * @param string $indention
     * @param null|string $defaultConnectionMode
     * @param null|string $defaultConnectionName
     * @return string
     * @todo find a better way to have it indented and readable
     * @todo move default values out
     */
    private function generateGetConnectionMethod(
        $indention,
        $defaultConnectionMode = null,
        $defaultConnectionName = null
    ) {

return $indention . '/**
' . $indention . ' * @param null|string $name - The data source name that is used to look up the DSN from the runtime configuration file.
' . $indention . ' * @param string $mode The connection mode (this applies to replication systems).
' . $indention . ' * @return PDO
' . $indention . ' */
' . $indention . 'public function getConnection($name = ' . $defaultConnectionName . ', $mode = ' . $defaultConnectionMode . ')
' . $indention . '{
' . (str_repeat($indention, 2)) . 'return Propel::getConnection($name, $mode);
' . $indention . '}' . PHP_EOL;
    }

    /**
     * @param boolean $includeCollection
     * @param null|string $namespace
     * @param EntityCollection $collection
     * @return array
     */
    private function generateUseStatements(
        $includeCollection,
        $namespace,
        EntityCollection $collection
    ) {
        $uses = [];

        if ($this->isValidString($namespace)) {
            $uses[] = 'use Propel;';
            $uses[] = 'use PDO;';
        }

        if ($includeCollection) {
            foreach ($collection as $entity) {
                $uses[] = 'use ' . $entity->fullQualifiedClassName() . ';';
            }
        }

        natsort($uses);

        return $uses;
    }

    /**
     * @param null|string $namespace
     * @return bool
     */
    private function isValidString($namespace)
    {
        return ((is_string($namespace))
            && (strlen($namespace) > 0));
    }
}
