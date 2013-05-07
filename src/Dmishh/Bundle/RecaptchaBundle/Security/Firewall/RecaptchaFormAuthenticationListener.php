<?php
/**
 * This file is part of the DmishhRecaptchaBundle package.
 * Based on UsernamePasswordFormAuthenticationListener from Symfony2 Security Component
 *
 * (c) Fabien Potencier
 * (c) Dmitriy Scherbina
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Dmishh\Bundle\RecaptchaBundle\Security\Firewall;

use Recaptcher\RecaptchaInterface;
use Recaptcher\Exception\Exception;
use Recaptcher\Exception\InvalidRecaptchaException;
use Symfony\Component\Form\Extension\Csrf\CsrfProvider\CsrfProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Http\Firewall\AbstractAuthenticationListener;
use Symfony\Component\Security\Http\Session\SessionAuthenticationStrategyInterface;
use Symfony\Component\Security\Http\HttpUtils;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class RecaptchaFormAuthenticationListener extends AbstractAuthenticationListener
{
    /**
     * @var \Symfony\Component\Form\Extension\Csrf\CsrfProvider\CsrfProviderInterface
     */
    private $csrfProvider;

    /**
     * @var \Recaptcher\RecaptchaInterface
     */
    private $recaptcha;

    /**
     * @var boolean
     */
    private $recaptchaDisabled;

    /**
     * {@inheritdoc}
     */
    public function __construct(SecurityContextInterface $securityContext, AuthenticationManagerInterface $authenticationManager, SessionAuthenticationStrategyInterface $sessionStrategy, HttpUtils $httpUtils, $providerKey, AuthenticationSuccessHandlerInterface $successHandler, AuthenticationFailureHandlerInterface $failureHandler, array $options = array(), LoggerInterface $logger = null, EventDispatcherInterface $dispatcher = null, CsrfProviderInterface $csrfProvider = null)
    {
        parent::__construct($securityContext, $authenticationManager, $sessionStrategy, $httpUtils, $providerKey, $successHandler, $failureHandler, array_merge(array(
            'username_parameter' => '_username',
            'password_parameter' => '_password',
            'csrf_parameter'     => '_csrf_token',
            'intention'          => 'authenticate',
            'post_only'          => true,
            ), $options), $logger, $dispatcher
        );

        $this->csrfProvider = $csrfProvider;
    }

    /**
     * @param \Recaptcher\RecaptchaInterface $recaptcha
     * @param bool $disabled
     */
    public function setRecaptcha(RecaptchaInterface $recaptcha, $disabled = false)
    {
        $this->recaptcha = $recaptcha;
        $this->recaptchaDisabled = $disabled;
    }

    /**
     * {@inheritdoc}
     */
    protected function requiresAuthentication(Request $request)
    {
        if ($this->options['post_only'] && !$request->isMethod('post')) {
            return false;
        }

        return parent::requiresAuthentication($request);
    }

    /**
     * {@inheritdoc}
     */
    protected function attemptAuthentication(Request $request)
    {
        if ($this->options['post_only'] && 'post' !== strtolower($request->getMethod())) {
            if (null !== $this->logger) {
                $this->logger->debug(sprintf('Authentication method not supported: %s.', $request->getMethod()));
            }

            return null;
        }

        if (null !== $this->csrfProvider) {
            $csrfToken = $request->get($this->options['csrf_parameter'], null, true);

            if (false === $this->csrfProvider->isCsrfTokenValid($this->options['intention'], $csrfToken)) {
                throw new InvalidCsrfTokenException('Invalid CSRF token.');
            }
        }

        if (null !== $this->recaptcha && false === $this->recaptchaDisabled) {
            try {
                if (true !== $this->recaptcha->checkAnswer(
                    $request->server->get('REMOTE_ADDR'),
                    $request->get($this->recaptcha->getChallengeField()),
                    $request->get($this->recaptcha->getResponseField()))
                ) {
                    throw new InvalidRecaptchaException('Invalid captcha.');
                }
            } catch (Exception $e) {
                throw new AuthenticationException('Invalid captcha.', null, null, $e);
            }
        }

        $username = trim($request->get($this->options['username_parameter'], null, true));
        $password = $request->get($this->options['password_parameter'], null, true);

        $request->getSession()->set(SecurityContextInterface::LAST_USERNAME, $username);

        return $this->authenticationManager->authenticate(new UsernamePasswordToken($username, $password, $this->providerKey));
    }
}
