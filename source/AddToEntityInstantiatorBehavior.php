<?php

use Net\Bazzline\Propel\Behavior\EntityInstantiator\Manager;
use Net\Bazzline\Propel\Behavior\EntityInstantiator\ObjectEntity;
use Net\Bazzline\Propel\Behavior\EntityInstantiator\QueryEntity;

//@todo do we need this if dependencies are managed via composer and composer autoloader?
$pathToClasses = __DIR__ . '/';

require_once($pathToClasses . 'AbstractEntity.php');
require_once($pathToClasses . 'Configuration.php');
require_once($pathToClasses . 'EntityCollection.php');
require_once($pathToClasses . 'FileContentGenerator.php');
require_once($pathToClasses . 'Manager.php');
require_once($pathToClasses . 'ObjectEntity.php');
require_once($pathToClasses . 'QueryEntity.php');

/**
 * @author stev leibelt <artodeto@bazzline.net>
 * @since 2015-08-02
 * @todo make parameters optional (only set them when they are set) - this would enable it to define everything in the database scope and the rest in the table scope without overwriting database scoped things
 */
class AddToEntityInstantiatorBehavior extends Behavior
{
    const PARAMETER_ENTITY_INSTANTIATOR_ADD_IT_TO_ENTITY_INSTANTIATOR   = 'entity_instantiator_add_to_entity_instantiator';
    const PARAMETER_ENTITY_DEFAULT_CONNECTION_MODE          = 'entity_default_connection_mode';
    const PARAMETER_ENTITY_DEFAULT_CONNECTION_NAME          = 'entity_default_connection_name';
    const PARAMETER_ENTITY_INSTANTIATOR_CLASS_NAME                      = 'entity_instantiator_class_name';
    const PARAMETER_ENTITY_INSTANTIATOR_EXTENDS                         = 'entity_instantiator_extends';
    const PARAMETER_ENTITY_INSTANTIATOR_INDENTION                       = 'entity_instantiator_indention';
    const PARAMETER_ENTITY_INSTANTIATOR_NAMESPACE                       = 'entity_instantiator_namespace';
    const PARAMETER_ENTITY_INSTANTIATOR_PATH_TO_OUTPUT                  = 'entity_instantiator_path_to_output';
    const PARAMETER_ENTITY_INSTANTIATOR_METHOD_NAME_PREFIX              = 'entity_instantiator_method_name_prefix';

    /** @var array */
    protected $parameters = array(
        self::PARAMETER_ENTITY_INSTANTIATOR_ADD_IT_TO_ENTITY_INSTANTIATOR    => 'true',
        self::PARAMETER_ENTITY_DEFAULT_CONNECTION_MODE          => null,
        self::PARAMETER_ENTITY_DEFAULT_CONNECTION_NAME          => null,
        self::PARAMETER_ENTITY_INSTANTIATOR_CLASS_NAME          => 'DatabaseEntityInstantiator',
        self::PARAMETER_ENTITY_INSTANTIATOR_INDENTION           => '    ',
        self::PARAMETER_ENTITY_INSTANTIATOR_NAMESPACE           => null,
        self::PARAMETER_ENTITY_INSTANTIATOR_PATH_TO_OUTPUT      => 'data',
        self::PARAMETER_ENTITY_INSTANTIATOR_METHOD_NAME_PREFIX               => null
    );

    /**
     * @param DataModelBuilder $builder
     * @return string
     */
    public function queryMethods($builder)
    {
        $this->addQueryToGenerator($builder);

        return '';
    }

    /**
     * @param DataModelBuilder $builder
     * @return string
     */
    public function objectMethods($builder)
    {
        $this->addObjectToGenerator($builder);

        return '';
    }

    /**
     * @param DataModelBuilder $builder
     */
    public function addObjectToGenerator(DataModelBuilder $builder)
    {
        if ($this->addIt()) {
            $manager    = $this->getManager();
            $entity     = $this->buildEntityFromObject($builder);
            $manager->add($entity);
        }
    }

    /**
     * @param DataModelBuilder $builder
     */
    public function addQueryToGenerator(DataModelBuilder $builder)
    {
        if ($this->addIt()) {
            $manager    = $this->getManager();
            $entity     = $this->buildEntityFromQuery($builder);
            $manager->add($entity);
        }
    }

    /**
     * @param DataModelBuilder $builder
     * @return ObjectEntity
     */
    private function buildEntityFromObject(DataModelBuilder $builder)
    {
        $methodNamePrefix = $this->returnDatabaseNameIfMethodNamePrefixIsNotProvided($builder);

        return new ObjectEntity(
            $builder->getStubObjectBuilder()->getClassname(),
            $builder->getDatabase()->getName(),
            $builder->getStubObjectBuilder()->getFullyQualifiedClassname(),
            $methodNamePrefix
        );
    }

    /**
     * @param DataModelBuilder $builder
     * @return QueryEntity
     */
    private function buildEntityFromQuery(DataModelBuilder $builder)
    {
        $methodNamePrefix = $this->returnDatabaseNameIfMethodNamePrefixIsNotProvided($builder);

        return new QueryEntity(
            $builder->getStubQueryBuilder()->getClassname(),
            $builder->getDatabase()->getName(),
            $builder->getStubQueryBuilder()->getFullyQualifiedClassname(),
            $methodNamePrefix
        );
    }

    /**
     * @param DataModelBuilder $builder
     * @return string
     */
    private function returnDatabaseNameIfMethodNamePrefixIsNotProvided(DataModelBuilder $builder)
    {
        $methodNamePrefix = (is_null($this->parameters[self::PARAMETER_ENTITY_INSTANTIATOR_METHOD_NAME_PREFIX]))
            ? 'create' . ucfirst($builder->getDatabase()->getName())
            : $this->parameters[self::PARAMETER_ENTITY_INSTANTIATOR_METHOD_NAME_PREFIX];

        return $methodNamePrefix;
    }

    /**
     * @return Manager
     */
    private function getManager()
    {
        $manager = Manager::getInstance();

        if ($manager->isNotConfigured()) {
            $pathToOutput   = $this->parameters[self::PARAMETER_ENTITY_INSTANTIATOR_PATH_TO_OUTPUT];
            $isAbsolutePath = (strncmp($pathToOutput, DIRECTORY_SEPARATOR, strlen(DIRECTORY_SEPARATOR)) === 0);    //like /foo/bar
            $isResource     = (strpos($pathToOutput, '://') !== false);  //like vfs://

            $isAbsolutePathOrResource   = ($isAbsolutePath || $isResource);

            $absolutePathToOutput   = ($isAbsolutePathOrResource)
                ? $pathToOutput
                : getcwd() . (str_repeat(DIRECTORY_SEPARATOR . '..', 4)) . DIRECTORY_SEPARATOR . $pathToOutput;
            $className              = $this->parameters[self::PARAMETER_ENTITY_INSTANTIATOR_CLASS_NAME];
            $defaultConnectionMode  = $this->parameters[self::PARAMETER_ENTITY_DEFAULT_CONNECTION_MODE];
            $defaultConnectionName  = $this->parameters[self::PARAMETER_ENTITY_DEFAULT_CONNECTION_NAME];
            $extends                = $this->parameters[self::PARAMETER_ENTITY_INSTANTIATOR_EXTENDS];
            $indention              = $this->parameters[self::PARAMETER_ENTITY_INSTANTIATOR_INDENTION];
            $namespace              = $this->parameters[self::PARAMETER_ENTITY_INSTANTIATOR_NAMESPACE];

            $manager->configure(
                $className,
                $indention,
                $absolutePathToOutput,
                $namespace,
                $extends,
                $defaultConnectionMode,
                $defaultConnectionName
            );
        }

        return $manager;
    }

    /**
     * @return bool
     */
    private function addIt()
    {
        return (isset($this->parameters[self::PARAMETER_ENTITY_INSTANTIATOR_ADD_IT_TO_ENTITY_INSTANTIATOR]))
            ? ($this->parameters[self::PARAMETER_ENTITY_INSTANTIATOR_ADD_IT_TO_ENTITY_INSTANTIATOR] === 'true')
            : false;
    }
}
