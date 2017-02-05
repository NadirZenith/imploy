<?php

namespace AppBundle\Security;

use AppBundle\Entity\Pipeline;
use AppBundle\Repository\PipelineRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\PreAuthenticatedToken;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Authentication\SimplePreAuthenticatorInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\HttpFoundation\Response;

class GitSecretAuthenticator implements SimplePreAuthenticatorInterface, AuthenticationFailureHandlerInterface
{
    private $pipelineRepository;

    public function __construct(PipelineRepository $pipelineRepository)
    {
        $this->pipelineRepository = $pipelineRepository;
    }

    public function createToken(Request $request, $providerKey)
    {
        // look for an x-hub-signature header
        $signature = $request->headers->get('x-hub-signature');

        if (!$signature) {
            throw new BadCredentialsException();
            // or to just skip api key authentication
//            return null;

        } else {
            list($algo, $hash) = explode('=', $signature, 2) + array('', '');
            if (!in_array($algo, hash_algos(), TRUE)) {
                throw new BadCredentialsException("Hash algorithm '$algo' is not supported.");
            }
        }

        $content = $request->getContent();
        $payload = json_decode($content, true);
        $request->attributes->set('githubPayload', $payload);

        $email = $payload['pusher']['email'];


        $key = [$payload, $content, $signature];
        return new PreAuthenticatedToken($email, $key, $providerKey);
    }

    public function supportsToken(TokenInterface $token, $providerKey)
    {
        return $token instanceof PreAuthenticatedToken && $token->getProviderKey() === $providerKey;
    }

    public function authenticateToken(TokenInterface $token, UserProviderInterface $userProvider, $providerKey)
    {
        if (!$userProvider instanceof GitSecretUserProvider) {
            throw new \InvalidArgumentException(
                sprintf(
                    'The user provider must be an instance of GitSecretUserProvider (%s was given).',
                    get_class($userProvider)
                )
            );
        }
        list($payload, $content, $signature) = $token->getCredentials();
//        dd($token);

        $user = $userProvider->loadUserByEmail($token->getUser());
        if (!$user) {
            dd('no user');
            // CAUTION: this message will be returned to the client
            // (so don't put any un-trusted messages / error strings here)
            throw new CustomUserMessageAuthenticationException(
                sprintf('User with email "%s" does not exist.', $token->getUser())
            );
        }


        $url = $payload['repository']['url'];
        /** @var Pipeline $pipeline */
        $pipeline = $this->pipelineRepository->findOneBy(['url' => $url]);
        if (!$pipeline) {
            throw new CustomUserMessageAuthenticationException(
                sprintf('Pipeline with url "%s" does not exist.', $url)
            );
        }

        list($algo, $hash) = explode('=', $signature, 2) + array('', '');
        if ($hash !== hash_hmac($algo, $content, $pipeline->getSecurityToken())) {
//            dd('bad secret');
            throw new CustomUserMessageAuthenticationException('Hook secret does not match.');
        }

//        return new PreAuthenticatedToken($user, $gitSecret, $providerKey, array());
        return new PreAuthenticatedToken($user, $signature, $providerKey, $user->getRoles());
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        return new Response(
        // this contains information about *why* authentication failed
        // use it, or return your own message
            strtr($exception->getMessageKey(), $exception->getMessageData()),
            401
        );
    }
}
