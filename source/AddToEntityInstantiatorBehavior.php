<?php

//@todo do we need this if dependencies are managed via composer and composer autoloader?
require_once(__DIR__ . DIRECTORY_SEPARATOR . 'AbstractEntity.php');
require_once(__DIR__ . DIRECTORY_SEPARATOR . 'EntityCollection.php');
require_once(__DIR__ . DIRECTORY_SEPARATOR . 'EntityInstantiatorGenerator.php');
require_once(__DIR__ . DIRECTORY_SEPARATOR . 'ObjectEntity.php');
require_once(__DIR__ . DIRECTORY_SEPARATOR . 'QueryEntity.php');

use Net\Bazzline\Propel\Behavior\EntityInstantiator\EntityInstantiatorGenerator;
use Net\Bazzline\Propel\Behavior\EntityInstantiator\ObjectEntity;
use Net\Bazzline\Propel\Behavior\EntityInstantiator\QueryEntity;

/**
 * @author stev leibelt <artodeto@bazzline.net>
 * @since 2015-08-02
 * @todo make parameters optional (only set them when they are set) - this would enable it to define everything in the database scope and the rest in the table scope without overwriting database scoped things
 */
class AddToEntityInstantiatorBehavior extends Behavior
{
    const PARAMETER_ENTITY_ADD_IT_TO_ENTITY_INSTANTIATOR    = 'entity_add_to_entity_instantiator';
    const PARAMETER_ENTITY_INSTANTIATOR_CLASS_NAME          = 'entity_instantiator_class_name';
    const PARAMETER_ENTITY_INSTANTIATOR_INDENTION           = 'entity_instantiator_indention';
    const PARAMETER_ENTITY_INSTANTIATOR_NAMESPACE           = 'entity_instantiator_namespace';
    const PARAMETER_ENTITY_INSTANTIATOR_PATH_TO_OUTPUT      = 'entity_instantiator_path_to_output';
    const PARAMETER_ENTITY_METHOD_NAME_PREFIX               = 'entity_method_name_prefix';

    /** @var array */
    protected $parameters = array(
        self::PARAMETER_ENTITY_ADD_IT_TO_ENTITY_INSTANTIATOR    => 'true',
        self::PARAMETER_ENTITY_INSTANTIATOR_CLASS_NAME          => 'DatabaseEntityInstantiator',
        self::PARAMETER_ENTITY_INSTANTIATOR_INDENTION           => '    ',
        self::PARAMETER_ENTITY_INSTANTIATOR_NAMESPACE           => null,
        self::PARAMETER_ENTITY_INSTANTIATOR_PATH_TO_OUTPUT      => 'data',
        self::PARAMETER_ENTITY_METHOD_NAME_PREFIX               => null
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
            $generator  = $this->getGenerator();
            $entity     = $this->buildEntityFromObject($builder);
            $generator->add($entity);
        }
    }

    /**
     * @param DataModelBuilder $builder
     */
    public function addQueryToGenerator(DataModelBuilder $builder)
    {
        if ($this->addIt()) {
            $generator  = $this->getGenerator();
            $entity     = $this->buildEntityFromQuery($builder);
            $generator->add($entity);
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
            $builder->getDatabase()->getName(),
            $builder->getStubObjectBuilder()->getFullyQualifiedClassname(),
            $builder->getStubObjectBuilder()->getClassname(),
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
            $builder->getDatabase()->getName(),
            $builder->getStubQueryBuilder()->getFullyQualifiedClassname(),
            $builder->getStubQueryBuilder()->getClassname(),
            $methodNamePrefix
        );
    }

    /**
     * @param DataModelBuilder $builder
     * @return string
     */
    private function returnDatabaseNameIfMethodNamePrefixIsNotProvided(DataModelBuilder $builder)
    {
        $methodNamePrefix = (is_null($this->parameters[self::PARAMETER_ENTITY_METHOD_NAME_PREFIX]))
            ? $builder->getDatabase()->getName()
            : $this->parameters[self::PARAMETER_ENTITY_METHOD_NAME_PREFIX];

        return $methodNamePrefix;
    }

    /**
     * @return EntityInstantiatorGenerator
     */
    private function getGenerator()
    {
        $generator = EntityInstantiatorGenerator::getInstance();

        if ($generator->isNotConfigured()) {
            $pathToOutput   = $this->parameters[self::PARAMETER_ENTITY_INSTANTIATOR_PATH_TO_OUTPUT];
            $isAbsolutePath = (strncmp($pathToOutput, DIRECTORY_SEPARATOR, strlen(DIRECTORY_SEPARATOR)) === 0);

            $absolutePathToOutput   = ($isAbsolutePath)
                ? $pathToOutput
                : getcwd() . (str_repeat(DIRECTORY_SEPARATOR . '..', 4)) . DIRECTORY_SEPARATOR . $pathToOutput;
            $className      = $this->parameters[self::PARAMETER_ENTITY_INSTANTIATOR_CLASS_NAME];
            $indention      = $this->parameters[self::PARAMETER_ENTITY_INSTANTIATOR_INDENTION];
            $namespace      = $this->parameters[self::PARAMETER_ENTITY_INSTANTIATOR_NAMESPACE];

            $generator->configure($absolutePathToOutput, $className, $indention, $namespace);
        }

        return $generator;
    }

    /**
     * @return bool
     */
    private function addIt()
    {
        return (isset($this->parameters[self::PARAMETER_ENTITY_ADD_IT_TO_ENTITY_INSTANTIATOR]))
            ? $this->parameters[self::PARAMETER_ENTITY_ADD_IT_TO_ENTITY_INSTANTIATOR]
            : false;
    }
}
