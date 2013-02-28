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
use Recaptcher\Recaptcha as RecaptchaService;
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
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Recaptcher\Recaptcha $recaptcha
     */
    public function __construct(RecaptchaService $recaptcha, Request $request)
    {
        $this->recaptcha = $recaptcha;
        $this->request = $request;
    }

    /**
     * {@inheritdoc}
     */
    public function isValid($value, Constraint $constraint)
    {
        try {
            return $this->recaptcha->checkAnswer(
                $this->request->server->get('REMOTE_ADDR'),
                $this->request->get(RecaptchaService::CHALLANGE_FIELD),
                $this->request->get(RecaptchaService::RESPONSE_FIELD)
            );
        } catch (Exception $e) {
            $this->context->addViolation($constraint->message);

            return false;
        }
    }
}
