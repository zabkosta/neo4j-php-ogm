# GraphAware Neo4j PHP OGM

## PHP Object Graph Mapper for Neo4j

!! WIP

### Usage :

Initialize the library :

```php

// Note that this will generally be done in your application by using dependency injection

require_once __DIR__.'/vendor/autoload.php';

use GraphAware\Neo4j\Client\ClientBuilder;
use GraphAware\Neo4j\OGM\Manager;

$client = ClientBuilder::create()
    ->addConnection('default', 'http://neo4j:neo4j@localhost:7474')
    ->build();

$entityManager = new Manager($client);
```


### Add annotations to your domain objects :

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
    protected $login;

    /**
     * @OGM\Property(type="int")
     */
    protected $age;


    // Getters and Setters
}
```

### Persisting and Flushing entities :

```php

$me = new User('me', 33);
$entityManager->persist($me);
$entityManager->flush();
```