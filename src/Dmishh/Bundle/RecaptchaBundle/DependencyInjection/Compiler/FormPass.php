<?php

namespace Dmishh\Bundle\RecaptchaBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Add a new templating.helper.form.resources and twig.form.resources
 */
class FormPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $templatingEngines = $container->getParameter('templating.engines');

        // PHP
        if (in_array('php', $templatingEngines)) {
            $template = 'RecaptchaBundle:Form';
            $resources = $container->getParameter('templating.helper.form.resources');
            if (!in_array($template, $resources)) {
                array_unshift($resources, $template);
            }
            $container->setParameter('templating.helper.form.resources', $resources);
        }

        // Twig
        if (in_array('twig', $templatingEngines)) {
            $template = 'RecaptchaBundle:Form:recaptcha_widget.html.twig';
            $resources = $container->getParameter('twig.form.resources');
            if (!in_array($template, $resources)) {
                array_unshift($resources, $template);
            }
            $container->setParameter('twig.form.resources', $resources);
        }
    }
}