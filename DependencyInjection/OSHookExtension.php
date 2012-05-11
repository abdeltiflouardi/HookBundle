<?php

namespace OS\HookBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class OSHookExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configs = $configs[0];

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');

        $scheme = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https' : 'http';

        foreach ($configs as $key => $config) {
            if (is_array($config)) {
                foreach ($config as $k => $v) {
                    $configs[$key][$k] = str_replace('{{_scheme}}', $scheme, $v);
                }
            } else {
                $configs[$key] = str_replace('{{_scheme}}', $scheme, $config);
            }
        }

        $container->setParameter('hook_configs', $configs);
    }
}