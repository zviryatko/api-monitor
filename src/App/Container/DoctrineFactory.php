<?php

namespace App\Container;

use Doctrine\Common\Cache\Cache;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\UnderscoreNamingStrategy;
use Doctrine\ORM\ORMSetup;
use Interop\Container\ContainerInterface;
use Symfony\Component\Cache\Adapter\PhpFilesAdapter;

class DoctrineFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $config = $container->has('config') ? $container->get('config') : [];
        $proxyDir = (isset($config['doctrine']['orm']['proxy_dir'])) ?
            $config['doctrine']['orm']['proxy_dir'] : 'data/cache/EntityProxy';
        $proxyNamespace = (isset($config['doctrine']['orm']['proxy_namespace'])) ?
            $config['doctrine']['orm']['proxy_namespace'] : 'EntityProxy';
        $autoGenerateProxyClasses = (isset($config['doctrine']['orm']['auto_generate_proxy_classes'])) ?
            $config['doctrine']['orm']['auto_generate_proxy_classes'] : false;
        $underscoreNamingStrategy = isset($config['doctrine']['orm']['underscore_naming_strategy']) && $config['doctrine']['orm']['underscore_naming_strategy'];

        // Doctrine ORM
        $doctrine = ORMSetup::createAttributeMetadataConfiguration(['src/App/Entity']);
        $doctrine->setProxyDir($proxyDir);
        $doctrine->setProxyNamespace($proxyNamespace);
        $doctrine->setAutoGenerateProxyClasses($autoGenerateProxyClasses);
        if ($underscoreNamingStrategy) {
            $doctrine->setNamingStrategy(new UnderscoreNamingStrategy());
        }

        // Cache
//        $cache = $container->get(Cache::class);
//        $doctrine->setQueryCache($cache);
//        $doctrine->setResultCache($cache);
//        $doctrine->setMetadataCache($cache);
        $queryCache = new PhpFilesAdapter('doctrine_queries');
        $doctrine->setQueryCache($queryCache);
        $resultCache = new PhpFilesAdapter('doctrine_results', 0, 'data/cache/');
        $doctrine->setResultCache($resultCache);
        $metaCache = new PhpFilesAdapter('doctrine_metadata', 0, 'data/cache/');
        $doctrine->setMetadataCache($metaCache);

        $connection = DriverManager::getConnection(
            $config['doctrine']['connection']['orm_default']['params'],
            $doctrine
        );

        // EntityManager
        return new EntityManager($connection, $doctrine);
    }
}
