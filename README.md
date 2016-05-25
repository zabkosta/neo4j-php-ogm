# GraphAware Neo4j PHP OGM

## Object Graph Mapper for Neo4j in PHP

Beta Release only

### Basic Usage :

Storing / retrieving entities is done by declaring your entities with mapping annotations. It is very similar to the Doctrine2 ORM.

```php
<?php

namespace Demo;

use GraphAware\Neo4j\OGM\Annotations as OGM;

/**
 * @OGM\Node(label="User")
 */
class User
{
    /**
     * @OGM\GraphId()
     */
    protected $id;

    /**
     * @OGM\Property(type="string")
     */
    protected $name;

    /**
     * @OGM\Property(type="int")
     */
    protected $age;


    // Getters and Setters
}
```

```php
$em = $this->getEntityManager();
// The entity manager is generally created somewhere else in your application and available in the dependency injection container.
// More info about the creation is in the documentation

// Creating and Persisting a User

$bart = new User('Bart Johnson', 33);
$entityManager->persist($bart);
$entityManager->flush();

// Retrieving from the database

$john = $this-em->getRepository(User::class)->findOneBy('name', 'John Doe');
echo $john->getAge();

// Updating
$john->setAge(35);
$this->em->flush();
```

---

## Documentation

Coming soon...

## License

The library is released under the MIT License, refer to the LICENSE file bundled with this package.