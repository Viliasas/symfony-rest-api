<?php

namespace App\Service;

use Firebase\JWT\JWT;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\User\UserInterface;

class JwtService {

    /**
     * @var string Base url of the system
     */
    private $baseUrl;

    /**
     * @var string RS256 private key
     */
    private $privateKey;

    /**
     * @var string Public key
     */
    private $publicKey;

    /**
     * JwtService constructor.
     *
     * @param \Symfony\Component\HttpFoundation\RequestStack $requestStack
     */
    public function __construct(RequestStack $requestStack) {
        $this->baseUrl = $requestStack->getCurrentRequest()
            ->getSchemeAndHttpHost();

        $this->privateKey = base64_decode($_ENV['JWT_PRIVATE_KEY']);
        $this->publicKey = base64_decode($_ENV['JWT_PUBLIC_KEY']);
    }

    /**
     * Validates JWT token and return decoded token data.
     *
     * @param string $token JWT token
     *
     * @return \stdClass|null
     */
    public function validateToken(string $token): ?\stdClass {
        return JWT::decode($token, $this->publicKey, ['RS256']);
    }

    /**
     * Generates JWT token from User.
     *
     * @param \Symfony\Component\Security\Core\User\UserInterface $user
     *
     * @return string
     */
    public function generateToken(UserInterface $user): string {
        $token = [
            'iss' => $this->baseUrl,
            'aud' => $this->baseUrl,
            'iat' => time(),
            'exp' => time() + $_ENV['JWT_TOKEN_VALIDITY'],
            'sub' => $user->getUsername(),
            'roles' => $user->getRoles()
        ];

        return JWT::encode($token, $this->privateKey, 'RS256');
    }

}
