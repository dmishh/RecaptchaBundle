<?php
/**
 * This file is part of the DmishhRecaptchaBundle package.
 *
 * (c) Dmitriy Scherbina
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Dmishh\Bundle\RecaptchaBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class RecaptchaExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        foreach ($config as $key => $value) {
            $container->setParameter('recaptcha.' . $key, $value);
        }

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');

        $this->registerResources($container);
    }

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    private function registerResources(ContainerBuilder $container)
    {
        $templateEngines = $container->getParameter('templating.engines');

        // PHP
        if (in_array('php', $templateEngines)) {
            $container->setParameter(
                'templating.helper.form.resources',
                array_merge(
                    $container->getParameter('templating.helper.form.resources'),
                    array('RecaptchaBundle:Form')
                )
            );
        }

        // Twig
        if (in_array('twig', $templateEngines)) {
            $container->setParameter(
                'twig.form.resources',
                array_merge(
                    $container->getParameter('twig.form.resources'),
                    array('RecaptchaBundle:Form:recaptcha_widget.html.twig')
                )
            );
        }
    }
}
