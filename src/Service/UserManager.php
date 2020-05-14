<?php


namespace App\Service;


use App\Entity\AccessToken;
use App\Entity\User;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use http\Client;
use OAuth2\OAuth2;
use OAuth2\OAuth2ServerException;
use Psr\Container\ContainerInterface;
use Symfony\Bridge\Doctrine\Messenger\DoctrineClearEntityManagerWorkerSubscriber;
use Symfony\Component\HttpFoundation\Request;
use Twig\TokenParser\SetTokenParser;

class UserManager
{
    private $entityManager;
    private $oauth;
    protected $server;

    public function __construct(EntityManagerInterface $entityManager , OAuth2 $server)
    {
        $this->entityManager = $entityManager;
        $this->server = $server;
    }

    public function getUserByEmail($email){
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
        return $user;
    }

    public function tokenAction(Request $request)
    {
        try {
            return $this->server->grantAccessToken($request);
        } catch (OAuth2ServerException $e) {
            return $e->getHttpResponse();
        }

    }

}