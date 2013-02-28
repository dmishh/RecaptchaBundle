<?php
/**
 * This file is part of the DmishhRecaptchaBundle package.
 *
 * (c) Dmitriy Scherbina
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Dmishh\Bundle\RecaptchaBundle\Form\Type;

use Dmishh\Bundle\RecaptchaBundle\Validator\Constraints;
use Recaptcher\RecaptchaInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class RecaptchaType extends AbstractType
{
    /**
     * @var \Recaptcher\RecaptchaInterface
     */
    private $recaptcha;

    /**
     * Construct.
     *
     * @param \Recaptcher\RecaptchaInterface $recaptcha
     */
    public function __construct(RecaptchaInterface $recaptcha)
    {
        $this->recaptcha = $recaptcha;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);

        $resolver->setDefaults(
            array(
                 'mapped' => false,
                 'error_bubbling' => false,
                 'constraints' => new Constraints\Recaptcha()
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars = array_replace($view->vars, array('recaptcha' => $this->recaptcha));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'recaptcha';
    }
}
