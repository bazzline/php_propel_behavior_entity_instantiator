<?php
use Net\Bazzline\Propel\Behavior\EntityInstantiator\Manager;
use org\bovigo\vfs\vfsStream;

/**
 * @author stev leibelt <artodeto@bazzline.net>
 * @since 2015-08-02
 */
class AddToEntityInstantiatorBehaviorTest extends PHPUnit_Framework_TestCase
{
    /** @var string */
    private $className;

    /** @var string */
    private $extends;

    /** @var string */
    private $indention;

    /** @var string */
    private $namespace;

    /** @var string */
    private $path;

    /** @var string */
    private $prefix;

    protected function setUp()
    {
        //begin of setting runtime environments
        $fileSystem = vfsStream::setup();

        $this->className    = 'ExampleInstantiator';
        $this->extends      = '\stdClass';
        $this->indention    = '  ';
        $this->namespace    = 'Test\Net\Bazzline\Propel';
        $this->path         = $fileSystem->url();
        $this->prefix       = 'create';
        //end of setting runtime environments

        $buildIsNeeded = ((!class_exists('TableOne'))
            || (!class_exists('TableTwo')));

        if ($buildIsNeeded) {

            $schema     = <<<EOF
<database name="example_database" defaultIdMethod="native">
    <behavior name="add_to_entity_instantiator">
        <parameter name="entity_instantiator_class_name" value="$this->className" />
        <parameter name="entity_instantiator_extends" value="$this->extends" />
        <parameter name="entity_instantiator_indention" value="$this->indention" />
        <parameter name="entity_instantiator_namespace" value="$this->namespace" />
        <parameter name="entity_instantiator_path_to_output" value="$this->path" />
        <parameter name="entity_instantiator_method_name_prefix" value="$this->prefix" />
        <parameter name="entity_instantiator_add_to_entity_instantiator" value="true" />
        <parameter name="entity_default_connection_mode" value="Propel::CONNECTION_READ" />
        <parameter name="entity_default_connection_name" value="my_default_connection_name" />
    </behavior>

    <table name="table_one">
        <column name="id" required="true" primaryKey="true" autoIncrement="true" type="INTEGER" />
    </table>

    <table name="table_two">
        <column name="id" required="true" primaryKey="true" autoIncrement="true" type="INTEGER" />

        <behavior name="add_to_entity_instantiator">
            <parameter name="entity_instantiator_add_to_entity_instantiator" value="false" />
        </behavior>
    </table>
</database>
EOF;

            $builder        = new PropelQuickBuilder();
            $configuration  = $builder->getConfig();
            $configuration->setBuildProperty('behavior.add_to_entity_instantiator.class', __DIR__ . '/../source/AddToEntityInstantiatorBehavior');
            $builder->setConfig($configuration);
            $builder->setSchema($schema);

            $builder->build();
            //we have to call generate manually since it is called only when php execution is finished
            Manager::getInstance()->generate();
        }
    }

    public function testInstantiatorFileExists()
    {
        $path = $this->path . DIRECTORY_SEPARATOR . $this->className . '.php';
        $this->assertTrue(file_exists($path));
echo(file_get_contents($path));
        require_once ($path);
    }

    /**
     * @depends testInstantiatorFileExists
     */
    public function testInstantiatorClassExists()
    {
        $fullQualifiedClassName = $this->namespace . '\\' . $this->className;
        $this->assertTrue(class_exists($fullQualifiedClassName));
    }

    /**
     * @depends testInstantiatorClassExists
     */
    public function testInstantiatorExtendsStdClass()
    {
        $fullQualifiedClassName = $this->namespace . '\\' . $this->className;
        $instantiator           = new $fullQualifiedClassName();

        $this->assertInstanceOf('stdClass', $instantiator);
    }

    /**
     * @depends testInstantiatorClassExists
     */
    public function testInstantiatorClassHasExpectedMethods()
    {
        $fullQualifiedClassName = $this->namespace . '\\' . $this->className;

        $methods = get_class_methods($fullQualifiedClassName);

        $this->assertContains('getConnection', $methods);
        $this->assertContains('createTableOne', $methods);
        $this->assertContains('createTableOneQuery', $methods);
    }

    /**
     * @depends testInstantiatorClassHasExpectedMethods
     */
    public function testThatMethodsReturningRightInstances()
    {
        $fullQualifiedClassName = $this->namespace . '\\' . $this->className;
        $instantiator           = new $fullQualifiedClassName();

        $this->assertTrue(($instantiator->createTableOne() instanceof TableOne));
        $this->assertTrue(($instantiator->createTableOneQuery() instanceof TableOneQuery));
    }
}
