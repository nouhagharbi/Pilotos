<?php

namespace App\Controller;


use App\Entity\User;
use FOS\UserBundle\Util\TokenGeneratorInterface;
use OAuth2\OAuth2ServerException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Authentication\Token\SwitchUserToken;
use FOS\UserBundle\Doctrine\UserManager;

class UserController extends AbstractController
{

    /**
     * @Route("/signin", name="signin")
     */
    public function loginAction(Request $request)
    {
        $email = $request->get('email');
        $password = $request->get('password');

        $um = $this->getUserManager();
        $user = $um->findUserByEmail($email);

        if (!$user instanceof User || !$this->container->get('app.global.service')->checkUserPassword($user, $password)) {
            return new JsonResponse(['message' => 'Invalid credentials'], 500);
        }

        if (!$user->isEnabled()) {
            return new JsonResponse(['message' => 'User account is disabled.'], 500);
        }


        return new JsonResponse($this->getAuth2Token($user, $request));
    }

    protected function getUserManager()
    {
        return $this->get('pugx_user_manager');
    }

}