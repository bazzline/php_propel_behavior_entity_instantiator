<?php
use org\bovigo\vfs\vfsStream;

/**
 * @author stev leibelt <artodeto@bazzline.net>
 * @since 2015-08-02
 */
class AddToEntityInstantiatorBehaviorTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $buildIsNeeded = ((!class_exists('CreateEntityInstantiatorBehaviorTableOne'))
            || (!class_exists('CreateEntityInstantiatorBehaviorTableTwo')));

        if ($buildIsNeeded) {
            $fileSystem = vfsStream::setup();
            $path       = $fileSystem->url();
            //$path       = __DIR__;
            $schema     = <<<EOF
<database name="create_entity_instantiator_behavior" defaultIdMethod="native">
    <table name="CreateEntityInstantiatorBehaviorTableOne">
        <column name="id" required="true" primaryKey="true" autoIncrement="true" type="INTEGER" />
    </table>

    <table name="CreateEntityInstantiatorBehaviorTableTwo">
        <column name="id" required="true" primaryKey="true" autoIncrement="true" type="INTEGER" />
    </table>

    <behavior name="add_to_entity_instantiator">
        <parameter name="entity_add_to_entity_instantiator" value="true" />
        <parameter name="entity_instantiator_class_name" value="Net\Bazzline\Propel" />
        <parameter name="entity_instantiator_indention" value="Net\Bazzline\Propel" />
        <parameter name="entity_instantiator_namespace" value="Net\Bazzline\Propel" />
        <parameter name="entity_instantiator_path_to_output" value="$path" />
        <parameter name="entity_method_name_prefix" value="Net\Bazzline\Propel" />
    </behavior>
</database>
EOF;

            $builder        = new PropelQuickBuilder();
            $configuration  = $builder->getConfig();
            $configuration->setBuildProperty('behavior.add_to_entity_instantiator.class', __DIR__ . '/../source/AddToEntityInstantiatorBehavior');
            $builder->setConfig($configuration);
            $builder->setSchema($schema);

            $builder->build();
        }
    }

    public function testFoo()
    {
        $this->assertTrue(true);
    }
/*
    public function testMethodExist()
    {
        $this->assertTrue(method_exists('PostQuery', 'createEntity'));
    }

    public function testCreateEntity()
    {
        $entity = new Post();
        $query  = PostQuery::create();

        $this->assertEquals($entity, $query->createEntity());
    }
*/
}
