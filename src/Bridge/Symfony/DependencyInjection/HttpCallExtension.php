<?php

namespace Shapecode\HTTPCall\Bridge\Symfony\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 * Class HttpCallExtension
 *
 * @package Shapecode\HTTPCall\Bridge\Symfony\DependencyInjection
 * @author  Nikita Loges
 */
class HttpCallExtension extends Extension
{

    /**
     * @inheritdoc
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $locator = new FileLocator(__DIR__ . '/../Resources/config');
        $loader = new YamlFileLoader($container, $locator);

        $loader->load('services.yml');
    }
}
