<?php

namespace App\Security;

use App\Repository\UserRepository;
use App\Service\JwtService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationCredentialsNotFoundException;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

class JwtTokenAuthenticator extends AbstractGuardAuthenticator {

    /**
     * @var \App\Service\JwtService
     */
    private $jwtService;

    /**
     * @var \App\Repository\UserRepository
     */
    private $userRepository;

    /**
     * JwtTokenAuthenticator constructor.
     *
     * @param \App\Service\JwtService $jwtService
     * @param \App\Repository\UserRepository $userRepository
     */
    public function __construct(JwtService $jwtService, UserRepository $userRepository) {
        $this->jwtService = $jwtService;
        $this->userRepository = $userRepository;
    }

    public function supports(Request $request) {
        return $request->headers->has('Authorization');
    }

    public function getCredentials(Request $request) {
        return [
            'token' => trim(str_replace('Bearer', '', $request->headers->get('Authorization')))
        ];
    }

    public function getUser($credentials, UserProviderInterface $userProvider): UserInterface {
        $authToken = $credentials['token'];

        if (is_null($authToken)) {
            throw new AuthenticationCredentialsNotFoundException();
        }

        try {
            $tokenData = $this->jwtService->validateToken($authToken);

            $user = $this->userRepository->findOneBy(['username' => $tokenData->sub]);

            if (is_null($user)) {
                throw new UsernameNotFoundException();
            }

            return $user;
        } catch (\Exception $exception) {
            throw new AuthenticationException($exception->getMessage());
        }
    }

    public function checkCredentials($credentials, UserInterface $user) {
        return TRUE;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception) {
        $data = [
            'message' => strtr($exception->getMessageKey(), $exception->getMessageData())
        ];

        return new JsonResponse($data, Response::HTTP_FORBIDDEN);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey) {
        return NULL;
    }

    public function start(Request $request, AuthenticationException $authException = NULL) {
        $data = [
            'message' => 'Authentication Required'
        ];

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }

    public function supportsRememberMe() {
        return FALSE;
    }

}
