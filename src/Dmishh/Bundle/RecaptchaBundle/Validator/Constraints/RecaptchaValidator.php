<?php
/**
 * This file is part of the DmishhRecaptchaBundle package.
 *
 * (c) Dmitriy Scherbina
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Dmishh\Bundle\RecaptchaBundle\Validator\Constraints;

use Recaptcher\Exception\Exception;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class RecaptchaValidator extends ConstraintValidator
{
    /**
     * @var \Symfony\Component\HttpFoundation\Request
     */
    private $request;

    /**
     * @var \Recaptcher\Recaptcha
     */
    private $recaptcha;

    /**
     * Construct.
     *
     * @param \Recaptcher\Recaptcha $recaptcha
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function __construct(\Recaptcher\Recaptcha $recaptcha, Request $request)
    {
        $this->recaptcha = $recaptcha;
        $this->request = $request;
    }

    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        try {
            $this->recaptcha->checkAnswer(
                $this->request->server->get('REMOTE_ADDR'),
                $this->request->get($this->recaptcha->getChallengeField()),
                $this->request->get($this->recaptcha->getResponseField())
            );
        } catch (Exception $e) {
            $this->context->addViolation($constraint->message);
        }
    }
}
