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

        return $object;
    }

    protected function createProxy()
    {
        $class = $this->classMetadata->getClassName();
        $proxyClass = $this->getProxyClass();
        $proxyFile = $this->proxyDir.'/'.$proxyClass.'.php';

        $content = <<<PROXY
<?php

use GraphAware\\Neo4j\\OGM\\Proxy\\EntityProxy;

class $proxyClass extends $class implements EntityProxy
{
    
}

PROXY;

        $this->checkProxyDirectory();
        file_put_contents($proxyFile, $content);

        require $proxyFile;

        return $this->newProxyInstance($proxyClass);

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