<?php

namespace GraphAware\Neo4j\OGM\Proxy;

use GraphAware\Common\Type\Node;
use GraphAware\Neo4j\OGM\EntityManager;
use GraphAware\Neo4j\OGM\Metadata\NodeEntityMetadata;

class ProxyFactory
{
    protected $em;

    protected $classMetadata;

    protected $proxyDir;

    public function __construct(EntityManager $em, NodeEntityMetadata $classMetadata, $proxyDir = null)
    {
        $this->em = $em;
        $this->classMetadata = $classMetadata;
        $this->proxyDir = $em->getProxyDirectory();
    }

    public function fromNode(Node $node)
    {
        $object = $this->createProxy();
        $object->__setNode($node);
        $initializers = [];
        foreach ($this->classMetadata->getSimpleRelationships() as $relationship) {
            $initializer = new SingleNodeInitializer($this->em, $relationship, $this->em->getClassMetadata($relationship->getTargetEntity()));
            $initializers[$relationship->getPropertyName()] = $initializer;
        }
        $object->__setInitializers($initializers);

        return $object;
    }

    protected function createProxy()
    {
        $class = $this->classMetadata->getClassName();
        $proxyClass = $this->getProxyClass();
        $proxyFile = $this->proxyDir.'/'.$proxyClass.'.php';
        $methodProxies = $this->getMethodProxies();

        $content = <<<PROXY
<?php

use GraphAware\\Neo4j\\OGM\\Proxy\\EntityProxy;

class $proxyClass extends $class implements EntityProxy
{
    private \$em;
    private \$initialized = [];
    private \$intializers = [];
    private \$node;
    
    public function __setNode(\$node)
    {
        \$this->node = \$node;
    }
    
    public function __setInitializers(array \$initializers)
    {
        \$this->initializers = \$initializers;
    }
    
    public function __initializeProperty(\$propertyName)
    {
        \$value = \$this->initializers[\$propertyName]->initialize(\$this->node);
        \$this->\$propertyName = \$value;
    }
    
    $methodProxies
}

PROXY;

        $this->checkProxyDirectory();
        file_put_contents($proxyFile, $content);

        require $proxyFile;

        return $this->newProxyInstance($proxyClass);

    }

    protected function getMethodProxies()
    {
        $proxies = '';
        foreach ($this->classMetadata->getRelationships() as $relationship) {
            $getter = 'get'.ucfirst($relationship->getPropertyName()).'()';
            $propertyName = $relationship->getPropertyName();
            $proxies .= <<<METHOD
public function $getter
{
    self::__initializeProperty('$propertyName');
    return parent::$getter;
}

METHOD;

        }

        return $proxies;
    }

    protected function getProxyClass()
    {
        return 'neo4j_ogm_proxy_'.str_replace('\\', '_', $this->classMetadata->getClassName());
    }

    private function newProxyInstance($proxyClass)
    {
        static $prototypes = [];

        if (!array_key_exists($proxyClass, $prototypes)) {
            $prototypes[$proxyClass] = unserialize(sprintf('O:%d:"%s":0:{}', strlen($proxyClass), $proxyClass));
        }

        return clone $prototypes[$proxyClass];
    }

    private function checkProxyDirectory()
    {
        if (!is_dir($this->em->getProxyDirectory())) {
            @mkdir($this->em->getProxyDirectory());
        }
    }


}