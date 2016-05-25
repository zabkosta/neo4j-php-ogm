### Introduction

Neo4j-PHP-OGM is an Object Graph Mapper for Neo4j in PHP.

It uses the RepositoryPattern and is very similar to the Doctrine2 ORM, also it makes uses of Doctrine Annotations and Collection library.

### Getting started - the Neo4j Movies Example

This getting started guide is based on the Neo4j movies example you can load by running the `:play movies` in the neo4j browser.

#### Installation

Require the OGM via composer :

```bash
composer require graphaware/neo4j-php-ogm:^1.0@beta
```

### Domain identification

Let's take a look at the movie graph and define what our domain objects will look like :

![Domain](_01-domain.png)

We can identify the following entities :

* a **Person** having a `name` and `born` properties
* a **Movie** having a `title`, `tagline` and `release` properties

Also, the following relationships can be identified :

a `Person` acted in a `Movie`
a `Person` wrote a `Movie`


### Mapping definition

Mapping definition is done by using **Annotations** on your domain object entities, let's build the Person model :

```php
<?php

use GraphAware\Neo4j\OGM\Annotations as OGM;

/**
 * @OGM\Node(label="Person")
 */
class Person
{
    /**
     * @OGM\GraphId()
     * @var int
     */
    protected $id;

    /**
     * @OGM\Property(type="string")
     * @var string
     */
    protected $name;

    /**
     * @OGM\Property(type="born")
     * @var int
     */
    protected $born;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getBorn()
    {
        return $this->born;
    }
}
```

##### Node

First off, you'll need to import the `GraphAware\Neo4j\OGM\Annotations` directory with the `use` statement.

Secondly, you'll need to declare your model as a graph entity, by adding the `@OGM\Node()` annotation on the class.

The `@OGM\Node()` annotation must contain the name of the label representing the person nodes in the database.


##### GraphId

The `@OGM\GraphId` annotation defines the property on which the internal neo4j node id will be mapped. This property and annotation
is mandatory.

As of now, the only allowed property name is `id`, in the future you'll be able to specify a custom property name.

##### Property

The `@OGM\Property` annotation defines which entity properties will be managed by the OGM. You can have properties without this
annotation and they will not be saved / loaded to / from the database.

The type argument defines the internal type (php) of the property, common types are `string`, 'int', `float`, ...

Currently, the exact property name used in your domain model is used as property key on the database node. (This will evolve).

