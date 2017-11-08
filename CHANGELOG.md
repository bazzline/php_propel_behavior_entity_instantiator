# Change Log

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/)
and this project adheres to [Semantic Versioning](http://semver.org/).

## [Open]

### To Add

* add a flag to enable or disable "createFoo" (ObjectEntity) generation
    * put flag into configuration
    * implement switch in FileContentGenerator
* add a flag to enable or disable "createFooQuery" (QueryEntity) generation
    * put flag into configuration
    * implement switch in FileContentGenerator

### To Change

* extend existing unit tests
    * rename the existing AddToEntityInstantiatorBehavior*Tests to IntegrationTest
    * write unit tests for all existing classes
    * write a unit test how the generated code behaves if the instantiator is in the same namespace as the class to load (can happen but is currently not supported)

## [Unreleased]

### Added

### Changed

## [1.2.2](https://github.com/bazzline/php_propel_behavior_create_entity/tree/1.2.2) - released at 08.11.2017

### Changed

* fixed error in the generated doc-block when fully qualified names are generated ("@return \Foo" instead of "@return Foo")

## [1.2.1](https://github.com/bazzline/php_propel_behavior_create_entity/tree/1.2.1) - released at 07.11.2017

### Changed

* fixed errors in the [readme](README.md)

## [1.2.0](https://github.com/bazzline/php_propel_behavior_create_entity/tree/1.2.0) - released at 07.11.2017

### Added

* implemented [requested feature](https://github.com/bazzline/php_propel_behavior_entity_instantiator/issues/5) by adding optional parameter `entity_instantiator_use_fully_qualified_name`

### Changed

* styled
* dropped official support to php lower 5.6

## [1.1.0](https://github.com/bazzline/php_propel_behavior_create_entity/tree/1.1.0) - released at 03.08.2017

### Added

* added optional xml parameter "entity_instantiator_default_connection_mode"
* added optional xml parameter "entity_instantiator_default_connection_name"
* added support for php 7.1

### Changed

* converted history into dedicated file CHANGELOG.md
* extended code coverage
* fixed [issue 2](https://github.com/bazzline/php_propel_behavior_entity_instantiator/issues/2)
* fixed [issue 3](https://github.com/bazzline/php_propel_behavior_entity_instantiator/issues/3)
* renamed xml parameter "entity_method_name_prefix" to "entity_instantiator_method_name_prefix"
* renamed xml parameter "entity_add_to_entity_instantiator" to "entity_instantiator_add_to_entity_instantiator"

## [1.0.2](https://github.com/bazzline/php_propel_behavior_create_entity/tree/1.0.2) - released at 23.01.2017

### Changed

* updated depenency

## [1.0.1](https://github.com/bazzline/php_propel_behavior_create_entity/tree/1.0.1) - released at 30.05.2016

### Added

### Changed

* updated mockery

## [1.0.0](https://github.com/bazzline/php_propel_behavior_create_entity/tree/1.0.0) - released at 06.03.2016

### Added

* added support for php 7.0

### Changed

* removed support for php 5.3.3
* updated depenency

## [0.4.3](https://github.com/bazzline/php_propel_behavior_create_entity/tree/0.4.2) - released at 11.01.2016

### Changed

* fixed dependency handling for phpunit 4.8.*

## [0.4.2](https://github.com/bazzline/php_propel_behavior_create_entity/tree/0.4.2) - released at 11.12.2015

### Changed

* updated dependencies

## [0.4.1](https://github.com/bazzline/php_propel_behavior_create_entity/tree/0.4.1) - released at 06.11.2015

### Changed

* updated dependencies

## [0.4.0](https://github.com/bazzline/php_propel_behavior_create_entity/tree/0.4.0) - released at 20.09.2015

### Changed

* refactor *EntityInstantiatorGenerator* (extracted *generateContent* and *configure* method to delegate responsibilities)

## [0.3.0](https://github.com/bazzline/php_propel_behavior_create_entity/tree/0.3.0) - released at 18.09.2015

### Added

### Changed

* covered "Propel" and "PDO" also with use statements
* use statements are ordered alphabetically
* use "use" instead and full qualified names usage in the methods

## [0.2.3](https://github.com/bazzline/php_propel_behavior_create_entity/tree/0.2.3) - released at 17.09.2015

### Changed

* fixed documentation issue
* fixed phpdoc issue for generated method *getConnection*
* phpdoc optimization when dealing with the `EntityCollection`

## [0.2.2](https://github.com/bazzline/php_propel_behavior_create_entity/tree/0.2.2) - released at 10.09.2015

### Changed

* refactored `EntityInstantiatorGenerator` internal code generation

## [0.2.1](https://github.com/bazzline/php_propel_behavior_create_entity/tree/0.2.1) - released at 09.09.2015

### Added

* added "$name" and "$mode" as optional parameter for "getConnection()"

## [0.2.0](https://github.com/bazzline/php_propel_behavior_create_entity/tree/0.2.0) - released at 09.09.2015

### Changed

* fixed [issues/1](https://github.com/bazzline/php_propel_behavior_entity_instantiator/issues/1)

## [0.1.2](https://github.com/bazzline/php_propel_behavior_create_entity/tree/0.1.2) - released at 02.09.2015

### Changed

* changed default method prefix from "databaseName" to "createDatabaseName"

## [0.1.1](https://github.com/bazzline/php_propel_behavior_create_entity/tree/0.1.1) - released at 31.08.2015

### Added

* added optional parameter `extends`

## [0.1.0](https://github.com/bazzline/php_propel_behavior_create_entity/tree/0.1.0) - released at 31.08.2015

### Added

* initial release
