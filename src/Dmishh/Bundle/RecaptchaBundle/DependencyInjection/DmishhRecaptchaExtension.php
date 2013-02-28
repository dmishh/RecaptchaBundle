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

class DmishhRecaptchaExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');

        $templateEngines = $container->getParameter('templating.engines');

        if (in_array('php', $templateEngines)) {
            $container->setParameter(
                'templating.helper.form.resources',
                array_merge(
                    $container->getParameter('templating.helper.form.resources'),
                    array('DmishhRecaptchaBundle:Form')
                )
            );
        }

        if (in_array('twig', $templateEngines)) {
            $container->setParameter(
                'twig.form.resources',
                array_merge(
                    $container->getParameter('twig.form.resources'),
                    array('DmishhRecaptchaBundle:Form:recaptcha_widget.html.twig')
                )
            );
        }
    }
}
